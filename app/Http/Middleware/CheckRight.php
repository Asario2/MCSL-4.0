<?php

namespace App\Http\Middleware;

use Closure;

class CheckRight
{
    public function handle($request, Closure $next, string $right)
    {
        if (!CheckZRights($right)) {
                abort(403);

        }

        return $next($request);
    }
}
