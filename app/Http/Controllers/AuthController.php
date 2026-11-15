<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Process authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Пожалуйста, введите ваш email.',
            'email.email' => 'Введите корректный адрес электронной почты.',
            'password.required' => 'Пожалуйста, введите пароль.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.index'))->with('success', 'С возвращением, Управляющий!');
            }

            if ($user->role === 'manager') {
                return redirect()->intended(route('manager.index'))->with('success', 'С возвращением, Администратор ресепшена!');
            }

            return redirect()->intended(route('client.bookings'))->with('success', 'Вы успешно вошли в личный кабинет.');
        }

        return back()->withErrors([
            'email' => 'Неверный адрес электронной почты или пароль.',
        ])->onlyInput('email');
    }

    /**
     * Show registration form.
     */
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Пожалуйста, укажите ваше имя.',
            'email.required' => 'Укажите адрес электронной почты.',
            'email.email' => 'Введите корректный email адрес.',
            'email.unique' => 'Пользователь с таким email уже зарегистрирован.',
            'password.required' => 'Придумайте пароль.',
            'password.min' => 'Пароль должен содержать минимум 6 символов.',
            'password.confirmed' => 'Введенные пароли не совпадают.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => 'client',
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('client.bookings')->with('success', 'Добро пожаловать в Nordic Huts! Ваш аккаунт успешно создан.');
    }

    /**
     * Log user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Вы успешно вышли из системы.');
    }

    /**
     * Fast login for teacher evaluation and project defense.
     */
    public function quickLogin(string $role): RedirectResponse
    {
        $allowedRoles = ['admin', 'manager', 'client', 'guest'];

        if (! in_array($role, $allowedRoles, true)) {
            return redirect()->route('home')->with('error', 'Недопустимая роль для быстрого входа.');
        }

        if ($role === 'guest') {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();

            return redirect()->route('home')->with('info', 'Вы переключились в режим Гостя.');
        }

        $user = User::where('role', $role)->first();

        if (! $user) {
            return redirect()->route('home')->with('error', "Пользователь с ролью '{$role}' не найден. Выполните сидирование БД.");
        }

        Auth::login($user);
        session()->regenerate();

        $roleLabels = [
            'admin' => 'Администратор (Управляющий)',
            'manager' => 'Менеджер ресепшена',
            'client' => 'Клиент (Гость)',
        ];

        $label = $roleLabels[$role] ?? $role;

        if ($role === 'admin') {
            return redirect()->route('admin.index')->with('success', "Быстрый вход выполнен: {$user->name} [{$label}]");
        }

        if ($role === 'manager') {
            return redirect()->route('manager.index')->with('success', "Быстрый вход выполнен: {$user->name} [{$label}]");
        }

        return redirect()->route('client.bookings')->with('success', "Быстрый вход выполнен: {$user->name} [{$label}]");
    }
}
