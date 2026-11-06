# Lemon Hotel — Zamonaviy Mehmonxona Veb-Sayti va Boshqaruv Tizimi

Ushbu loyiha zamonaviy 5 yulduzli mehmonxona uchun to'liq qayta ishlab chiqilgan, mobil qurilmalarga 100% moslashuvchan (responsive) veb-sayt va ma'lumotlar bazasiga ulangan administrator boshqaruv tizimidir.

---

## 🌟 Asosiy Imkoniyatlar

1. **Hashamatli va Zamonaviy Dizayn:**
   - Oliy toifadagi mehmonxona talablariga mos premium interfeys (HTML5, zamonaviy CSS3 Flexbox/Grid, mikromobil animatsiyalar).
   - Smartfon, planshet, noutbuk va katta monitorlarga to'liq moslashuvchanlik.
   - Sifatli va tezkor fotosuratlar (Unsplash CDN).

2. **Onlayn Xona Bron Qilish (Booking Engine):**
   - Bosh sahifada tezkor bron qidirish paneli.
   - Tanlangan xona turi va sanalarga qarab umumiy summani **real-vaqtda avtomatik hisoblash**.
   - O'tib ketgan sanalarni tanlashni cheklovchi xavfsiz taqvim.
   - Har bir buyurtma uchun unikal buyurtma kodi yaratish (masalan: `LH-82914`).
   - Ma'lumotlarni sahifani yangilamasdan (AJAX orqali) bazaga yozish va xaridorni chiroyli modal bilan xabardor qilish.

3. **Aloqa va Murojaatlar:**
   - Interaktiv Google Xarita.
   - Telefon, email va manzil bloklari.
   - Onlayn murojaat formasi (to'g'ridan-to'g'ri bazaga saqlanadi).

4. **Xavfsiz va Qulay Administrator Paneli (`admin.php`):**
   - **Login/Parol bilan himoyalangan:** Standart kirish ma'lumotlari:
     - **Login:** `admin`
     - **Parol:** `admin123`
   - **Boshqaruv vositalari:**
     - Kelib tushgan buyurtmalarni ko'rish, bir klikda **"Tasdiqlash"** yoki **"Bekor qilish"**, o'chirish.
     - Mijozlardan kelgan xabarlarni o'qish va boshqarish.
     - Xonalar narxlarini boshqaruv panelidan to'g'ridan-to'g'ri o'zgartirish va xonani vaqtincha "Band" yoki "Mavjud" qilib belgilash.
     - Asosiy ko'rsatkichlar statistikasi (Jami buyurtmalar, kutilayotgan bronlar, tasdiqlangan summa va yangi xabarlar).

5. **Universal Ma'lumotlar Bazasi:**
   - MySQL avtomatik aniqlanadi va `lemon_hotel` bazasi hamda barcha jadvallar (`bookings`, `messages`, `rooms`, `admins`) birinchi kirishdayoq o'zi avtomatik yaratiladi.
   - Agar kompyuterda MySQL hali yoqilmagan bo'lsa, sinovdan o'tkazish to'xtab qolmasligi uchun tizim avtomatik ravishda lokal `SQLite` bazasidan foydalanadi (hech qanday xatoliksiz ishlaydi).
   - Qo'lda import qilishni xohlovchilar uchun `database.sql` fayli ham taqdim etilgan.

---

## 📁 Loyiha Fayllari Tuzilishi

- [`index.html`](file:///c:/Users/amir23/Desktop/lemon_hotel-main/index.html) — Asosiy bosh sahifa (Hero, tezkor bron, xonalar, xizmatlar, sharhlar, FAQ)
- [`rooms.html`](file:///c:/Users/amir23/Desktop/lemon_hotel-main/rooms.html) — Barcha xonalar katalogi, qulayliklar va narxlar
- [`about.html`](file:///c:/Users/amir23/Desktop/lemon_hotel-main/about.html) — Mehmonxona tarixi, missiyasi va qadriyatlari
- [`booking.html`](file:///c:/Users/amir23/Desktop/lemon_hotel-main/booking.html) — Onlayn xona bron qilish sahifasi (kalkulyator bilan)
- [`contact.html`](file:///c:/Users/amir23/Desktop/lemon_hotel-main/contact.html) — Aloqa ma'lumotlari, xarita va qayta aloqa formasi
- [`style.css`](file:///c:/Users/amir23/Desktop/lemon_hotel-main/style.css) — Barcha sahifalar uchun zamonaviy, yagona CSS uslublari
- [`main.js`](file:///c:/Users/amir23/Desktop/lemon_hotel-main/main.js) — Interaktiv skriptlar (kalkulyator, AJAX, mobil menyu, FAQ)
- [`admin.php`](file:///c:/Users/amir23/Desktop/lemon_hotel-main/admin.php) — Administrator boshqaruv paneli
- [`db_config.php`](file:///c:/Users/amir23/Desktop/lemon_hotel-main/db_config.php) — Baza sozlamalari va jadvallarni avto-initsializatsiya qilish
- [`save_booking.php`](file:///c:/Users/amir23/Desktop/lemon_hotel-main/save_booking.php) — Bron buyurtmalarini saqlovchi API
- [`send_message.php`](file:///c:/Users/amir23/Desktop/lemon_hotel-main/send_message.php) — Xabarlarni saqlovchi API
- [`database.sql`](file:///c:/Users/amir23/Desktop/lemon_hotel-main/database.sql) — MySQL ma'lumotlar bazasi eksporti

---

## 🚀 Qanday Ishga Tushirish Mumkin?

### 1-usul: XAMPP / Laragon / OpenServer orqali
1. Ushbu loyiha papkasini `C:\xampp\htdocs\lemon_hotel` yoki `C:\laragon\www\lemon_hotel` ichiga joylashtiring.
2. Apache va MySQL xizmatlarini ishga tushiring.
3. Brauzerda oching:
   - Sayt: `http://localhost/lemon_hotel/index.html`
   - Admin panel: `http://localhost/lemon_hotel/admin.php`

### 2-usul: PHP o'rnatilgan bo'lsa (Tezkor buyruq orqali)
Loyiha papkasida terminalni ochib quyidagi buyruqni bering:
```bash
php -S localhost:8000
```
Brauzerda: `http://localhost:8000` manziliga kiring.

---

## 🔑 Admin Panelga Kirish
- **Manzil:** `admin.php`
- **Login:** `admin`
- **Parol:** `admin123`
*(Tizimga kirgach, barcha buyurtmalar, xabarlar va xona narxlarini to'liq nazorat qila olasiz).*
