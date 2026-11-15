@echo off
chcp 65001 >nul
cd /d "%~dp0"

echo ======================================================================
echo  [INFO] Проект: «Nordic Huts» (Сервис бронирования эко-глэмпинга)
echo  [INFO] Автоматический лаунчер для демонстрации и защиты проекта
echo ======================================================================
echo.

:: 1. Проверка наличия PHP
where php >nul 2>nul
if errorlevel 1 (
    echo [ERROR] PHP не найден в системной переменной PATH!
    echo [INFO] Пожалуйста, установите PHP 8.2+ или добавьте путь к php.exe в PATH.
    echo.
    pause
    exit /b 1
)

:: 2. Проверка файла конфигурации .env
if not exist "%~dp0.env" (
    echo [WARN] Файл конфигурации .env не найден. Создание из .env.example...
    copy /y "%~dp0.env.example" "%~dp0.env" >nul
    if errorlevel 1 (
        echo [ERROR] Не удалось скопировать .env.example в .env!
        pause
        exit /b 1
    )
    echo [OK] Файл .env успешно создан.
    echo [INFO] Генерация ключа приложения APP_KEY...
    php artisan key:generate --force
)

:: 3. Проверка и инициализация базы данных SQLite
if not exist "%~dp0database\database.sqlite" (
    echo [WARN] База данных SQLite не найдена. Создание database/database.sqlite...
    type nul > "%~dp0database\database.sqlite"
    echo [INFO] Выполнение миграций и наполнение тестовыми данными (Seeder)...
    php artisan migrate:fresh --seed --force
    if errorlevel 1 (
        echo [ERROR] Ошибка при выполнении миграций базы данных!
        pause
        exit /b 1
    )
    echo [OK] База данных успешно инициализирована и наполнена (4 роли, 6 домиков, отзывы).
)

echo.
echo ======================================================================
echo  [OK] Все компоненты готовы к работе!
echo  [INFO] Адрес приложения: http://127.0.0.1:8000
echo  [INFO] В шапке сайта доступна панель «Быстрый вход для преподавателя»:
echo         - [Гость]
echo         - [Клиент: Анна Смирнова]
echo         - [Менеджер: Елена]
echo         - [Админ: Александр]
echo  [INFO] Для остановки сервера нажмите Ctrl+C в этом окне.
echo ======================================================================
echo.

:: 4. Открытие браузера по умолчанию
start "" "http://127.0.0.1:8000"

:: 5. Запуск локального сервера
php artisan serve --port=8000
if errorlevel 1 (
    echo [ERROR] Сервер завершил работу с ошибкой.
    pause
    exit /b %errorlevel%
)
