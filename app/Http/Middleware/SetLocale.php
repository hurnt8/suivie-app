<?php

namespace App\Http\Middleware;

use App\Models\Settings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Available locales, kept in sync with the `lang/` directory.
     */
    public const AVAILABLE = ['fr', 'en', 'es', 'de', 'it', 'pt', 'ro', 'pl'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = Session::get('locale') ?? Settings::current()->default_locale ?? config('app.locale');

        if (! in_array($locale, self::AVAILABLE, true)) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
