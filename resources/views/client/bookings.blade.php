@extends('layouts.app')

@section('title', 'Мои бронирования — NORDIC HUTS')

@section('content')

<div class="py-10 md:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        
        <!-- Breadcrumbs & Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-border-subtle pb-6">
            <div class="space-y-1">
                <nav class="flex items-center gap-2 text-xs text-text-muted">
                    <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Главная</a>
                    <span>/</span>
                    <span class="text-text-main font-medium">Личный кабинет гостя</span>
                </nav>
                <h1 class="font-serif text-3xl sm:text-4xl text-primary font-normal">
                    Мои бронирования
                </h1>
                <p class="text-xs text-text-muted">
                    История ваших поездок и управление активными заявками на проживание
                </p>
            </div>

            <!-- Profile Badge -->
            <div class="flex items-center gap-3 bg-surface p-3 rounded-xl border border-border-subtle shadow-xs">
                <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="text-xs">
                    <div class="font-semibold text-text-main">{{ auth()->user()->name }}</div>
                    <div class="text-text-muted">{{ auth()->user()->email }}</div>
                </div>
            </div>
        </div>

        <!-- BOOKINGS LIST -->
        @if($bookings->count() > 0)
            <div class="space-y-6">
                @foreach($bookings as $booking)
                    @php
                        $status = $booking->status_info;
                    @endphp
                    <div class="bg-surface rounded-2xl border border-border-subtle shadow-elevation-1 overflow-hidden transition-all duration-200 hover:shadow-elevation-2">
                        <div class="p-6 sm:p-8 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                            
                            <!-- Left: House preview & Main details -->
                            <div class="flex flex-col sm:flex-row gap-6 items-start sm:items-center flex-1">
                                <!-- House Thumbnail -->
                                <div class="w-full sm:w-44 h-36 rounded-xl overflow-hidden bg-stone-100 shrink-0 border border-border-subtle">
                                    <img src="{{ $booking->house->image_url }}" 
                                         alt="{{ $booking->house->name }}" 
                                         class="w-full h-full object-cover">
                                </div>

                                <!-- House info -->
                                <div class="space-y-2 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-linen text-primary border border-border-subtle">
                                            {{ $booking->house->category->name }}
                                        </span>
                                        <span class="text-xs text-text-light font-mono tabular-nums">
                                            Бронь #{{ $booking->id }}
                                        </span>
                                    </div>

                                    <h3 class="font-serif text-xl font-semibold text-text-main hover:text-primary transition-colors">
                                        <a href="{{ route('houses.show', $booking->house->slug) }}">
                                            {{ $booking->house->name }}
                                        </a>
                                    </h3>

                                    <!-- Dates & Duration -->
                                    <div class="flex flex-wrap items-center gap-4 text-xs text-text-muted pt-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-primary text-sm" aria-hidden="true">calendar_month</span>
                                            <span>
                                                <strong class="text-text-main font-mono tabular-nums">{{ $booking->check_in_date->format('d.m.Y') }}</strong>
                                                &rarr;
                                                <strong class="text-text-main font-mono tabular-nums">{{ $booking->check_out_date->format('d.m.Y') }}</strong>
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-primary text-sm" aria-hidden="true">bedtime</span>
                                            <span>{{ $booking->nights_count }} {{ $booking->nights_count === 1 ? 'ночь' : ($booking->nights_count < 5 ? 'ночи' : 'ночей') }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-primary text-sm" aria-hidden="true">group</span>
                                            <span>{{ $booking->guests_count }} {{ $booking->guests_count === 1 ? 'гость' : ($booking->guests_count < 5 ? 'гостя' : 'гостей') }}</span>
                                        </div>
                                    </div>

                                    @if($booking->comment)
                                        <p class="text-xs text-text-muted bg-linen-light p-2.5 rounded-lg border border-border-subtle/50 mt-2 italic">
                                            «{{ $booking->comment }}»
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Right: Status, Price & Cancel Action -->
                            <div class="lg:w-64 flex flex-row lg:flex-col items-center lg:items-end justify-between border-t lg:border-t-0 border-border-subtle/60 pt-4 lg:pt-0 gap-4">
                                <!-- Status Badge -->
                                <div>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $status['bg'] }} {{ $status['text'] }} border">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current" aria-hidden="true"></span>
                                        <span>{{ $status['label'] }}</span>
                                    </span>
                                </div>

                                <!-- Total Price -->
                                <div class="text-right">
                                    <span class="block text-[11px] text-text-muted">Итоговая стоимость</span>
                                    <span class="font-serif text-2xl font-bold text-primary font-mono tabular-nums">
                                        {{ number_format($booking->total_price, 0, '', ' ') }}&nbsp;₽
                                    </span>
                                </div>

                                <!-- Cancel Button (Only for pending bookings) -->
                                @if($booking->status === 'pending')
                                    <form method="POST" 
                                          action="{{ route('client.bookings.cancel', $booking) }}" 
                                          onsubmit="return confirm('Вы действительно хотите отменить заявку на бронирование домика «{{ $booking->house->name }}»?');">
                                        @csrf
                                        <button type="submit" 
                                                class="px-3.5 py-1.5 text-xs font-semibold text-secondary hover:text-secondary-dark border border-secondary/30 hover:border-secondary rounded transition-colors"
                                                aria-label="Отменить бронирование #{{ $booking->id }}">
                                            Отменить бронь
                                        </button>
                                    </form>
                                @endif
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- EMPTY STATE -->
            <div class="bg-surface border border-border-subtle rounded-2xl p-12 text-center space-y-4 max-w-lg mx-auto shadow-elevation-1">
                <div class="w-16 h-16 rounded-full bg-linen flex items-center justify-center text-primary mx-auto" aria-hidden="true">
                    <span class="material-symbols-outlined text-3xl">book_online</span>
                </div>
                <div class="space-y-1">
                    <h3 class="font-serif text-xl font-semibold text-text-main">У вас пока нет бронирований</h3>
                    <p class="text-xs text-text-muted leading-relaxed">
                        Выберите уютный A-frame домик или купольную сферу в нашем каталоге, чтобы провести незабываемый уикенд в карельском лесу.
                    </p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('catalog') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-dark text-white rounded text-xs font-semibold transition-colors shadow-xs">
                        <span class="material-symbols-outlined text-sm" aria-hidden="true">search</span>
                        <span>Выбрать домик в каталоге</span>
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>

@endsection
