<?php
namespace App\Http\Controllers\Business;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PrizeDraw;
use App\Models\PrizeDrawEntry; // Use the correct singular model name
use App\Models\PrizeDrawEntryBackup; // We will create this model for the backup table
use Illuminate\Support\Facades\Gate; 
use App\Models\User; 


use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 

class BusinessPrizeDrawController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $usersprizeDraws = PrizeDraw::where('draw_type','users')->orderBy('draw_date', 'desc')->get();
        $organisationsprizeDraws = PrizeDraw::where('draw_type','organisations')->orderBy('draw_date', 'desc')->get();

        return view('business.prize-draws.index', compact('usersprizeDraws','organisationsprizeDraws'));
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
