<?php
// save_booking.php - Xona bronlarini qabul qiluvchi va saqlovchi API
header('Content-Type: application/json; charset=utf-8');
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Faqat POST so\'rovlari qabul qilinadi.']);
    exit;
}

// Formadan kelgan ma'lumotlarni qabul qilish
$full_name        = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$phone            = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$email            = isset($_POST['email']) ? trim($_POST['email']) : '';
$room_type        = isset($_POST['room_type']) ? trim($_POST['room_type']) : '';
$check_in         = isset($_POST['check_in']) ? trim($_POST['check_in']) : '';
$check_out        = isset($_POST['check_out']) ? trim($_POST['check_out']) : '';
$guests_count     = isset($_POST['guests_count']) ? intval($_POST['guests_count']) : 1;
$special_requests = isset($_POST['special_requests']) ? trim($_POST['special_requests']) : '';

// Maydonlarni tekshirish
if (empty($full_name) || empty($phone) || empty($room_type) || empty($check_in) || empty($check_out)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Iltimos, barcha majburiy maydonlarni (Ism, Telefon, Xona, Sanalar) to\'ldiring!'
    ]);
    exit;
}

// Sanalarni tekshirish
$in_timestamp  = strtotime($check_in);
$out_timestamp = strtotime($check_out);

if (!$in_timestamp || !$out_timestamp) {
    echo json_encode(['status' => 'error', 'message' => 'Sanalar noto\'g\'ri formatda kiritildi!']);
    exit;
}

$today = strtotime(date('Y-m-d'));
if ($in_timestamp < $today) {
    echo json_encode(['status' => 'error', 'message' => 'Kelish sanasi o\'tib ketgan kun bo\'lishi mumkin emas!']);
    exit;
}

if ($out_timestamp <= $in_timestamp) {
    echo json_encode(['status' => 'error', 'message' => 'Ketish sanasi kelish sanasidan kamida 1 kun keyin bo\'lishi kerak!']);
    exit;
}

// Kunlar sonini hisoblash
$nights = round(($out_timestamp - $in_timestamp) / (60 * 60 * 24));
if ($nights < 1) $nights = 1;

// Xona narxini bazadan aniqlash
$room_stmt = $pdo->prepare("SELECT name, price FROM rooms WHERE slug = :slug LIMIT 1");
$room_stmt->execute([':slug' => $room_type]);
$room = $room_stmt->fetch();

$price_per_night = $room ? intval($room['price']) : 450000;
$room_name = $room ? $room['name'] : ucfirst($room_type);
$total_price = $nights * $price_per_night;

// Unikal buyurtma kodi yaratish (masalan: LH-74829)
$booking_code = 'LH-' . strtoupper(substr(uniqid(), -5));

try {
    $stmt = $pdo->prepare("INSERT INTO bookings (
        booking_code, full_name, phone, email, room_type, check_in, check_out, guests_count, total_price, special_requests, status
    ) VALUES (
        :booking_code, :full_name, :phone, :email, :room_type, :check_in, :check_out, :guests_count, :total_price, :special_requests, 'kutilmoqda'
    )");

    $stmt->execute([
        ':booking_code'     => $booking_code,
        ':full_name'        => htmlspecialchars($full_name),
        ':phone'            => htmlspecialchars($phone),
        ':email'            => htmlspecialchars($email),
        ':room_type'        => htmlspecialchars($room_type),
        ':check_in'         => $check_in,
        ':check_out'        => $check_out,
        ':guests_count'     => $guests_count,
        ':total_price'      => $total_price,
        ':special_requests' => htmlspecialchars($special_requests)
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Xona muvaffaqiyatli bron qilindi! Tez orada xodimlarimiz siz bilan bog\'lanishadi.',
        'data' => [
            'booking_code' => $booking_code,
            'full_name'    => $full_name,
            'phone'        => $phone,
            'room_name'    => $room_name,
            'check_in'     => $check_in,
            'check_out'    => $check_out,
            'nights'       => $nights,
            'guests'       => $guests_count,
            'total_price'  => number_format($total_price, 0, '', ' ') . " so'm"
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Buyurtmani saqlashda xatolik yuz berdi: ' . $e->getMessage()
    ]);
}
?>