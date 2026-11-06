-- ========================================================
-- Lemon Hotel Ma'lumotlar Bazasi Tuzilmasi (lemon_hotel)
-- ========================================================

CREATE DATABASE IF NOT EXISTS `lemon_hotel` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `lemon_hotel`;

-- 1. Buyurtmalar jadvali (Bookings)
CREATE TABLE IF NOT EXISTS `bookings` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Xabarlar jadvali (Messages)
CREATE TABLE IF NOT EXISTS `messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(100) NOT NULL,
  `subject` VARCHAR(200) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'yangi',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Xonalar katalogi (Rooms)
CREATE TABLE IF NOT EXISTS `rooms` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Admin foydalanuvchilar (Admins)
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dastlabki admin foydalanuvchi: Login: admin, Parol: admin123
INSERT INTO `admins` (`username`, `password`, `full_name`) 
VALUES ('admin', '$2y$10$vD3jW5T37Y3G06.V0w9c8.qM9vK7nZ8i1c6uO9i6c2x8u6j2q4yZe', 'Bosh Administrator')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Dastlabki xonalar ma'lumotlari
INSERT INTO `rooms` (`slug`, `name`, `price`, `capacity`, `size_sqm`, `image`, `description`, `amenities`, `is_available`)
VALUES 
('standard', 'Standart Qulay Xona', 380000, 2, 28, 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80', 'Yakkaxon yoki juftliklar uchun barcha qulayliklarga ega, sokin va shinam xona.', 'Tezkor Wi-Fi, Konditsioner, Smart TV, Dush & Fen, Issiq qahva/choy to\'plami', 1),
('deluxe', 'Deluxe Premium Xona', 580000, 2, 40, 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=800&q=80', 'Shahar manzarasi, keng derazalar va qo\'shimcha dam olish hududiga ega nafis xona.', 'Tezkor Wi-Fi, Shved stoli nonushtasi, King Size krovat, Mini-bar, Vanna & Dush, Xalat va shippaklar', 1),
('suite', 'Luxe Biznes Suite', 890000, 3, 58, 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80', 'Alohida mehmonxona zali va yotoqxonadan iborat, ishbilarmonlar va VIP mehmonlar uchun hashamatli xona.', 'VIP Nonushta, Ish stoli va Lounge zona, 4K Smart TV, Djakuzi vanna, 24/7 Room Service, Shaxsiy seyf', 1),
('family', 'Prezident Oilaviy Suite', 1400000, 4, 85, 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=800&q=80', 'Katta oilalar uchun ikkita alohida yotoqxona, panoramali balkon va eng oliy xizmat ko\'rsatish to\'plami.', '2 ta yotoqxona, Panoramali shahar manzarasi, Bepul aeroport transferi, VIP lounge va Spa kirish, To\'liq oshxona burchagi', 1)
ON DUPLICATE KEY UPDATE `id`=`id`;
