<?php
// send_message.php - Aloqa formasi xabarlarini saqlovchi API
header('Content-Type: application/json; charset=utf-8');
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Faqat POST so\'rovlari qabul qilinadi.']);
    exit;
}

$name    = isset($_POST['name']) ? trim($_POST['name']) : '';
$phone   = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$email   = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : 'Mijozdan murojaat';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Iltimos, ism, email va xabar matnini kiriting!']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Email manzili noto\'g\'ri kiritildi!']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO messages (name, phone, email, subject, message, status) 
                           VALUES (:name, :phone, :email, :subject, :message, 'yangi')");
    $stmt->execute([
        ':name'    => htmlspecialchars($name),
        ':phone'   => htmlspecialchars($phone),
        ':email'   => htmlspecialchars($email),
        ':subject' => htmlspecialchars($subject),
        ':message' => htmlspecialchars($message)
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Xabaringiz qabul qilindi! Tez orada mutaxassislarimiz sizga javob berishadi.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Xabarni yuborishda xatolik yuz berdi: ' . $e->getMessage()
    ]);
}
?>