<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryFee;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $countries = \App\Models\Country::orderBy('name')->paginate(10);
        $deliveryFees = DeliveryFee::orderBy('created_at', 'desc')->get();
        
        return view('admin.settings', compact('countries', 'deliveryFees'));
    }

    /**
     * Store a new delivery fee.
     */
    public function storeDeliveryFee(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'fee_per_unit' => 'required|numeric|min:0',
            'active' => 'boolean',
        ]);

        DeliveryFee::create([
            'name' => $request->input('name'),
            'fee_per_unit' => $request->input('fee_per_unit'),
            'active' => $request->has('active') ? true : false,
        ]);

        return redirect()->route('admin.settings')->with('status', 'Delivery fee added successfully.');
    }

    /**
     * Update an existing delivery fee.
     */
    public function updateDeliveryFee(Request $request, DeliveryFee $deliveryFee)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'fee_per_unit' => 'required|numeric|min:0',
            'active' => 'boolean',
        ]);

        $deliveryFee->update([
            'name' => $request->input('name'),
            'fee_per_unit' => $request->input('fee_per_unit'),
            'active' => $request->has('active') ? true : false,
        ]);

        return redirect()->route('admin.settings')->with('status', 'Delivery fee updated successfully.');
    }

    /**
     * Delete a delivery fee.
     */
    public function destroyDeliveryFee(DeliveryFee $deliveryFee)
    {
        $deliveryFee->delete();

        return redirect()->route('admin.settings')->with('status', 'Delivery fee deleted successfully.');
    }
}
