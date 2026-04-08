<?php

namespace App\Http\Controllers;

use App\Models\GoogleReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class GoogleReviewController extends Controller
{
    public function index()
    {
        $reviews = GoogleReview::latest()->get();
        return view('reviews.index', compact('reviews'));
    }

    public function create()
    {
        return view('reviews.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'author_name' => 'required|string|max:255',
            'rating'      => 'required|integer|min:1|max:5',
            'review_text' => 'required',
            'author_img'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'type' => 'nullable|in:google,facebook',
        ]);

        $data = $request->all();

        if ($request->hasFile('author_img')) {
            $data['author_img'] = $this->uploadAsWebp($request->file('author_img'));
        }

        GoogleReview::create($data);
        return redirect()->route('google-reviews.index')->with('success', 'Review created!');
    }

    public function edit(GoogleReview $googleReview)
    {
        return view('reviews.edit', compact('googleReview'));
    }

    public function update(Request $request, GoogleReview $googleReview)
    {
        $request->validate([
            'author_name' => 'required|string|max:255',
            'rating'      => 'required|integer|min:1|max:5',
            'review_text' => 'required',
            'author_img'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'type' => 'nullable|in:google,facebook',
        ]);

        $data = $request->all();

        if ($request->hasFile('author_img')) {
            if ($googleReview->author_img) {
                Storage::disk('public')->delete($googleReview->author_img);
            }
            $data['author_img'] = $this->uploadAsWebp($request->file('author_img'));
        }

        $googleReview->update($data);
        return redirect()->route('google-reviews.index')->with('success', 'Review updated!');
    }

    public function destroy(GoogleReview $googleReview)
    {
        if ($googleReview->author_img) {
            Storage::disk('public')->delete($googleReview->author_img);
        }
        $googleReview->delete();
        return redirect()->route('google-reviews.index')->with('success', 'Review deleted!');
    }

    /**
     * Helper to handle WebP conversion and storage
     */
    private function uploadAsWebp($file)
    {
        $manager = new ImageManager(new Driver());
        
        // Create unique name
        $name = 'reviews/' . uniqid() . '.webp';
        
        // Ensure directory exists in storage/app/public/reviews
        if (!Storage::disk('public')->exists('reviews')) {
            Storage::disk('public')->makeDirectory('reviews');
        }

        $image = $manager->read($file->getRealPath());
        
        // Scale down if too large, then convert
        if ($image->width() > 800) {
            $image->scaleDown(800);
        }

        // Save to storage/app/public/reviews/...
        $image->toWebp(80)->save(storage_path('app/public/' . $name));

        return $name;
    }
}