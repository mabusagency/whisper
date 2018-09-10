<?php

namespace App\Http\Middleware;

use Closure;

class Session
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(isset($_SERVER['REQUEST_URI'])) {
            $route = $_SERVER['REQUEST_URI'];

            if(!session('institution') && !strstr($route,'institutions')) {
                return redirect(route('institutions.index'));
            }

            elseif(!session('campaign') && strstr($route,'campaigns')
                && $route != '/campaigns' && !strstr($route,'campaigns/set')
                && $route != '/campaigns' && !strstr($route,'campaigns/create')) {
                return redirect(route('campaigns.index'));
            }
        }

        return $next($request);
    }
}
