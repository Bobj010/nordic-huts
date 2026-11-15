@extends('layouts.app')

@section('title', 'Регистрация — NORDIC HUTS')

@section('content')

<div class="py-12 sm:py-20 flex items-center justify-center">
    <div class="max-w-md w-full mx-auto px-4 sm:px-6 space-y-8">
        
        <!-- Header -->
        <div class="text-center space-y-2">
            <span class="material-symbols-outlined text-primary text-4xl" style="font-variation-settings: 'FILL' 1;">forest</span>
            <h1 class="font-serif text-3xl text-primary font-normal">
                Создание аккаунта
            </h1>
            <p class="text-xs text-text-muted">
                Зарегистрируйтесь, чтобы бронировать отдых и сохранять историю поездок
            </p>
        </div>

        <!-- Register Card -->
        <div class="bg-surface border border-border-subtle rounded-2xl p-6 sm:p-8 shadow-elevation-2 space-y-6">
            
            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf

                <!-- Name field -->
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-text-muted">Ваше имя</label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name') }}" 
                           placeholder="Анна Смирнова" 
                           required 
                           autocomplete="name"
                           class="w-full text-xs bg-background border @error('name') border-secondary @else border-border-subtle @enderror rounded-lg px-3 py-2.5 text-text-main focus:border-primary focus:ring-0">
                    @error('name')
                        <p class="text-[11px] text-secondary font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email field -->
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-text-muted">Электронная почта</label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           placeholder="anna@example.com" 
                           required 
                           autocomplete="email"
                           class="w-full text-xs bg-background border @error('email') border-secondary @else border-border-subtle @enderror rounded-lg px-3 py-2.5 text-text-main focus:border-primary focus:ring-0">
                    @error('email')
                        <p class="text-[11px] text-secondary font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone field -->
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-text-muted">Телефон для связи</label>
                    <input type="text" 
                           name="phone" 
                           value="{{ old('phone') }}" 
                           placeholder="+7 (999) 000-00-00" 
                           class="w-full text-xs bg-background border border-border-subtle rounded-lg px-3 py-2.5 text-text-main focus:border-primary focus:ring-0">
                </div>

                <!-- Password field -->
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-text-muted">Пароль (минимум 6 символов)</label>
                    <input type="password" 
                           name="password" 
                           placeholder="••••••••" 
                           required 
                           autocomplete="new-password"
                           class="w-full text-xs bg-background border @error('password') border-secondary @else border-border-subtle @enderror rounded-lg px-3 py-2.5 text-text-main focus:border-primary focus:ring-0">
                    @error('password')
                        <p class="text-[11px] text-secondary font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password confirmation -->
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-text-muted">Подтверждение пароля</label>
                    <input type="password" 
                           name="password_confirmation" 
                           placeholder="••••••••" 
                           required 
                           autocomplete="new-password"
                           class="w-full text-xs bg-background border border-border-subtle rounded-lg px-3 py-2.5 text-text-main focus:border-primary focus:ring-0">
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-3 bg-primary hover:bg-primary-dark text-white rounded-lg text-sm font-semibold transition-colors shadow-xs mt-2">
                    Зарегистрироваться
                </button>
            </form>

            <!-- Login Link -->
            <div class="text-center pt-2 border-t border-border-subtle/60 text-xs text-text-muted">
                Уже зарегистрированы? 
                <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">
                    Войти в личный кабинет
                </a>
            </div>

        </div>

    </div>
</div>

@endsection
