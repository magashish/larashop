<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UnregisteredUser;
use Illuminate\Http\Request;

class UnregisteredUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = 50;

    // Start query
        $users = UnregisteredUser::query();

    // 🔍 Search (name / email / mobile)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $users->where(function ($query) use ($search) {
                $query->where('email', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

    // 📌 Follow-up step filter
        if ($request->filled('step')) {
            $users->where('followup_step', $request->step);
        }

    // ⏰ Pending follow-ups only
        if ($request->input('pending') === 'yes') {
            $users->whereNotNull('next_followup_at')
            ->where('next_followup_at', '<=', now());
        }

    // ❌ No follow-up scheduled
        if ($request->input('pending') === 'no') {
            $users->whereNull('next_followup_at');
        }

    // 📅 Sorting
        $users->orderBy('created_at', 'desc');

    // Pagination
        $users = $users->paginate($perPage)->appends($request->query());

        return view('unregisteredusers.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(UnregisteredUser $unregisteredUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UnregisteredUser $unregisteredUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UnregisteredUser $unregisteredUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UnregisteredUser $unregistereduser)
    {
        $unregistereduser->delete();

        return redirect()
        ->route('unregisteredusers.index')
        ->with('status', 'Unregistered user deleted successfully.');
    }

}
