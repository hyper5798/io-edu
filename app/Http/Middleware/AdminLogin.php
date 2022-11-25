<?php

namespace App\Http\Middleware;

use Closure;

class AdminLogin
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
        if(!session('user')){
            if (strpos($request->getRequestUri(), 'module') !== false) {
                //dd('module');
                return redirect('/login?link=module');
            } else if (strpos($request->getRequestUri(), 'develop') !== false) {
                //dd('develop');
                return redirect('/login?link=develop');
            }

            return redirect('/login');
        }
        return $next($request);
    }
}
