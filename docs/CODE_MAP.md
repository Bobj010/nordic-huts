# 🗺️ Архитектурная карта кода (CODE_MAP.md)

> Карта модулей, файлов и зон ответственности проекта «Nordic Huts».  
> Служит единым реестром для агентов и разработчиков, исключая дублирование кода и хаотичное создание файлов.

---

## 📂 Структура проекта

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php          # Регистрация, вход, выход, Quick Login в 1 клик
│   │   ├── HomeController.php          # Главная страница (Hero, ТОП-домики, отзывы)
│   │   ├── HouseController.php         # Каталог с фильтрами, детальная карточка домика
│   │   ├── BookingController.php       # Создание бронирования, личный кабинет гостя
│   │   ├── ManagerController.php       # Панель менеджера: просмотр заявок, смена статусов
│   │   └── Admin/
│   │       ├── AdminController.php     # Обзорная панель администратора
│   │       ├── AdminHouseController.php# CRUD домиков и привязка удобств (Many-to-Many)
│   │       └── AdminUserController.php # Управление ролями зарегистрированных пользователей
│   ├── Middleware/
│   │   └── RoleMiddleware.php          # Проверка ролей пользователя (client, manager, admin)
│   └── Requests/
│       ├── BookingStoreRequest.php     # Валидация дат заезда, телефона, количества гостей
│       └── HouseStoreRequest.php       # Валидация создания/редактирования домика
│
├── Models/
│   ├── User.php                        # Пользователь, методы проверки ролей isAdmin, isManager
│   ├── Category.php                    # Категория домиков (A-frame, Шале, Купол)
│   ├── Amenity.php                     # Удобство (Чан, Баня, Камин, Проектор)
│   ├── House.php                       # Домик (цена, вместимость, связь с категорией и удобствами)
│   ├── Booking.php                     # Заявка на бронь (даты, статус, расчет ночей)
│   └── Review.php                      # Отзыв гостя с рейтингом (1-5)
│
database/
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── xxxx_xx_xx_xxxxxx_create_categories_table.php
│   ├── xxxx_xx_xx_xxxxxx_create_amenities_table.php
│   ├── xxxx_xx_xx_xxxxxx_create_houses_table.php
│   ├── xxxx_xx_xx_xxxxxx_create_house_amenity_table.php  # Pivot Many-to-Many
│   ├── xxxx_xx_xx_xxxxxx_create_bookings_table.php
│   └── xxxx_xx_xx_xxxxxx_create_reviews_table.php
└── seeders/
    └── DatabaseSeeder.php              # Заполнение 4 ролей пользователей, удобств, 6 домиков и отзывов

resources/
└── views/
    ├── layouts/
    │   ├── app.blade.php               # Главный каркас (шапка, Quick Login, футер, flash-сообщения)
    │   └── navigation.blade.php        # Меню навигации с динамическими пунктами по ролям
    ├── home.blade.php                  # Главная страница витрины
    ├── catalog/
    │   ├── index.blade.php             # Каталог с фильтрами по вместимости и удобствам
    │   └── show.blade.php              # Карточка домика, галерея и форма бронирования
    ├── auth/
    │   ├── login.blade.php             # Форма входа
    │   └── register.blade.php          # Форма регистрации
    ├── client/
    │   └── bookings.blade.php          # Личный кабинет гостя («Мои бронирования»)
    ├── manager/
    │   └── index.blade.php             # Рабочее место менеджера (управление заявками)
    └── admin/
        ├── index.blade.php             # Панель администратора
        ├── houses/
        │   ├── create.blade.php        # Добавление домика с чекбоксами удобств
        │   └── edit.blade.php          # Редактирование домика
        └── users/
            └── index.blade.php         # Управление ролями пользователей

routes/
└── web.php                             # Все веб-маршруты приложения, сгруппированные по Middleware
