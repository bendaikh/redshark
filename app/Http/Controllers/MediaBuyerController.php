<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MediaBuyerController extends Controller
{
    /**
     * Display the media buyer's testing dashboard.
     */
    public function testing(Request $request)
    {
        $user = auth()->user();
        
        // Get testing products assigned to this media buyer
        $query = $user->testingProducts();
        
        // Search functionality
        if ($search = $request->input('q')) {
            $query->where('product_name', 'like', "%{$search}%");
        }
        
        $testingProducts = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        return view('media-buyer.testing', compact('testingProducts'));
    }
}
