<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Country;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $countryId = (int) ($request->input('country_id') ?? $request->session()->get('current_country_id'));
        
        $query = Expense::with('expenseCategory', 'country')
            ->when($countryId, fn($q) => $q->where('country_id', $countryId))
            ->orderByDesc('date')
            ->orderByDesc('created_at');
        
        $expenses = $query->paginate(15)->withQueryString();
        $countries = Country::orderBy('name')->get();
        
        return view('expenses.index', compact('expenses', 'countries', 'countryId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $expenseCategories = ExpenseCategory::orderBy('name')->get();
        $countries = Country::orderBy('name')->get();
        return view('expenses.create', compact('expenseCategories', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'country_id' => 'required|exists:countries,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);
        
        Expense::create($data);
        return redirect()->route('expenses.index')->with('status', 'Expense added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        $expenseCategories = ExpenseCategory::orderBy('name')->get();
        $countries = Country::orderBy('name')->get();
        return view('expenses.edit', compact('expense', 'expenseCategories', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'country_id' => 'required|exists:countries,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);
        
        $expense->update($data);
        return redirect()->route('expenses.index')->with('status', 'Expense updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('status', 'Expense deleted.');
    }
}
