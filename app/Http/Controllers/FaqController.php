<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import the Auth facade

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; // <--- ADD THIS LINE IF MISSING

class FaqController extends Controller
{

     public function __construct()
    {
        $this->middleware(['permission:view faqs'])->only('index');
        $this->middleware(['permission:create faqs'])->only(['create', 'store']);
        $this->middleware(['permission:edit faqs'])->only(['edit', 'update']);
        $this->middleware(['permission:delete faqs'])->only('destroy');
    }

    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$faqs = Faq::latest()->get(); 
        $faqs = Faq::orderBy('position')->get();
        return view('faqs.index', compact('faqs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('faqs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);
        $validatedData['admin_id'] = Auth::id(); 
        // Get the next position (max + 1)
        $maxPosition = Faq::max('position');
        $validatedData['position'] = $maxPosition ? $maxPosition + 1 : 1;
        Faq::create($validatedData);

        return redirect()->route('faqs.index')->with('success', 'FAQ created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Faq $faq)
    {
        return view('faqs.show', compact('faq'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faq $faq)
    {
        return view('faqs.edit', compact('faq'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Faq $faq)
    {
        $validatedData = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        $faq->update($validatedData);

        return redirect()->route('faqs.index')->with('success', 'FAQ updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()->route('faqs.index')->with('success', 'FAQ deleted successfully!');
    }
}
