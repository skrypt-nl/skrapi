<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class APIVersion
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param $guard
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next, $guard): Response|JsonResponse|RedirectResponse
    {
        config(['api.version' => $guard]);
        return $next($request);
    }
}
