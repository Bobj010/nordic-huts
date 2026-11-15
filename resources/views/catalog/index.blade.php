@extends('layouts.app')

@section('title', 'Каталог домиков — NORDIC HUTS')

@section('content')

<div class="py-10 md:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        
        <!-- Page Title & Breadcrumb -->
        <div class="space-y-2">
            <nav class="flex items-center gap-2 text-xs text-text-muted">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Главная</a>
                <span>/</span>
                <span class="text-text-main font-medium">Каталог домиков</span>
            </nav>
            <h1 class="font-serif text-3xl sm:text-5xl text-primary font-normal">
                Коллекция уединенных домиков
            </h1>
            <p class="text-sm text-text-muted max-w-2xl">
                Выберите идеальное пространство для отдыха в тишине карельского леса. Все домики оборудованы персональной видовой террасой и автономным отоплением.
            </p>
        </div>

        <!-- FILTER & SORT BAR (GET-form) -->
        <div class="bg-surface border border-border-subtle rounded-xl p-5 shadow-elevation-1">
            <form method="GET" action="{{ route('catalog') }}" class="space-y-4">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- 1. Category Filter -->
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-text-muted">Категория</label>
                        <select name="category" class="w-full text-xs font-medium bg-background border border-border-subtle rounded-lg px-3 py-2.5 text-text-main focus:border-primary focus:ring-0">
                            <option value="">Все категории</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (string)$selectedCategory === (string)$category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2. Capacity (Guests) -->
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-text-muted">Количество гостей</label>
                        <select name="capacity" class="w-full text-xs font-medium bg-background border border-border-subtle rounded-lg px-3 py-2.5 text-text-main focus:border-primary focus:ring-0">
                            <option value="">Любая вместимость</option>
                            <option value="2" {{ (string)$selectedCapacity === '2' ? 'selected' : '' }}>От 2 гостей</option>
                            <option value="4" {{ (string)$selectedCapacity === '4' ? 'selected' : '' }}>От 4 гостей</option>
                            <option value="6" {{ (string)$selectedCapacity === '6' ? 'selected' : '' }}>От 6 гостей</option>
                        </select>
                    </div>

                    <!-- 3. Key Amenity -->
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-text-muted">Ключевое удобство</label>
                        <select name="amenity" class="w-full text-xs font-medium bg-background border border-border-subtle rounded-lg px-3 py-2.5 text-text-main focus:border-primary focus:ring-0">
                            <option value="">Любые удобства</option>
                            @foreach($amenities as $amenity)
                                <option value="{{ $amenity->id }}" {{ (string)$selectedAmenity === (string)$amenity->id ? 'selected' : '' }}>
                                    {{ $amenity->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 4. Sorting -->
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-text-muted">Сортировка</label>
                        <select name="sort" class="w-full text-xs font-medium bg-background border border-border-subtle rounded-lg px-3 py-2.5 text-text-main focus:border-primary focus:ring-0">
                            <option value="" {{ empty($selectedSort) ? 'selected' : '' }}>Сначала популярные</option>
                            <option value="price_asc" {{ $selectedSort === 'price_asc' ? 'selected' : '' }}>Сначала дешевле</option>
                            <option value="price_desc" {{ $selectedSort === 'price_desc' ? 'selected' : '' }}>Сначала дороже</option>
                            <option value="capacity" {{ $selectedSort === 'capacity' ? 'selected' : '' }}>По вместимости</option>
                        </select>
                    </div>

                </div>

                <!-- Action Buttons: Apply & Reset -->
                <div class="flex items-center justify-between pt-2 border-t border-border-subtle/50 text-xs">
                    <div class="text-text-muted">
                        Найдено домиков: <strong class="text-text-main font-semibold">{{ $houses->total() }}</strong>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($selectedCategory || $selectedCapacity || $selectedAmenity || $selectedSort)
                            <a href="{{ route('catalog') }}" class="px-3 py-1.5 text-text-muted hover:text-secondary font-medium transition-colors">
                                Сбросить фильтры
                            </a>
                        @endif
                        <button type="submit" class="px-5 py-2 bg-primary hover:bg-primary-dark text-white rounded text-xs font-semibold transition-colors">
                            Применить
                        </button>
                    </div>
                </div>

            </form>
        </div>

        <!-- HOUSES GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($houses as $house)
                <div class="bg-surface rounded-xl border border-border-subtle shadow-elevation-1 overflow-hidden flex flex-col justify-between group hover:shadow-elevation-2 transition-all duration-300">
                    <div>
                        <!-- Cabin Image with Badges -->
                        <div class="relative h-64 overflow-hidden bg-stone-100">
                            <img src="{{ $house->image_url }}" 
                                 alt="{{ $house->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            
                            <!-- Category Badge -->
                            <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-xs px-2.5 py-1 rounded text-[11px] font-semibold text-primary shadow-xs">
                                {{ $house->category->name }}
                            </div>

                            <!-- Featured Terracotta Pill -->
                            @if($house->is_featured)
                                <div class="absolute top-3 right-3 bg-secondary text-white px-2.5 py-1 rounded text-[11px] font-bold tracking-wide shadow-xs">
                                    Хит сезона
                                </div>
                            @endif
                        </div>

                        <!-- Content Details -->
                        <div class="p-6 space-y-4">
                            <div>
                                <h2 class="font-serif text-xl font-semibold text-text-main group-hover:text-primary transition-colors">
                                    <a href="{{ route('houses.show', $house->slug) }}">
                                        {{ $house->name }}
                                    </a>
                                </h2>
                                <p class="text-xs text-text-muted mt-1.5 line-clamp-2 leading-relaxed">
                                    {{ $house->description }}
                                </p>
                            </div>

                            <!-- Specs (capacity, area) -->
                            <div class="flex items-center gap-4 text-xs text-text-muted pt-1">
                                <div class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm text-text-light">group</span>
                                    <span>Вместимость: до {{ $house->capacity }} чел.</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm text-text-light">square_foot</span>
                                    <span>{{ $house->area }} м²</span>
                                </div>
                            </div>

                            <!-- Amenities Grid -->
                            <div class="grid grid-cols-2 gap-2 pt-3 border-t border-border-subtle/60 text-xs text-text-muted">
                                @foreach($house->amenities->take(4) as $amenity)
                                    <div class="flex items-center gap-1.5 truncate">
                                        <span class="material-symbols-outlined text-xs text-primary/70">check</span>
                                        <span class="truncate">{{ $amenity->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Price & CTA Button -->
                    <div class="p-6 pt-0 mt-2 flex items-center justify-between border-t border-border-subtle/50 pt-4">
                        <div>
                            <span class="block text-[11px] text-text-muted">Цена за сутки</span>
                            <span class="font-serif text-lg font-semibold text-primary">
                                {{ number_format($house->price_per_night, 0, '', ' ') }} ₽
                            </span>
                        </div>
                        <a href="{{ route('houses.show', $house->slug) }}" 
                           class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded text-xs font-semibold transition-colors">
                            Подробнее и бронь
                        </a>
                    </div>
                </div>
            @empty
                <!-- EMPTY STATE -->
                <div class="col-span-full bg-surface border border-border-subtle rounded-2xl p-12 text-center space-y-4">
                    <div class="w-16 h-16 rounded-full bg-linen flex items-center justify-center text-primary mx-auto">
                        <span class="material-symbols-outlined text-3xl">cabin</span>
                    </div>
                    <div class="max-w-md mx-auto space-y-1">
                        <h3 class="font-serif text-xl font-semibold text-text-main">Домики не найдены</h3>
                        <p class="text-xs text-text-muted leading-relaxed">
                            По выбранным критериям поиска сейчас нет свободных домиков. Попробуйте смягчить параметры фильтра или сбросить их.
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('catalog') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary text-white rounded text-xs font-semibold hover:bg-primary-dark transition-colors">
                            <span class="material-symbols-outlined text-sm">refresh</span>
                            <span>Сбросить все фильтры</span>
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- PAGINATION -->
        @if($houses->hasPages())
            <div class="pt-6 flex justify-center">
                {{ $houses->links() }}
            </div>
        @endif

    </div>
</div>

@endsection
