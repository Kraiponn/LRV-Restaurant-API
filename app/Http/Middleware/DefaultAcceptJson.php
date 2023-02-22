<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DefaultAcceptJson
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('accept', 'application/json', true);
        // $request->headers->set('accept', 'multipart/form-data', true);

        return $next($request);
    }
}
