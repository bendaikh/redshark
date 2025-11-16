<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Country;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $countryId = (int) ($request->input('country_id') ?? $request->session()->get('current_country_id'));
        
        $query = Balance::with('country')
            ->when($countryId, fn($q) => $q->where('country_id', $countryId))
            ->orderByDesc('date')
            ->orderByDesc('created_at');
        
        $balances = $query->paginate(15)->withQueryString();
        $countries = Country::orderBy('name')->get();
        
        return view('balances.index', compact('balances', 'countries', 'countryId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = Country::orderBy('name')->get();
        return view('balances.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);
        
        Balance::create($data);
        return redirect()->route('balances.index')->with('status', 'Balance added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Balance $balance)
    {
        return view('balances.show', compact('balance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Balance $balance)
    {
        $countries = Country::orderBy('name')->get();
        return view('balances.edit', compact('balance', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Balance $balance)
    {
        $data = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);
        
        $balance->update($data);
        return redirect()->route('balances.index')->with('status', 'Balance updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Balance $balance)
    {
        $balance->delete();
        return redirect()->route('balances.index')->with('status', 'Balance deleted.');
    }
}
