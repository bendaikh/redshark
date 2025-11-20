<?php

namespace App\Http\Controllers;

use App\Models\TestingProduct;
use App\Models\Country;
use Illuminate\Http\Request;

class TestingProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TestingProduct::query();
        
        // Search functionality
        if ($search = $request->input('q')) {
            $query->where('product_name', 'like', "%{$search}%");
        }
        
        $testingProducts = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        return view('testing-products.index', compact('testingProducts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = Country::where('active', true)->orderBy('name')->get();
        return view('testing-products.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_link' => 'required|url|max:1000',
            'facebook_library_link' => 'nullable|url|max:1000',
            'country_ids' => 'nullable|array',
            'country_ids.*' => 'exists:countries,id',
            'video_url' => 'nullable|url|max:1000',
        ]);

        TestingProduct::create($data);
        
        return redirect()->route('testing-products.index')->with('status', 'Testing product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TestingProduct $testingProduct)
    {
        return view('testing-products.show', compact('testingProduct'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TestingProduct $testingProduct)
    {
        $countries = Country::where('active', true)->orderBy('name')->get();
        return view('testing-products.edit', compact('testingProduct', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TestingProduct $testingProduct)
    {
        $data = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_link' => 'required|url|max:1000',
            'facebook_library_link' => 'nullable|url|max:1000',
            'country_ids' => 'nullable|array',
            'country_ids.*' => 'exists:countries,id',
            'video_url' => 'nullable|url|max:1000',
        ]);

        $testingProduct->update($data);
        
        return redirect()->route('testing-products.index')->with('status', 'Testing product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TestingProduct $testingProduct)
    {
        $testingProduct->delete();
        
        return redirect()->route('testing-products.index')->with('status', 'Testing product deleted successfully.');
    }
}
