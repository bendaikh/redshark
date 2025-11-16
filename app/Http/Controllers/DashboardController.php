<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Balance;
use App\Models\Expense;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $countryId = (int) $request->session()->get('current_country_id');
        
        // Calculate total balance (sum of all balance entries)
        $totalBalance = Balance::when($countryId, fn($q) => $q->where('country_id', $countryId))
            ->sum('amount') ?? 0;
        
        // Calculate total expenses (sum of all expense entries)
        $totalExpenses = Expense::when($countryId, fn($q) => $q->where('country_id', $countryId))
            ->sum('amount') ?? 0;
        
        // Calculate net profit balance (total balance - all expenses)
        $netProfitBalance = $totalBalance - $totalExpenses;
        
        return view('dashboard', compact('totalBalance', 'netProfitBalance', 'totalExpenses'));
    }
}
