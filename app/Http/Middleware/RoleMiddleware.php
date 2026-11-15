<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->guest(route('login'))->with('error', 'Пожалуйста, войдите в систему для доступа к этому разделу.');
        }

        $user = Auth::user();

        // If user role is in the list of permitted roles
        if (empty($roles) || in_array($user->role, $roles, true)) {
            return $next($request);
        }

        abort(403, 'Доступ ограничен. У вашей учетной записи недостаточно прав для просмотра этого раздела.');
    }
}
