<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


class PermissionController extends Controller 
{
  
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::all();
        return view('permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'name'=> 'required|unique:permissions|min:3',
            'group'=> 'required|min:3'
        ]);
        if($validator->passes()){
           Permission::create(['name'=>$request->name,'group'=>$request->group]);
            return to_route('permissions.index')->with('status', 'Permission added successfully.');
        }else{
            return redirect()->route('permissions.create')->withInput()->withErrors($validator);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
   
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate(['name' => 'required|min:3','group'=> 'required|min:3']);
        $permission->update($validated);
        return to_route('permissions.index')->with('status', 'Permission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return back()->with('status', 'Permission deleted successfully.');
    }
}
