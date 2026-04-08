<?php

namespace App\Http\Controllers;

use App\Models\Events;
use Illuminate\Http\Request;

use Carbon\Carbon; 
use Illuminate\Support\Facades\Storage; 

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Events::all();
        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Store a newly created resource in storage.
     */

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
            'all_day' => 'boolean',
            'location' => 'nullable|string|max:255',
            'event_category' => 'nullable|integer',
            'facebook_url' => 'nullable|url|max:255',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);
        

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events/images', 'public');
        } else {
            $validated['image'] = null; 
        }

        Events::create($validated);

        return redirect()->route('events.index')->with('status', 'Event created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Events $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Events $event)
    {

         return view('events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Events $event)
    {
       
        $validatedData =$request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
            'all_day' => 'boolean',
            'location' => 'nullable|string|max:255',
            'event_category' => 'nullable|integer',
            'image' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'facebook_url' => 'nullable|url|max:255', 

        ]);

        $currentImagePath = $event->image; 
        if ($request->hasFile('image')) {

             if (!empty($currentImagePath) && Storage::disk('public')->exists($currentImagePath)) {
                Storage::disk('public')->delete($currentImagePath);
            }
            
            if ($currentImagePath && Storage::disk('public')->exists($currentImagePath)) {
                Storage::disk('public')->delete($currentImagePath);
            }
            $newImagePath = $request->file('image')->store('events/images', 'public');
            $validatedData['image'] = $newImagePath;
        }elseif ($request->has('remove_image') && $currentImagePath) {
            if (Storage::disk('public')->exists($currentImagePath)) {
                Storage::disk('public')->delete($currentImagePath);
            }
            $validatedData['image'] = null;
        }
        else {
            $validatedData['image'] = $currentImagePath;
        }

        
        $event->update($validatedData);

        return redirect()->route('events.index')->with('status', 'Event updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Events $event)
    {
        $currentImagePath = $event->image; 
        if ($currentImagePath && Storage::disk('public')->exists($currentImagePath)) {
            Storage::disk('public')->delete($currentImagePath);
        }
        $event->delete();
        return redirect()->route('events.index')->with('status', 'Event deleted successfully.');
    }
}
