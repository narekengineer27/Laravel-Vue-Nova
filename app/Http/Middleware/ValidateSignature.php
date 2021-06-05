<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\ExpiredSignatureException;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

class ValidateSignature
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
        // It checks expiration, too
        if ($request->hasValidSignature()) {
            return $next($request);
        }

        if (now()->getTimestamp() > $request->expires) {
            throw new ExpiredSignatureException('Email has been expired.');
        }

        throw new InvalidSignatureException;
    }
}
