@extends('layouts.app')

@section('title', $house->name . ' — NORDIC HUTS')

@section('content')

<div class="py-10 md:py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs text-text-muted">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Главная</a>
            <span>/</span>
            <a href="{{ route('catalog') }}" class="hover:text-primary transition-colors">Каталог домиков</a>
            <span>/</span>
            <span class="text-text-main font-medium truncate">{{ $house->name }}</span>
        </nav>

        <!-- MAIN GRID: Left Details (7 cols) + Right Sticky Booking Widget (5 cols) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            
            <!-- LEFT COLUMN: Gallery, Specs, Description, Amenities, Reviews -->
            <div class="lg:col-span-7 space-y-10">
                
                <!-- Main Gallery / Photo -->
                <div class="space-y-4">
                    <div class="relative rounded-2xl overflow-hidden shadow-elevation-2 border border-border-subtle h-[380px] sm:h-[460px] bg-stone-100">
                        <img src="{{ $house->image_url }}" 
                             alt="{{ $house->name }}" 
                             class="w-full h-full object-cover">
                        
                        <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-xs px-3 py-1 rounded text-xs font-semibold text-primary shadow-xs">
                            {{ $house->category->name }}
                        </div>

                        @if($house->is_featured)
                            <div class="absolute top-4 right-4 bg-secondary text-white px-3 py-1 rounded text-xs font-bold tracking-wide shadow-xs">
                                Хит сезона
                            </div>
                        @endif
                    </div>

                    <!-- Mini Gallery Thumbnails (Atmospheric Details) -->
                    <div class="grid grid-cols-3 gap-3">
                        <div class="h-24 sm:h-28 rounded-lg overflow-hidden border border-border-subtle">
                            <img src="{{ asset('images/gallery/interior.jpg') }}" alt="Интерьер" class="w-full h-full object-cover">
                        </div>
                        <div class="h-24 sm:h-28 rounded-lg overflow-hidden border border-border-subtle">
                            <img src="{{ asset('images/gallery/terrace.jpg') }}" alt="Терраса" class="w-full h-full object-cover">
                        </div>
                        <div class="h-24 sm:h-28 rounded-lg overflow-hidden border border-border-subtle">
                            <img src="{{ asset('images/gallery/forest.jpg') }}" alt="Вид на природу" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>

                <!-- House Title & Core Specs -->
                <div class="space-y-4 border-b border-border-subtle pb-8">
                    <h1 class="font-serif text-3xl sm:text-4xl text-primary font-normal">
                        {{ $house->name }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-6 text-sm text-text-muted">
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-primary">group</span>
                            <span>Вместимость: <strong>до {{ $house->capacity }} чел.</strong></span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-primary">square_foot</span>
                            <span>Площадь: <strong>{{ $house->area }} м²</strong></span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-primary">bed</span>
                            <span>Кровать: <strong>King-Size</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-4 border-b border-border-subtle pb-8">
                    <h2 class="font-serif text-xl font-semibold text-text-main">Об этом домике</h2>
                    <p class="text-sm text-text-muted leading-relaxed whitespace-pre-line">
                        {{ $house->description }}
                    </p>
                </div>

                <!-- Amenities Checklist -->
                <div class="space-y-4 border-b border-border-subtle pb-8">
                    <h2 class="font-serif text-xl font-semibold text-text-main">Все удобства и оснащение</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($house->amenities as $amenity)
                            <div class="flex items-center gap-3 p-3 rounded-lg bg-surface border border-border-subtle">
                                <span class="material-symbols-outlined text-primary text-xl">check_circle</span>
                                <span class="text-xs font-medium text-text-main">{{ $amenity->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Reviews for this house -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="font-serif text-xl font-semibold text-text-main">Отзывы гостей</h2>
                        <span class="text-xs text-text-muted">{{ $house->reviews->count() }} отзыва</span>
                    </div>

                    <div class="space-y-4">
                        @forelse($house->reviews as $review)
                            <div class="bg-surface p-5 rounded-xl border border-border-subtle space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-semibold text-text-main">{{ $review->user->name ?? 'Гость' }}</span>
                                    <div class="flex text-secondary">
                                        @for($i = 0; $i < $review->rating; $i++)
                                            <span class="material-symbols-outlined text-xs" style="font-variation-settings: 'FILL' 1;">star</span>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-xs text-text-muted leading-relaxed italic">
                                    «{{ $review->content }}»
                                </p>
                            </div>
                        @empty
                            <p class="text-xs text-text-muted">Пока нет отзывов для этого домика. Оставьте отзыв первым после проживания!</p>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Sticky Booking Calculation Widget -->
            <div class="lg:col-span-5 sticky top-28" 
                 x-data="{
                    pricePerNight: {{ (float) $house->price_per_night }},
                    capacity: {{ (int) $house->capacity }},
                    checkIn: '{{ date('Y-m-d', strtotime('+1 day')) }}',
                    checkOut: '{{ date('Y-m-d', strtotime('+3 days')) }}',
                    guests: 2,
                    get nights() {
                        if (!this.checkIn || !this.checkOut) return 0;
                        const d1 = new Date(this.checkIn);
                        const d2 = new Date(this.checkOut);
                        const diffTime = d2 - d1;
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                        return diffDays > 0 ? diffDays : 0;
                    },
                    get totalPrice() {
                        return this.nights * this.pricePerNight;
                    },
                    formatPrice(val) {
                        return new Intl.NumberFormat('ru-RU').format(val);
                    }
                 }">
                
                <div class="bg-surface border border-border-subtle rounded-2xl p-6 sm:p-8 shadow-elevation-2 space-y-6">
                    
                    <!-- Header Price per night -->
                    <div class="flex items-baseline justify-between border-b border-border-subtle pb-5">
                        <div>
                            <span class="text-xs text-text-muted block">Стоимость проживания</span>
                            <span class="font-serif text-2xl sm:text-3xl font-bold text-primary">
                                {{ number_format($house->price_per_night, 0, '', ' ') }} ₽
                            </span>
                            <span class="text-xs text-text-muted">/ ночь</span>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center gap-1 text-xs text-secondary font-semibold">
                                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                                <span>4.98</span>
                            </span>
                        </div>
                    </div>

                    <!-- Booking Form -->
                    <form method="POST" action="{{ route('bookings.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="house_id" value="{{ $house->id }}">

                        <!-- Date range picker -->
                        <div class="grid grid-cols-2 gap-2">
                            <div class="space-y-1">
                                <label class="block text-xs font-semibold text-text-muted">Заезд (с 15:00)</label>
                                <input type="date" 
                                       name="check_in_date" 
                                       x-model="checkIn"
                                       min="{{ date('Y-m-d') }}"
                                       required
                                       class="w-full text-xs bg-background border border-border-subtle rounded px-2.5 py-2 text-text-main focus:border-primary focus:ring-0">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-semibold text-text-muted">Выезд (до 12:00)</label>
                                <input type="date" 
                                       name="check_out_date" 
                                       x-model="checkOut"
                                       :min="checkIn"
                                       required
                                       class="w-full text-xs bg-background border border-border-subtle rounded px-2.5 py-2 text-text-main focus:border-primary focus:ring-0">
                            </div>
                        </div>

                        <!-- Guests selector -->
                        <div class="space-y-1">
                            <label class="block text-xs font-semibold text-text-muted">Количество гостей</label>
                            <select name="guests_count" 
                                    x-model="guests" 
                                    required
                                    class="w-full text-xs bg-background border border-border-subtle rounded px-2.5 py-2 text-text-main focus:border-primary focus:ring-0">
                                @for($i = 1; $i <= $house->capacity; $i++)
                                    <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'гость' : ($i <= 4 ? 'гостя' : 'гостей') }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Contact details -->
                        <div class="space-y-1">
                            <label class="block text-xs font-semibold text-text-muted">Ваше имя</label>
                            <input type="text" 
                                   name="guest_name" 
                                   value="{{ auth()->check() ? auth()->user()->name : old('guest_name') }}" 
                                   placeholder="Иван Иванов" 
                                   required
                                   class="w-full text-xs bg-background border border-border-subtle rounded px-2.5 py-2 text-text-main focus:border-primary focus:ring-0">
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-semibold text-text-muted">Телефон для связи</label>
                            <input type="text" 
                                   name="guest_phone" 
                                   value="{{ auth()->check() ? auth()->user()->phone : old('guest_phone') }}" 
                                   placeholder="+7 (999) 000-00-00" 
                                   required
                                   class="w-full text-xs bg-background border border-border-subtle rounded px-2.5 py-2 text-text-main focus:border-primary focus:ring-0">
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-semibold text-text-muted">Пожелания (опционально)</label>
                            <textarea name="comment" 
                                      rows="2" 
                                      placeholder="Например, растопить чан к заезду или подготовить детскую кроватку"
                                      class="w-full text-xs bg-background border border-border-subtle rounded px-2.5 py-2 text-text-main focus:border-primary focus:ring-0">{{ old('comment') }}</textarea>
                        </div>

                        <!-- Dynamic Price Breakdown Box -->
                        <div class="bg-linen p-4 rounded-xl space-y-2 border border-border-subtle/80 text-xs">
                            <div class="flex justify-between text-text-muted">
                                <span>Расчет стоимости:</span>
                                <span x-text="nights + ' ' + (nights === 1 ? 'ночь' : (nights < 5 ? 'ночи' : 'ночей'))"></span>
                            </div>
                            <div class="flex justify-between text-text-muted">
                                <span>{{ number_format($house->price_per_night, 0, '', ' ') }} ₽ × <span x-text="nights"></span></span>
                                <span x-text="formatPrice(totalPrice) + ' ₽'"></span>
                            </div>
                            <div class="pt-2 border-t border-border-subtle/50 flex justify-between font-bold text-sm text-primary">
                                <span>Итого к оплате:</span>
                                <span x-text="formatPrice(totalPrice) + ' ₽'"></span>
                            </div>
                        </div>

                        <!-- Action Submit or Auth Prompt -->
                        @auth
                            <button type="submit" 
                                    id="btn-book-now"
                                    :disabled="nights <= 0"
                                    class="w-full py-3.5 bg-primary hover:bg-primary-dark disabled:opacity-50 text-white rounded text-sm font-semibold transition-colors shadow-sm">
                                Забронировать отдых
                            </button>
                        @else
                            <div class="space-y-2">
                                <button type="submit" 
                                        id="btn-book-now"
                                        :disabled="nights <= 0"
                                        class="w-full py-3.5 bg-primary hover:bg-primary-dark disabled:opacity-50 text-white rounded text-sm font-semibold transition-colors shadow-sm">
                                    Оформить бронирование
                                </button>
                                <p class="text-[11px] text-text-muted text-center">
                                    Для бронирования требуется авторизация. Вы можете <a href="{{ route('quick-login', 'client') }}" class="text-primary font-semibold underline">войти как Клиент (Анна) в 1 клик</a> прямо сейчас.
                                </p>
                            </div>
                        @endauth
                    </form>

                    <!-- Trust indicators -->
                    <div class="pt-4 border-t border-border-subtle text-[11px] text-text-muted space-y-1.5">
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-xs text-primary">credit_card_off</span>
                            <span>Оплата при заселении. Без списания с карты.</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-xs text-primary">cancel</span>
                            <span>Бесплатная отмена за 3 дня до заезда.</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- RELATED HOUSES SECTION -->
        @if($relatedHouses->count() > 0)
            <div class="pt-16 border-t border-border-subtle space-y-8">
                <div class="flex items-end justify-between">
                    <div>
                        <span class="text-xs font-bold text-secondary uppercase tracking-widest">Альтернативные варианты</span>
                        <h2 class="font-serif text-2xl text-primary font-normal mt-1">
                            Похожие домики в категории «{{ $house->category->name }}»
                        </h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @foreach($relatedHouses as $related)
                        <div class="bg-surface rounded-xl border border-border-subtle shadow-elevation-1 overflow-hidden flex flex-col sm:flex-row group hover:shadow-elevation-2 transition-all">
                            <div class="sm:w-1/2 h-52 sm:h-auto overflow-hidden">
                                <img src="{{ $related->image_url }}" alt="{{ $related->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                            <div class="sm:w-1/2 p-5 flex flex-col justify-between space-y-3">
                                <div>
                                    <h3 class="font-serif text-lg font-semibold text-text-main group-hover:text-primary transition-colors">
                                        <a href="{{ route('houses.show', $related->slug) }}">{{ $related->name }}</a>
                                    </h3>
                                    <p class="text-xs text-text-muted mt-1 line-clamp-2">{{ $related->description }}</p>
                                </div>
                                <div class="flex items-center justify-between border-t border-border-subtle/50 pt-3">
                                    <span class="text-xs font-semibold text-primary">от {{ number_format($related->price_per_night, 0, '', ' ') }} ₽</span>
                                    <a href="{{ route('houses.show', $related->slug) }}" class="text-xs font-semibold text-primary hover:text-secondary">Смотреть &rarr;</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>

@endsection
