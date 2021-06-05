<?php

namespace Acme\Report\Http\Middleware;

use Acme\Report\Report;

class Authorize
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        return resolve(Report::class)->authorize($request) ? $next($request) : abort(403);
    }
}
