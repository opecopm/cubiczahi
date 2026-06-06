<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserType
{
    public function handle(Request $request, Closure $next, ...$types)
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('login');
        }

        $allowedTypes = array_values(array_filter($types, static fn ($type) => $type !== null && $type !== ''));

        if ($allowedTypes === []) {
            return $next($request);
        }

        $userType = $user->type ?? null;

        if ($userType instanceof \BackedEnum) {
            $userType = $userType->value;
        } elseif (! is_scalar($userType)) {
            $userType = null;
        }

        if (! in_array((string) $userType, $allowedTypes, true)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
