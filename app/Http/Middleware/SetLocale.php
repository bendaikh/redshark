<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

class SetLocale
{
    /**
     * Supported locales with their properties
     */
    public static array $supportedLocales = [
        'en' => [
            'name' => 'English',
            'native' => 'English',
            'flag' => '🇬🇧',
            'dir' => 'ltr',
        ],
        'fr' => [
            'name' => 'French',
            'native' => 'Français',
            'flag' => '🇫🇷',
            'dir' => 'ltr',
        ],
        'ar' => [
            'name' => 'Arabic',
            'native' => 'العربية',
            'flag' => '🇸🇦',
            'dir' => 'rtl',
        ],
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if locale is being changed via query parameter
        if ($request->query('lang') !== null) {
            $locale = $request->query('lang');
            if (array_key_exists($locale, self::$supportedLocales)) {
                $request->session()->put('locale', $locale);
            }
        }

        // Get locale from session or use default
        $locale = $request->session()->get('locale', config('app.locale', 'en'));

        // Validate locale
        if (!array_key_exists($locale, self::$supportedLocales)) {
            $locale = 'en';
        }

        // Set application locale
        App::setLocale($locale);

        // Get current locale properties
        $currentLocale = self::$supportedLocales[$locale];
        $isRtl = $currentLocale['dir'] === 'rtl';

        // Share locale data with all views
        View::share('currentLocale', $locale);
        View::share('currentLocaleData', $currentLocale);
        View::share('isRtl', $isRtl);
        View::share('textDir', $currentLocale['dir']);
        View::share('supportedLocales', self::$supportedLocales);

        return $next($request);
    }
}

