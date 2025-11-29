<?php

namespace App\Helpers;

class LocaleHelper
{
    /**
     * Get URL to switch language while preserving other query parameters
     */
    public static function switchLanguageUrl(string $locale): string
    {
        $currentUrl = url()->current();
        $queryParams = request()->except('lang');
        $queryParams['lang'] = $locale;
        
        return $currentUrl . '?' . http_build_query($queryParams);
    }
}

