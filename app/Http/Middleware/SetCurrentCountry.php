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

		// If selectedId is 0, it means "Global" view (all countries)
		// Don't try to fetch a country for Global view
		if ($selectedId === 0 && $user) {
			// For Global view, just mark as global
			View::share('currentCountry', null);
			View::share('isGlobalView', true);
			return $next($request);
		}

		if (! $selectedId && $user) {
			if ($user->hasRole('superadmin')) {
				$selectedId = Country::value('id');
			} else {
				$selectedId = $user->countries()->value('countries.id');
			}
			if ($selectedId) {
				$request->session()->put('current_country_id', $selectedId);
			}
		}

		$currentCountry = $selectedId ? Country::find($selectedId) : null;

		if ($user && $currentCountry && ! $user->hasRole('superadmin')) {
			$hasAccess = $user->countries()->whereKey($currentCountry->id)->exists();
			if (! $hasAccess) {
				$currentCountry = $user->countries()->first();
				$request->session()->put('current_country_id', optional($currentCountry)->id);
			}
		}

		View::share('currentCountry', $currentCountry);
		View::share('isGlobalView', false);

		return $next($request);
	}
}

