@extends('layouts.app')

@section('title', 'NORDIC HUTS — Отдых наедине с тишиной карельского леса')

@section('content')

<!-- HERO SECTION -->
<section class="relative pt-6 pb-24 md:pb-32 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Hero Visual Banner Container -->
        <div class="relative w-full rounded-2xl overflow-hidden min-h-[580px] flex flex-col justify-end p-6 sm:p-10 md:p-14 border border-border-subtle shadow-elevation-2">
            
            <!-- Atmospheric Karelian Forest Background Image -->
            <img class="absolute inset-0 w-full h-full object-cover" 
                 src="{{ asset('images/hero.jpg') }}" 
                 alt="Скандинавский A-frame домик в вечернем сосновом лесу Карелии">
            
            <!-- Tonal Earth Gradient (Strictly natural pine, no neon) -->
            <div class="absolute inset-0 bg-gradient-to-t from-stone-950/95 via-stone-900/60 to-stone-900/10"></div>
            
            <!-- Hero Text Content -->
            <div class="relative z-10 max-w-3xl space-y-4 text-white">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-white/15 backdrop-blur-sm border border-white/20 text-white text-xs font-semibold uppercase tracking-wider">
                    <span class="material-symbols-outlined text-secondary text-sm" style="font-variation-settings: 'FILL' 1;">spa</span>
                    <span>Северный эко-ретрит в Ладожских шхерах</span>
                </div>
                
                <h1 class="font-serif text-3xl sm:text-5xl md:text-6xl text-white font-normal leading-tight tracking-tight">
                    Отдых наедине с тишиной карельского леса
                </h1>
                
                <p class="text-base sm:text-lg text-stone-200/90 max-w-2xl font-light leading-relaxed">
                    Уединенные дизайнерские домики и купольные сферы среди вековых сосен. Индивидуальный подогреваемый банный чан, дровяной камин и бескрайнее звездное небо.
                </p>
                
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <a href="{{ route('catalog') }}" 
                       class="inline-flex items-center justify-center px-7 py-3.5 bg-primary hover:bg-primary-dark text-white rounded text-sm font-semibold transition-all duration-150 shadow-md">
                        Выбрать домик
                    </a>
                    <a href="#about" 
                       class="inline-flex items-center justify-center px-6 py-3.5 bg-white/90 hover:bg-white text-text-main rounded text-sm font-semibold transition-all duration-150">
                        О комплексе
                    </a>
                </div>
            </div>
        </div>

        <!-- QUICK SEARCH / BOOKING WIDGET (Overlapping Hero bottom) -->
        <div class="relative -mt-10 sm:-mt-12 z-20 max-w-5xl mx-auto bg-surface border border-border-subtle rounded-xl p-4 sm:p-6 shadow-elevation-2">
            <form method="GET" action="{{ route('catalog') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                
                <!-- Category Select -->
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-text-muted">Тип домика</label>
                    <div class="flex items-center border border-border-subtle rounded px-3 py-2.5 bg-background hover:border-primary focus-within:border-primary transition-colors">
                        <span class="material-symbols-outlined text-text-muted mr-2 text-lg">cabin</span>
                        <select name="category" class="w-full text-xs font-medium text-text-main bg-transparent border-0 p-0 focus:ring-0">
                            <option value="">Все категории</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Guests Count -->
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-text-muted">Количество гостей</label>
                    <div class="flex items-center border border-border-subtle rounded px-3 py-2.5 bg-background hover:border-primary focus-within:border-primary transition-colors">
                        <span class="material-symbols-outlined text-text-muted mr-2 text-lg">group</span>
                        <select name="capacity" class="w-full text-xs font-medium text-text-main bg-transparent border-0 p-0 focus:ring-0">
                            <option value="">Любое число</option>
                            <option value="2">2 гостя (для двоих)</option>
                            <option value="4">4 гостя (семья / друзья)</option>
                            <option value="6">6+ гостей (большая компания)</option>
                        </select>
                    </div>
                </div>

                <!-- Amenity Filter -->
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-text-muted">Ключевое удобство</label>
                    <div class="flex items-center border border-border-subtle rounded px-3 py-2.5 bg-background hover:border-primary focus-within:border-primary transition-colors">
                        <span class="material-symbols-outlined text-text-muted mr-2 text-lg">hot_tub</span>
                        <select name="amenity" class="w-full text-xs font-medium text-text-main bg-transparent border-0 p-0 focus:ring-0">
                            <option value="">Любые удобства</option>
                            @foreach($amenities as $amenity)
                                <option value="{{ $amenity->id }}">{{ $amenity->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                            class="w-full h-[42px] bg-primary hover:bg-primary-dark text-white rounded text-sm font-semibold transition-colors flex items-center justify-center gap-2 shadow-xs">
                        <span class="material-symbols-outlined text-lg">search</span>
                        <span>Показать домики</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</section>

<!-- 4 KEY FEATURES & USP -->
<section class="py-16 bg-linen border-y border-border-subtle/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="max-w-2xl">
            <span class="text-xs font-bold text-secondary uppercase tracking-widest">Философия пространства</span>
            <h2 class="font-serif text-2xl sm:text-3xl text-primary font-normal mt-1">
                Истинная тишина и контакт с северной природой
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Card 1 -->
            <div class="bg-surface p-6 rounded-xl border border-border-subtle shadow-elevation-1 flex flex-col justify-between hover:-translate-y-1 transition-transform duration-200">
                <div class="space-y-3">
                    <div class="w-12 h-12 rounded-lg bg-linen flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-2xl">nature_people</span>
                    </div>
                    <h3 class="font-serif text-lg text-text-main font-semibold">100% приватность</h3>
                    <p class="text-xs text-text-muted leading-relaxed">
                        Домики расположены на расстоянии не менее 50 метров друг от друга среди вековых сосен. Никаких заборов и шумных соседей.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-border-subtle/40 flex items-center gap-1.5 text-[11px] text-text-muted">
                    <span class="material-symbols-outlined text-xs text-primary">check_circle</span>
                    <span>Только лес и пение птиц</span>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bg-surface p-6 rounded-xl border border-border-subtle shadow-elevation-1 flex flex-col justify-between hover:-translate-y-1 transition-transform duration-200">
                <div class="space-y-3">
                    <div class="w-12 h-12 rounded-lg bg-linen flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined text-2xl">hot_tub</span>
                    </div>
                    <h3 class="font-serif text-lg text-text-main font-semibold">Банный чан на дровах</h3>
                    <p class="text-xs text-text-muted leading-relaxed">
                        Горячая купель с хвойными ветками пихты и таежными травами прямо на персональной террасе вашего домика.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-border-subtle/40 flex items-center gap-1.5 text-[11px] text-text-muted">
                    <span class="material-symbols-outlined text-xs text-secondary">check_circle</span>
                    <span>Растапливается к вашему заезду</span>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="bg-surface p-6 rounded-xl border border-border-subtle shadow-elevation-1 flex flex-col justify-between hover:-translate-y-1 transition-transform duration-200">
                <div class="space-y-3">
                    <div class="w-12 h-12 rounded-lg bg-linen flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-2xl">mode_night</span>
                    </div>
                    <h3 class="font-serif text-lg text-text-main font-semibold">Панорама звезд</h3>
                    <p class="text-xs text-text-muted leading-relaxed">
                        Панорамное остекление в пол и мансардные окна над кроватью для наблюдения за северным сиянием и метеоритными дождями.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-border-subtle/40 flex items-center gap-1.5 text-[11px] text-text-muted">
                    <span class="material-symbols-outlined text-xs text-primary">check_circle</span>
                    <span>Телескоп в купольных сферах</span>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="bg-surface p-6 rounded-xl border border-border-subtle shadow-elevation-1 flex flex-col justify-between hover:-translate-y-1 transition-transform duration-200">
                <div class="space-y-3">
                    <div class="w-12 h-12 rounded-lg bg-linen flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-2xl">coffee</span>
                    </div>
                    <h3 class="font-serif text-lg text-text-main font-semibold">Утренний кофе</h3>
                    <p class="text-xs text-text-muted leading-relaxed">
                        Завтраки из локальных фермерских продуктов Карелии с бережной доставкой к порогу вашего домика в плетеной корзине.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-border-subtle/40 flex items-center gap-1.5 text-[11px] text-text-muted">
                    <span class="material-symbols-outlined text-xs text-primary">check_circle</span>
                    <span>Карельские калитки и ягоды</span>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- FEATURED CABINS SHOWCASE -->
<section class="py-20" id="catalog-preview">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <span class="text-xs font-bold text-secondary uppercase tracking-widest">Пространства для отдыха</span>
                <h2 class="font-serif text-2xl sm:text-4xl text-primary font-normal mt-1">
                    Популярные домики
                </h2>
            </div>
            <a href="{{ route('catalog') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary hover:text-secondary transition-colors group">
                <span>Смотреть весь каталог ({{ \App\Models\House::active()->count() }} домиков)</span>
                <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </a>
        </div>

        <!-- Cabins Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($houses as $house)
                <div class="bg-surface rounded-xl border border-border-subtle shadow-elevation-1 overflow-hidden flex flex-col justify-between group hover:shadow-elevation-2 transition-all duration-300">
                    <div>
                        <!-- Cabin Image with Badges -->
                        <div class="relative h-64 overflow-hidden bg-stone-100">
                            <img src="{{ $house->image_url }}" 
                                 alt="{{ $house->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            
                            <!-- Category Pill -->
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

                        <!-- Content Area -->
                        <div class="p-6 space-y-4">
                            <div>
                                <h3 class="font-serif text-xl font-semibold text-text-main group-hover:text-primary transition-colors">
                                    <a href="{{ route('houses.show', $house->slug) }}">
                                        {{ $house->name }}
                                    </a>
                                </h3>
                                <p class="text-xs text-text-muted mt-1.5 line-clamp-2 leading-relaxed">
                                    {{ $house->description }}
                                </p>
                            </div>

                            <!-- Spec tags (capacity, area) -->
                            <div class="flex items-center gap-4 text-xs text-text-muted pt-1">
                                <div class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm text-text-light">group</span>
                                    <span>До {{ $house->capacity }} гостей</span>
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

                    <!-- Bottom Pricing & Action -->
                    <div class="p-6 pt-0 mt-2 flex items-center justify-between border-t border-border-subtle/50 pt-4">
                        <div>
                            <span class="block text-[11px] text-text-muted">Стоимость</span>
                            <span class="font-serif text-lg font-semibold text-primary">
                                от {{ number_format($house->price_per_night, 0, '', ' ') }} ₽ 
                                <span class="font-sans text-xs font-normal text-text-muted">/ ночь</span>
                            </span>
                        </div>
                        <a href="{{ route('houses.show', $house->slug) }}" 
                           class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded text-xs font-semibold transition-colors">
                            Подробнее
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>

<!-- ABOUT & NATURE MANIFESTO SECTION -->
<section class="py-20 bg-linen border-y border-border-subtle/80" id="about">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            
            <div class="space-y-6">
                <div>
                    <span class="text-xs font-bold text-secondary uppercase tracking-widest">Экологический манифест</span>
                    <h2 class="font-serif text-2xl sm:text-4xl text-primary font-normal mt-1 leading-snug">
                        Архитектура, растворенная в карельском пейзаже
                    </h2>
                </div>
                
                <p class="text-sm text-text-muted leading-relaxed">
                    Мы возвели эко-комплекс NORDIC HUTS на берегу Ладожских шхер без вырубки деревьев. Каркасы домов изготовлены из местной сухостойной сосны, а внутренняя отделка выполнена с использованием льна, термодерева и натурального камня.
                </p>

                <div class="space-y-4 pt-2">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-lg bg-surface flex items-center justify-center text-primary shrink-0 shadow-xs">
                            <span class="material-symbols-outlined text-xl">energy_savings_leaf</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-text-main">Бережное энергопотребление</h4>
                            <p class="text-xs text-text-muted mt-0.5">Геотермальное отопление и нулевой углеродный след при заготовке березовых дров.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-lg bg-surface flex items-center justify-center text-primary shrink-0 shadow-xs">
                            <span class="material-symbols-outlined text-xl">water_drop</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-text-main">Кристальная родниковая вода</h4>
                            <p class="text-xs text-text-muted mt-0.5">Собственная артезианская скважина глубиной 120 метров в реликтовом гранитном пласте.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <div class="inline-flex items-center gap-2 text-xs font-semibold text-primary">
                        <span class="material-symbols-outlined text-sm">verified</span>
                        <span>Сертифицированный эко-отель Северо-Запада</span>
                    </div>
                </div>
            </div>

            <!-- Visual Atmosphere Photo -->
            <div class="relative">
                <div class="rounded-2xl overflow-hidden shadow-elevation-2 border border-border-subtle">
                    <img class="w-full h-[460px] object-cover" 
                         src="{{ asset('images/nature.jpg') }}" 
                         alt="Озеро и сосновый лес Карелии">
                </div>
                <div class="absolute -bottom-6 -left-6 bg-surface border border-border-subtle p-5 rounded-xl shadow-elevation-1 max-w-xs hidden sm:block">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-secondary text-3xl">local_florist</span>
                        <div>
                            <p class="font-serif text-xl font-bold text-text-main">32 гектара</p>
                            <p class="text-xs text-text-muted">нетронутого соснового заказника</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- REVIEWS SECTION -->
<section class="py-20" id="reviews">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <span class="text-xs font-bold text-secondary uppercase tracking-widest">Настоящие впечатления</span>
                <h2 class="font-serif text-2xl sm:text-4xl text-primary font-normal mt-1">
                    Отзывы наших гостей
                </h2>
            </div>
            
            <div class="inline-flex items-center gap-3 bg-linen px-4 py-2 rounded-xl border border-border-subtle">
                <div class="flex items-center text-secondary">
                    <span class="material-symbols-outlined text-lg" style="font-variation-settings: 'FILL' 1;">star</span>
                    <span class="ml-1 text-sm font-bold text-text-main">4.98</span>
                </div>
                <span class="text-text-light">&bull;</span>
                <span class="text-xs text-text-muted">340+ проверенных отзывов</span>
            </div>
        </div>

        <!-- Reviews Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @forelse($reviews as $review)
                <div class="bg-surface p-7 rounded-xl border border-border-subtle shadow-elevation-1 flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center gap-1 text-secondary">
                            @for($i = 0; $i < $review->rating; $i++)
                                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                            @endfor
                        </div>
                        <p class="text-sm text-text-main leading-relaxed italic">
                            «{{ $review->content }}»
                        </p>
                    </div>

                    <div class="pt-4 border-t border-border-subtle/50 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-semibold text-text-main">{{ $review->user->name ?? 'Гость' }}</span>
                            <span class="text-text-muted block text-[11px]">Домик: {{ $review->house->name ?? 'Глэмпинг' }}</span>
                        </div>
                        <span class="text-[11px] text-text-light">{{ $review->created_at->format('d.m.Y') }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-2 text-center py-10 bg-surface rounded-xl border border-border-subtle text-text-muted text-sm">
                    Первые отзывы появятся совсем скоро. Будьте первыми, кто насладится отдыхом!
                </div>
            @endforelse
        </div>

    </div>
</section>

@endsection
