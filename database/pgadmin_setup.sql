-- ==============================================================================
-- NORDIC HUTS — Скрипт инициализации базы данных для PostgreSQL / pgAdmin 4
-- Индивидуальный проект: Сервис онлайн-бронирования эко-глэмпинга
-- Стандарт: 3NF (Третья нормальная форма), связи 1:N и N:M, внешние ключи, индексы
-- ==============================================================================

-- 1. Очистка старых таблиц (если существовали)
DROP TABLE IF EXISTS reviews CASCADE;
DROP TABLE IF EXISTS bookings CASCADE;
DROP TABLE IF EXISTS house_amenity CASCADE;
DROP TABLE IF EXISTS houses CASCADE;
DROP TABLE IF EXISTS amenities CASCADE;
DROP TABLE IF EXISTS categories CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS sessions CASCADE;
DROP TABLE IF EXISTS password_reset_tokens CASCADE;
DROP TABLE IF EXISTS migrations CASCADE;

-- 2. Создание таблиц ядра

-- 2.1. Таблица пользователей с ролевой моделью
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(255) NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'client',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
CREATE INDEX idx_users_role ON users(role);

-- 2.2. Таблица категорий домиков
CREATE TABLE categories (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 2.3. Справочник удобств
CREATE TABLE amenities (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(50) NOT NULL DEFAULT 'check',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 2.4. Таблица домиков
CREATE TABLE houses (
    id BIGSERIAL PRIMARY KEY,
    category_id BIGINT NOT NULL REFERENCES categories(id) ON DELETE RESTRICT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    price_per_night NUMERIC(10, 2) NOT NULL,
    capacity SMALLINT NOT NULL,
    area SMALLINT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_featured BOOLEAN NOT NULL DEFAULT FALSE,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
CREATE INDEX idx_houses_cat_active ON houses(category_id, is_active);
CREATE INDEX idx_houses_price ON houses(price_per_night);
CREATE INDEX idx_houses_capacity ON houses(capacity);
CREATE INDEX idx_houses_featured ON houses(is_featured);

-- 2.5. Сводная таблица связи Many-to-Many: Домики <-> Удобства
CREATE TABLE house_amenity (
    house_id BIGINT NOT NULL REFERENCES houses(id) ON DELETE CASCADE,
    amenity_id BIGINT NOT NULL REFERENCES amenities(id) ON DELETE CASCADE,
    PRIMARY KEY (house_id, amenity_id)
);
CREATE INDEX idx_house_amenity_amenity_id ON house_amenity(amenity_id);

-- 2.6. Таблица бронирований
CREATE TABLE bookings (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    house_id BIGINT NOT NULL REFERENCES houses(id) ON DELETE CASCADE,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    guests_count SMALLINT NOT NULL,
    total_price NUMERIC(10, 2) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'pending',
    guest_name VARCHAR(255) NOT NULL,
    guest_phone VARCHAR(255) NOT NULL,
    comment TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
CREATE INDEX idx_bookings_user_id ON bookings(user_id);
CREATE INDEX idx_bookings_status ON bookings(status);
CREATE INDEX idx_bookings_house_dates ON bookings(house_id, check_in_date, check_out_date);

-- 2.7. Таблица отзывов
CREATE TABLE reviews (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    house_id BIGINT NOT NULL REFERENCES houses(id) ON DELETE CASCADE,
    rating SMALLINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    content TEXT NOT NULL,
    is_published BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
CREATE INDEX idx_reviews_house_published ON reviews(house_id, is_published);

-- 2.8. Системные таблицы сессий и миграций
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload TEXT NOT NULL,
    last_activity INTEGER NOT NULL
);
CREATE INDEX idx_sessions_user_id ON sessions(user_id);
CREATE INDEX idx_sessions_last_activity ON sessions(last_activity);

CREATE TABLE migrations (
    id SERIAL PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INTEGER NOT NULL
);

-- 3. Данные таблицы users (4 записей)
INSERT INTO users (id, name, email, phone, email_verified_at, password, role, remember_token, created_at, updated_at) VALUES (1, 'Александр (Управляющий)', 'admin@nordic.local', '+7 (999) 111-22-33', NULL, '$2y$12$Y4pK2IE7kwPmOYVDMYmg6.h7nqAX9aSJxmspBuBiBPjpBhaVD97Fm', 'admin', NULL, '2026-11-15 15:09:40', '2026-11-15 15:09:40');
INSERT INTO users (id, name, email, phone, email_verified_at, password, role, remember_token, created_at, updated_at) VALUES (2, 'Елена (Менеджер ресепшена)', 'manager@nordic.local', '+7 (999) 222-33-44', NULL, '$2y$12$jluJO7N0tfednSk0LRCVI.PE0VM3O4oGjf87KWBCQTTr3abU2GyVy', 'manager', NULL, '2026-11-15 15:09:40', '2026-11-15 15:09:40');
INSERT INTO users (id, name, email, phone, email_verified_at, password, role, remember_token, created_at, updated_at) VALUES (3, 'Анна Смирнова', 'client@nordic.local', '+7 (999) 333-44-55', NULL, '$2y$12$SJkaahQ8KWeLodMSTktZhu89APvlMg.kwL5vobDRezqmBAR7yf.Jm', 'client', NULL, '2026-11-15 15:09:41', '2026-11-15 15:09:41');
INSERT INTO users (id, name, email, phone, email_verified_at, password, role, remember_token, created_at, updated_at) VALUES (4, 'Михаил Кузнецов', 'mikhail@nordic.local', '+7 (999) 444-55-66', NULL, '$2y$12$eZTl2NJ2Tz/Xtws7AHO1/uj6m.ws//8acGjGcQXb8hth.y9N6a0b6', 'client', NULL, '2026-11-15 15:09:41', '2026-11-15 15:09:41');

SELECT setval('users_id_seq', (SELECT COALESCE(MAX(id), 1) FROM users));

-- 3. Данные таблицы categories (3 записей)
INSERT INTO categories (id, name, slug, description, created_at, updated_at) VALUES (1, 'A-frame Дома', 'a-frame', 'Культовые треугольные домики-шалаши с панорамным остеклением и видом на реликтовый сосновый бор.', '2026-11-15 15:09:41', '2026-11-15 15:09:41');
INSERT INTO categories (id, name, slug, description, created_at, updated_at) VALUES (2, 'Купольные сферы', 'dome', 'Футуристичные геокуполы с прозрачным куполом для наблюдения за северными звездами прямо из постели.', '2026-11-15 15:09:42', '2026-11-15 15:09:42');
INSERT INTO categories (id, name, slug, description, created_at, updated_at) VALUES (3, 'Лесные шале', 'chalet', 'Просторные теплые дома из клееного бруса с настоящим дровяным камином и приватной террасой.', '2026-11-15 15:09:42', '2026-11-15 15:09:42');

SELECT setval('categories_id_seq', (SELECT COALESCE(MAX(id), 1) FROM categories));

-- 3. Данные таблицы amenities (8 записей)
INSERT INTO amenities (id, name, icon, created_at, updated_at) VALUES (1, 'Банный чан на дровах', 'bath', '2026-11-15 15:09:42', '2026-11-15 15:09:42');
INSERT INTO amenities (id, name, icon, created_at, updated_at) VALUES (2, 'Русская дровяная баня', 'sparkles', '2026-11-15 15:09:42', '2026-11-15 15:09:42');
INSERT INTO amenities (id, name, icon, created_at, updated_at) VALUES (3, 'Камин на дровах', 'fire', '2026-11-15 15:09:42', '2026-11-15 15:09:42');
INSERT INTO amenities (id, name, icon, created_at, updated_at) VALUES (4, 'Кинопроектор 4K', 'film', '2026-11-15 15:09:42', '2026-11-15 15:09:42');
INSERT INTO amenities (id, name, icon, created_at, updated_at) VALUES (5, 'Wi-Fi Starlink', 'wifi', '2026-11-15 15:09:42', '2026-11-15 15:09:42');
INSERT INTO amenities (id, name, icon, created_at, updated_at) VALUES (6, 'Мангальная BBQ-зона', 'flame', '2026-11-15 15:09:42', '2026-11-15 15:09:42');
INSERT INTO amenities (id, name, icon, created_at, updated_at) VALUES (7, 'Панорамная терраса', 'sun', '2026-11-15 15:09:42', '2026-11-15 15:09:42');
INSERT INTO amenities (id, name, icon, created_at, updated_at) VALUES (8, 'Кофемашина и капсулы', 'coffee', '2026-11-15 15:09:42', '2026-11-15 15:09:42');

SELECT setval('amenities_id_seq', (SELECT COALESCE(MAX(id), 1) FROM amenities));

-- 3. Данные таблицы houses (6 записей)
INSERT INTO houses (id, category_id, name, slug, description, price_per_night, capacity, area, image_url, is_featured, is_active, created_at, updated_at) VALUES (1, 1, 'A-frame «Хвойный» у озера', 'a-frame-pine-lake', 'Уединенный треугольный дом прямо на берегу озера. Панорамный фасад обращен на закатную сторону. На террасе установлен персональный подогреваемый чан на дровах. Внутри уютный лофт со спальней на втором ярусе и виниловый проигрыватель.', 9500, 2, 42, 'images/houses/a-frame-1.jpg', TRUE, TRUE, '2026-11-15 15:09:43', '2026-11-15 15:09:43');
INSERT INTO houses (id, category_id, name, slug, description, price_per_night, capacity, area, image_url, is_featured, is_active, created_at, updated_at) VALUES (2, 1, 'A-frame «Скандинавия» с чаном', 'a-frame-scandinavia', 'Просторный двухэтажный дом-шалаш в стиле ваби-саби. Две изолированные спальни, теплая терраса, чан из кедра и зона для медитаций среди сосен. Идеально для уикенда с семьей или близкими друзьями.', 11000, 4, 55, 'images/houses/a-frame-2.jpg', TRUE, TRUE, '2026-11-15 15:09:43', '2026-11-15 15:09:43');
INSERT INTO houses (id, category_id, name, slug, description, price_per_night, capacity, area, image_url, is_featured, is_active, created_at, updated_at) VALUES (3, 2, 'Купольная сфера «Северное сияние»', 'dome-aurora', 'Уникальная геокупольная сфера с круговой панорамой и прозрачной крышей. Засыпайте под звездами на анатомической кровати размера king-size. В сфере установлена дизайнерская отдельностоящая ванна с панорамным видом на лес.', 13500, 2, 38, 'images/houses/dome-1.jpg', TRUE, TRUE, '2026-11-15 15:09:43', '2026-11-15 15:09:43');
INSERT INTO houses (id, category_id, name, slug, description, price_per_night, capacity, area, image_url, is_featured, is_active, created_at, updated_at) VALUES (4, 2, 'Купольная сфера «Лесной Оазис»', 'dome-forest-oasis', 'Теплая сфера в глубине соснового бора. Минималистичный интерьер из светлого ясеня, домашний кинотеатр с проектором, теплый пол и уютная терраса с подвесными креслами.', 12000, 2, 36, 'images/houses/dome-2.jpg', FALSE, TRUE, '2026-11-15 15:09:43', '2026-11-15 15:09:43');
INSERT INTO houses (id, category_id, name, slug, description, price_per_night, capacity, area, image_url, is_featured, is_active, created_at, updated_at) VALUES (5, 3, 'Лесное Шале «Медвежий угол»', 'chalet-bear-corner', 'Премиальное шале из вековых бревен. Огромная гостиная со вторым светом, настоящий дровяной камин, полностью оборудованная кухня и собственная русская баня на территории.', 16500, 6, 85, 'images/houses/chalet-1.jpg', TRUE, TRUE, '2026-11-15 15:09:44', '2026-11-15 15:09:44');
INSERT INTO houses (id, category_id, name, slug, description, price_per_night, capacity, area, image_url, is_featured, is_active, created_at, updated_at) VALUES (6, 3, 'Лесное Шале «Береговой Уют»', 'chalet-coastal-cozy', 'Семейный деревянный коттедж с видом на тихий залив. Панорамная веранда с шезлонгами, финская сауна, зона гриля и свой мостик для рыбалки или прогулок на сапбордах.', 14000, 4, 70, 'images/houses/chalet-2.jpg', FALSE, TRUE, '2026-11-15 15:09:44', '2026-11-15 15:09:44');

SELECT setval('houses_id_seq', (SELECT COALESCE(MAX(id), 1) FROM houses));

-- 3. Данные таблицы house_amenity (29 записей)
INSERT INTO house_amenity (house_id, amenity_id) VALUES (1, 1);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (1, 5);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (1, 7);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (1, 8);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (1, 6);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (2, 1);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (2, 3);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (2, 5);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (2, 7);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (2, 6);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (3, 4);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (3, 5);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (3, 8);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (3, 7);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (4, 4);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (4, 5);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (4, 7);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (4, 6);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (5, 2);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (5, 3);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (5, 5);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (5, 7);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (5, 6);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (5, 8);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (6, 2);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (6, 5);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (6, 7);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (6, 6);
INSERT INTO house_amenity (house_id, amenity_id) VALUES (6, 8);

-- 3. Данные таблицы bookings (3 записей)
INSERT INTO bookings (id, user_id, house_id, check_in_date, check_out_date, guests_count, total_price, status, guest_name, guest_phone, comment, created_at, updated_at) VALUES (1, 3, 1, '2026-11-18 00:00:00', '2026-11-20 00:00:00', 2, 19000, 'confirmed', 'Анна Смирнова', '+7 (999) 333-44-55', 'Хотим протопить чан к нашему приезду к 18:00.', '2026-11-15 15:09:44', '2026-11-15 15:09:44');
INSERT INTO bookings (id, user_id, house_id, check_in_date, check_out_date, guests_count, total_price, status, guest_name, guest_phone, comment, created_at, updated_at) VALUES (2, 3, 3, '2026-11-25 00:00:00', '2026-11-27 00:00:00', 2, 27000, 'pending', 'Анна Смирнова', '+7 (999) 333-44-55', 'Годовщина свадьбы, требуется романтическое оформление.', '2026-11-15 15:09:44', '2026-11-15 15:09:44');
INSERT INTO bookings (id, user_id, house_id, check_in_date, check_out_date, guests_count, total_price, status, guest_name, guest_phone, comment, created_at, updated_at) VALUES (3, 4, 5, '2026-11-05 00:00:00', '2026-11-08 00:00:00', 5, 49500, 'completed', 'Михаил Кузнецов', '+7 (999) 444-55-66', 'Отдыхали семьей, всё прошло великолепно!', '2026-11-15 15:09:44', '2026-11-15 15:09:44');

SELECT setval('bookings_id_seq', (SELECT COALESCE(MAX(id), 1) FROM bookings));

-- 3. Данные таблицы reviews (2 записей)
INSERT INTO reviews (id, user_id, house_id, rating, content, is_published, created_at, updated_at) VALUES (1, 4, 5, 5, 'Невероятное место! Камин создавал неповторимую атмосферу, баня просто огонь. Обязательно вернемся зимой.', TRUE, '2026-11-15 15:09:44', '2026-11-15 15:09:44');
INSERT INTO reviews (id, user_id, house_id, rating, content, is_published, created_at, updated_at) VALUES (2, 3, 1, 5, 'Чан с видом на звездное небо и сосны — это лучшее, что случалось с нами за последнее время. Идеальная тишина.', TRUE, '2026-11-15 15:09:45', '2026-11-15 15:09:45');

SELECT setval('reviews_id_seq', (SELECT COALESCE(MAX(id), 1) FROM reviews));

-- 3. Данные таблицы migrations (9 записей)
INSERT INTO migrations (id, migration, batch) VALUES (1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO migrations (id, migration, batch) VALUES (2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO migrations (id, migration, batch) VALUES (3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO migrations (id, migration, batch) VALUES (4, '2026_09_27_182000_create_categories_table', 1);
INSERT INTO migrations (id, migration, batch) VALUES (5, '2026_09_27_182100_create_amenities_table', 1);
INSERT INTO migrations (id, migration, batch) VALUES (6, '2026_09_27_182200_create_houses_table', 1);
INSERT INTO migrations (id, migration, batch) VALUES (7, '2026_09_27_182300_create_house_amenity_table', 1);
INSERT INTO migrations (id, migration, batch) VALUES (8, '2026_09_27_182400_create_bookings_table', 1);
INSERT INTO migrations (id, migration, batch) VALUES (9, '2026_09_27_182500_create_reviews_table', 1);

SELECT setval('migrations_id_seq', (SELECT COALESCE(MAX(id), 1) FROM migrations));
