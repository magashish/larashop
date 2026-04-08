<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate; // For authorization (uncomment and implement if needed)

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PromoCodeController extends Controller
{

    public function __construct()
    {
        // $this->middleware(['permission:view promo_codes'])->only('index');
        // $this->middleware(['permission:create promo_codes'])->only(['create', 'store']);
        // $this->middleware(['permission:edit promo_codes'])->only(['edit', 'update']);
        // $this->middleware(['permission:delete promo_codes'])->only('destroy');
        $this->middleware(['permission:manage promo_codes'])->only([
            'index',
            'show',
            'create',
            'store',
            'edit',
            'update',
            'destroy',
        ]);
    }
    

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $distinctInfluencerNames = PromoCode::select('influencer')
        ->distinct()
        ->whereNotNull('influencer') 
        ->where('influencer', '!=', '') 
        ->orderBy('influencer', 'asc') 
        ->get();

        $influencerNamesOnCurrentPage = $distinctInfluencerNames->pluck('influencer')->toArray();
        $promoCodesByInfluencer = PromoCode::whereIn('influencer', $influencerNamesOnCurrentPage)
        ->orderBy('influencer') 
        ->orderBy('created_at', 'desc') 
        ->get()
        ->groupBy('influencer'); 
        $promoCodes = PromoCode::orderBy('created_at', 'desc')->paginate(10);
        return view('promo-codes.index', compact('promoCodes','distinctInfluencerNames', 'promoCodesByInfluencer'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Gate::authorize('create', PromoCode::class);
        return view('promo-codes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'influencer' => 'nullable|string|max:255',
            'promo_code' => 'required|string|max:8|alpha_num|unique:promo_codes,promo_code',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        PromoCode::create($validatedData);
        return redirect()->route('promo_codes.index')
                         ->with('success', 'Promo code created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PromoCode $promoCode)
    {
        // Gate::authorize('view', $promoCode);
        return view('promo-codes.show', compact('promoCode'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PromoCode $promoCode)
    {
        // Gate::authorize('update', $promoCode);
        return view('promo-codes.edit', compact('promoCode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PromoCode $promoCode)
    {
        // Gate::authorize('update', $promoCode);

        $validatedData = $request->validate([
            'influencer' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $promoCode->update($validatedData);

        return redirect()->route('promo_codes.index')
                         ->with('success', 'Promo code updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PromoCode $promoCode)
    {
        // Gate::authorize('delete', $promoCode);

        $promoCode->delete();

        return redirect()->route('promo_codes.index')
                         ->with('success', 'Promo code deleted successfully!');
    }
}
