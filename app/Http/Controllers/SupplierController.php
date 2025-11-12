<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Country;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::with('country')->orderBy('name')->paginate(15);
        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = Country::orderBy('name')->get();
        return view('suppliers.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
        ]);
        
        Supplier::create($data);
        return redirect()->route('suppliers.index')->with('status', 'Supplier created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        $countries = Country::orderBy('name')->get();
        return view('suppliers.edit', compact('supplier', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
        ]);
        
        $supplier->update($data);
        return redirect()->route('suppliers.index')->with('status', 'Supplier updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('status', 'Supplier deleted.');
    }
}
