<?php
namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Offer; 
use Illuminate\Http\Request;
use App\Enums\OrganisationStatus;   // Make sure you have this enum defined

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; // <--- ADD THIS LINE IF MISSING
use Carbon\Carbon; 
use Illuminate\Support\Facades\Storage; 
class CategoryController extends Controller
{

     public function __construct()
    {
        $this->middleware(['permission:view categories'])->only('index');
        $this->middleware(['permission:create categories'])->only(['create', 'store']);
        $this->middleware(['permission:edit categories'])->only(['edit', 'update']);
        $this->middleware(['permission:delete categories'])->only('destroy');
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::orderBy('position')->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
        ]);
        Category::create($validated);
        return redirect()->route('categories.index')->with('status', 'Category created successfully!');
 }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
       

       // Get the current date and time
        $today = Carbon::now();
        // 1. Pending Approval Offers for this Category
        $offer_new = Offer::with(['organisation', 'categories'])
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('categories.id', $category->id);
            })
            ->where('processing_status', OrganisationStatus::NEW)
            ->get();

        // 2. Approved Offers for this Category
        $offer_approved = Offer::with(['organisation', 'categories'])
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('categories.id', $category->id);
            })
            ->where('processing_status', OrganisationStatus::APPROVED)
            ->get();

        // 3. Rejected Offers for this Category
        $offer_rejected = Offer::with(['organisation', 'categories'])
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('categories.id', $category->id);
            })
            ->where('processing_status', OrganisationStatus::REJECTED)
            ->get();

        // 4. Expired Offers for this Category (must be approved and end date is past)
        $offer_expire = Offer::with(['organisation', 'categories'])
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('categories.id', $category->id);
            })
            ->where('processing_status', OrganisationStatus::APPROVED) 
            ->where('end_date', '<', $today) 
            ->get();

        // 5. Live Offers for this Category (must be approved and currently active)
        // $live_offers = Offer::with(['organisation', 'categories'])
        //     ->whereHas('categories', function ($query) use ($category) {
        //         $query->where('categories.id', $category->id);
        //     })
        //     ->where('processing_status', OrganisationStatus::APPROVED) 
        //     ->where('start_date', '<=', $today) 
        //     ->where('end_date', '>=', $today)   
        //     ->get();

            $live_offers = Offer::with(['organisation', 'categories'])
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('categories.id', $category->id);
            })
            ->where('processing_status', OrganisationStatus::APPROVED) 
            ->where('start_date', '<=', $today) 
            ->where(function($query) use ($today) {
            $query->where('end_date', '>=', $today)
            ->orWhere('no_end_date', 1); 
        })
        ->get();

        $offers = Offer::with(['organisation', 'categories'])->latest()->paginate(10);
        return view('categories.show', compact('category','offers','offer_new','offer_expire','live_offers','offer_approved','offer_rejected'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */


    public function update(Request $request, Category $category)
    {
        $validatedData =$request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $currentImagePath = $category->icon; 
        if ($request->hasFile('icon')) {

             if (!empty($currentImagePath) && Storage::disk('public')->exists($currentImagePath)) {
                Storage::disk('public')->delete($currentImagePath);
            }
            
            if ($currentImagePath && Storage::disk('public')->exists($currentImagePath)) {
                Storage::disk('public')->delete($currentImagePath);
            }
            $newImagePath = $request->file('icon')->store('category/images', 'public');
            $validatedData['icon'] = $newImagePath;
        }elseif ($request->has('remove_image') && $currentImagePath) {
            if (Storage::disk('public')->exists($currentImagePath)) {
                Storage::disk('public')->delete($currentImagePath);
            }
            $validatedData['icon'] = null;
        }
        else {
            $validatedData['icon'] = $currentImagePath;
        }

        
        $category->update($validatedData);

        //$category->update($request->all());
        return redirect()->route('categories.index')->with('status', 'Category updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('status', 'Category deleted successfully.');
    }
}
