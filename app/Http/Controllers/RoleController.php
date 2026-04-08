<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller 
{

 


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$roles = Role::whereNotIn('name', ['admin'])->get();
        $roles = Role::all();
        return view('roles.index', compact('roles')); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'name'=> 'required|unique:roles|min:3',
            'guard_name'=> 'required'
        ]);

        if($validator->passes()){
            $role=Role::create(['name'=>$request->name,'guard_name'=>$request->guard_name]);
            if (!empty($request->permissions)) {
                foreach ($request->permissions as $key => $permission) {
                    $role->givePermissionTo($permission);
                }
            }

             return to_route('roles.index')->with('status', 'Role created successfully.');
         }else{
             return redirect()->route('roles.create')->withInput()->withErrors($validator);
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
    public function edit(Role $role)
    {
        $hasPermissions = $role->permissions->pluck('name');
        $permissions = Permission::all();
        return view('roles.edit', compact('role', 'permissions','hasPermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        /*$validated = $request->validate(['name' => ['required', 'min:3']]);
        $role->update($validated);
        return to_route('admin.roles.index')->with('message', 'Role Updated successfully.');*/

        $role=Role::FindOrFail($id);
         $validator=Validator::make($request->all(),[
            'name'=> 'required|unique:roles,name,'.$id.',id|min:3',
            'guard_name'=> 'required'
         ]);

        if($validator->passes()){
             $role->update($validator->validated());
             $role->syncPermissions($request->permissions ?? []);
             return to_route('roles.index')->with('status', 'Role Updated successfully.');
         }else{
             return redirect()->route('roles.edit',$id)->withInput()->withErrors($validator);
         }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {

        $role->delete();

        return redirect()->route('roles.index')->with('status', 'Role deleted successfully!');
    }
}
