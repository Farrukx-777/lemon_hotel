<?php
// admin.php - Lemon Hotel Administrator Boshqaruv Paneli
session_start();
require_once 'db_config.php';

// Tizimdan chiqish
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_user']);
    header('Location: admin.php');
    exit;
}

// Login formasi yuborilganda
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :u LIMIT 1");
        $stmt->execute([':u' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $admin['username'];
            $_SESSION['admin_name'] = $admin['full_name'];
            header('Location: admin.php');
            exit;
        } else {
            $login_error = 'Login yoki parol noto\'g\'ri!';
        }
    } else {
        $login_error = 'Barcha maydonlarni to\'ldiring!';
    }
}

// Agar tizimga kirmagan bo'lsa, login sahifasini ko'rsatamiz
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true):
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Kirish | Lemon Hotel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #ca8a04;
            --primary-hover: #a16207;
            --dark: #0f172a;
            --card-bg: #ffffff;
            --border: #e2e8f0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-box {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header .logo-icon {
            width: 56px;
            height: 56px;
            background: #fef08a;
            color: #854d0e;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 12px;
        }
        .login-header h2 {
            font-size: 24px;
            color: #0f172a;
            font-weight: 700;
        }
        .login-header p {
            color: #64748b;
            font-size: 14px;
            margin-top: 4px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
        }
        .input-wrap {
            position: relative;
        }
        .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        .input-wrap input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
        }
        .input-wrap input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(202, 138, 4, 0.15);
        }
        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #ca8a04;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 10px;
        }
        .btn-submit:hover {
            background: #a16207;
        }
        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .hint-box {
            margin-top: 24px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 8px;
            font-size: 12px;
            color: #64748b;
            text-align: center;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            font-size: 13px;
            color: #64748b;
            text-decoration: none;
        }
        .back-link:hover { color: #ca8a04; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-header">
            <div class="logo-icon"><i class="fa-solid fa-lemon"></i></div>
            <h2>Lemon Hotel</h2>
            <p>Admin Boshqaruv Paneliga Kirish</p>
        </div>

        <?php if (!empty($login_error)): ?>
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= htmlspecialchars($login_error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="admin.php">
            <input type="hidden" name="login_submit" value="1">
            <div class="form-group">
                <label for="username">Login</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" id="username" name="username" value="admin" required placeholder="admin">
                </div>
            </div>
            <div class="form-group">
                <label for="password">Parol</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password" name="password" value="admin123" required placeholder="••••••••">
                </div>
            </div>
            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-right-to-bracket" style="margin-right: 6px;"></i> Tizimga kirish
            </button>
        </form>

        <div class="hint-box">
            Sinov uchun standart ma'lumotlar:<br>
            <strong>Login:</strong> admin &nbsp;|&nbsp; <strong>Parol:</strong> admin123
        </div>

        <a href="index.html" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Asosiy veb-saytga qaytish
        </a>
    </div>
</body>
</html>
<?php
exit;
endif;

// ================= ADMIN AUTENTIFIKATSIYADAN O'TGAN HOLAT =================

// 1. Buyurtma statusini o'zgartirish (Tasdiqlash / Bekor qilish)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] === 'confirm_booking') {
        $pdo->prepare("UPDATE bookings SET status = 'tasdiqlandi' WHERE id = ?")->execute([$id]);
        header("Location: admin.php?tab=bookings&msg=confirmed");
        exit;
    }
    if ($_GET['action'] === 'cancel_booking') {
        $pdo->prepare("UPDATE bookings SET status = 'bekor_qilindi' WHERE id = ?")->execute([$id]);
        header("Location: admin.php?tab=bookings&msg=cancelled");
        exit;
    }
    if ($_GET['action'] === 'delete_booking') {
        $pdo->prepare("DELETE FROM bookings WHERE id = ?")->execute([$id]);
        header("Location: admin.php?tab=bookings&msg=deleted");
        exit;
    }
    if ($_GET['action'] === 'read_message') {
        $pdo->prepare("UPDATE messages SET status = 'oqildi' WHERE id = ?")->execute([$id]);
        header("Location: admin.php?tab=messages&msg=marked");
        exit;
    }
    if ($_GET['action'] === 'delete_message') {
        $pdo->prepare("DELETE FROM messages WHERE id = ?")->execute([$id]);
        header("Location: admin.php?tab=messages&msg=deleted");
        exit;
    }
    if ($_GET['action'] === 'toggle_room') {
        $pdo->prepare("UPDATE rooms SET is_available = CASE WHEN is_available = 1 THEN 0 ELSE 1 END WHERE id = ?")->execute([$id]);
        header("Location: admin.php?tab=rooms&msg=room_updated");
        exit;
    }
}

// Xona narxini yangilash
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_room_price'])) {
    $room_id = intval($_POST['room_id']);
    $new_price = intval($_POST['new_price']);
    if ($room_id > 0 && $new_price > 0) {
        $pdo->prepare("UPDATE rooms SET price = ? WHERE id = ?")->execute([$new_price, $room_id]);
        header("Location: admin.php?tab=rooms&msg=price_saved");
        exit;
    }
}

// Statistikalarni hisoblash
$total_bookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$pending_bookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'kutilmoqda'")->fetchColumn();
$confirmed_bookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'tasdiqlandi'")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(total_price) FROM bookings WHERE status = 'tasdiqlandi'")->fetchColumn() ?: 0;

$total_messages = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
$unread_messages = $pdo->query("SELECT COUNT(*) FROM messages WHERE status = 'yangi'")->fetchColumn();

// Ma'lumotlarni yuklash
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'bookings';
$bookings = $pdo->query("SELECT * FROM bookings ORDER BY id DESC")->fetchAll();
$messages = $pdo->query("SELECT * FROM messages ORDER BY id DESC")->fetchAll();
$rooms = $pdo->query("SELECT * FROM rooms ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Lemon Hotel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #ca8a04;
            --primary-light: #fef08a;
            --dark: #0f172a;
            --sidebar-bg: #1e293b;
            --body-bg: #f8fafc;
            --card-bg: #ffffff;
            --border: #e2e8f0;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --success: #16a34a;
            --danger: #dc2626;
            --warning: #ea580c;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: var(--body-bg); color: var(--text-dark); display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            color: #fff;
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            transition: all 0.3s;
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 24px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 24px;
        }
        .brand-icon {
            width: 42px;
            height: 42px;
            background: var(--primary);
            color: #fff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .brand-text h2 { font-size: 18px; font-weight: 700; letter-spacing: -0.5px; }
        .brand-text span { font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }

        .nav-menu { list-style: none; display: flex; flex-direction: column; gap: 6px; flex-grow: 1; }
        .nav-item a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .nav-item a:hover { background: rgba(255,255,255,0.06); color: #fff; }
        .nav-item.active a { background: var(--primary); color: #fff; font-weight: 600; }
        .nav-badge {
            margin-left: auto;
            background: rgba(255,255,255,0.2);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
        }
        .nav-badge.danger { background: #ef4444; }

        .sidebar-footer {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .user-info { display: flex; align-items: center; gap: 10px; }
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #334155;
            color: #ca8a04;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }
        .user-meta h4 { font-size: 13px; font-weight: 600; color: #fff; }
        .user-meta span { font-size: 11px; color: #94a3b8; }
        .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px;
            color: #f87171;
            background: rgba(239, 68, 68, 0.1);
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: 0.2s;
        }
        .btn-logout:hover { background: #ef4444; color: #fff; }

        /* Main Content */
        .main-wrapper { flex-grow: 1; display: flex; flex-direction: column; overflow-y: auto; height: 100vh; }
        .topbar {
            background: #fff;
            padding: 16px 32px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .topbar h1 { font-size: 20px; font-weight: 700; }
        .site-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            transition: 0.2s;
        }
        .site-link:hover { background: #f1f5f9; color: var(--text-dark); }

        .content-area { padding: 32px; }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .stat-icon.primary { background: #fef08a; color: #854d0e; }
        .stat-icon.warning { background: #ffedd5; color: #c2410c; }
        .stat-icon.success { background: #dcfce7; color: #15803d; }
        .stat-icon.info { background: #e0f2fe; color: #0369a1; }
        .stat-info h3 { font-size: 22px; font-weight: 700; }
        .stat-info p { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

        /* Table Card */
        .table-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-header h2 { font-size: 16px; font-weight: 700; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th {
            background: #f8fafc;
            padding: 14px 20px;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border);
        }
        td {
            padding: 16px 20px;
            font-size: 13px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: #fafafa; }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-kutilmoqda { background: #fef3c7; color: #b45309; }
        .badge-tasdiqlandi { background: #dcfce7; color: #15803d; }
        .badge-bekor_qilindi { background: #fee2e2; color: #b91c1c; }
        .badge-yangi { background: #dbeafe; color: #1e40af; }
        .badge-oqildi { background: #f1f5f9; color: #64748b; }

        /* Action buttons */
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            transition: 0.2s;
            border: 1px solid transparent;
        }
        .btn-action.btn-confirm { background: #dcfce7; color: #15803d; }
        .btn-action.btn-confirm:hover { background: #15803d; color: #fff; }
        .btn-action.btn-cancel { background: #fef3c7; color: #b45309; }
        .btn-action.btn-cancel:hover { background: #b45309; color: #fff; }
        .btn-action.btn-delete { background: #fee2e2; color: #b91c1c; }
        .btn-action.btn-delete:hover { background: #b91c1c; color: #fff; }
        .btn-action.btn-read { background: #e0f2fe; color: #0369a1; }
        .btn-action.btn-read:hover { background: #0369a1; color: #fff; }

        /* Rooms Card Grid */
        .rooms-admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
            padding: 24px;
        }
        .room-admin-card {
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
        }
        .room-admin-card img { width: 100%; height: 160px; object-fit: cover; }
        .room-admin-body { padding: 16px; }
        .room-admin-body h3 { font-size: 16px; margin-bottom: 6px; }
        .room-admin-body p { font-size: 12px; color: var(--text-muted); margin-bottom: 12px; }
        .price-form { display: flex; gap: 8px; margin-top: 12px; }
        .price-form input { flex-grow: 1; padding: 6px 10px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; }
        .price-form button { padding: 6px 12px; background: var(--primary); color: #fff; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; }

        @media (max-width: 992px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; height: auto; }
            .main-wrapper { height: auto; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="fa-solid fa-lemon"></i></div>
            <div class="brand-text">
                <h2>Lemon Hotel</h2>
                <span>Boshqaruv Paneli</span>
            </div>
        </div>

        <ul class="nav-menu">
            <li class="nav-item <?= $active_tab === 'bookings' ? 'active' : '' ?>">
                <a href="admin.php?tab=bookings">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>Buyurtmalar</span>
                    <?php if ($pending_bookings > 0): ?>
                        <span class="nav-badge danger"><?= $pending_bookings ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item <?= $active_tab === 'messages' ? 'active' : '' ?>">
                <a href="admin.php?tab=messages">
                    <i class="fa-solid fa-envelope"></i>
                    <span>Xabarlar</span>
                    <?php if ($unread_messages > 0): ?>
                        <span class="nav-badge"><?= $unread_messages ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item <?= $active_tab === 'rooms' ? 'active' : '' ?>">
                <a href="admin.php?tab=rooms">
                    <i class="fa-solid fa-bed"></i>
                    <span>Xonalar & Narxlar</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar"><i class="fa-solid fa-user-shield"></i></div>
                <div class="user-meta">
                    <h4><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></h4>
                    <span>Bosh boshqaruvchi</span>
                </div>
            </div>
            <a href="admin.php?action=logout" class="btn-logout">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Chiqish
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-wrapper">
        <div class="topbar">
            <h1>
                <?php
                    if ($active_tab === 'bookings') echo 'Xona buyurtmalari (Bookings)';
                    elseif ($active_tab === 'messages') echo 'Mijozlar xabarlari (Messages)';
                    else echo 'Xonalar va Narxlar boshqaruvi';
                ?>
            </h1>
            <a href="index.html" target="_blank" class="site-link">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Asosiy saytni ochish
            </a>
        </div>

        <div class="content-area">
            <!-- Statistika kartochkalari -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary"><i class="fa-solid fa-calendar-days"></i></div>
                    <div class="stat-info">
                        <h3><?= $total_bookings ?> ta</h3>
                        <p>Jami buyurtmalar</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon warning"><i class="fa-solid fa-clock"></i></div>
                    <div class="stat-info">
                        <h3><?= $pending_bookings ?> ta</h3>
                        <p>Kutilayotgan bronlar</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon success"><i class="fa-solid fa-circle-check"></i></div>
                    <div class="stat-info">
                        <h3><?= $confirmed_bookings ?> ta</h3>
                        <p>Tasdiqlangan bronlar</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon info"><i class="fa-solid fa-money-bill-wave"></i></div>
                    <div class="stat-info">
                        <h3><?= number_format($total_revenue, 0, '', ' ') ?> so'm</h3>
                        <p>Tasdiqlangan tushum</p>
                    </div>
                </div>
            </div>

            <!-- TAB 1: BUYURTMALAR -->
            <?php if ($active_tab === 'bookings'): ?>
            <div class="table-card">
                <div class="card-header">
                    <h2>Barcha kelib tushgan buyurtmalar (<?= count($bookings) ?>)</h2>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Kodi & Vaqti</th>
                                <th>Mijoz ma'lumotlari</th>
                                <th>Xona turi</th>
                                <th>Kelish - Ketish</th>
                                <th>Mehmonlar</th>
                                <th>Jami narx</th>
                                <th>Status</th>
                                <th>Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($bookings) === 0): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; color: #94a3b8; padding: 40px;">
                                        <i class="fa-regular fa-folder-open" style="font-size: 32px; margin-bottom: 10px; display: block;"></i>
                                        Hozircha hech qanday buyurtma kelib tushmagan.
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($b['booking_code'] ?? ('#' . $b['id'])) ?></strong><br>
                                    <small style="color: #94a3b8;"><?= date('d.m.Y H:i', strtotime($b['created_at'])) ?></small>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($b['full_name']) ?></strong><br>
                                    <span style="color: #64748b;"><i class="fa-solid fa-phone" style="font-size: 11px;"></i> <?= htmlspecialchars($b['phone'] ?? '-') ?></span>
                                    <?php if (!empty($b['email'])): ?>
                                        <br><span style="color: #94a3b8; font-size: 11px;"><?= htmlspecialchars($b['email']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="font-weight: 600; text-transform: capitalize;"><?= htmlspecialchars($b['room_type']) ?></span>
                                </td>
                                <td>
                                    <span style="color: #059669; font-weight: 600;"><?= $b['check_in'] ?></span> &rarr; 
                                    <span style="color: #dc2626; font-weight: 600;"><?= $b['check_out'] ?></span>
                                </td>
                                <td><?= $b['guests_count'] ?> kishi</td>
                                <td><strong><?= number_format($b['total_price'], 0, '', ' ') ?> so'm</strong></td>
                                <td>
                                    <span class="badge badge-<?= $b['status'] ?>">
                                        <?= $b['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                        <?php if ($b['status'] !== 'tasdiqlandi'): ?>
                                            <a href="admin.php?action=confirm_booking&id=<?= $b['id'] ?>" class="btn-action btn-confirm" title="Tasdiqlash">
                                                <i class="fa-solid fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($b['status'] !== 'bekor_qilindi'): ?>
                                            <a href="admin.php?action=cancel_booking&id=<?= $b['id'] ?>" class="btn-action btn-cancel" title="Bekor qilish">
                                                <i class="fa-solid fa-ban"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="admin.php?action=delete_booking&id=<?= $b['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Ushbu buyurtmani rostdan ham o\'chirmoqchimisiz?')" title="O'chirish">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- TAB 2: XABARLAR -->
            <?php if ($active_tab === 'messages'): ?>
            <div class="table-card">
                <div class="card-header">
                    <h2>Kelib tushgan murojaatlar (<?= count($messages) ?>)</h2>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Yuboruvchi</th>
                                <th>Aloqa</th>
                                <th>Mavzu & Xabar</th>
                                <th>Vaqti</th>
                                <th>Status</th>
                                <th>Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($messages) === 0): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; color: #94a3b8; padding: 40px;">
                                        Hozircha hech qanday xabar kelib tushmagan.
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($messages as $m): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($m['name']) ?></strong></td>
                                <td>
                                    <?= htmlspecialchars($m['email']) ?><br>
                                    <small style="color: #64748b;"><?= htmlspecialchars($m['phone'] ?? '-') ?></small>
                                </td>
                                <td style="max-width: 320px;">
                                    <?php if (!empty($m['subject'])): ?>
                                        <strong style="font-size: 12px; color: #334155;"><?= htmlspecialchars($m['subject']) ?></strong><br>
                                    <?php endif; ?>
                                    <p style="color: #475569; font-size: 13px; line-height: 1.4;"><?= nl2br(htmlspecialchars($m['message'])) ?></p>
                                </td>
                                <td><small style="color: #94a3b8;"><?= date('d.m.Y H:i', strtotime($m['created_at'])) ?></small></td>
                                <td><span class="badge badge-<?= $m['status'] ?>"><?= $m['status'] ?></span></td>
                                <td>
                                    <div style="display: flex; gap: 6px;">
                                        <?php if ($m['status'] === 'yangi'): ?>
                                            <a href="admin.php?action=read_message&id=<?= $m['id'] ?>" class="btn-action btn-read" title="O'qildi deb belgilash">
                                                <i class="fa-solid fa-envelope-open"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="admin.php?action=delete_message&id=<?= $m['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Xabarni o\'chirmoqchimisiz?')" title="O'chirish">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- TAB 3: XONALAR VA NARXLAR -->
            <?php if ($active_tab === 'rooms'): ?>
            <div class="table-card">
                <div class="card-header">
                    <h2>Xonalar katalogi va narxlarini boshqarish</h2>
                </div>
                <div class="rooms-admin-grid">
                    <?php foreach ($rooms as $r): ?>
                    <div class="room-admin-card">
                        <img src="<?= htmlspecialchars($r['image']) ?>" alt="<?= htmlspecialchars($r['name']) ?>">
                        <div class="room-admin-body">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <h3><?= htmlspecialchars($r['name']) ?></h3>
                                <a href="admin.php?action=toggle_room&id=<?= $r['id'] ?>" class="badge badge-<?= $r['is_available'] ? 'tasdiqlandi' : 'bekor_qilindi' ?>" style="text-decoration: none;">
                                    <?= $r['is_available'] ? 'Mavjud' : 'Band' ?>
                                </a>
                            </div>
                            <p><?= htmlspecialchars($r['description']) ?></p>
                            <div style="font-size: 12px; color: #64748b; margin-bottom: 8px;">
                                <i class="fa-solid fa-user-group"></i> <?= $r['capacity'] ?> kishilik &nbsp;|&nbsp; 
                                <i class="fa-solid fa-ruler-combined"></i> <?= $r['size_sqm'] ?> m²
                            </div>
                            <form method="POST" action="admin.php" class="price-form">
                                <input type="hidden" name="update_room_price" value="1">
                                <input type="hidden" name="room_id" value="<?= $r['id'] ?>">
                                <input type="number" name="new_price" value="<?= $r['price'] ?>" step="10000" required>
                                <button type="submit"><i class="fa-solid fa-floppy-disk"></i> Saqlash</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </main>
</body>
</html>