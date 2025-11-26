<?php

namespace App\Http\Controllers;

use App\Models\TestingProduct;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;

class TestingProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TestingProduct::with(['mediaBuyers' => function($q) {
            $q->orderBy('name');
        }]);
        
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

    /**
     * Show the form for assigning media buyers to a testing product.
     */
    public function assignMediaBuyers(TestingProduct $testingProduct)
    {
        $mediaBuyers = User::role('media_buyer')->orderBy('name')->get();
        $assignedMediaBuyers = $testingProduct->mediaBuyers->pluck('id')->toArray();
        
        return view('testing-products.assign-media-buyers', compact('testingProduct', 'mediaBuyers', 'assignedMediaBuyers'));
    }

    /**
     * Update the media buyers assigned to a testing product.
     */
    public function updateMediaBuyers(Request $request, TestingProduct $testingProduct)
    {
        $request->validate([
            'media_buyers' => 'nullable|array',
            'media_buyers.*' => 'exists:users,id',
        ]);

        $testingProduct->mediaBuyers()->sync($request->input('media_buyers', []));
        
        return redirect()->route('testing-products.index')->with('status', 'Media buyers assigned successfully.');
    }

    /**
     * Review testing results submitted by media buyers.
     */
    public function reviewResults(Request $request)
    {
        // Get all testing products with media buyers that have submitted results (status: done)
        $testingProducts = TestingProduct::with(['mediaBuyers' => function($query) {
            $query->wherePivot('status', 'done');
        }])->get();
        
        // Filter only products that have media buyers with done status
        $testingProducts = $testingProducts->filter(function($product) {
            return $product->mediaBuyers->count() > 0;
        });
        
        return view('testing-products.review-results', compact('testingProducts'));
    }

    /**
     * Approve testing results.
     */
    public function approveResult($testing_product, User $user)
    {
        $testingProduct = TestingProduct::findOrFail($testing_product);
        
        // Verify the user is assigned to this testing product
        $pivot = $testingProduct->mediaBuyers()->where('users.id', $user->id)->first();
        
        if (!$pivot || $pivot->pivot->status !== 'done') {
            return redirect()->back()->with('error', 'Cannot approve this result.');
        }
        
        $testingProduct->mediaBuyers()->updateExistingPivot($user->id, [
            'status' => 'approved',
        ]);
        
        return redirect()->back()->with('status', 'Result approved successfully.');
    }

    /**
     * Reject testing results.
     */
    public function rejectResult($testing_product, User $user)
    {
        $testingProduct = TestingProduct::findOrFail($testing_product);
        
        // Verify the user is assigned to this testing product
        $pivot = $testingProduct->mediaBuyers()->where('users.id', $user->id)->first();
        
        if (!$pivot || $pivot->pivot->status !== 'done') {
            return redirect()->back()->with('error', 'Cannot reject this result.');
        }
        
        $testingProduct->mediaBuyers()->updateExistingPivot($user->id, [
            'status' => 'rejected',
        ]);
        
        return redirect()->back()->with('status', 'Result rejected. Media buyer can resubmit.');
    }
}
