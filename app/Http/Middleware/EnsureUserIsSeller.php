<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSeller
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! $request->user()->isSeller() || ! $request->user()->sellerProfile()->exists()) {
            return redirect()->route('seller.request')->with('error', 'Kamu perlu menjadi seller terverifikasi untuk mengakses Seller Center.');
        }

        return $next($request);
    }
}
