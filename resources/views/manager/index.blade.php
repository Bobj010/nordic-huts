@extends('layouts.app')

@section('title', 'Рабочее место менеджера ресепшена — NORDIC HUTS')

@section('content')

<div class="py-10 md:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-border-subtle pb-6">
            <div class="space-y-1">
                <nav class="flex items-center gap-2 text-xs text-text-muted">
                    <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Главная</a>
                    <span>/</span>
                    <span class="text-text-main font-medium">Ресепшен глэмпинга</span>
                </nav>
                <h1 class="font-serif text-3xl sm:text-4xl text-primary font-normal">
                    Рабочее место менеджера
                </h1>
                <p class="text-xs text-text-muted">
                    Оперативный прием входящих бронирований, координация заселения и контроль статусов
                </p>
            </div>

            <div class="flex items-center gap-2 bg-secondary/10 border border-secondary/20 px-3.5 py-2 rounded-xl text-xs text-secondary font-semibold">
                <span class="material-symbols-outlined text-base" aria-hidden="true">concierge</span>
                <span>Дежурный менеджер: {{ auth()->user()->name }}</span>
            </div>
        </div>

        <!-- KPI STATS CARDS -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            
            <!-- Total -->
            <a href="{{ route('manager.index') }}" 
               class="bg-surface p-4 rounded-xl border border-border-subtle shadow-xs hover:border-primary transition-all {{ empty($selectedStatus) ? 'ring-2 ring-primary/20' : '' }}">
                <span class="text-[11px] font-semibold text-text-muted block">Всего заявок</span>
                <span class="font-serif text-2xl font-bold text-text-main font-mono tabular-nums mt-1 block">
                    {{ $stats['total'] }}
                </span>
            </a>

            <!-- Pending (High priority attention) -->
            <a href="{{ route('manager.index', ['status' => 'pending']) }}" 
               class="bg-amber-50/60 p-4 rounded-xl border border-amber-200 shadow-xs hover:border-amber-400 transition-all {{ $selectedStatus === 'pending' ? 'ring-2 ring-amber-400' : '' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-amber-800">Ожидают ответа</span>
                    <span class="material-symbols-outlined text-amber-600 text-lg" aria-hidden="true">pending_actions</span>
                </div>
                <span class="font-serif text-2xl font-bold text-amber-900 font-mono tabular-nums mt-1 block">
                    {{ $stats['pending'] }}
                </span>
            </a>

            <!-- Confirmed -->
            <a href="{{ route('manager.index', ['status' => 'confirmed']) }}" 
               class="bg-emerald-50/50 p-4 rounded-xl border border-emerald-200 shadow-xs hover:border-emerald-400 transition-all {{ $selectedStatus === 'confirmed' ? 'ring-2 ring-emerald-400' : '' }}">
                <span class="text-[11px] font-semibold text-emerald-800 block">Подтверждено</span>
                <span class="font-serif text-2xl font-bold text-emerald-900 font-mono tabular-nums mt-1 block">
                    {{ $stats['confirmed'] }}
                </span>
            </a>

            <!-- Completed -->
            <a href="{{ route('manager.index', ['status' => 'completed']) }}" 
               class="bg-surface p-4 rounded-xl border border-border-subtle shadow-xs hover:border-stone-400 transition-all {{ $selectedStatus === 'completed' ? 'ring-2 ring-stone-400' : '' }}">
                <span class="text-[11px] font-semibold text-text-muted block">Завершено</span>
                <span class="font-serif text-2xl font-bold text-stone-700 font-mono tabular-nums mt-1 block">
                    {{ $stats['completed'] }}
                </span>
            </a>

            <!-- Cancelled -->
            <a href="{{ route('manager.index', ['status' => 'cancelled']) }}" 
               class="bg-surface p-4 rounded-xl border border-border-subtle shadow-xs hover:border-rose-400 transition-all {{ $selectedStatus === 'cancelled' ? 'ring-2 ring-rose-400' : '' }}">
                <span class="text-[11px] font-semibold text-text-muted block">Отменено</span>
                <span class="font-serif text-2xl font-bold text-rose-800 font-mono tabular-nums mt-1 block">
                    {{ $stats['cancelled'] }}
                </span>
            </a>

        </div>

        <!-- STATUS FILTER TABS -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 text-xs">
            <a href="{{ route('manager.index') }}" 
               class="px-3.5 py-1.5 rounded-lg font-medium transition-colors shrink-0 {{ empty($selectedStatus) ? 'bg-primary text-white font-semibold' : 'bg-surface text-text-muted hover:text-primary border border-border-subtle' }}">
                Все заявки ({{ $stats['total'] }})
            </a>
            <a href="{{ route('manager.index', ['status' => 'pending']) }}" 
               class="px-3.5 py-1.5 rounded-lg font-medium transition-colors shrink-0 {{ $selectedStatus === 'pending' ? 'bg-amber-600 text-white font-semibold' : 'bg-surface text-amber-800 hover:bg-amber-50 border border-amber-200' }}">
                Ожидают подтверждения ({{ $stats['pending'] }})
            </a>
            <a href="{{ route('manager.index', ['status' => 'confirmed']) }}" 
               class="px-3.5 py-1.5 rounded-lg font-medium transition-colors shrink-0 {{ $selectedStatus === 'confirmed' ? 'bg-emerald-700 text-white font-semibold' : 'bg-surface text-emerald-800 hover:bg-emerald-50 border border-emerald-200' }}">
                Подтвержденные ({{ $stats['confirmed'] }})
            </a>
            <a href="{{ route('manager.index', ['status' => 'completed']) }}" 
               class="px-3.5 py-1.5 rounded-lg font-medium transition-colors shrink-0 {{ $selectedStatus === 'completed' ? 'bg-stone-700 text-white font-semibold' : 'bg-surface text-text-muted hover:bg-stone-100 border border-border-subtle' }}">
                Завершенные ({{ $stats['completed'] }})
            </a>
            <a href="{{ route('manager.index', ['status' => 'cancelled']) }}" 
               class="px-3.5 py-1.5 rounded-lg font-medium transition-colors shrink-0 {{ $selectedStatus === 'cancelled' ? 'bg-rose-700 text-white font-semibold' : 'bg-surface text-rose-800 hover:bg-rose-50 border border-rose-200' }}">
                Отмененные ({{ $stats['cancelled'] }})
            </a>
        </div>

        <!-- BOOKINGS TABLE -->
        <div class="bg-surface rounded-2xl border border-border-subtle shadow-elevation-1 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-linen-light border-b border-border-subtle text-[11px] font-semibold text-text-muted uppercase tracking-wider">
                            <th scope="col" class="py-3.5 px-4 font-mono">№</th>
                            <th scope="col" class="py-3.5 px-4">Домик</th>
                            <th scope="col" class="py-3.5 px-4">Клиент</th>
                            <th scope="col" class="py-3.5 px-4">Даты проживания</th>
                            <th scope="col" class="py-3.5 px-4">Сумма</th>
                            <th scope="col" class="py-3.5 px-4">Статус</th>
                            <th scope="col" class="py-3.5 px-4 text-right">Действия в 1 клик</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle/70">
                        @forelse($bookings as $booking)
                            @php
                                $status = $booking->status_info;
                            @endphp
                            <tr class="hover:bg-linen/40 transition-colors">
                                <!-- ID -->
                                <td class="py-4 px-4 font-mono font-semibold text-text-main tabular-nums whitespace-nowrap">
                                    #{{ $booking->id }}
                                </td>

                                <!-- House -->
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-lg bg-stone-100 overflow-hidden shrink-0 border border-border-subtle">
                                            <img src="{{ $booking->house->image_url }}" alt="{{ $booking->house->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <a href="{{ route('houses.show', $booking->house->slug) }}" target="_blank" class="font-semibold text-text-main hover:text-primary transition-colors block">
                                                {{ $booking->house->name }}
                                            </a>
                                            <span class="text-[10px] text-text-muted">
                                                {{ $booking->house->category->name }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Guest -->
                                <td class="py-4 px-4">
                                    <div class="space-y-0.5">
                                        <div class="font-semibold text-text-main">{{ $booking->guest_name }}</div>
                                        <a href="tel:{{ $booking->guest_phone }}" class="text-primary hover:underline block font-mono tabular-nums">
                                            {{ $booking->guest_phone }}
                                        </a>
                                        @if($booking->comment)
                                            <span class="text-[10px] text-text-light block truncate max-w-xs" title="{{ $booking->comment }}">
                                                «{{ $booking->comment }}»
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Dates -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <div class="font-mono tabular-nums font-medium text-text-main">
                                        {{ $booking->check_in_date->format('d.m.Y') }} &rarr; {{ $booking->check_out_date->format('d.m.Y') }}
                                    </div>
                                    <div class="text-[11px] text-text-muted mt-0.5">
                                        {{ $booking->nights_count }} {{ $booking->nights_count === 1 ? 'ночь' : ($booking->nights_count < 5 ? 'ночи' : 'ночей') }}
                                    </div>
                                </td>

                                <!-- Price -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <div class="font-mono tabular-nums font-bold text-primary text-sm">
                                        {{ number_format($booking->total_price, 0, '', ' ') }}&nbsp;₽
                                    </div>
                                    <div class="text-[10px] text-text-light">
                                        {{ $booking->guests_count }} {{ $booking->guests_count === 1 ? 'гость' : ($booking->guests_count < 5 ? 'гостя' : 'гостей') }}
                                    </div>
                                </td>

                                <!-- Status Badge -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $status['bg'] }} {{ $status['text'] }} border">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current" aria-hidden="true"></span>
                                        <span>{{ $status['label'] }}</span>
                                    </span>
                                </td>

                                <!-- Quick Actions in 1 Click -->
                                <td class="py-4 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if($booking->status === 'pending')
                                            <!-- Confirm Button -->
                                            <form method="POST" action="{{ route('manager.bookings.status', $booking) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" 
                                                        class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded font-semibold text-[11px] transition-colors shadow-2xs"
                                                        aria-label="Подтвердить бронь #{{ $booking->id }}">
                                                    Подтвердить
                                                </button>
                                            </form>

                                            <!-- Reject Button -->
                                            <form method="POST" action="{{ route('manager.bookings.status', $booking) }}" onsubmit="return confirm('Отклонить бронь #{{ $booking->id }}?');">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" 
                                                        class="px-2.5 py-1 bg-surface hover:bg-rose-50 text-rose-700 border border-rose-200 rounded font-semibold text-[11px] transition-colors"
                                                        aria-label="Отклонить бронь #{{ $booking->id }}">
                                                    Отклонить
                                                </button>
                                            </form>
                                        @elseif($booking->status === 'confirmed')
                                            <!-- Complete Button -->
                                            <form method="POST" action="{{ route('manager.bookings.status', $booking) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" 
                                                        class="px-2.5 py-1 bg-stone-800 hover:bg-stone-900 text-white rounded font-semibold text-[11px] transition-colors shadow-2xs"
                                                        aria-label="Завершить проживание #{{ $booking->id }}">
                                                    Завершить
                                                </button>
                                            </form>

                                            <!-- Cancel Button -->
                                            <form method="POST" action="{{ route('manager.bookings.status', $booking) }}" onsubmit="return confirm('Отменить подтвержденную бронь #{{ $booking->id }}?');">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" 
                                                        class="px-2 py-1 text-rose-700 hover:underline text-[11px] transition-colors"
                                                        aria-label="Отменить бронь #{{ $booking->id }}">
                                                    Отменить
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-text-light text-[11px] italic">
                                                В архиве
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-text-muted">
                                    <span class="material-symbols-outlined text-3xl block text-text-light mb-2" aria-hidden="true">inbox</span>
                                    <span>Заявок с выбранным статусом не обнаружено.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Table Pagination -->
            @if($bookings->hasPages())
                <div class="p-4 border-t border-border-subtle bg-linen-light flex justify-center">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

@endsection
