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
    
    <link rel="stylesheet" href="/SWUIS/assets/css/style.css">
    <link rel="stylesheet" href="/SWUIS/assets/css/style_activity.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600;800&family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .header {
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            height: 70px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            display: flex;
            align-items: center;
        }
        .login-section { display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0; margin-left: auto; }
        .user-info { display: flex; align-items: center; gap: 0.5rem; white-space: nowrap; }
        .role-badge { display: inline-block; padding: 0.2rem 0.65rem; border-radius: 999px; font-size: 0.72rem; font-weight: 700; white-space: nowrap; }
        .badge-student { background: #dbeafe; color: #1d4ed8; }
        .badge-teacher { background: #d1fae5; color: #065f46; }
        .badge-admin   { background: #fef3c7; color: #92400e; }
        .user-display-name { font-size: 0.85rem; color: #475569; font-weight: 600; white-space: nowrap; }
        .logout-btn { padding: 0.3rem 0.9rem; background: #fee2e2; color: #b91c1c; border-radius: 0.5rem; text-decoration: none; font-size: 0.8rem; font-weight: 600; white-space: nowrap; }

        .nav-links { list-style: none; display: flex; gap: 5px; margin: 0; align-items: center; }
        .nav-links a { text-decoration: none; color: #333; font-family: 'Prompt', sans-serif; font-size: 0.9rem; padding: 8px 10px; display: block; }
        .dropdown { position: relative; }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #fff;
            min-width: 200px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.1);
            z-index: 100;
            list-style: none;
            padding: 10px 0;
            border-radius: 8px;
            top: 100%;
        }
        .dropdown:hover .dropdown-content { display: block; }
        .dropdown-content li a { padding: 8px 20px; }
        .dropdown-content li a:hover { background-color: #f1f1f1; }

        /* Hamburger */
        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            padding: 8px;
            background: none;
            border: none;
            z-index: 1100;
        }
        .hamburger span {
            display: block;
            width: 24px;
            height: 2px;
            background: #8a0000;
            border-radius: 2px;
            transition: all 0.3s;
        }
        .hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .hamburger.open span:nth-child(2) { opacity: 0; }
        .hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        /* Mobile Nav Overlay */
        .mobile-nav {
            display: none;
            position: fixed;
            top: 70px; left: 0;
            width: 100%;
            background: #fff;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            z-index: 999;
            padding: 20px;
            flex-direction: column;
            gap: 5px;
            max-height: calc(100vh - 70px);
            overflow-y: auto;
        }
        .mobile-nav.open { display: flex; }
        .mobile-nav a {
            display: block;
            padding: 12px 15px;
            color: #333;
            text-decoration: none;
            font-family: 'Prompt', sans-serif;
            font-size: 1rem;
            border-radius: 8px;
            border-bottom: 1px solid #f0f0f0;
        }
        .mobile-nav a:hover { background: #fff5f5; color: #8a0000; }
        .mobile-nav .mobile-sub {
            padding-left: 30px;
            font-size: 0.9rem;
            color: #666;
        }
        .mobile-login {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #f0f0f0;
        }

        @media (max-width: 900px) {
            nav { display: none; }
            .login-section { display: none; }
            .hamburger { display: flex; }
        }
        @media (max-width: 480px) {
            .logo-text p { display: none; }
            .logo-text h1 { font-size: 1rem !important; }
        }
    </style>
</head>
<body>
<header class="header">
    <div class="container container-nav" style="display: flex; justify-content: space-between; align-items: center; width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <a href="/SWUIS/index.php" class="logo-group" style="text-decoration: none; display: flex; align-items: center; gap: 10px;">
            <img src="https://swu.ac.th/images/Srinakharinwirot_Logo.png" alt="SWU Logo" style="height: 45px;">
            <div class="logo-text">
                <h1 style="font-size: 1.1rem; color: #8a0000; margin: 0; font-family: 'Prompt';">INFORMATION STUDIES</h1>
                <p style="font-size: 0.75rem; color: #666; margin: 0;">Srinakharinwirot University</p>
            </div>
        </a>

        <nav>
            <ul class="nav-links">
                <?php if ($role !== 'teacher' && $role !== 'admin'): ?>
                <li><a href="/SWUIS/aboutIS.php">เกี่ยวกับหลักสูตร</a></li>
                <li><a href="/SWUIS/pages/admin/staff.php">บุคลากร</a></li>
                <?php endif; ?>
                <?php if ($role === 'guest'): ?>
                    <li><a href="/SWUIS/Internship.php">การฝึกงานและสหกิจศึกษา</a></li>
                <?php elseif ($role === 'student'): ?>
                    <li class="dropdown">
                        <a href="/SWUIS/Internship.php" class="dropbtn">การฝึกงานฯ ▾</a>
                        <ul class="dropdown-content">
                            <li><a href="/SWUIS/form.php">ยื่นเรื่องฝึกงาน</a></li>
                            <li><a href="/SWUIS/pages/admin/student_list.php">ติดตามสถานะ</a></li>
                            <li><a href="/SWUIS/pages/student/student_profile.php">ข้อมูลของฉัน</a></li>
                        </ul>
                    </li>
                <?php elseif ($role === 'teacher'): ?>
                    <li><a href="/SWUIS/pages/teacher/T_dashboard.php">คำร้องขอฝึกงาน</a></li>
                    <li><a href="/SWUIS/pages/teacher/teacher_students.php">ข้อมูลนิสิต</a></li>
                <?php elseif ($role === 'admin'): ?>
                    <li><a href="/SWUIS/Internship.php">การฝึกงาน</a></li>
                    <li><a href="/SWUIS/pages/admin/staff_dashboard.php">ระบบจัดการ</a></li>
                    <li><a href="/SWUIS/pages/admin/add_user.php">จัดการบัญชี</a></li>
                <?php endif; ?>
                <li><a href="/SWUIS/ourteam.php"><i class="fa-solid fa-circle-user"></i></a></li>
            </ul>
        </nav>

        <div class="login-section">
            <?php if ($role === 'guest'): ?>
                <a href="/SWUIS/login.php" class="login-btn-circle" style="background: #8a0000; color: #fff; padding: 8px 20px; border-radius: 20px; text-decoration: none; font-family: 'Prompt'; white-space: nowrap;">Login</a>
            <?php else: ?>
                <div class="user-info">
                    <span class="role-badge <?= $role_label['class'] ?>"><?= $role_label['label'] ?></span>
                    <span class="user-display-name"><?= htmlspecialchars($display_name) ?></span>
                    <a href="/SWUIS/logout.php" class="logout-btn">ออกจากระบบ</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Hamburger Button -->
        <button class="hamburger" id="hamburger" aria-label="เมนู">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<!-- Mobile Nav -->
<div class="mobile-nav" id="mobileNav">
    <?php if ($role !== 'teacher' && $role !== 'admin'): ?>
    <a href="/SWUIS/aboutIS.php">เกี่ยวกับหลักสูตร</a>
    <a href="/SWUIS/pages/admin/staff.php">บุคลากร</a>
    <?php endif; ?>
    <?php if ($role === 'guest'): ?>
        <a href="/SWUIS/Internship.php">การฝึกงานและสหกิจศึกษา</a>
    <?php elseif ($role === 'student'): ?>
        <a href="/SWUIS/Internship.php">การฝึกงานและสหกิจศึกษา</a>
        <a href="/SWUIS/form.php" class="mobile-sub">↳ ยื่นเรื่องฝึกงาน</a>
        <a href="/SWUIS/pages/admin/student_list.php" class="mobile-sub">↳ ติดตามสถานะ</a>
        <a href="/SWUIS/pages/student/student_profile.php" class="mobile-sub">↳ ข้อมูลของฉัน</a>
    <?php elseif ($role === 'teacher'): ?>
        <a href="/SWUIS/pages/teacher/T_dashboard.php">Teacher Dashboard</a>
        <a href="/SWUIS/pages/teacher/teacher_students.php">ข้อมูลนิสิต</a>
    <?php elseif ($role === 'admin'): ?>
        <a href="/SWUIS/Internship.php">การฝึกงาน</a>
        <a href="/SWUIS/pages/admin/staff_dashboard.php">Staff Dashboard</a>
        <a href="/SWUIS/pages/admin/add_user.php">จัดการบัญชีผู้ใช้</a>
    <?php endif; ?>
    <a href="/SWUIS/ourteam.php">คณะผู้จัดทำ</a>
    <div class="mobile-login">
        <?php if ($role === 'guest'): ?>
            <a href="/SWUIS/login.php" style="background:#8a0000; color:#fff; border-radius:10px; text-align:center;">เข้าสู่ระบบ</a>
        <?php else: ?>
            <a style="color:#475569; font-weight:600; pointer-events:none;"><?= htmlspecialchars($display_name) ?> (<?= $role_label['label'] ?? '' ?>)</a>
            <a href="/SWUIS/logout.php" style="color:#b91c1c;">ออกจากระบบ</a>
        <?php endif; ?>
    </div>
</div>

<script>
    const hamburger = document.getElementById('hamburger');
    const mobileNav = document.getElementById('mobileNav');
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('open');
        mobileNav.classList.toggle('open');
    });
    // ปิด menu เมื่อคลิก link
    mobileNav.querySelectorAll('a').forEach(a => {
        a.addEventListener('click', () => {
            hamburger.classList.remove('open');
            mobileNav.classList.remove('open');
        });
    });
</script>