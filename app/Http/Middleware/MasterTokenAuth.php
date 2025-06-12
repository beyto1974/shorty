<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class MasterTokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $masterToken = Config::string('app.master_token');

        abort_if(empty($masterToken), 500, 'No master token set');
        abort_if(request()->bearerToken() !== $masterToken, 401, 'Token invalid');

        return $next($request);
    }
}
