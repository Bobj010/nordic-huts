@extends('layouts.app')

@section('title', 'Панель администратора — NORDIC HUTS')

@section('content')

<div class="py-10 md:py-16" x-data="{ currentTab: 'houses' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-border-subtle pb-6">
            <div class="space-y-1">
                <nav class="flex items-center gap-2 text-xs text-text-muted">
                    <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Главная</a>
                    <span>/</span>
                    <span class="text-text-main font-medium">Администрирование</span>
                </nav>
                <h1 class="font-serif text-3xl sm:text-4xl text-primary font-normal">
                    Панель управляющего комплексом
                </h1>
                <p class="text-xs text-text-muted">
                    Сводная финансовая аналитика, мониторинг номерного фонда и разграничение прав персонала
                </p>
            </div>

            <div class="flex items-center gap-2 bg-stone-900 text-white px-3.5 py-2 rounded-xl text-xs font-semibold shadow-xs">
                <span class="material-symbols-outlined text-base" aria-hidden="true">admin_panel_settings</span>
                <span>Главный администратор: {{ auth()->user()->name }}</span>
            </div>
        </div>

        <!-- KPI METRICS (Financial & Operations) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            
            <!-- Revenue -->
            <div class="bg-surface p-5 rounded-2xl border border-border-subtle shadow-elevation-1 space-y-1">
                <span class="text-xs font-semibold text-text-muted">Выручка по завершенным</span>
                <div class="font-serif text-2xl sm:text-3xl font-bold text-primary font-mono tabular-nums">
                    {{ number_format($stats['revenue_total'], 0, '', ' ') }}&nbsp;₽
                </div>
                <span class="text-[10px] text-text-light block">По факту проживания гостей</span>
            </div>

            <!-- Houses count -->
            <div class="bg-surface p-5 rounded-2xl border border-border-subtle shadow-elevation-1 space-y-1">
                <span class="text-xs font-semibold text-text-muted">Номерной фонд</span>
                <div class="font-serif text-2xl sm:text-3xl font-bold text-text-main font-mono tabular-nums">
                    {{ $stats['houses_count'] }}
                </div>
                <span class="text-[10px] text-text-light block">Домиков в {{ $stats['categories_count'] }} категориях</span>
            </div>

            <!-- Total bookings -->
            <div class="bg-surface p-5 rounded-2xl border border-border-subtle shadow-elevation-1 space-y-1">
                <span class="text-xs font-semibold text-text-muted">Всего бронирований</span>
                <div class="font-serif text-2xl sm:text-3xl font-bold text-text-main font-mono tabular-nums">
                    {{ $stats['bookings_count'] }}
                </div>
                <span class="text-[10px] text-text-light block">За все время работы сервиса</span>
            </div>

            <!-- Users count -->
            <div class="bg-surface p-5 rounded-2xl border border-border-subtle shadow-elevation-1 space-y-1">
                <span class="text-xs font-semibold text-text-muted">База пользователей</span>
                <div class="font-serif text-2xl sm:text-3xl font-bold text-text-main font-mono tabular-nums">
                    {{ $stats['users_count'] }}
                </div>
                <span class="text-[10px] text-text-light block">Клиенты, менеджеры, админы</span>
            </div>

        </div>

        <!-- TABS SWITCHER -->
        <div class="flex items-center gap-2 border-b border-border-subtle pb-px text-xs font-semibold">
            <button @click="currentTab = 'houses'" 
                    type="button"
                    :class="currentTab === 'houses' ? 'border-primary text-primary border-b-2 pb-3' : 'text-text-muted hover:text-text-main pb-3'"
                    class="px-4 transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-base" aria-hidden="true">cottage</span>
                <span>Номерной фонд ({{ $houses->count() }})</span>
            </button>

            <button @click="currentTab = 'users'" 
                    type="button"
                    :class="currentTab === 'users' ? 'border-primary text-primary border-b-2 pb-3' : 'text-text-muted hover:text-text-main pb-3'"
                    class="px-4 transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-base" aria-hidden="true">manage_accounts</span>
                <span>Управление сотрудниками и ролями ({{ $users->count() }})</span>
            </button>
        </div>

        <!-- TAB 1: HOUSES INVENTORY -->
        <div x-show="currentTab === 'houses'" x-cloak class="space-y-4">
            <div class="bg-surface rounded-2xl border border-border-subtle shadow-elevation-1 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-linen-light border-b border-border-subtle text-[11px] font-semibold text-text-muted uppercase tracking-wider">
                                <th scope="col" class="py-3.5 px-4">Домик</th>
                                <th scope="col" class="py-3.5 px-4">Категория</th>
                                <th scope="col" class="py-3.5 px-4">Вместимость / Площадь</th>
                                <th scope="col" class="py-3.5 px-4">Цена / сутки</th>
                                <th scope="col" class="py-3.5 px-4">Удобства</th>
                                <th scope="col" class="py-3.5 px-4">Статус</th>
                                <th scope="col" class="py-3.5 px-4 text-right">Действие</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-subtle/70">
                            @foreach($houses as $house)
                                <tr class="hover:bg-linen/40 transition-colors">
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 rounded-lg bg-stone-100 overflow-hidden shrink-0 border border-border-subtle">
                                                <img src="{{ $house->image_url }}" alt="{{ $house->name }}" class="w-full h-full object-cover">
                                            </div>
                                            <div class="font-semibold text-text-main">
                                                <a href="{{ route('houses.show', $house->slug) }}" target="_blank" class="hover:text-primary transition-colors">
                                                    {{ $house->name }}
                                                </a>
                                                @if($house->is_featured)
                                                    <span class="ml-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-secondary/10 text-secondary uppercase">Хит</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-4 px-4 text-text-muted">
                                        {{ $house->category->name }}
                                    </td>

                                    <td class="py-4 px-4 whitespace-nowrap text-text-muted font-mono tabular-nums">
                                        {{ $house->capacity }} чел. &bull; {{ $house->area }} м²
                                    </td>

                                    <td class="py-4 px-4 whitespace-nowrap font-mono tabular-nums font-bold text-primary">
                                        {{ number_format($house->price_per_night, 0, '', ' ') }}&nbsp;₽
                                    </td>

                                    <td class="py-4 px-4">
                                        <div class="flex flex-wrap gap-1 max-w-xs">
                                            @foreach($house->amenities->take(3) as $amenity)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] bg-linen text-text-muted border border-border-subtle/60 truncate">
                                                    {{ $amenity->name }}
                                                </span>
                                            @endforeach
                                            @if($house->amenities->count() > 3)
                                                <span class="text-[10px] text-text-light">+{{ $house->amenities->count() - 3 }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="py-4 px-4 whitespace-nowrap">
                                        @if($house->is_active)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600" aria-hidden="true"></span>
                                                Активен
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-stone-100 text-stone-600 border border-stone-200">
                                                Черновик
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-4 text-right whitespace-nowrap">
                                        <a href="{{ route('houses.show', $house->slug) }}" 
                                           target="_blank" 
                                           class="text-xs font-semibold text-primary hover:underline inline-flex items-center gap-1"
                                           aria-label="Просмотреть домик {{ $house->name }}">
                                            <span>Просмотр</span>
                                            <span class="material-symbols-outlined text-xs" aria-hidden="true">open_in_new</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: USERS & ROLES MANAGEMENT -->
        <div x-show="currentTab === 'users'" x-cloak class="space-y-4">
            <div class="bg-surface rounded-2xl border border-border-subtle shadow-elevation-1 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-linen-light border-b border-border-subtle text-[11px] font-semibold text-text-muted uppercase tracking-wider">
                                <th scope="col" class="py-3.5 px-4 font-mono">ID</th>
                                <th scope="col" class="py-3.5 px-4">Имя сотрудника / гостя</th>
                                <th scope="col" class="py-3.5 px-4">Контакты</th>
                                <th scope="col" class="py-3.5 px-4">Дата регистрации</th>
                                <th scope="col" class="py-3.5 px-4">Текущая роль</th>
                                <th scope="col" class="py-3.5 px-4 text-right">Управление доступом</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-subtle/70">
                            @foreach($users as $user)
                                <tr class="hover:bg-linen/40 transition-colors">
                                    <td class="py-4 px-4 font-mono font-semibold text-text-main tabular-nums">
                                        #{{ $user->id }}
                                    </td>

                                    <td class="py-4 px-4 font-semibold text-text-main">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span class="ml-1 text-[10px] text-primary font-normal">(Вы)</span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-4">
                                        <div class="text-text-main">{{ $user->email }}</div>
                                        <div class="text-[11px] text-text-muted font-mono tabular-nums">{{ $user->phone ?? '—' }}</div>
                                    </td>

                                    <td class="py-4 px-4 whitespace-nowrap text-text-muted font-mono tabular-nums">
                                        {{ $user->created_at->format('d.m.Y H:i') }}
                                    </td>

                                    <td class="py-4 px-4 whitespace-nowrap">
                                        @if($user->role === 'admin')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[11px] font-semibold bg-stone-900 text-white">
                                                Администратор
                                            </span>
                                        @elseif($user->role === 'manager')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[11px] font-semibold bg-secondary/15 text-secondary border border-secondary/30">
                                                Менеджер
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[11px] font-semibold bg-linen text-text-muted border border-border-subtle">
                                                Клиент
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-4 text-right whitespace-nowrap">
                                        @if($user->id === auth()->id())
                                            <span class="text-[11px] text-text-light italic">Роль защищена</span>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.role', $user) }}" class="inline-flex items-center gap-2 justify-end">
                                                @csrf
                                                @method('PATCH')
                                                <label for="role-select-{{ $user->id }}" class="sr-only">Роль пользователя {{ $user->name }}</label>
                                                <select id="role-select-{{ $user->id }}" 
                                                        name="role" 
                                                        class="text-xs bg-background border border-border-subtle rounded px-2 py-1 text-text-main focus:border-primary focus:ring-0">
                                                    <option value="client" {{ $user->role === 'client' ? 'selected' : '' }}>Клиент</option>
                                                    <option value="manager" {{ $user->role === 'manager' ? 'selected' : '' }}>Менеджер</option>
                                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Админ</option>
                                                </select>
                                                <button type="submit" 
                                                        class="px-2.5 py-1 bg-primary hover:bg-primary-dark text-white rounded text-xs font-semibold transition-colors shadow-2xs"
                                                        aria-label="Сохранить роль для {{ $user->name }}">
                                                    Сохранить
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
