<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class SetCurrentCountry
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
	 */
	public function handle(Request $request, Closure $next): Response
	{
		$user = Auth::user();
		
		// Check if country is being selected from dropdown
		if ($request->query('country') !== null) {
			$request->session()->put('current_country_id', (int) $request->query('country'));
		}
		
		$selectedId = (int) ($request->session()->get('current_country_id') ?? 0);

		// If selectedId is 0, it means "All" view (all accessible countries)
		// Don't try to fetch a country for "All" view
		if ($selectedId === 0 && $user) {
			// For "All" view, just mark as global
			View::share('currentCountry', null);
			View::share('isGlobalView', true);
			return $next($request);
		}

		if (! $selectedId && $user) {
			if ($user->hasRole('superadmin')) {
				$selectedId = Country::value('id');
			} elseif ($user->hasRole('media_buyer')) {
				// For media buyers, get the first country from their accessible countries
				$accessibleCountries = $user->getAccessibleCountries();
				$selectedId = $accessibleCountries->first()->id ?? null;
			} else {
				$selectedId = $user->countries()->value('countries.id');
			}
			if ($selectedId) {
				$request->session()->put('current_country_id', $selectedId);
			}
		}

		$currentCountry = $selectedId ? Country::find($selectedId) : null;

		if ($user && $currentCountry && ! $user->hasRole('superadmin')) {
			// Check if user has access to the selected country
			if ($user->hasRole('media_buyer')) {
				// For media buyers, check if they have products in this country
				$accessibleCountries = $user->getAccessibleCountries();
				$hasAccess = $accessibleCountries->contains('id', $currentCountry->id);
				
				if (! $hasAccess) {
					$currentCountry = $accessibleCountries->first() ?? null;
					$request->session()->put('current_country_id', optional($currentCountry)->id);
				}
			} else {
				$hasAccess = $user->countries()->whereKey($currentCountry->id)->exists();
				if (! $hasAccess) {
					$currentCountry = $user->countries()->first();
					$request->session()->put('current_country_id', optional($currentCountry)->id);
				}
			}
		}

		View::share('currentCountry', $currentCountry);
		View::share('isGlobalView', false);

		return $next($request);
	}
}

