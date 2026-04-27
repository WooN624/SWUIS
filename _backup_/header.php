<?php
/**
 * header.php — Navigation พร้อมระบบสิทธิ์ 4 ระดับ
 */
require_once __DIR__ . '/auth.php';

$role         = get_role();
$display_name = $_SESSION['display_name'] ?? '';

$role_label = [
    'student' => ['label' => 'นิสิต',     'class' => 'badge-student'],
    'teacher' => ['label' => 'อาจารย์',   'class' => 'badge-teacher'],
    'admin'   => ['label' => 'แอดมิน',   'class' => 'badge-admin'],
][$role] ?? null;
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Information Studies - SWU</title>
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style_activity.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600;800&family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* สไตล์พื้นฐานของ Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 80px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            display: flex;
            align-items: center;
        }
        .user-info { display: flex; align-items: center; gap: 0.5rem; }
        .role-badge { display: inline-block; padding: 0.2rem 0.65rem; border-radius: 999px; font-size: 0.72rem; font-weight: 700; }
        .badge-student { background: #dbeafe; color: #1d4ed8; }
        .badge-teacher { background: #d1fae5; color: #065f46; }
        .badge-admin   { background: #fef3c7; color: #92400e; }
        .user-display-name { font-size: 0.85rem; color: #475569; font-weight: 600; }
        .logout-btn { padding: 0.3rem 0.9rem; background: #fee2e2; color: #b91c1c; border-radius: 0.5rem; text-decoration: none; font-size: 0.8rem; font-weight: 600; }
        
        /* Dropdown Menu Style */
        .nav-links { list-style: none; display: flex; gap: 20px; margin: 0; align-items: center; }
        .nav-links a { text-decoration: none; color: #333; font-family: 'Prompt', sans-serif; font-size: 0.95rem; }
        .dropdown { position: relative; }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #fff;
            min-width: 200px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.1);
            z-index: 1;
            list-style: none;
            padding: 10px 0;
            border-radius: 8px;
        }
        .dropdown:hover .dropdown-content { display: block; }
        .dropdown-content li a { padding: 8px 20px; display: block; }
        .dropdown-content li a:hover { background-color: #f1f1f1; }
    </style>
</head>
<body>
<header class="header">
    <div class="container container-nav" style="display: flex; justify-content: space-between; align-items: center; width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <a href="index.php" class="logo-group" style="text-decoration: none; display: flex; align-items: center; gap: 10px;">
            <img src="https://swu.ac.th/images/Srinakharinwirot_Logo.png" alt="SWU Logo" style="height: 50px;">
            <div class="logo-text">
                <h1 style="font-size: 1.2rem; color: #8a0000; margin: 0; font-family: 'Prompt';">INFORMATION STUDIES</h1>
                <p style="font-size: 0.8rem; color: #666; margin: 0;">Srinakharinwirot University</p>
            </div>
        </a>

        <nav>
            <ul class="nav-links">
                <li><a href="./aboutIS.php">เกี่ยวกับหลักสูตร</a></li>
                <li><a href="./staff.php">บุคลากร</a></li>
                
                <?php if ($role === 'guest'): ?>
                    <li><a href="./Internship.php">การฝึกงานและสหกิจศึกษา</a></li>
                <?php elseif ($role === 'student'): ?>
                    <li class="dropdown">
                        <a href="./Internship.php" class="dropbtn">การฝึกงานและสหกิจศึกษา <span class="arrow">▾</span></a>
                        <ul class="dropdown-content">
                            <li><a href="./form.php">ยื่นเรื่องฝึกงาน</a></li>
                            <li><a href="./student_list.php">ติดตามสถานะ</a></li>
                            <li><a href="./student_profile.php">ข้อมูลของฉัน</a></li>
                        </ul>
                    </li>
                <?php elseif ($role === 'teacher'): ?>
                    <li><a href="./T_dashboard.php">Teacher Dashboard</a></li>
                    <li><a href="./teacher_students.php">ข้อมูลนิสิต</a></li>
                <?php elseif ($role === 'admin'): ?>
                    <li><a href="./Internship.php">การฝึกงาน</a></li>
                    <li><a href="./staff_dashboard.php">Staff Dashboard</a></li>
                    <li><a href="./add_user.php">จัดการบัญชีผู้ใช้</a></li>
                <?php endif; ?>

                <li class="nav-item-user">
                    <a href="./ourteam.php" class="cute-user-btn"><i class="fa-solid fa-circle-user"></i></a>
                </li>
            </ul>
        </nav>

        <div class="login-section">
            <?php if ($role === 'guest'): ?>
                <a href="login.php" class="login-btn-circle" style="background: #8a0000; color: #fff; padding: 8px 20px; border-radius: 20px; text-decoration: none; font-family: 'Prompt';">Login</a>
            <?php else: ?>
                <div class="user-info">
                    <span class="role-badge <?= $role_label['class'] ?>"><?= $role_label['label'] ?></span>
                    <span class="user-display-name"><?= htmlspecialchars($display_name) ?></span>
                    <a href="logout.php" class="logout-btn">ออกจากระบบ</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>