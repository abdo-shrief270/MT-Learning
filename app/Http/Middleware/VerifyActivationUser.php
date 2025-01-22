<?php

namespace App\Http\Middleware;

use App\Filament\Pages\AccountNotActivated;
use Closure;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyActivationUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->email_verified_at !== null && !Auth::user()->active) {
            if ($request->url() !== route('account-not-activated')) {

                return redirect(route('account-not-activated'));
            }
        }

        if ($request->url() == route('account-not-activated') && Auth::check() && Auth::user()->active) {
            return redirect(Dashboard::getUrl());
        }

        return $next($request); // Continue with the next middleware
    }
}
