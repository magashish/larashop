<?php
namespace App\Http\Controllers;
use App\Models\ProductPackage;
use Illuminate\Http\Request;

class ProductPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productPackages = ProductPackage::latest()->paginate(10);
        return view('product-packages.index', compact('productPackages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('product-packages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'entries_into_draw' => 'required|integer|min:1',
            'access_duration_weeks' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        ProductPackage::create($validatedData);

        return redirect()->route('product-packages.index')
                         ->with('success', 'Product package created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductPackage $productPackage)
    {
        // For a simple CRUD, you might not need a dedicated show page,
        // or you could display more details here if needed.
        return view('product-packages.show', compact('productPackage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductPackage $productPackage)
    {
        return view('product-packages.edit', compact('productPackage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductPackage $productPackage)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'entries_into_draw' => 'required|integer|min:1',
            'access_duration_weeks' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $productPackage->update($validatedData);

        return redirect()->route('product-packages.index')
                         ->with('success', 'Product package updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductPackage $productPackage)
    {
        $productPackage->delete();

        return redirect()->route('product-packages.index')
                         ->with('success', 'Product package deleted successfully.');
    }
}