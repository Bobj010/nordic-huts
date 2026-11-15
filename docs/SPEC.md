# ⚙️ Техническая спецификация: «Nordic Huts» (Backend & Frontend Architecture)

> **Для кого этот документ:** Для разработчика, ИИ-агента и преподавателя (технический разбор).  
> **О чем он:** Схемы таблиц базы данных, реляционные связи, маршруты, Middleware, валидация и архитектура кода.

---

## 1. Стек и инфраструктура
* **PHP:** `^8.3`
* **Фреймворк:** `Laravel 11.x / 12.x`
* **Шаблонизатор:** `Blade` + `Vite`
* **Стилизация:** `Tailwind CSS v4`
* **Микро-интерактивность:** `Alpine.js`
* **СУБД:** `SQLite` (драйвер по умолчанию: файл `database/database.sqlite`), полная переносимость на `MySQL` / `PostgreSQL` без правок миграций.
* **Аутентификация:** Встроенная сессионная аутентификация Laravel + ролевая модель через кастомный `RoleMiddleware`.

---

## 2. Схема базы данных (Relational Database Schema)

### 2.1. Таблица `users` (Пользователи и роли)
| Колонка | Тип | Ограничения | Описание |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK, Auto Increment | Идентификатор |
| `name` | VARCHAR(255) | NOT NULL | Имя пользователя |
| `email` | VARCHAR(255) | NOT NULL, UNIQUE, Index | Электронная почта |
| `phone` | VARCHAR(50) | NULLABLE | Контактный телефон |
| `password` | VARCHAR(255) | NOT NULL | Хэш пароля (Bcrypt) |
| `role` | VARCHAR(50) | NOT NULL, DEFAULT 'client' | Роль: `client`, `manager`, `admin` |
| `remember_token` | VARCHAR(100) | NULLABLE | Токен «Запомнить меня» |
| `timestamps` | TIMESTAMP | NULLABLE | `created_at`, `updated_at` |

*Индексы:* `idx_users_role` на колонку `role`.

---

### 2.2. Таблица `categories` (Типы домиков)
| Колонка | Тип | Ограничения | Описание |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK, Auto Increment | Идентификатор |
| `name` | VARCHAR(100) | NOT NULL | Название: «A-frame», «Купольная сфера», «Лесное шале» |
| `slug` | VARCHAR(100) | NOT NULL, UNIQUE | ЧПУ-идентификатор: `a-frame`, `dome`, `chalet` |
| `description` | TEXT | NULLABLE | Краткое описание категории |
| `timestamps` | TIMESTAMP | NULLABLE | `created_at`, `updated_at` |

---

### 2.3. Таблица `amenities` (Справочник удобств)
| Колонка | Тип | Ограничения | Описание |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK, Auto Increment | Идентификатор |
| `name` | VARCHAR(100) | NOT NULL | Название: «Банный чан», «Камин», «Wi-Fi», «Проектор», «Мангальная зона» |
| `icon` | VARCHAR(50) | NOT NULL | Имя иконки или SVG-маркер: `bath`, `fire`, `wifi`, `tv`, `grill` |
| `timestamps` | TIMESTAMP | NULLABLE | `created_at`, `updated_at` |

---

### 2.4. Таблица `houses` (Каталог домиков)
| Колонка | Тип | Ограничения | Описание |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK, Auto Increment | Идентификатор |
| `category_id` | BIGINT UNSIGNED | FK -> `categories.id`, ON DELETE RESTRICT | Категория строения |
| `name` | VARCHAR(255) | NOT NULL | Название: «A-frame у озера», «Chalet Pine» |
| `slug` | VARCHAR(255) | NOT NULL, UNIQUE | ЧПУ для URL |
| `description` | TEXT | NOT NULL | Полный текст с описанием отдыха |
| `price_per_night` | DECIMAL(10, 2) | NOT NULL | Цена за сутки проживания (руб) |
| `capacity` | SMALLINT UNSIGNED | NOT NULL | Максимальное число гостей (2, 4, 6) |
| `area` | SMALLINT UNSIGNED | NOT NULL | Площадь домика в м² |
| `image_url` | VARCHAR(255) | NOT NULL | Главное изображение / обложка |
| `is_featured` | BOOLEAN | NOT NULL, DEFAULT false | Флаг для показа на главной (ТОП-домики) |
| `is_active` | BOOLEAN | NOT NULL, DEFAULT true | Доступность для бронирования |
| `timestamps` | TIMESTAMP | NULLABLE | `created_at`, `updated_at` |

*Индексы:* `idx_houses_category_active` (`category_id`, `is_active`), `idx_houses_price` (`price_per_night`).

---

### 2.5. Таблица `house_amenity` (Промежуточная таблица Many-to-Many)
| Колонка | Тип | Ограничения | Описание |
| :--- | :--- | :--- | :--- |
| `house_id` | BIGINT UNSIGNED | FK -> `houses.id`, ON DELETE CASCADE | Домик |
| `amenity_id` | BIGINT UNSIGNED | FK -> `amenities.id`, ON DELETE CASCADE | Удобство |

*Первичный составной ключ:* `PRIMARY KEY (house_id, amenity_id)`.

---

### 2.6. Таблица `bookings` (Бронирования / Заявки)
| Колонка | Тип | Ограничения | Описание |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK, Auto Increment | Идентификатор |
| `user_id` | BIGINT UNSIGNED | FK -> `users.id`, ON DELETE CASCADE | Автор бронирования |
| `house_id` | BIGINT UNSIGNED | FK -> `houses.id`, ON DELETE CASCADE | Забронированный домик |
| `check_in_date` | DATE | NOT NULL | Дата заезда |
| `check_out_date` | DATE | NOT NULL | Дата выезда |
| `guests_count` | SMALLINT UNSIGNED | NOT NULL | Количество гостей |
| `total_price` | DECIMAL(10, 2) | NOT NULL | Итоговая сумма за период |
| `status` | VARCHAR(50) | NOT NULL, DEFAULT 'pending' | `pending`, `confirmed`, `completed`, `cancelled` |
| `guest_name` | VARCHAR(255) | NOT NULL | Контактное имя |
| `guest_phone` | VARCHAR(50) | NOT NULL | Контактный телефон |
| `comment` | TEXT | NULLABLE | Пожелания гостя |
| `timestamps` | TIMESTAMP | NULLABLE | `created_at`, `updated_at` |

*Индексы:* `idx_bookings_user` (`user_id`), `idx_bookings_house_dates` (`house_id`, `check_in_date`, `check_out_date`), `idx_bookings_status` (`status`).

---

### 2.7. Таблица `reviews` (Отзывы)
| Колонка | Тип | Ограничения | Описание |
| :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK, Auto Increment | Идентификатор |
| `user_id` | BIGINT UNSIGNED | FK -> `users.id`, ON DELETE CASCADE | Автор отзыва |
| `house_id` | BIGINT UNSIGNED | FK -> `houses.id`, ON DELETE CASCADE | Оцениваемый домик |
| `rating` | TINYINT UNSIGNED | NOT NULL | Оценка от 1 до 5 |
| `content` | TEXT | NOT NULL | Текст отзыва |
| `is_published` | BOOLEAN | NOT NULL, DEFAULT true | Флаг публикации модератором |
| `timestamps` | TIMESTAMP | NULLABLE | `created_at`, `updated_at` |

---

## 3. Реляционные связи моделей (Eloquent ORM)

```php
// User
public function bookings(): HasMany { return $this->hasMany(Booking::class); }
public function reviews(): HasMany { return $this->hasMany(Review::class); }

// Category
public function houses(): HasMany { return $this->hasMany(House::class); }

// House
public function category(): BelongsTo { return $this->belongsTo(Category::class); }
public function amenities(): BelongsToMany { return $this->belongsToMany(Amenity::class, 'house_amenity'); }
public function bookings(): HasMany { return $this->hasMany(Booking::class); }
public function reviews(): HasMany { return $this->hasMany(Review::class); }

// Amenity
public function houses(): BelongsToMany { return $this->belongsToMany(House::class, 'house_amenity'); }

// Booking
public function user(): BelongsTo { return $this->belongsTo(User::class); }
public function house(): BelongsTo { return $this->belongsTo(House::class); }
```

---

## 4. Маршрутизация (Route Groups & Middlewares)

```
/
├── Публичная зона (без авторизации)
│   ├── GET  /                  -> HomeController@index (Главная с ТОП-домиками и отзывами)
│   ├── GET  /catalog           -> HouseController@catalog (Каталог с фильтрами)
│   ├── GET  /houses/{slug}     -> HouseController@show (Карточка домика)
│   ├── GET  /quick-login/{role}-> AuthController@quickLogin (Вход в 1 клик для проверяющего)
│   └── (гостевые маршруты: /login, /register)
│
├── Зона авторизованного клиента (middleware: auth)
│   ├── POST /logout            -> AuthController@logout
│   ├── POST /bookings          -> BookingController@store (Создание заявки)
│   ├── GET  /my-bookings       -> BookingController@myBookings (Личный кабинет)
│   └── POST /my-bookings/{id}/cancel -> BookingController@cancel (Отмена брони)
│
├── Зона менеджера и админа (middleware: auth, role:manager,admin)
│   ├── GET  /manager           -> ManagerController@index (Лента заявок на бронь)
│   └── PATCH /manager/bookings/{booking}/status -> ManagerController@updateStatus
│
└── Зона администратора (middleware: auth, role:admin)
    ├── GET  /admin             -> AdminController@index (Статистика)
    ├── GET  /admin/houses/create -> AdminHouseController@create
    ├── POST /admin/houses      -> AdminHouseController@store
    ├── GET  /admin/houses/{house}/edit -> AdminHouseController@edit
    ├── PUT  /admin/houses/{house}      -> AdminHouseController@update
    ├── DELETE /admin/houses/{house}   -> AdminHouseController@destroy
    └── PATCH /admin/users/{user}/role  -> AdminUserController@updateRole
```
