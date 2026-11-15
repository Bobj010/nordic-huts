# 🛠️ Детальный план: Этап 1. Архитектура БД и Спецификация (Data Layer)

> **Стандарт выполнения:** `/sql-pro` (Проектирование высокопроизводительных реляционных схем, 3NF, индексация, целостность данных).  
> **Методология:** Vibecoding Acceptance Criteria (DoD) & Red-Green Verification.

---

## 1. Архитектурный аудит БД по стандарту `sql-pro`

### 1.1. Нормализация (3NF)
* Все сущности приведены к Третьей нормальной форме:
  * Атрибуты зависят только от первичного ключа.
  * Устранено дублирование категорий и справочников (вынесены в `categories` и `amenities`).
  * Многозначная связь «Домик ↔ Удобства» выделена в отдельную промежуточную таблицу `house_amenity`.

### 1.2. Индексная стратегия (Исключение Table Scans)
* `users(email)`: B-Tree Unique Index для O(1) поиска при аутентификации.
* `users(role)`: индекс `idx_users_role` для быстрой выборки по ролям (админы, менеджеры).
* `houses(category_id, is_active)`: составной индекс для мгновенной фильтрации в каталоге.
* `houses(price_per_night)`: индекс для быстрой сортировки по возрастанию/убыванию стоимости.
* `bookings(house_id, check_in_date, check_out_date)`: составной индекс для проверки пересечения дат броней.
* `bookings(user_id)`: индекс для выборки истории броней конкретного клиента в ЛК.
* `bookings(status)`: индекс для выборки новых заявок в панели менеджера.

### 1.3. Целостность данных и каскадные правила
* Внешние ключи со строгими правилами:
  * `house_amenity.house_id` -> `ON DELETE CASCADE`
  * `house_amenity.amenity_id` -> `ON DELETE CASCADE`
  * `bookings.house_id` -> `ON DELETE CASCADE`
  * `bookings.user_id` -> `ON DELETE CASCADE`
  * `houses.category_id` -> `ON DELETE RESTRICT` (защита от случайного удаления категории, пока в ней есть домики).

---

## 2. Пошаговые точечные задачи Этапа 1

### Задача 1.1. Инициализация каркаса проекта
* Проверка наличия Laravel-каркаса в рабочей директории `c:\Users\eduar\Desktop\Учеба\individual'nyy_proyekt_laravel`.
* Настройка конфигурации `.env` (установка `DB_CONNECTION=sqlite`, `APP_NAME="Nordic Huts"`).
* Создание пустого файла базы данных `database/database.sqlite`.

### Задача 1.2. Миграция таблицы пользователей (`users`)
* Добавление полей:
  * `role` (VARCHAR, default: `'client'`, index)
  * `phone` (VARCHAR, nullable)

### Задача 1.3. Миграция категорий (`categories`)
* Поля: `name`, `slug` (unique), `description` (nullable).

### Задача 1.4. Миграция удобств (`amenities`)
* Поля: `name`, `icon` (строковый ключ иконки).

### Задача 1.5. Миграция домиков (`houses`)
* Поля: `category_id` (FK), `name`, `slug` (unique), `description`, `price_per_night` (decimal), `capacity` (unsigned smallint), `area` (unsigned smallint), `image_url`, `is_featured` (boolean, default false), `is_active` (boolean, default true).
* Индексы: `category_id + is_active`, `price_per_night`.

### Задача 1.6. Миграция промежуточной таблицы `house_amenity`
* Поля: `house_id` (FK cascade), `amenity_id` (FK cascade).
* Первичный ключ: составной `(house_id, amenity_id)`.

### Задача 1.7. Миграция заявок на бронирование (`bookings`)
* Поля: `user_id` (FK cascade), `house_id` (FK cascade), `check_in_date`, `check_out_date`, `guests_count`, `total_price` (decimal), `status` (default: 'pending'), `guest_name`, `guest_phone`, `comment` (nullable).
* Индексы: `user_id`, `status`, `house_id + check_in_date + check_out_date`.

### Задача 1.8. Миграция отзывов (`reviews`)
* Поля: `user_id` (FK cascade), `house_id` (FK cascade), `rating` (unsigned tinyint: 1-5), `content`, `is_published` (boolean, default true).

### Задача 1.9. Разработка Eloquent моделей с явными связями
* `User`:
  * Связи: `hasMany(Booking::class)`, `hasMany(Review::class)`.
  * Хелперы ролей: `isAdmin(): bool`, `isManager(): bool`, `isClient(): bool`.
* `Category`:
  * Связи: `hasMany(House::class)`.
* `Amenity`:
  * Связи: `belongsToMany(House::class, 'house_amenity')`.
* `House`:
  * Связи: `belongsTo(Category::class)`, `belongsToMany(Amenity::class, 'house_amenity')`, `hasMany(Booking::class)`, `hasMany(Review::class)`.
* `Booking`:
  * Связи: `belongsTo(User::class)`, `belongsTo(House::class)`.
  * Касты: `check_in_date` => `'date'`, `check_out_date` => `'date'`.
  * Аксессор: `nights_count` (вычисление разницы между датами).
* `Review`:
  * Связи: `belongsTo(User::class)`, `belongsTo(House::class)`.

### Задача 1.10. Наполнение базы реалистичными данными (`DatabaseSeeder`)
* **Пользователи (4 роли с готовыми хэшами паролей):**
  * `admin@nordic.local` (роль: `admin`, пароль: `password`) — Владелец
  * `manager@nordic.local` (роль: `manager`, пароль: `password`) — Администратор ресепшена
  * `client@nordic.local` (роль: `client`, пароль: `password`) — Гость Анна
  * Тестовый пользователь для демонстраций.
* **Категории:**
  * `A-frame Дома` (треугольные уютные домики-шалаши с панорамным фронтоном)
  * `Купольные сферы` (футуристичные сферы с видом на звездное небо)
  * `Лесные шале` (просторные дома из бруса для семейного отдыха)
* **Справочник удобств (с красивыми иконками):**
  * Банный чан на дровах, Русская баня, Камин на дровах, Проектор для фильмов, Wi-Fi Starlink, Мангальная зона BBQ, Панорамная терраса, Кухня с кофемашиной.
* **6 проработанных домиков:**
  * Реалистичные названия, продуманные описания, честные цены и привязка к качественным фото скандинавских домов с Unsplash.
* **Связи `house_amenity`:**
  * У каждого домика свой уникальный набор удобств.
* **Примеры бронирований и отзывов со статусами.**

---

## 3. Критерии приемки Этапа 1 (Definition of Done)
1. ✅ Команда `php artisan migrate:fresh --seed` отрабатывает без единой ошибки и ворнинга.
2. ✅ База данных `database/database.sqlite` содержит все 7 таблиц с корректными внешними ключами.
3. ✅ Запрос `$house->amenities` возвращает корректную коллекцию удобств через промежуточную таблицу `house_amenity`.
4. ✅ Все 4 тестовых пользователя авторизуются по паролю `password`.
