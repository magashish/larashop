<?php
namespace App\Http\Controllers;
use App\Models\License;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $licenses = License::latest()->paginate(10);
        return view('licenses.index', compact('licenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('licenses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'license_type' => 'required|in:unit,subscription',
            'is_active' => 'required|boolean',
            'is_public' => 'required|boolean',
        ]);

        License::create($request->all());

        return redirect()->route('licenses.index')->with('status', 'License created successfully.');
    }
    /**
     * Display the specified resource.
     */
    public function show(License $license)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(License $license)
    {
        return view('licenses.edit', compact('license'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, License $license)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'license_type' => 'required|in:unit,subscription',
            'is_active' => 'required|boolean',
            'is_public' => 'required|boolean',
        ]);

        $license->update($request->all());
        return redirect()->route('licenses.index')->with('status', 'License updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(License $license)
    {
        $license->delete();
        return redirect()->route('licenses.index')->with('status', 'License deleted successfully.');
    }
}
