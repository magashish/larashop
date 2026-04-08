<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // For unique rule with ignore
use Illuminate\Support\Str; // Import the Str facade for slug generation
use Illuminate\Support\Facades\Storage; 

class PlanController extends Controller
{
    /**
     * Display a listing of the resource (Plans).
     */
    public function index()
    {  
        $allPlans=Plan::orderBy('position')->get();
        $subscription = Plan::where('type', 'subscription')->get();
        $package = Plan::where('type', 'package')->get();
        return view('plans.index', compact('allPlans','subscription','package'));
    }

    /**
     * Show the form for creating a new resource (Plan).
     */
    public function create()
    {
        $types =subscription_types();
       return view('plans.create', compact('types'));
    }

    /**
     * Store a newly created resource (Plan) in storage.
     */
    public function store(Request $request)
    {

        // 1. Define base validation rules common to all offerings
        $rules = [
            'name'        => 'required|string|max:255',
            'type'        => ['required', 'string', Rule::in(['subscription', 'package'])], 
            'price' => 'required|numeric|min:0.00',
            'price_description' => 'nullable|string',
            'description' => 'nullable|string',
            'small_description' => 'nullable|string',
            'read_more' => 'nullable|string',
            'sub_description' => 'nullable|string',
        ];

        // 2. Add conditional validation rules based on the 'type' selected
        if ($request->input('type') === 'subscription') {
            $rules['stripe_plan'] = 'required|string|unique:plans,stripe_plan|max:255'; 
            $rules['paypal_plan_id'] = 'required|string|unique:plans,paypal_plan_id|max:255'; 
        } elseif ($request->input('type') === 'package') {
           // $rules['entries_into_draw']   = 'required|integer|min:1';
            $rules['access_duration_weeks'] = 'required|integer|min:1';
        }

        // 3. Validate the incoming request data
        $validatedData = $request->validate($rules);
        if ($validatedData['type'] === 'subscription') {
            //$validatedData['entries_into_draw']   = null;
            $validatedData['access_duration_weeks'] = null;
        } elseif ($validatedData['type'] === 'package') {
            $validatedData['stripe_plan'] = null;
            $validatedData['paypal_plan_id'] = null;
        }

        $validatedData['slug'] = Str::slug($validatedData['name']);
        $originalSlug = $validatedData['slug'];
        $count = 1;
        while (Plan::where('type', 'subscription')->where('slug', $validatedData['slug'])->exists()) { 
            $validatedData['slug'] = $originalSlug . '-' . $count++;
        }

        Plan::create($validatedData);
        return redirect()->route('plans.index')->with('success', 'Subscription Plan created successfully!');
    }

    /**
     * Display the specified resource (Plan).
     */
    public function show(Plan $plan)
    {
        return view('plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified resource (Plan).
     */
    public function edit(Plan $plan)
    {
        $types =subscription_types();
        return view('plans.edit', compact('plan','types'));
    }

    /**
     * Update the specified resource (Plan) in storage.
     */
    public function update(Request $request, Plan $plan)
    {
        // $validatedData = $request->validate([
        //     'name' => 'required|string|max:255',
        //     'stripe_plan' => [
        //         'required',
        //         'string',
        //         'max:255',
        //         Rule::unique('plans', 'stripe_plan')->ignore($plan->id), 
        //     ],
        //     'price' => 'required|integer|min:0',
        //     'description' => 'nullable|string',
        // ]);


        // 1. Define base validation rules
        $rules = [
            'name'        => 'required|string|max:255',
            'type'        => ['required', 'string', Rule::in(['subscription', 'package'])],
            'price' => 'required|numeric|min:0.00',
            'price_description' => 'nullable|string',
            'description' => 'nullable|string',
            'small_description' => 'nullable|string',
            'read_more' => 'nullable|string',
            'sub_description' => 'nullable|string',
            'payment_description' => 'nullable|string',
            'is_public' => ['sometimes', 'boolean'], 
        ];

      // 2. Add conditional validation rules based on 'type'
        if ($request->input('type') === 'subscription') {
            $rules['stripe_plan'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('plans', 'stripe_plan')->ignore($plan->id), 
            ];

            $rules['paypal_plan_id'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('plans', 'paypal_plan_id')->ignore($plan->id), 
            ];
        } elseif ($request->input('type') === 'package') {
            //$rules['entries_into_draw']   = 'required|integer|min:1';
            $rules['access_duration_weeks'] = 'required|integer|min:1';
        }

     // 3. Validate the request
        $validatedData = $request->validate($rules);
        if ($validatedData['type'] === 'subscription') {
           // $validatedData['entries_into_draw']   = null;
            $validatedData['access_duration_weeks'] = null;

        } elseif ($validatedData['type'] === 'package') {
            $validatedData['stripe_plan'] = null;
            $validatedData['paypal_plan_id'] = null;
        }

        $generatedSlug = Str::slug($validatedData['name']);
        $originalSlug = $generatedSlug;
        $count = 1;
        while (Plan::where('slug', $generatedSlug)->where('id', '!=', $plan->id)->exists()) {
            $generatedSlug = $originalSlug . '-' . $count++;
        }
        $validatedData['slug'] = $generatedSlug;
        
        $currentImagePath = $plan->image;
        if ($request->hasFile('image')) {
            if (!empty($currentImagePath) && Storage::disk('public')->exists($currentImagePath)) {
                Storage::disk('public')->delete($currentImagePath);
            }
            $newImagePath = $request->file('image')->store('plans/images', 'public');
            $validatedData['image'] = $newImagePath;

        } elseif ($request->has('remove_image')) {
            if (!empty($currentImagePath) && Storage::disk('public')->exists($currentImagePath)) {
                Storage::disk('public')->delete($currentImagePath);
            }
            $validatedData['image'] = null; 

        } else {
            $validatedData['image'] = $currentImagePath;
        }

        $validatedData['is_public'] = $request->has('is_public');


        $plan->update($validatedData);

        return redirect()->route('plans.index')->with('success', 'Subscription Plan updated successfully!');
    }

    /**
     * Remove the specified resource (Plan) from storage.
     */
    public function destroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('plans.index')->with('success', 'Subscription Plan deleted successfully!');
    }
}
