<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\AdminLog;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;


class AdminUserController extends Controller
{

   public function __construct()
   {
    // $this->middleware(['permission:view admins'])->only('index');
    // $this->middleware(['permission:create admins'])->only(['create', 'store']);
    // $this->middleware(['permission:edit admins'])->only(['edit', 'update']);
    // $this->middleware(['permission:delete admins'])->only('destroy');
}


    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $users = Admin::with('latestLog')->orderBy('level')->orderBy('name')->paginate(100); 
        $processedUsers = [];
        foreach ($users as $admin) {
            $lastActionFormatted = '-';
            $lastActionSortValue = 0;

            if ($admin->latestLog) {
                $lastAction = Carbon::parse($admin->latestLog->created_at);
                $lastActionFormatted = $lastAction->format('D j/n/Y g:ia');

                $daysAgo = $lastAction->diffInDays(Carbon::now());
                if ($daysAgo === 0) {
                    $lastActionFormatted .= ' (today)';
                } else {
                    $lastActionFormatted .= ' (' . $daysAgo . ' day' . ($daysAgo === 1 ? '' : 's') . ' ago)';
                }
                $lastActionSortValue = $lastAction->timestamp;
            }

            $processedUsers[] = (object) [
                'id' => $admin->id,
                'email' => $admin->email,
                'name' => $admin->name,
                'level' => $admin->level,
                'status' => $admin->status,
                'last_action_formatted' => $lastActionFormatted,
                'last_action_sort_value' => $lastActionSortValue,
            ];
        }

        $users = Admin::latest()->paginate(10);
        return view('admin.index', compact('users','processedUsers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $adminroles = Role::where('guard_name', 'admin')->get();
        return view('admin.create', compact('adminroles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $validated['password'] = Hash::make($validated['password']);
        Admin::create($validated);
        return redirect()->route('admins.index')->with('status', 'User created successfully!');   
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
    public function edit(Admin $admin)

    {   
        $adminroles = Role::where('guard_name', 'admin')->get();
        $permissions_group = Permission::all()->groupBy('group');
        $hasPermissions = $admin->permissions->pluck('name');
        $permissions = Permission::all();
        return view('admin.edit', compact('admin','adminroles','permissions_group','hasPermissions','permissions'));
    }


    public function update(Request $request, Admin $admin)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('admins')->ignore($admin->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'level' => ['required', Rule::in(['admin', 'editor'])],
            'status' => ['required', Rule::in(['active', 'deactive'])],
        ];
        $validatedData = $request->validate($rules);
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($validatedData['password']); // Using Hash::make
        } else {
            unset($validatedData['password']);
        }
        $admin->update($validatedData);
         // Remove previous roles & permissions
        $admin->syncRoles([]); 
        $admin->syncPermissions([]); 
        // Assign new role
        $admin->assignRole($request->level);
        if ($request->level === 'admin') {
        // Give all permissions
            $admin->givePermissionTo(Permission::all());
        } elseif ($request->level === 'editor') {
        // Give only selected permissions
            if ($request->has('permissions')) {
                $admin->syncPermissions($request->permissions ?? []);
            }
        }

        return redirect()->route('admins.index')->with('status', 'Admin user updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        $admin->delete();
        return redirect()->route('admins.index')->with('status', 'Admin user deleted successfully.');
    }
}
