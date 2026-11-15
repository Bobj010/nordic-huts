<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Booking;
use App\Models\Category;
use App\Models\House;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Создание пользователей с различными ролями
        $admin = User::create([
            'name' => 'Александр (Управляющий)',
            'email' => 'admin@nordic.local',
            'phone' => '+7 (999) 111-22-33',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $manager = User::create([
            'name' => 'Елена (Менеджер ресепшена)',
            'email' => 'manager@nordic.local',
            'phone' => '+7 (999) 222-33-44',
            'role' => 'manager',
            'password' => Hash::make('password'),
        ]);

        $client1 = User::create([
            'name' => 'Анна Смирнова',
            'email' => 'client@nordic.local',
            'phone' => '+7 (999) 333-44-55',
            'role' => 'client',
            'password' => Hash::make('password'),
        ]);

        $client2 = User::create([
            'name' => 'Михаил Кузнецов',
            'email' => 'mikhail@nordic.local',
            'phone' => '+7 (999) 444-55-66',
            'role' => 'client',
            'password' => Hash::make('password'),
        ]);

        // 2. Создание категорий домиков
        $catAframe = Category::create([
            'name' => 'A-frame Дома',
            'slug' => 'a-frame',
            'description' => 'Культовые треугольные домики-шалаши с панорамным остеклением и видом на реликтовый сосновый бор.',
        ]);

        $catDome = Category::create([
            'name' => 'Купольные сферы',
            'slug' => 'dome',
            'description' => 'Футуристичные геокуполы с прозрачным куполом для наблюдения за северными звездами прямо из постели.',
        ]);

        $catChalet = Category::create([
            'name' => 'Лесные шале',
            'slug' => 'chalet',
            'description' => 'Просторные теплые дома из клееного бруса с настоящим дровяным камином и приватной террасой.',
        ]);

        // 3. Создание справочника удобств
        $amenityBath = Amenity::create(['name' => 'Банный чан на дровах', 'icon' => 'bath']);
        $amenitySauna = Amenity::create(['name' => 'Русская дровяная баня', 'icon' => 'sparkles']);
        $amenityFire = Amenity::create(['name' => 'Камин на дровах', 'icon' => 'fire']);
        $amenityProjector = Amenity::create(['name' => 'Кинопроектор 4K', 'icon' => 'film']);
        $amenityWifi = Amenity::create(['name' => 'Wi-Fi Starlink', 'icon' => 'wifi']);
        $amenityBbq = Amenity::create(['name' => 'Мангальная BBQ-зона', 'icon' => 'flame']);
        $amenityTerrace = Amenity::create(['name' => 'Панорамная терраса', 'icon' => 'sun']);
        $amenityCoffee = Amenity::create(['name' => 'Кофемашина и капсулы', 'icon' => 'coffee']);

        // 4. Создание 6 эстетичных домиков в скандинавском стиле
        $house1 = House::create([
            'category_id' => $catAframe->id,
            'name' => 'A-frame «Хвойный» у озера',
            'slug' => 'a-frame-pine-lake',
            'description' => 'Уединенный треугольный дом прямо на берегу озера. Панорамный фасад обращен на закатную сторону. На террасе установлен персональный подогреваемый чан на дровах. Внутри уютный лофт со спальней на втором ярусе и виниловый проигрыватель.',
            'price_per_night' => 9500.00,
            'capacity' => 2,
            'area' => 42,
            'image_url' => 'images/houses/a-frame-1.jpg',
            'is_featured' => true,
            'is_active' => true,
        ]);
        $house1->amenities()->attach([
            $amenityBath->id, $amenityWifi->id, $amenityTerrace->id, $amenityCoffee->id, $amenityBbq->id
        ]);

        $house2 = House::create([
            'category_id' => $catAframe->id,
            'name' => 'A-frame «Скандинавия» с чаном',
            'slug' => 'a-frame-scandinavia',
            'description' => 'Просторный двухэтажный дом-шалаш в стиле ваби-саби. Две изолированные спальни, теплая терраса, чан из кедра и зона для медитаций среди сосен. Идеально для уикенда с семьей или близкими друзьями.',
            'price_per_night' => 11000.00,
            'capacity' => 4,
            'area' => 55,
            'image_url' => 'images/houses/a-frame-2.jpg',
            'is_featured' => true,
            'is_active' => true,
        ]);
        $house2->amenities()->attach([
            $amenityBath->id, $amenityFire->id, $amenityWifi->id, $amenityTerrace->id, $amenityBbq->id
        ]);

        $house3 = House::create([
            'category_id' => $catDome->id,
            'name' => 'Купольная сфера «Северное сияние»',
            'slug' => 'dome-aurora',
            'description' => 'Уникальная геокупольная сфера с круговой панорамой и прозрачной крышей. Засыпайте под звездами на анатомической кровати размера king-size. В сфере установлена дизайнерская отдельностоящая ванна с панорамным видом на лес.',
            'price_per_night' => 13500.00,
            'capacity' => 2,
            'area' => 38,
            'image_url' => 'images/houses/dome-1.jpg',
            'is_featured' => true,
            'is_active' => true,
        ]);
        $house3->amenities()->attach([
            $amenityProjector->id, $amenityWifi->id, $amenityCoffee->id, $amenityTerrace->id
        ]);

        $house4 = House::create([
            'category_id' => $catDome->id,
            'name' => 'Купольная сфера «Лесной Оазис»',
            'slug' => 'dome-forest-oasis',
            'description' => 'Теплая сфера в глубине соснового бора. Минималистичный интерьер из светлого ясеня, домашний кинотеатр с проектором, теплый пол и уютная терраса с подвесными креслами.',
            'price_per_night' => 12000.00,
            'capacity' => 2,
            'area' => 36,
            'image_url' => 'images/houses/dome-2.jpg',
            'is_featured' => false,
            'is_active' => true,
        ]);
        $house4->amenities()->attach([
            $amenityProjector->id, $amenityWifi->id, $amenityTerrace->id, $amenityBbq->id
        ]);

        $house5 = House::create([
            'category_id' => $catChalet->id,
            'name' => 'Лесное Шале «Медвежий угол»',
            'slug' => 'chalet-bear-corner',
            'description' => 'Премиальное шале из вековых бревен. Огромная гостиная со вторым светом, настоящий дровяной камин, полностью оборудованная кухня и собственная русская баня на территории.',
            'price_per_night' => 16500.00,
            'capacity' => 6,
            'area' => 85,
            'image_url' => 'images/houses/chalet-1.jpg',
            'is_featured' => true,
            'is_active' => true,
        ]);
        $house5->amenities()->attach([
            $amenitySauna->id, $amenityFire->id, $amenityWifi->id, $amenityTerrace->id, $amenityBbq->id, $amenityCoffee->id
        ]);

        $house6 = House::create([
            'category_id' => $catChalet->id,
            'name' => 'Лесное Шале «Береговой Уют»',
            'slug' => 'chalet-coastal-cozy',
            'description' => 'Семейный деревянный коттедж с видом на тихий залив. Панорамная веранда с шезлонгами, финская сауна, зона гриля и свой мостик для рыбалки или прогулок на сапбордах.',
            'price_per_night' => 14000.00,
            'capacity' => 4,
            'area' => 70,
            'image_url' => 'images/houses/chalet-2.jpg',
            'is_featured' => false,
            'is_active' => true,
        ]);
        $house6->amenities()->attach([
            $amenitySauna->id, $amenityWifi->id, $amenityTerrace->id, $amenityBbq->id, $amenityCoffee->id
        ]);

        // 5. Создание демонстрационных бронирований с разными статусами
        Booking::create([
            'user_id' => $client1->id,
            'house_id' => $house1->id,
            'check_in_date' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'check_out_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'guests_count' => 2,
            'total_price' => 19000.00,
            'status' => 'confirmed',
            'guest_name' => $client1->name,
            'guest_phone' => $client1->phone,
            'comment' => 'Хотим протопить чан к нашему приезду к 18:00.',
        ]);

        Booking::create([
            'user_id' => $client1->id,
            'house_id' => $house3->id,
            'check_in_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
            'check_out_date' => Carbon::now()->addDays(12)->format('Y-m-d'),
            'guests_count' => 2,
            'total_price' => 27000.00,
            'status' => 'pending',
            'guest_name' => $client1->name,
            'guest_phone' => $client1->phone,
            'comment' => 'Годовщина свадьбы, требуется романтическое оформление.',
        ]);

        Booking::create([
            'user_id' => $client2->id,
            'house_id' => $house5->id,
            'check_in_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
            'check_out_date' => Carbon::now()->subDays(7)->format('Y-m-d'),
            'guests_count' => 5,
            'total_price' => 49500.00,
            'status' => 'completed',
            'guest_name' => $client2->name,
            'guest_phone' => $client2->phone,
            'comment' => 'Отдыхали семьей, всё прошло великолепно!',
        ]);

        // 6. Создание демонстрационных отзывов
        Review::create([
            'user_id' => $client2->id,
            'house_id' => $house5->id,
            'rating' => 5,
            'content' => 'Невероятное место! Камин создавал неповторимую атмосферу, баня просто огонь. Обязательно вернемся зимой.',
            'is_published' => true,
        ]);

        Review::create([
            'user_id' => $client1->id,
            'house_id' => $house1->id,
            'rating' => 5,
            'content' => 'Чан с видом на звездное небо и сосны — это лучшее, что случалось с нами за последнее время. Идеальная тишина.',
            'is_published' => true,
        ]);
    }
}
