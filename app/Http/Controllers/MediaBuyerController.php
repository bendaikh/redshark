<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\AdsCampaign;
use App\Models\AdsPlatform;
use App\Models\Country;
use App\Models\Product;
use App\Models\TestingProduct;
use App\Services\AdsCampaignImportService;
use Illuminate\Http\Request;

class MediaBuyerController extends Controller
{
    /**
     * Display the media buyer's main dashboard with analytics.
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        $countryId = (int) $request->session()->get('current_country_id');
        
        // Get all campaigns for this media buyer, filtered by country if selected
        $campaigns = AdsCampaign::where('user_id', $user->id)
            ->when($countryId, fn($q) => $q->where('country_id', $countryId))
            ->with(['products'])
            ->get();
        
        // Calculate total metrics
        $totalCampaigns = $campaigns->count();
        $totalLeads = $campaigns->sum('total_leads');
        $totalSpent = $campaigns->sum('total_amount_spent');
        $avgCostPerLead = $totalLeads > 0 ? $totalSpent / $totalLeads : 0;
        
        // Get total expenses filtered by country
        $totalExpenses = Expense::where('user_id', $user->id)
            ->when($countryId, fn($q) => $q->where('country_id', $countryId))
            ->sum('amount');
        
        // Get assigned products count filtered by country
        $totalProducts = $user->products()
            ->when($countryId, fn($q) => $q->where('products.country_id', $countryId))
            ->count();
        
        // Get recent campaigns (last 5) filtered by country
        $recentCampaigns = AdsCampaign::where('user_id', $user->id)
            ->when($countryId, fn($q) => $q->where('country_id', $countryId))
            ->with(['platform', 'country', 'products'])
            ->orderByDesc('date_from')
            ->limit(5)
            ->get();
        
        // Get campaign data for chart (last 30 days) filtered by country
        $campaignStats = AdsCampaign::where('user_id', $user->id)
            ->when($countryId, fn($q) => $q->where('country_id', $countryId))
            ->where('date_from', '>=', now()->subDays(30))
            ->selectRaw('DATE(date_from) as date, COUNT(*) as count, SUM((SELECT SUM(amount_spent) FROM ads_campaign_product WHERE ads_campaign_id = ads_campaigns.id)) as spent')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Get top performing products (based on leads from user's campaigns) filtered by country
        $topProducts = Product::whereHas('mediaBuyers', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->when($countryId, fn($q) => $q->where('products.country_id', $countryId))
            ->with(['adsCampaigns' => function($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
            ->get()
            ->map(function($product) use ($user, $countryId) {
                $userCampaigns = AdsCampaign::where('user_id', $user->id)
                    ->when($countryId, fn($q) => $q->where('country_id', $countryId))
                    ->whereHas('products', function($q) use ($product) {
                        $q->where('products.id', $product->id);
                    })
                    ->with('products')
                    ->get();
                
                $leads = 0;
                $spent = 0;
                foreach ($userCampaigns as $campaign) {
                    $productInCampaign = $campaign->products->where('id', $product->id)->first();
                    if ($productInCampaign) {
                        $leads += $productInCampaign->pivot->leads ?? 0;
                        $spent += $productInCampaign->pivot->amount_spent ?? 0;
                    }
                }
                
                $product->user_leads = $leads;
                $product->user_spent = $spent;
                return $product;
            })
            ->sortByDesc('user_leads')
            ->take(5);
        
        return view('media-buyer.dashboard', compact(
            'totalCampaigns',
            'totalLeads', 
            'totalSpent',
            'avgCostPerLead',
            'totalExpenses',
            'totalProducts',
            'recentCampaigns',
            'campaignStats',
            'topProducts'
        ));
    }

    /**
     * Display the media buyer's testing dashboard.
     */
    public function testing(Request $request)
    {
        $user = auth()->user();
        $countryId = (int) $request->session()->get('current_country_id');
        
        // Get testing products assigned to this media buyer, filtered by country
        $query = $user->testingProducts()
            ->when($countryId, function($q) use ($countryId) {
                // Testing products use a JSON array of country_ids
                $q->whereJsonContains('country_ids', $countryId);
            });
        
        // Search functionality
        if ($search = $request->input('q')) {
            $query->where('product_name', 'like', "%{$search}%");
        }
        
        // Filter by status
        if ($status = $request->input('status')) {
            $query->wherePivot('status', $status);
        }
        
        $testingProducts = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        return view('media-buyer.testing', compact('testingProducts'));
    }

    /**
     * Update testing product status.
     */
    public function updateTestingStatus(Request $request, $testing_product)
    {
        $user = auth()->user();
        $testingProduct = TestingProduct::findOrFail($testing_product);
        
        // Verify this product is assigned to this media buyer
        if (!$user->testingProducts()->where('testing_products.id', $testingProduct->id)->exists()) {
            abort(403);
        }
        
        $request->validate([
            'status' => 'required|in:in_progress,done',
        ]);
        
        $user->testingProducts()->updateExistingPivot($testingProduct->id, [
            'status' => $request->status,
        ]);
        
        return redirect()->route('media-buyer.testing')->with('status', 'Status updated successfully.');
    }

    /**
     * Show form to submit testing results.
     */
    public function showSubmitResults($testing_product)
    {
        $user = auth()->user();
        $testingProduct = TestingProduct::findOrFail($testing_product);
        
        // Verify this product is assigned to this media buyer
        $pivot = $user->testingProducts()->where('testing_products.id', $testingProduct->id)->first();
        if (!$pivot) {
            abort(403);
        }
        
        return view('media-buyer.testing-submit-results', compact('testingProduct', 'pivot'));
    }

    /**
     * Submit testing results.
     */
    public function submitResults(Request $request, $testing_product)
    {
        $user = auth()->user();
        $testingProduct = TestingProduct::findOrFail($testing_product);
        
        // Verify this product is assigned to this media buyer
        if (!$user->testingProducts()->where('testing_products.id', $testingProduct->id)->exists()) {
            abort(403);
        }
        
        $request->validate([
            'leads' => 'required|integer|min:0',
            'ads_spend' => 'required|numeric|min:0',
        ]);
        
        $user->testingProducts()->updateExistingPivot($testingProduct->id, [
            'status' => 'done',
            'leads' => $request->leads,
            'ads_spend' => $request->ads_spend,
        ]);
        
        return redirect()->route('media-buyer.testing')->with('status', 'Results submitted successfully!');
    }

    /**
     * Display the media buyer's products dashboard.
     */
    public function products(Request $request)
    {
        $user = auth()->user();
        $countryId = (int) $request->session()->get('current_country_id');
        
        // Get products assigned to this media buyer, filtered by country
        $query = $user->products()
            ->when($countryId, function($q) use ($countryId) {
                $q->where('products.country_id', $countryId);
            });
        
        // Search functionality
        if ($search = $request->input('q')) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        $products = $query->with(['country', 'category'])
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();
        
        return view('media-buyer.products', compact('products'));
    }

    /**
     * Display the media buyer's expenses.
     */
    public function expenses(Request $request)
    {
        $user = auth()->user();
        $countryId = (int) $request->session()->get('current_country_id');
        
        $query = Expense::with('expenseCategory', 'country')
            ->where('user_id', $user->id)
            ->when($countryId, fn($q) => $q->where('country_id', $countryId))
            ->orderByDesc('date')
            ->orderByDesc('created_at');
        
        $expenses = $query->paginate(15)->withQueryString();
        
        return view('media-buyer.expenses', compact('expenses'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function createExpense()
    {
        $user = auth()->user();
        // Media buyers can only see public expense categories
        $expenseCategories = ExpenseCategory::where('is_public', true)->orderBy('name')->get();
        
        // Get countries accessible to this media buyer
        $countries = $user->getAccessibleCountries();
        
        return view('media-buyer.expenses-create', compact('expenseCategories', 'countries'));
    }

    /**
     * Store a newly created expense.
     */
    public function storeExpense(Request $request)
    {
        $data = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'country_id' => 'required|exists:countries,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);
        
        $data['user_id'] = auth()->id();
        
        Expense::create($data);
        return redirect()->route('media-buyer.expenses')->with('status', 'Expense added successfully.');
    }

    /**
     * Remove the specified expense.
     */
    public function destroyExpense(Expense $expense)
    {
        // Ensure the expense belongs to the authenticated user
        if ($expense->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $expense->delete();
        return redirect()->route('media-buyer.expenses')->with('status', 'Expense deleted successfully.');
    }

    /**
     * Display the media buyer's campaigns.
     */
    public function campaigns(Request $request)
    {
        $user = auth()->user();
        $countryId = (int) $request->session()->get('current_country_id');
        $from = $request->input('from');
        $to = $request->input('to');

        $query = AdsCampaign::with(['platform', 'country', 'products'])
            ->where('user_id', $user->id)
            ->when($countryId, fn($q) => $q->where('country_id', $countryId))
            ->when($from, fn($q) => $q->whereDate('date_from', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('date_to', '<=', $to))
            ->orderByDesc('date_from');

        $campaigns = $query->paginate(15)->withQueryString();

        $allCampaigns = (clone $query)->get();
        $totalSpent = $allCampaigns->sum('total_amount_spent');
        $totalLeads = $allCampaigns->sum('total_leads');
        $costPerLead = $totalLeads > 0 ? $totalSpent / $totalLeads : 0;
        $platforms = AdsPlatform::where('is_active', true)->orderBy('name')->get();

        return view('media-buyer.campaigns', compact(
            'campaigns',
            'totalSpent',
            'totalLeads',
            'costPerLead',
            'from',
            'to',
            'platforms'
        ));
    }

    /**
     * Show the form for creating a new campaign.
     */
    public function createCampaign()
    {
        $user = auth()->user();
        $countries = $user->getAccessibleCountries();
        $platforms = AdsPlatform::where('is_active', true)->orderBy('name')->get();
        return view('media-buyer.campaigns-create', compact('countries', 'platforms'));
    }

    /**
     * Store a newly created campaign.
     */
    public function storeCampaign(Request $request)
    {
        $data = $request->validate([
            'platform_id' => 'required|exists:ads_platforms,id',
            'country_id' => 'required|exists:countries,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.amount_spent' => 'required|numeric|min:0',
            'products.*.leads' => 'nullable|integer|min:0',
        ]);

        $campaign = AdsCampaign::create([
            'user_id' => auth()->id(),
            'name' => 'Campaign ' . now()->format('Y-m-d H:i'),
            'platform_id' => $data['platform_id'],
            'country_id' => $data['country_id'],
            'date_from' => $data['date_from'],
            'date_to' => $data['date_to'],
        ]);

        $productsData = [];
        foreach ($data['products'] as $product) {
            $productsData[$product['id']] = [
                'amount_spent' => $product['amount_spent'],
                'leads' => $product['leads'] ?? 0,
            ];
        }
        $campaign->products()->attach($productsData);

        return redirect()->route('media-buyer.campaigns')->with('status', 'Campaign created successfully.');
    }

    /**
     * Import campaigns from an Excel/CSV file for the current media buyer.
     */
    public function importCampaign(Request $request, AdsCampaignImportService $importService)
    {
        $user = auth()->user();
        $countryId = (int) $request->session()->get('current_country_id');

        if (! $countryId) {
            return back()->withErrors([
                'file' => __('Please select a country from the top-right dropdown before importing.'),
            ]);
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ]);

        try {
            $rows = $importService->parse($request->file('file'));
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['file' => $e->getMessage()]);
        } catch (\Throwable $e) {
            return back()->withErrors(['file' => __('Unable to read the uploaded file. Please check the format and try again.')]);
        }

        if (empty($rows)) {
            return back()->withErrors(['file' => __('No valid campaign rows were found in the file.')]);
        }

        $productsByImportId = Product::where('country_id', $countryId)
            ->whereNotNull('import_id')
            ->whereHas('mediaBuyers', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->get()
            ->keyBy(fn (Product $product) => strtolower(trim($product->import_id)));

        $totalAmountSpent = array_sum(array_column($rows, 'amount_spent'));
        $totalLeads = array_sum(array_column($rows, 'leads'));

        $campaign = AdsCampaign::create([
            'user_id' => $user->id,
            'name' => __('Imported Campaign :date', ['date' => now()->format('Y-m-d H:i')]),
            'amount_spent' => $totalAmountSpent,
            'leads' => $totalLeads,
            'country_id' => $countryId,
            'platform_id' => null,
            'date_from' => null,
            'date_to' => null,
        ]);

        $productsData = [];
        $unmatchedRows = [];

        foreach ($rows as $row) {
            $importId = AdsCampaignImportService::extractImportId($row['name']);
            $product = $importId
                ? $productsByImportId->get(strtolower($importId))
                : null;

            if (! $product) {
                $unmatchedRows[] = $row['name'];
                continue;
            }

            $productId = $product->id;

            if (isset($productsData[$productId])) {
                $productsData[$productId]['amount_spent'] += $row['amount_spent'];
                $productsData[$productId]['leads'] += $row['leads'];
            } else {
                $productsData[$productId] = [
                    'amount_spent' => $row['amount_spent'],
                    'leads' => $row['leads'],
                ];
            }
        }

        if (! empty($productsData)) {
            $campaign->products()->attach($productsData);
        }

        $matchedCount = count($productsData);

        if ($matchedCount === 0) {
            $campaign->delete();

            return back()->withErrors([
                'file' => __('No products could be matched. Make sure each product has an Import ID matching the code before the hyphen in the campaign name.'),
            ]);
        }

        $statusMessage = __('1 campaign imported with :count product(s). Assign platform and date range to the selected row.', [
            'count' => $matchedCount,
        ]);

        if (! empty($unmatchedRows)) {
            $statusMessage .= ' '.__(':count row(s) could not be matched to a product (check the import ID before the hyphen in the campaign name).', [
                'count' => count($unmatchedRows),
            ]);
        }

        return redirect()
            ->route('media-buyer.campaigns', ['imported' => (string) $campaign->id])
            ->with('status', $statusMessage);
    }

    /**
     * Bulk update platform and/or date range for selected campaigns.
     */
    public function bulkUpdateCampaigns(Request $request)
    {
        $data = $request->validate([
            'campaign_ids' => 'required|array|min:1',
            'campaign_ids.*' => 'integer|exists:ads_campaigns,id',
            'platform_id' => 'nullable|exists:ads_platforms,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        if (empty($data['platform_id']) && empty($data['date_from']) && empty($data['date_to'])) {
            return back()->withErrors([
                'bulk' => __('Please select a platform and/or date range to apply.'),
            ]);
        }

        if (! empty($data['date_from']) xor ! empty($data['date_to'])) {
            return back()->withErrors([
                'bulk' => __('Please provide both start and end dates.'),
            ]);
        }

        $updates = [];

        if (! empty($data['platform_id'])) {
            $updates['platform_id'] = $data['platform_id'];
        }

        if (! empty($data['date_from']) && ! empty($data['date_to'])) {
            $updates['date_from'] = $data['date_from'];
            $updates['date_to'] = $data['date_to'];
        }

        $updatedCount = AdsCampaign::where('user_id', auth()->id())
            ->whereIn('id', $data['campaign_ids'])
            ->update($updates);

        return redirect()
            ->route('media-buyer.campaigns', $request->only(['from', 'to']))
            ->with('status', __(':count campaigns updated.', ['count' => $updatedCount]));
    }

    /**
     * Show the form for editing a campaign.
     */
    public function editCampaign(AdsCampaign $campaign)
    {
        $this->authorizeCampaignOwnership($campaign);

        $user = auth()->user();
        $campaign->load('products');
        $countries = $user->getAccessibleCountries();
        $platforms = AdsPlatform::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('country_id', $campaign->country_id)
            ->whereHas('mediaBuyers', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->orderBy('name')
            ->get();

        return view('media-buyer.campaigns-edit', compact('campaign', 'countries', 'platforms', 'products'));
    }

    /**
     * Update the specified campaign.
     */
    public function updateCampaign(Request $request, AdsCampaign $campaign)
    {
        $this->authorizeCampaignOwnership($campaign);

        $data = $request->validate([
            'platform_id' => 'required|exists:ads_platforms,id',
            'country_id' => 'required|exists:countries,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.amount_spent' => 'required|numeric|min:0',
            'products.*.leads' => 'nullable|integer|min:0',
        ]);

        $campaign->update([
            'platform_id' => $data['platform_id'],
            'country_id' => $data['country_id'],
            'date_from' => $data['date_from'],
            'date_to' => $data['date_to'],
        ]);

        $productsData = [];
        foreach ($data['products'] as $product) {
            $productsData[$product['id']] = [
                'amount_spent' => $product['amount_spent'],
                'leads' => $product['leads'] ?? 0,
            ];
        }
        $campaign->products()->sync($productsData);

        return redirect()->route('media-buyer.campaigns')->with('status', __('Campaign updated.'));
    }

    /**
     * Delete the specified campaign.
     */
    public function destroyCampaign(AdsCampaign $campaign)
    {
        $this->authorizeCampaignOwnership($campaign);

        $campaign->delete();

        return redirect()->route('media-buyer.campaigns')->with('status', __('Campaign deleted.'));
    }

    /**
     * Get products by country for media buyer.
     * Only returns products assigned to this media buyer.
     */
    public function getProductsByCountry(Request $request)
    {
        $countryId = $request->input('country_id');
        $userId = auth()->id();

        $products = Product::where('country_id', $countryId)
            ->whereHas('mediaBuyers', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($products);
    }

    /**
     * Ensure the campaign belongs to the authenticated media buyer.
     */
    protected function authorizeCampaignOwnership(AdsCampaign $campaign): void
    {
        if ((int) $campaign->user_id !== (int) auth()->id()) {
            abort(403);
        }
    }
}
