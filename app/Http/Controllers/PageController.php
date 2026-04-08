<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;

class PageController extends Controller
{
    private function generateWebpPath($file, $folder)
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $baseName = Str::slug($originalName);
        $filename = $baseName . '.webp';
        return $folder . '/' . $filename;
    }

    public function index()
    {
        $pages = Page::all();
        return view('pages.index', compact('pages'));
    }

    public function create()
    {
        return view('pages.create');
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255']);
        $slug = Str::slug($request->input('title'));

        if (Page::where('slug', $slug)->exists()) {
            return redirect()->back()->withErrors(['title' => 'A page with a similar title already exists.'])->withInput();
        }

        Page::create(['title' => $request->input('title'), 'slug' => $slug]);
        return redirect()->route('pages.index')->with('success', 'Page created successfully!');
    }

    public function edit(Page $page)
    {
        $viewName = 'pages.edit-' . $page->slug;
        if (!view()->exists($viewName)) {
            $viewName = 'pages.edit-default';
        }

        $metaData = $page->meta->pluck('meta_value', 'meta_key')->toArray();
        return view($viewName, compact('page', 'metaData'));
    }

    public function update(Request $request, Page $page)
    {
        // 1. Load Config
        $config = config("page-fields.{$page->slug}");

        if (!$config) {
            return redirect()->back()->withErrors("Configuration for '{$page->slug}' not found.");
        }

        // 2. Dynamic Validation
        $rules = ['title' => 'required|string|max:255'];
        foreach ($config['text_fields'] as $field) $rules[$field] = 'nullable|string';
        foreach ($config['image_fields'] as $field) $rules[$field] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:9048';
        foreach ($config['no_webp_fields'] ?? [] as $field) $rules[$field] = 'nullable|image|max:9048';
        foreach ($config['video_fields'] as $field) $rules[$field] = 'nullable|file|mimes:mp4,mov,ogg|max:90240';
        foreach ($config['slider_fields'] as $field) {
            $rules[$field] = 'array';
            $rules["$field.*"] = 'image|max:9048';
        }
        foreach ($config['select2_offers_fields'] ?? [] as $field) {
            $rules[$field] = 'nullable|array';
            $rules["{$field}.*"] = 'exists:offers,id';
        }
        
        // Add validation for dynamic slider
        foreach ($config['dynamic_slider_fields'] ?? [] as $field) {
            $rules['slider_bg_new'] = 'nullable|array';
            $rules['slider_bg_new.*'] = 'nullable|image|max:9048';
            $rules['slider_fg_new'] = 'nullable|array';
            $rules['slider_fg_new.*'] = 'nullable|image|max:9048';
            $rules['slider_text'] = 'nullable|array';
            $rules['slider_text.*'] = 'nullable|string';
        }

        $validatedData = $request->validate($rules);

        // 3. Update Title
        $page->update(['title' => $request->title]);

        $manager = new ImageManager(new Driver());

        // 4. Handle Text Fields
        foreach ($config['text_fields'] as $field) {
            if ($request->has($field)) {
                $this->updateMeta($page->id, $field, $request->input($field));
            }
        }

        // 5. Handle Standard WebP Images + Removal
        foreach ($config['image_fields'] as $field) {
            // Logic for Removal
            if ($request->has('remove_' . $field)) {
                $this->deleteOldFile($page, $field);
                $this->updateMeta($page->id, $field, null);
            } 
            // Logic for New Upload
            elseif ($request->hasFile($field)) {
                $this->processImage($request->file($field), $page, $field, $manager, true);
            }
        }

        // 6. Handle "No WebP" Images + Removal
        foreach ($config['no_webp_fields'] ?? [] as $field) {
            if ($request->has('remove_' . $field)) {
                $this->deleteOldFile($page, $field);
                $this->updateMeta($page->id, $field, null);
            } 
            elseif ($request->hasFile($field)) {
                $this->processImage($request->file($field), $page, $field, $manager, false);
            }
        }

        // 7. Handle Videos & Video Removal
        foreach ($config['video_fields'] as $field) {
            // Check for removal (supports both field_remove and remove_field naming conventions)
            if ($request->has($field . '_remove') || $request->has('remove_' . $field)) {
                $this->deleteOldFile($page, $field);
                $this->updateMeta($page->id, $field, null);
            } 
            elseif ($request->hasFile($field)) {
                $this->deleteOldFile($page, $field);
                $path = $request->file($field)->store('pages', 'public');
                $this->updateMeta($page->id, $field, $path);
            }
        }

        // 8. Handle Multi-Image Sliders (Handles addition and deletion internally)
        foreach ($config['slider_fields'] as $field) {
            $this->handleSlider($request, $page, $field, $manager);
        }

        // 9. Handle Dynamic Sliders (BG + FG + Text)
        foreach ($config['dynamic_slider_fields'] ?? [] as $field) {
            $this->handleDynamicSlider($request, $page, $field, $manager);
        }

        // 10. Handle Select2 Fields (multi-select)
        foreach ($config['select2_offers_fields'] ?? [] as $field) {
            if ($request->has($field)) {
                $this->updateMeta(
                    $page->id,
                    $field,
                    json_encode($request->input($field))
                );
            } else {
                // If nothing selected, clear it
                $this->updateMeta($page->id, $field, null);
            }
        }

        return redirect()->route('pages.edit', $page->id)->with('status', 'Page updated successfully!');
    }

    // --- Private Helper Methods ---

    private function updateMeta($pageId, $key, $value)
    {
        PageMeta::updateOrCreate(['page_id' => $pageId, 'meta_key' => $key], ['meta_value' => $value]);
    }

    private function deleteOldFile($page, $key)
    {
        $oldPath = $page->meta->firstWhere('meta_key', $key)?->meta_value;
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }
    }

    private function processImage($file, $page, $field, $manager, $webp = true)
    {
        $this->deleteOldFile($page, $field);

        if ($webp) {
            $relativePath = $this->generateWebpPath($file, 'pages');

            if ($file->getMimeType() === 'image/webp') {
                // Already WebP → store original as-is, no re-encoding
                Storage::disk('public')->putFileAs(
                    dirname($relativePath),
                    $file,
                    basename($relativePath)
                );
            } else {
                $manager->read($file->getRealPath())
                        ->scaleDown(1400)
                        ->toWebp(80)
                        ->save(storage_path('app/public/' . $relativePath));
            }
        } else {
            $relativePath = $file->store('pages', 'public');
        }

        $this->updateMeta($page->id, $field, $relativePath);
    }

    private function handleSlider($request, $page, $field, $manager)
    {
        // 1. Get what is CURRENTLY in the Database
        $oldMeta = $page->meta->firstWhere('meta_key', $field);
        $dbPaths = $oldMeta ? json_decode($oldMeta->meta_value, true) : [];
        if (!is_array($dbPaths)) $dbPaths = [];

        // 2. Determine what to KEEP from the existing images
        $existing = $request->input($field . '_existing'); 
        $toRemove = $request->input('remove_' . $field, []);

        if (is_array($existing)) {
            $keepPaths = array_diff($existing, (array)$toRemove);
        } else {
            $keepPaths = array_diff($dbPaths, (array)$toRemove);
        }

        // 3. Physical Deletion Safety
        foreach ($dbPaths as $pathInDb) {
            if (!in_array($pathInDb, $keepPaths) && in_array($pathInDb, (array)$toRemove)) {
                Storage::disk('public')->delete($pathInDb);
            }
        }

        // 4. Process New Uploads
        $newPaths = [];
        if ($request->hasFile($field)) {
            foreach ($request->file($field) as $file) {
                $mime = $file->getMimeType();
                $path = $this->generateWebpPath($file, 'pages');

                if ($mime === 'image/webp') {
                    // Already WebP → store as-is
                    Storage::disk('public')->putFileAs(
                        dirname($path),
                        $file,
                        basename($path)
                    );
                } else {
                    $manager->read($file->getRealPath())
                            ->scaleDown(1200)
                            ->toWebp(75)
                            ->save(storage_path('app/public/' . $path));
                }

                $newPaths[] = $path;
            }
        }

        // 5. Final Merge
        $finalPaths = array_values(array_merge($keepPaths, $newPaths));

        // Only update if we actually have data or if the field was present in request
        if ($request->hasFile($field) || $request->has($field . '_existing') || $request->has('remove_' . $field)) {
            $this->updateMeta($page->id, $field, empty($finalPaths) ? null : json_encode($finalPaths));
        }
    }

    /**
     * Handle dynamic slider with BG image, FG image, and text
     */
    private function handleDynamicSlider($request, $page, $field, $manager)
    {
        // Get existing slides from database
        $oldMeta = $page->meta->firstWhere('meta_key', $field);
        $existingSlides = $oldMeta ? json_decode($oldMeta->meta_value, true) : [];
        if (!is_array($existingSlides)) $existingSlides = [];
        
        // Get form data
        $indexes = $request->input('slider_index', []);
        $texts = $request->input('slider_text', []);
        $bgExisting = $request->input('slider_bg_existing', []);
        $bgMobileExisting = $request->input('slider_bg_mobile_existing', []);
        $fgExisting = $request->input('slider_fg_existing', []);
        $bgRemove = $request->input('slider_bg_remove', []);
        $bgMobileRemove = $request->input('slider_bg_mobile_remove', []);
        $fgRemove = $request->input('slider_fg_remove', []);
        $bgNewFiles = $request->file('slider_bg_new', []);
        $bgMobileNewFiles = $request->file('slider_bg_mobile_new', []);
        $fgNewFiles = $request->file('slider_fg_new', []);
        
        $newSlides = [];
        
        foreach ($indexes as $key => $index) {
            $slide = [];
            
            // ===== Handle Background Image (Desktop) =====
            
            $hasNewBg = isset($bgNewFiles[$key]) && $bgNewFiles[$key];
            
            if ($hasNewBg) {
                // Delete old BG if it exists
                if ($index !== 'new' && isset($existingSlides[$index]['bg_image'])) {
                    Storage::disk('public')->delete($existingSlides[$index]['bg_image']);
                }
                
                // Upload new BG
                $file = $bgNewFiles[$key];
                $mime = $file->getMimeType();
                $path = $this->generateWebpPath($file, 'pages/slider/bg');

                if ($mime === 'image/webp') {
                    // Already WebP → store original as-is, no re-encoding
                    Storage::disk('public')->putFileAs(
                        dirname($path),
                        $file,
                        basename($path)
                    );
                } else {
                    $manager->read($file->getRealPath())
                            ->scaleDown(1400)
                            ->toWebp(80)
                            ->save(storage_path('app/public/' . $path));
                }

                $slide['bg_image'] = $path;
                
            } elseif ($index !== 'new' && isset($existingSlides[$index]['bg_image']) && !in_array($index, $bgRemove)) {
                $slide['bg_image'] = $existingSlides[$index]['bg_image'];
                
            } elseif ($index !== 'new' && isset($existingSlides[$index]['bg_image']) && in_array($index, $bgRemove)) {
                Storage::disk('public')->delete($existingSlides[$index]['bg_image']);
            }
            
            // ===== Handle Mobile Background Image =====
            
            $hasNewBgMobile = isset($bgMobileNewFiles[$key]) && $bgMobileNewFiles[$key];
            
            if ($hasNewBgMobile) {
                // Delete old mobile BG if it exists
                if ($index !== 'new' && isset($existingSlides[$index]['bg_mobile_image'])) {
                    Storage::disk('public')->delete($existingSlides[$index]['bg_mobile_image']);
                }
                
                // Upload new mobile BG
                $file = $bgMobileNewFiles[$key];
                $mime = $file->getMimeType();

                if ($mime === 'image/webp') {
                    // Already WebP → store original as-is without re-encoding
                    $path = $this->generateWebpPath($file, 'pages/slider/bg-mobile');
                    Storage::disk('public')->putFileAs(
                        dirname($path),
                        $file,
                        basename($path)
                    );
                } else {
                    // Convert to WebP
                    $path = $this->generateWebpPath($file, 'pages/slider/bg-mobile');
                    $manager->read($file->getRealPath())
                            ->scaleDown(1400)
                            ->toWebp(80)
                            ->save(storage_path('app/public/' . $path));
                }

                $slide['bg_mobile_image'] = $path;
                
            } elseif ($index !== 'new' && isset($existingSlides[$index]['bg_mobile_image']) && !in_array($index, $bgMobileRemove)) {
                $slide['bg_mobile_image'] = $existingSlides[$index]['bg_mobile_image'];
                
            } elseif ($index !== 'new' && isset($existingSlides[$index]['bg_mobile_image']) && in_array($index, $bgMobileRemove)) {
                Storage::disk('public')->delete($existingSlides[$index]['bg_mobile_image']);
            }
            
            // ===== Handle Foreground Image =====
            
            $hasNewFg = isset($fgNewFiles[$key]) && $fgNewFiles[$key];
            
            if ($hasNewFg) {
                // Delete old FG if it exists
                if ($index !== 'new' && isset($existingSlides[$index]['fg_image'])) {
                    Storage::disk('public')->delete($existingSlides[$index]['fg_image']);
                }
                
                // Upload new FG
                $file = $fgNewFiles[$key];
                $mime = $file->getMimeType();
                $path = $this->generateWebpPath($file, 'pages/slider/fg');

                if ($mime === 'image/webp') {
                    // Already WebP → store original as-is, no re-encoding
                    Storage::disk('public')->putFileAs(
                        dirname($path),
                        $file,
                        basename($path)
                    );
                } else {
                    $manager->read($file->getRealPath())
                            ->scaleDown(1400)
                            ->toWebp(80)
                            ->save(storage_path('app/public/' . $path));
                }

                $slide['fg_image'] = $path;
                
            } elseif ($index !== 'new' && isset($existingSlides[$index]['fg_image']) && !in_array($index, $fgRemove)) {
                $slide['fg_image'] = $existingSlides[$index]['fg_image'];
                
            } elseif ($index !== 'new' && isset($existingSlides[$index]['fg_image']) && in_array($index, $fgRemove)) {
                Storage::disk('public')->delete($existingSlides[$index]['fg_image']);
            }
            
            // ===== Handle Text =====
            $slide['text'] = $texts[$key] ?? '';
            
            // Only add slide if it has at least one piece of content
            if (!empty($slide['bg_image']) || !empty($slide['bg_mobile_image']) || !empty($slide['fg_image']) || !empty($slide['text'])) {
                $newSlides[] = $slide;
            }
        }
        
        // Save to database
        $this->updateMeta($page->id, $field, empty($newSlides) ? null : json_encode($newSlides));
    }

}