<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Organisation;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Faq;
use App\Models\Category;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


class AjaxController extends Controller
{

    public function organisationssearch(Request $request)
    {
        $results = Organisation::where('title', 'LIKE', '%' . $request->q . '%')->get(['id', 'title']);
        return response()->json($results);
    }

    public function getorganisationsdetail($id)
    {
        $organisation = Organisation::findOrFail($id);
        $roles = Role::all(); 
        return view('organisation.organisation_row', compact('organisation', 'roles'));
    }


    public function searchbusinessUsers(Request $request)
    {
        $search = trim($request->get('search'));
        return User::where('level', 'business')
        ->when($search, function ($q) use ($search) {
            $q->where(function ($qq) use ($search) {
                $qq->where('name', 'LIKE', "%{$search}%")
                ->orWhere('first_name', 'LIKE', "%{$search}%")
                ->orWhere('last_name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%");
            });
        })
        ->orderBy('name')
        ->limit(10)
        ->get()
        ->map(function ($user) {
            return [
                'id'   => $user->id,
                'text' => trim(
                    ($user->first_name . ' ' . $user->last_name) ?: $user->name
                ) . ' — ' . $user->email,
            ];
        });
    }

  

 public function searchsubbusinessusers(Request $request,  $businessId)
{
    $users = User::where('parent_id', $businessId)
        ->orderBy('name')
        ->get();

    $html = '';
    $index = (int) $request->get('start_index', 0);

    foreach ($users as $user) {
        $html .= view(
            'organisation.user-row',
            [
                'user'  => $user,
                'index' => $index++
            ]
        )->render();
    }

    return response()->json([
        'status' => true,
        'html'   => $html
    ]);
}


public function createbusinessusers(Request $request)
{
    $validator = Validator::make($request->all(), [
        'first_name' => 'required|string|max:255',
        'last_name'  => 'required|string|max:255',
        'email'      => 'required|email|unique:users,email',
        'password'   => 'required|min:8|confirmed',
        'mobile'     => 'required|regex:/^\+?\d{10,15}$/|unique:users,mobile',
    ]);

    // ❌ Validation failed
    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    // ✅ Create Business User
    $user = User::create([
        'first_name' => $request->first_name,
        'last_name'  => $request->last_name,
        'name'       => $request->first_name.' '.$request->last_name,
        'email'      => $request->email,
        'mobile'     => $request->mobile,
        'password'   => Hash::make($request->password),
        'level'      => 'business',
    ]);

    // ✅ Return Select2-ready response
    return response()->json([
        'status' => true,
        'message' => 'Business user created successfully!',
        'business' => [
            'id'   => $user->id,
            'text' => $user->name.' — '.$user->email
        ]
    ]);
}

public function createsubbusinessusers(Request $request)
{
    $validator = Validator::make($request->all(), [
        'first_name' => 'required|string|max:255',
        'last_name'  => 'required|string|max:255',
        'email'      => 'required|email|unique:users,email',
        'password'   => 'required|min:8|confirmed',
        'mobile'     => 'required|regex:/^\+?\d{10,15}$/|unique:users,mobile',
        'parent_id'   => 'required',

    ]);

    // ❌ Validation failed
    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    // ✅ Create Business User
    $user = User::create([
        'first_name' => $request->first_name,
        'last_name'  => $request->last_name,
        'name'       => $request->first_name.' '.$request->last_name,
        'email'      => $request->email,
        'mobile'     => $request->mobile,
        'password'   => Hash::make($request->password),
        'level'      => 'sub_business',
        'parent_id'  => $request->parent_id,
    ]);

    // ✅ AJAX response
    if ($request->expectsJson()) {
        $rowHtml = view('business.organisation.user-row', [
            'association' => $user,
            'rowKey' => uniqid(),
        ])->render();

        return response()->json([
            'status'  => true,
            'message' => 'User created successfully!',
            'row'     => $rowHtml
        ]);
    }
}




    public function userssearch(Request $request)
    {
        $query = User::select('id', 'name', 'first_name', 'last_name', 'email')
                ->where(function ($q) use ($request) {
                    $q->where('email', 'LIKE', '%' . $request->q . '%')
                      ->orWhere('first_name', 'LIKE', '%' . $request->q . '%')
                      ->orWhere('last_name', 'LIKE', '%' . $request->q . '%')
                      ->orWhere('name', 'LIKE', '%' . $request->q . '%');
                });
        if (auth()->user()->level === 'business' || auth()->user()->level === 'sub_business') {
            $query->where('parent_id', auth()->id());
        }
        $results = $query->get();

       // $results = User::where('email', 'LIKE', '%' . $request->q . '%')->get(['id', 'name','first_name','last_name','email']);

        return response()->json($results);
    }

    public function getuserdetail($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all(); 
        return view('user.user_row', compact('user', 'roles'));
    }

    public function addorganisationsdetail($id)
    {

        $user=Auth::user();
        $organisationIdToAssign = $id;
        $pivotData = [
            'admin_id'   => null, 
            'user_id'    => Auth::id(), 
            'user_assign_id'    => Auth::id(), 
            'start_date' => now()->toDateString(),
            'end_date'   => null,
            'role'       => json_encode([]),   
            'status'     => 'pending',  
        ];
        $user->associatedOrganisations()->attach($organisationIdToAssign, $pivotData);
        $organisation = Organisation::findOrFail($id);
        $roles = Role::all(); 
        return view('business.organisation.organisation_row', compact('organisation', 'roles','pivotData'));
    }


    public function organisationsupdateusertatus(Request $request)
    {
        $pivotId = $request->input('pivot_id');
        $organisationId = $request->input('organisation_id');
        $userId = $request->input('user_id'); 
        $status = $request->input('status'); 
        if (!in_array($status, ['approve', 'reject'])) {
            return response()->json(['success' => false, 'message' => 'Invalid status provided.'], 400);
        }
        if (empty($pivotId) || empty($organisationId) || empty($userId)) {
         return response()->json(['success' => false, 'message' => 'Missing required parameters.'], 400);
     }
     try {
        $updated = DB::table('organisation_user')
        ->where('id', $pivotId)
        ->where('organisation_assign_id', $organisationId)
        ->where('user_assign_id', $userId) 
        ->update(['status' => $status]);

        if ($updated === 0) { 
            return response()->json(['success' => false, 'message' => 'Association record not found or no change needed.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Status updated successfully.', 'new_status' => $status]);

    } catch (\Exception $e) {
        \Log::error('Error updating user organisation status (DB::table): ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json(['success' => false, 'message' => 'An error occurred while updating the status.'], 500);
    }
}


public function sortItems(Request $request)
{
    $type = $request->input('type');
    $order = $request->input('order');

    switch ($type) {
        case 'faqs':
        foreach ($order as $item) {
            Faq::where('id', $item['id'])->update(['position' => $item['position']]);
        }
        break;

        case 'categories':
        foreach ($order as $item) {
            Category::where('id', $item['id'])->update(['position' => $item['position']]);
        }
        break;

        case 'plan':
        foreach ($order as $item) {
            Plan::where('id', $item['id'])->update(['position' => $item['position']]);
        }
        break;

        default:
        return response()->json(['success' => false, 'message' => 'Invalid type.'], 400);
    }

    return response()->json(['success' => true]);
}





public function getOrganisation(Request $request)
{
    $searchTerm = $request->query('term'); 
    $currentUserId = $request->query('user_id');
    $organisations = Organisation::query()
    ->when($searchTerm, function ($query, $term) {
        $query->where('title', 'like', '%' . $term . '%');
    })
    ->when($currentUserId, function ($query, $userId) {
         $query->where('user_id', $userId); 
    })
    ->get(['id', 'title', 'full_address']);
    $formattedOrganisations = $organisations->map(function ($organisation) {
        return [
            'id' => $organisation->id,
            'text' => $organisation->title,
            'address' => $organisation->full_address, 
        ];
    });
    return response()->json([
        'results' => $formattedOrganisations,
        'pagination' => ['more' => false] 
    ]);
}




}




