<?php
/**
 * admin_profile.php — หน้าข้อมูลส่วนตัวแอดมิน
 */
require_once '../../includes/auth.php';
require_once '../../includes/db_connect.php';

require_role('admin', 'login.php');

$staffid = $_SESSION['user_id'];
$msg = '';

// เปลี่ยนรหัสผ่าน
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old  = $_POST['old_pass']     ?? '';
    $new  = $_POST['new_pass']     ?? '';
    $conf = $_POST['confirm_pass'] ?? '';

    $stmtP = $conn->prepare("SELECT password FROM staff WHERE staffid = ?");
    $stmtP->execute([$staffid]);
    $currentPass = $stmtP->fetchColumn();

    if ($old !== $currentPass && !password_verify($old, $currentPass)) {
        $msg = "<div class='alert alert-danger'><i class='fas fa-times-circle me-2'></i>รหัสผ่านปัจจุบันไม่ถูกต้อง</div>";
    } elseif ($new !== $conf) {
        $msg = "<div class='alert alert-danger'><i class='fas fa-times-circle me-2'></i>รหัสผ่านใหม่ไม่ตรงกัน</div>";
    } elseif (strlen($new) < 4) {
        $msg = "<div class='alert alert-danger'><i class='fas fa-times-circle me-2'></i>รหัสผ่านต้องมีอย่างน้อย 4 ตัวอักษร</div>";
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $stmtU = $conn->prepare("UPDATE staff SET password = ? WHERE staffid = ?");
        $stmtU->execute([$hashed, $staffid]);
        $msg = "<div class='alert alert-success'><i class='fas fa-check-circle me-2'></i>เปลี่ยนรหัสผ่านเรียบร้อยแล้ว</div>";
    }
}

$stmt = $conn->prepare("SELECT * FROM staff WHERE staffid = ?");
$stmt->execute([$staffid]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

// สถิติภาพรวม
$stats = [];
$statsQ = [
    'นิสิตทั้งหมด'    => "SELECT COUNT(*) FROM student",
    'อาจารย์ทั้งหมด'  => "SELECT COUNT(*) FROM teacher",
    'คำขอทั้งหมด'     => "SELECT COUNT(*) FROM internship_request",
    'รออนุมัติ'        => "SELECT COUNT(*) FROM internship_request WHERE status = 'กำลังดำเนินการ'",
];
foreach ($statsQ as $label => $sql) {
    $stats[$label] = $conn->query($sql)->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลส่วนตัว — Information Studies SWU</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --swu-red:#c8102e; --swu-dark-red:#9e0b23; --bg:#f8fafc; --card:#fff; --text:#1e293b; --muted:#64748b; }
        body { font-family:'Prompt',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; }
        .profile-hero {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            padding: 2.5rem 0 0; border-radius: 0 0 2.5rem 2.5rem; margin-bottom: 1.5rem;
        }
        .avatar-circle {
            width:80px; height:80px; background:rgba(255,255,255,0.15);
            border:3px solid rgba(255,255,255,0.3); border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            font-size:2rem; color:white; margin:0 auto 0.75rem;
        }
        .hero-name { font-size:1.4rem; font-weight:700; color:white; margin:0; }
        .hero-id   { font-size:0.85rem; color:rgba(255,255,255,0.6); margin:0.25rem 0 0; }
        .admin-badge {
            display:inline-flex; align-items:center; gap:0.4rem;
            background:rgba(200,16,46,0.3); border:1px solid rgba(200,16,46,0.5);
            color:white; padding:0.25rem 0.85rem; border-radius:50px;
            font-size:0.78rem; font-weight:600; margin-top:0.5rem;
        }
        .hero-tabs { display:flex; justify-content:center; margin-top:1.5rem; }
        .hero-tab {
            padding:0.75rem 1.75rem; border:none; background:transparent;
            color:rgba(255,255,255,0.55); font-family:'Prompt',sans-serif;
            font-size:0.9rem; font-weight:600; cursor:pointer; transition:all 0.2s;
            border-bottom:3px solid transparent; display:flex; align-items:center; gap:0.4rem;
        }
        .hero-tab:hover { color:white; }
        .hero-tab.active { color:white; border-bottom-color:#c8102e; }
        .modern-container { max-width:860px; margin:0 auto; padding:0 1.25rem; }
        .tab-pane { display:none; animation:fadeIn 0.3s ease; }
        .tab-pane.active { display:block; }
        .info-card, .pass-card { background:var(--card); border-radius:1.5rem; box-shadow:0 20px 40px -15px rgba(0,0,0,0.07); padding:2rem; margin-bottom:1.5rem; }
        .section-title { font-size:0.95rem; font-weight:700; color:var(--text); margin-bottom:1.25rem; display:flex; align-items:center; gap:0.5rem; }
        .section-title i { color:var(--swu-red); }
        .info-row { display:flex; align-items:flex-start; padding:0.7rem 0; border-bottom:1px solid #f1f5f9; }
        .info-row:last-child { border-bottom:none; }
        .info-label { width:140px; font-size:0.82rem; color:var(--muted); flex-shrink:0; }
        .info-value { font-size:0.92rem; font-weight:500; }
        .stats-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:0.75rem; margin-bottom:1.5rem; }
        .stat-box { background:var(--card); border-radius:1rem; padding:1.25rem; text-align:center; box-shadow:0 4px 12px -4px rgba(0,0,0,0.05); }
        .stat-num { font-size:1.8rem; font-weight:700; color:var(--swu-red); }
        .stat-label { font-size:0.78rem; color:var(--muted); margin-top:0.15rem; }
        .form-control:focus { border-color:var(--swu-red); box-shadow:0 0 0 0.2rem rgba(200,16,46,0.15); }
        .btn-swu { background:linear-gradient(135deg,var(--swu-dark-red),var(--swu-red)); color:white; border:none; border-radius:0.75rem; padding:0.6rem 1.5rem; font-family:'Prompt',sans-serif; font-weight:600; }
        .btn-swu:hover { opacity:0.9; color:white; }
        @keyframes fadeIn { from{opacity:0;} to{opacity:1;} }
    </style>
</head>
<body>

<?php include '../../includes/header.php'; ?>

<div class="profile-hero">
    <div class="text-center px-3">
        <div class="avatar-circle"><i class="fas fa-user-shield"></i></div>
        <h1 class="hero-name"><?= htmlspecialchars($staff['staffname'] ?? '-') ?></h1>
        <p class="hero-id"><?= htmlspecialchars($staff['staffid'] ?? '') ?></p>
        <div><span class="admin-badge"><i class="fas fa-shield-alt"></i> ผู้ดูแลระบบ</span></div>
    </div>
    <div class="hero-tabs">
        <button class="hero-tab active" onclick="switchTab('profile', this)">
            <i class="fas fa-user"></i> ข้อมูลส่วนตัว
        </button>
        <button class="hero-tab" onclick="switchTab('overview', this)">
            <i class="fas fa-chart-bar"></i> ภาพรวมระบบ
        </button>
        <button class="hero-tab" onclick="switchTab('password', this)">
            <i class="fas fa-lock"></i> เปลี่ยนรหัสผ่าน
        </button>
    </div>
</div>

<div class="modern-container mb-5">

    <!-- Tab: ข้อมูลส่วนตัว -->
    <div id="tab-profile" class="tab-pane active">
        <div class="info-card">
            <p class="section-title"><i class="fas fa-id-card"></i> ข้อมูลส่วนตัว</p>
            <div class="info-row">
                <span class="info-label">รหัสเจ้าหน้าที่</span>
                <span class="info-value"><?= htmlspecialchars($staff['staffid'] ?? '-') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">ชื่อ-นามสกุล</span>
                <span class="info-value"><?= htmlspecialchars($staff['staffname'] ?? '-') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">อีเมล</span>
                <span class="info-value"><?= htmlspecialchars($staff['email'] ?? '-') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">สิทธิ์การใช้งาน</span>
                <span class="info-value"><span class="admin-badge" style="font-size:0.82rem;"><i class="fas fa-shield-alt"></i> ผู้ดูแลระบบ</span></span>
            </div>
        </div>
    </div>

    <!-- Tab: ภาพรวมระบบ -->
    <div id="tab-overview" class="tab-pane">
        <div class="info-card">
            <p class="section-title"><i class="fas fa-chart-bar"></i> ภาพรวมระบบ</p>
            <div class="stats-row">
                <?php foreach ($stats as $label => $num): ?>
                <div class="stat-box">
                    <div class="stat-num"><?= $num ?></div>
                    <div class="stat-label"><?= $label ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Tab: เปลี่ยนรหัสผ่าน -->
    <div id="tab-password" class="tab-pane">
        <div class="pass-card">
            <p class="section-title"><i class="fas fa-lock"></i> เปลี่ยนรหัสผ่าน</p>
            <?= $msg ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">รหัสผ่านปัจจุบัน</label>
                    <input type="password" name="old_pass" class="form-control" placeholder="กรอกรหัสผ่านปัจจุบัน" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">รหัสผ่านใหม่</label>
                    <input type="password" name="new_pass" class="form-control" placeholder="กรอกรหัสผ่านใหม่" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                    <input type="password" name="confirm_pass" class="form-control" placeholder="กรอกรหัสผ่านใหม่อีกครั้ง" required>
                </div>
                <button type="submit" name="change_password" class="btn btn-swu">
                    <i class="fas fa-save me-2"></i>บันทึกรหัสผ่านใหม่
                </button>
            </form>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function switchTab(id, btn) {
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.hero-tab').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + id).classList.add('active');
        btn.classList.add('active');
    }
    <?php if ($msg): ?>
    switchTab('password', document.querySelectorAll('.hero-tab')[2]);
    <?php endif; ?>
</script>

<?php include '../../includes/footer.php'; ?>
</body>
</html>
