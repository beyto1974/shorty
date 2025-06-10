<?php

namespace App\Http\Middleware;

use App\Models\Shortener;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OwnerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * @var Shortener
         */
        $shortener = $request->shortener;

        abort_if($shortener->created_by_user_id !== auth()->user()->id, 401, 'No access to shortener');

        return $next($request);
    }
}
