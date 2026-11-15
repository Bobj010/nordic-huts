@extends('layouts.app')

@section('title', 'Вход в систему — NORDIC HUTS')

@section('content')

<div class="py-12 sm:py-20 flex items-center justify-center">
    <div class="max-w-md w-full mx-auto px-4 sm:px-6 space-y-8">
        
        <!-- Header -->
        <div class="text-center space-y-2">
            <span class="material-symbols-outlined text-primary text-4xl" style="font-variation-settings: 'FILL' 1;">forest</span>
            <h1 class="font-serif text-3xl text-primary font-normal">
                Вход в личный кабинет
            </h1>
            <p class="text-xs text-text-muted">
                Управляйте своими бронированиями и персональными спецпредложениями
            </p>
        </div>

        <!-- Login Card -->
        <div class="bg-surface border border-border-subtle rounded-2xl p-6 sm:p-8 shadow-elevation-2 space-y-6">
            
            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                <!-- Email field -->
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-text-muted">Электронная почта</label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           placeholder="name@example.com"
                           required 
                           autocomplete="email"
                           class="w-full text-xs bg-background border @error('email') border-secondary @else border-border-subtle @enderror rounded-lg px-3 py-2.5 text-text-main focus:border-primary focus:ring-0">
                    @error('email')
                        <p class="text-[11px] text-secondary font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password field -->
                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-semibold text-text-muted">Пароль</label>
                    </div>
                    <input type="password" 
                           name="password" 
                           placeholder="••••••••"
                           required 
                           autocomplete="current-password"
                           class="w-full text-xs bg-background border @error('password') border-secondary @else border-border-subtle @enderror rounded-lg px-3 py-2.5 text-text-main focus:border-primary focus:ring-0">
                    @error('password')
                        <p class="text-[11px] text-secondary font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember me -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded text-primary focus:ring-primary border-border-subtle">
                        <span class="text-text-muted">Запомнить меня</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-3 bg-primary hover:bg-primary-dark text-white rounded-lg text-sm font-semibold transition-colors shadow-xs">
                    Войти в аккаунт
                </button>
            </form>

            <!-- Register Link -->
            <div class="text-center pt-2 border-t border-border-subtle/60 text-xs text-text-muted">
                Еще нет аккаунта? 
                <a href="{{ route('register') }}" class="font-semibold text-primary hover:underline">
                    Зарегистрироваться
                </a>
            </div>

        </div>

        <!-- FAST TEACHER QUICK LOGIN HELPER -->
        <div class="bg-linen p-5 rounded-xl border border-border-subtle/80 space-y-3">
            <div class="flex items-center gap-1.5 text-xs font-semibold text-primary">
                <span class="material-symbols-outlined text-sm">school</span>
                <span>Быстрый вход для преподавателя (1 клик):</span>
            </div>
            <div class="grid grid-cols-3 gap-2 text-center">
                <a href="{{ route('quick-login', 'client') }}" 
                   class="p-2 bg-surface hover:bg-white text-text-main border border-border-subtle rounded text-xs font-medium transition-all shadow-2xs hover:border-primary">
                    <strong class="block text-primary">Клиент</strong>
                    <span class="text-[10px] text-text-muted">Анна</span>
                </a>
                <a href="{{ route('quick-login', 'manager') }}" 
                   class="p-2 bg-surface hover:bg-white text-text-main border border-border-subtle rounded text-xs font-medium transition-all shadow-2xs hover:border-secondary">
                    <strong class="block text-secondary">Менеджер</strong>
                    <span class="text-[10px] text-text-muted">Елена</span>
                </a>
                <a href="{{ route('quick-login', 'admin') }}" 
                   class="p-2 bg-surface hover:bg-white text-text-main border border-border-subtle rounded text-xs font-medium transition-all shadow-2xs hover:border-stone-800">
                    <strong class="block text-stone-900">Админ</strong>
                    <span class="text-[10px] text-text-muted">Александр</span>
                </a>
            </div>
            <p class="text-[10px] text-text-muted text-center pt-1">
                Пароль по умолчанию для всех демо-пользователей: <code class="font-mono bg-white px-1 py-0.5 rounded text-primary">password</code>
            </p>
        </div>

    </div>
</div>

@endsection
