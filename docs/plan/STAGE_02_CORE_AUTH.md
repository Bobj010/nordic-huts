# 🛠️ Детальный план: Этап 2. Каркас системы, Аутентификация и Роли (Core & Auth)

> **Стандарты реализации:**  
> • `/ub-mode` (Чистая архитектура Uncle Bob: Boundary Discipline, тонкие контроллеры, TDD, Single Responsibility, DRY).  
> • `/antigravity-guide` (Иерархическая структура роутов, современный `bootstrap/app.php` в Laravel 11/12).  
> • `/vibecoding-methodology` (Запрет скрытого техдолга, проверяемые критерии приёмки DoD).

---

## 1. Архитектурные принципы Этапа 2 (по стандарту `ub-mode`)

1. **Boundary Discipline (Дисциплина границ):**
   - Входные данные проверяются **только на границе** системы через специализированные `FormRequest` (`BookingStoreRequest`).
   - Внутри контроллера данные уже валидны и типизированы — контроллер не содержит ручных проверок `if (!$request->name)`.
2. **Тонкие контроллеры (Thin Controllers):**
   - Контроллеры отвечают только за оркестрацию: принять валидированный запрос → вызвать метод модели / сервиса → вернуть ответ (редирект или представление).
   - Никаких SQL-запросов и сложных формул внутри контроллеров.
3. **Безопасность ролей в одном месте (Laziness Protocol):**
   - Единое кастомное `RoleMiddleware`, зарегистрированное в `bootstrap/app.php`.
   - Проверка ролей в роутах через декларативный синтаксис: `middleware(['auth', 'role:manager,admin'])`.

---

## 2. Пошаговые точечные задачи Этапа 2

### Задача 2.1. Регистрация `RoleMiddleware` в ядре Laravel 11/12
* **Файл:** `app/Http/Middleware/RoleMiddleware.php`
* **Файл ядра:** `bootstrap/app.php`
* **Логика:**
  * Проверка: авторизован ли пользователь (`Auth::check()`). Если нет — редирект на `route('login')` с информативным сообщением.
  * Проверка ролей: метод принимает список разрешенных ролей через запятую (`...$roles`).
  * Если роль текущего пользователя входит в список — пропускаем (`return $next($request)`).
  * Если нет — возвращаем `abort(403, 'Доступ ограничен. Недостаточно прав для просмотра раздела.')`.
  * Регистрация алиаса `'role' => \App\Http\Middleware\RoleMiddleware::class` в методе `withMiddleware` файла `bootstrap/app.php`.

### Задача 2.2. Контроллер аутентификации и Quick Login (`AuthController`)
* **Файл:** `app/Http/Controllers/AuthController.php`
* **Методы:**
  1. `showLoginForm()` — отображение формы входа.
  2. `login(Request $request)` — валидация `email` и `password`, вызов `Auth::attempt()`, регенерация сессии `session()->regenerate()`, редирект в зависимости от роли (админ → `/admin`, менеджер → `/manager`, клиент → `/my-bookings`).
  3. `showRegisterForm()` — отображение формы регистрации.
  4. `register(Request $request)` — валидация `name`, `email` (unique), `phone`, `password` (min:6, confirmed), создание пользователя с ролью `client`, автоматическая авторизация `Auth::login()`.
  5. `logout(Request $request)` — `Auth::logout()`, очистка сессии `session()->invalidate()`, сброс CSRF-токена `session()->regenerateToken()`.
  6. **Фирменный `quickLogin(string $role)` (Киллер-фича для защиты):**
     * Валидация входного параметра `$role` по белому списку: `['admin', 'manager', 'client', 'guest']`.
     * Если `$role === 'guest'` — разлогиниваем `Auth::logout()` и перенаправляем на главную.
     * Если передана роль — находим первого пользователя этой роли (`User::where('role', $role)->first()`), вызываем `Auth::login($user)` и возвращаем назад с flash-уведомлением «Вы успешно вошли как: {имя} ({роль})».

### Задача 2.3. Валидация бронирования (`BookingStoreRequest`)
* **Файл:** `app/Http/Requests/BookingStoreRequest.php`
* **Правила валидации (Boundary Discipline):**
  * `house_id`: `['required', 'exists:houses,id']`
  * `check_in_date`: `['required', 'date', 'after_or_equal:today']`
  * `check_out_date`: `['required', 'date', 'after:check_in_date']`
  * `guests_count`: `['required', 'integer', 'min:1']`
  * `guest_name`: `['required', 'string', 'max:255']`
  * `guest_phone`: `['required', 'string', 'max:50']`
  * `comment`: `['nullable', 'string', 'max:1000']`
* **Кастомные понятные сообщения на русском языке** (например: «Дата выезда должна быть позже даты заезда»).

### Задача 2.4. Публичные контроллеры (`HomeController`, `HouseController`)
* **`app/Http/Controllers/HomeController.php`:**
  * `index()` — выборка ТОП-домиков (`House::featured()->with(['category', 'amenities'])->take(3)->get()`) и отзывов (`Review::with(['user', 'house'])->where('is_published', true)->take(2)->get()`).
* **`app/Http/Controllers/HouseController.php`:**
  * `catalog(Request $request)` — умная фильтрация каталога:
    * по категории (`category_id`)
    * по вместимости (`capacity >= $guests`)
    * по удобствам (`whereHas('amenities', ...)`)
    * по диапазону цен
  * `show(string $slug)` — детальная карточка домика с предзагрузкой категорий, удобств и отзывов.

### Задача 2.5. Контроллеры бронирований и кабинетов (`BookingController`, `ManagerController`)
* **`app/Http/Controllers/BookingController.php`:**
  * `store(BookingStoreRequest $request)`:
    * Расчет количества ночей: `$nights = $checkIn->diffInDays($checkOut) ?: 1`.
    * Расчет итоговой суммы: `$totalPrice = $nights * $house->price_per_night`.
    * Проверка вместимости: `$guests_count <= $house->capacity`.
    * Создание записи `Booking::create(...)`.
    * Редирект в личный кабинет `route('client.bookings')` с уведомлением о создании заявки.
  * `myBookings()`:
    * Выборка бронирований текущего авторизованного пользователя: `Auth::user()->bookings()->with('house.category')->latest()->get()`.
  * `cancel(Booking $booking)`:
    * Проверка прав: отменить бронь может только её владелец.
    * Проверка статуса: отмена доступна только для заявок со статусом `pending`.
* **`app/Http/Controllers/ManagerController.php`:**
  * `index(Request $request)`:
    * Просмотр всех заявок глэмпинга с фильтром по статусам (`pending`, `confirmed`, `completed`, `cancelled`).
  * `updateStatus(Request $request, Booking $booking)`:
    * Валидация статуса по белому списку: `in:pending,confirmed,completed,cancelled`.
    * Обновление статуса заявки: `$booking->update(['status' => $request->status])`.
    * Редирект назад с flash-сообщением.

### Задача 2.6. Конфигурация маршрутизации (`routes/web.php`)
* Полная группировка маршрутов с префиксами и именованием (`as`, `name`):
  1. **Публичная витрина:** `/`, `/catalog`, `/houses/{slug}`, `/quick-login/{role}`.
  2. **Гостевая зона (`middleware: guest`):** `/login`, `/register`.
  3. **Клиентская зона (`middleware: auth`):** `/logout`, `/bookings` (POST), `/my-bookings` (GET), `/my-bookings/{booking}/cancel` (POST).
  4. **Зона менеджера (`middleware: ['auth', 'role:manager,admin']`):** `/manager` (GET), `/manager/bookings/{booking}/status` (PATCH).
  5. **Зона администратора (`middleware: ['auth', 'role:admin']`):** каркас маршрутов `/admin`.

### Задача 2.7. Автоматизированные тесты ядра (TDD по `ub-mode`)
* **`tests/Feature/AuthAndRoleTest.php`:**
  * Тест: Гость не может попасть в `/manager` (редирект на `/login`).
  * Тест: Обычный клиент получает `403 Forbidden` при попытке открыть `/manager`.
  * Тест: Менеджер и Админ успешно открывают `/manager`.
  * Тест: Quick Login корректно переключает пользователя под ролью `manager` и `admin`.
* **`tests/Feature/BookingLogicTest.php`:**
  * Тест: Успешное создание брони с автоматическим расчетом итоговой стоимости (`total_price = nights * price_per_night`).
  * Тест: Валидация отклоняет бронь, если дата выезда раньше даты заезда.
  * Тест: Клиент видит в личном кабинете только свои собственные брони.

---

## 3. Критерии приемки Этапа 2 (Definition of Done)

1. ✅ Все маршруты зарегистрированы и выводятся без ошибок через `php artisan route:list`.
2. ✅ `RoleMiddleware` строго разграничивает доступ (гость → редирект на логин, клиент → 403, менеджер → 200).
3. ✅ Функция `/quick-login/{role}` мгновенно авторизует любого из 4 тестовых пользователей.
4. ✅ Контроллер бронирования корректно рассчитывает ночи и сумму.
5. ✅ Все тесты в `tests/Feature/` зеленые (`php artisan test`).
