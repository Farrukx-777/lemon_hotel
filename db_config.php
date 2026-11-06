<?php
// db_config.php - Ma'lumotlar bazasiga xavfsiz va universal ulanish fayli
// MySQL birinchi o'rinda tekshiriladi, agar u yoqilmagan bo'lsa, SQLite lokal bazasiga avtomatik o'tadi.

$mysql_host = 'localhost';
$mysql_db   = 'lemon_hotel';
$mysql_user = 'root';
$mysql_pass = '';

$pdo = null;
$db_driver = 'mysql';

// 1. MySQL ga ulanishga urinish
try {
    // Avval serverga ulanib, baza mavjud bo'lmasa uni yaratamiz
    $init_pdo = new PDO("mysql:host=$mysql_host;charset=utf8mb4", $mysql_user, $mysql_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 2
    ]);
    $init_pdo->exec("CREATE DATABASE IF NOT EXISTS `$mysql_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    // Bazaga ulanamiz
    $pdo = new PDO("mysql:host=$mysql_host;dbname=$mysql_db;charset=utf8mb4", $mysql_user, $mysql_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    $db_driver = 'mysql';
} catch (Exception $e) {
    // Agar MySQL serveri ishlamayotgan bo'lsa, sinov uchun SQLite faylli bazadan foydalanamiz
    try {
        $sqlite_file = __DIR__ . DIRECTORY_SEPARATOR . 'lemon_hotel.db';
        $pdo = new PDO("sqlite:" . $sqlite_file, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        $db_driver = 'sqlite';
    } catch (Exception $sqle) {
        die(json_encode(['status' => 'error', 'message' => 'Ma\'lumotlar bazasiga ulanib bo\'lmadi: ' . $sqle->getMessage()]));
    }
}

// 2. Jadvallarni avtomatik tekshirish va yaratish (Birinchi marta ochilganda o'zi tayyor bo'ladi)
function initHotelDatabase($pdo, $driver) {
    if ($driver === 'mysql') {
        // Bookings jadvali
        $pdo->exec("CREATE TABLE IF NOT EXISTS `bookings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `booking_code` VARCHAR(20) NOT NULL UNIQUE,
            `full_name` VARCHAR(150) NOT NULL,
            `phone` VARCHAR(50) NOT NULL,
            `email` VARCHAR(100) DEFAULT NULL,
            `room_type` VARCHAR(50) NOT NULL,
            `check_in` DATE NOT NULL,
            `check_out` DATE NOT NULL,
            `guests_count` INT NOT NULL DEFAULT 1,
            `total_price` DECIMAL(12,2) NOT NULL DEFAULT 0,
            `special_requests` TEXT DEFAULT NULL,
            `status` VARCHAR(20) NOT NULL DEFAULT 'kutilmoqda',
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Messages jadvali
        $pdo->exec("CREATE TABLE IF NOT EXISTS `messages` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(150) NOT NULL,
            `phone` VARCHAR(50) DEFAULT NULL,
            `email` VARCHAR(100) NOT NULL,
            `subject` VARCHAR(200) DEFAULT NULL,
            `message` TEXT NOT NULL,
            `status` VARCHAR(20) NOT NULL DEFAULT 'yangi',
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Rooms jadvali
        $pdo->exec("CREATE TABLE IF NOT EXISTS `rooms` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `slug` VARCHAR(50) NOT NULL UNIQUE,
            `name` VARCHAR(100) NOT NULL,
            `price` INT NOT NULL,
            `capacity` INT NOT NULL DEFAULT 2,
            `size_sqm` INT NOT NULL DEFAULT 25,
            `image` VARCHAR(255) NOT NULL,
            `description` TEXT NOT NULL,
            `amenities` TEXT NOT NULL,
            `is_available` TINYINT(1) NOT NULL DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Admins jadvali
        $pdo->exec("CREATE TABLE IF NOT EXISTS `admins` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `full_name` VARCHAR(100) NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    } else {
        // SQLite sintaksisi
        $pdo->exec("CREATE TABLE IF NOT EXISTS bookings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            booking_code TEXT NOT NULL UNIQUE,
            full_name TEXT NOT NULL,
            phone TEXT NOT NULL,
            email TEXT,
            room_type TEXT NOT NULL,
            check_in TEXT NOT NULL,
            check_out TEXT NOT NULL,
            guests_count INTEGER NOT NULL DEFAULT 1,
            total_price REAL NOT NULL DEFAULT 0,
            special_requests TEXT,
            status TEXT NOT NULL DEFAULT 'kutilmoqda',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            phone TEXT,
            email TEXT NOT NULL,
            subject TEXT,
            message TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT 'yangi',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS rooms (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            slug TEXT NOT NULL UNIQUE,
            name TEXT NOT NULL,
            price INTEGER NOT NULL,
            capacity INTEGER NOT NULL DEFAULT 2,
            size_sqm INTEGER NOT NULL DEFAULT 25,
            image TEXT NOT NULL,
            description TEXT NOT NULL,
            amenities TEXT NOT NULL,
            is_available INTEGER NOT NULL DEFAULT 1
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            full_name TEXT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        );");
    }

    // Standart admin foydalanuvchisini qo'shish (Login: admin, Parol: admin123)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $insert = $pdo->prepare("INSERT INTO admins (username, password, full_name) VALUES ('admin', :pass, 'Bosh Administrator')");
        $insert->execute([':pass' => $hash]);
    }

    // Boshlang'ich xonalar ma'lumotlarini to'ldirish
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM rooms");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $rooms = [
            [
                'slug' => 'standard',
                'name' => 'Standart Qulay Xona',
                'price' => 380000,
                'capacity' => 2,
                'size_sqm' => 28,
                'image' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',
                'description' => 'Yakkaxon yoki juftliklar uchun barcha qulayliklarga ega, sokin va shinam xona.',
                'amenities' => 'Tezkor Wi-Fi, Konditsioner, Smart TV, Dush & Fen, Issiq qahva/choy to\'plami'
            ],
            [
                'slug' => 'deluxe',
                'name' => 'Deluxe Premium Xona',
                'price' => 580000,
                'capacity' => 2,
                'size_sqm' => 40,
                'image' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=800&q=80',
                'description' => 'Shahar manzarasi, keng derazalar va qo\'shimcha dam olish hududiga ega nafis xona.',
                'amenities' => 'Tezkor Wi-Fi, Shved stoli nonushtasi, King Size krovat, Mini-bar, Vanna & Dush, Xalat va shippaklar'
            ],
            [
                'slug' => 'suite',
                'name' => 'Luxe Biznes Suite',
                'price' => 890000,
                'capacity' => 3,
                'size_sqm' => 58,
                'image' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
                'description' => 'Alohida mehmonxona zali va yotoqxonadan iborat, ishbilarmonlar va VIP mehmonlar uchun hashamatli xona.',
                'amenities' => 'VIP Nonushta, Ish stoli va Lounge zona, 4K Smart TV, Djakuzi vanna, 24/7 Room Service, Shaxsiy seyf'
            ],
            [
                'slug' => 'family',
                'name' => 'Prezident Oilaviy Suite',
                'price' => 1400000,
                'capacity' => 4,
                'size_sqm' => 85,
                'image' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=800&q=80',
                'description' => 'Katta oilalar uchun ikkita alohida yotoqxona, panoramali balkon va eng oliy xizmat ko\'rsatish to\'plami.',
                'amenities' => '2 ta yotoqxona, Panoramali shahar manzarasi, Bepul aeroport transferi, VIP lounge va Spa kirish, To\'liq oshxona burchagi'
            ]
        ];

        $ins = $pdo->prepare("INSERT INTO rooms (slug, name, price, capacity, size_sqm, image, description, amenities, is_available) 
                              VALUES (:slug, :name, :price, :capacity, :size_sqm, :image, :description, :amenities, 1)");
        foreach ($rooms as $r) {
            $ins->execute([
                ':slug' => $r['slug'],
                ':name' => $r['name'],
                ':price' => $r['price'],
                ':capacity' => $r['capacity'],
                ':size_sqm' => $r['size_sqm'],
                ':image' => $r['image'],
                ':description' => $r['description'],
                ':amenities' => $r['amenities']
            ]);
        }
    }
}

// Baza strukturasini kafolatli yuklash
initHotelDatabase($pdo, $db_driver);
?>