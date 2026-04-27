<?php
/**
 * student_profile.php — หน้าข้อมูลส่วนตัวนิสิต
 */
require_once 'auth.php';
require_once 'db_connect.php';

require_role('student', 'login.php');

$stdid = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM student WHERE stdid = ?");
$stmt->execute([$stdid]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt2 = $conn->prepare("
    SELECT r.*, c.companyname, c.address AS company_address, c.email AS company_email, t.tchname
    FROM internship_request r
    LEFT JOIN company c ON r.companyid = c.companyid
    LEFT JOIN teacher t ON r.tchID = t.tchID
    WHERE r.stdid = ?
    ORDER BY r.requestid DESC
");
$stmt2->execute([$stdid]);
$requests = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$statusMap = [
    'กำลังดำเนินการ' => ['class'=>'status-1','icon'=>'fa-paper-plane'],
    'อนุมัติ'        => ['class'=>'status-2','icon'=>'fa-user-check'],
    'ออกใบส่งตัว'    => ['class'=>'status-3','icon'=>'fa-envelope-open-text'],
    'เสร็จสิ้น'      => ['class'=>'status-4','icon'=>'fa-check-circle'],
    'ปฏิเสธ'         => ['class'=>'status-9','icon'=>'fa-times-circle'],
];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลของฉัน — Information Studies SWU</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --swu-red:#c8102e; --swu-dark-red:#9e0b23; --bg:#f8fafc; --card:#fff; --text:#1e293b; --muted:#64748b; }
        body { font-family:'Prompt',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; }

        /* ── Hero ── */
        .profile-hero {
            background: linear-gradient(135deg, var(--swu-dark-red) 0%, var(--swu-red) 100%);
            padding: 2.5rem 0 0;
            border-radius: 0 0 2.5rem 2.5rem;
            margin-bottom: 1.5rem;
            position: relative; z-index: 1;
        }
        .avatar-circle {
            width:80px; height:80px; background:rgba(255,255,255,0.2);
            border:3px solid rgba(255,255,255,0.5); border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            font-size:2rem; color:white; margin:0 auto 0.75rem;
        }
        .hero-name { font-size:1.4rem; font-weight:700; color:white; margin:0; }
        .hero-id   { font-size:0.85rem; color:rgba(255,255,255,0.75); margin:0.25rem 0 0; }

        /* ── Tab bar in hero ── */
        .hero-tabs {
            display: flex; justify-content: center; gap: 0;
            margin-top: 1.5rem;
        }
        .hero-tab {
            padding: 0.75rem 1.75rem;
            border: none; background: transparent;
            color: rgba(255,255,255,0.65);
            font-family: 'Prompt', sans-serif;
            font-size: 0.9rem; font-weight: 600;
            cursor: pointer; transition: all 0.2s;
            border-bottom: 3px solid transparent;
            display: flex; align-items: center; gap: 0.4rem;
        }
        .hero-tab:hover { color: white; }
        .hero-tab.active {
            color: white;
            border-bottom-color: white;
        }

        /* ── Container ── */
        .modern-container { max-width:860px; margin:0 auto; padding:0 1.25rem; }

        /* ── Tab panes ── */
        .tab-pane { display:none; animation:fadeIn 0.3s ease; }
        .tab-pane.active { display:block; }

        /* ── Info card ── */
        .info-card { background:var(--card); border-radius:1.5rem; box-shadow:0 20px 40px -15px rgba(0,0,0,0.07); padding:2rem; margin-bottom:1.5rem; }
        .section-title { font-size:0.95rem; font-weight:700; color:var(--text); margin-bottom:1.25rem; display:flex; align-items:center; gap:0.5rem; }
        .section-title i { color:var(--swu-red); }

        .info-row { display:flex; align-items:flex-start; padding:0.7rem 0; border-bottom:1px solid #f1f5f9; }
        .info-row:last-child { border-bottom:none; }
        .info-label { width:140px; font-size:0.82rem; color:var(--muted); flex-shrink:0; padding-top:1px; }
        .info-value { font-size:0.92rem; font-weight:500; color:var(--text); }

        /* ── Request cards ── */
        .req-card { border:1px solid #f1f5f9; border-radius:1rem; padding:1.25rem 1.5rem; margin-bottom:1rem; transition:box-shadow 0.2s; }
        .req-card:hover { box-shadow:0 8px 20px -8px rgba(0,0,0,0.1); }
        .req-company  { font-weight:600; font-size:1rem; margin-bottom:0.2rem; }
        .req-position { font-size:0.85rem; color:var(--muted); margin-bottom:0.65rem; }
        .req-meta { font-size:0.82rem; color:var(--muted); display:flex; gap:1.25rem; flex-wrap:wrap; }
        .req-meta i { color:var(--swu-red); margin-right:3px; }

        .status-badge { display:inline-flex; align-items:center; gap:0.4rem; padding:0.3rem 0.85rem; border-radius:50px; font-size:0.8rem; font-weight:600; }
        .status-1 { background:#eef2ff; color:#4338ca; border:1px solid #e0e7ff; }
        .status-2 { background:#fffbeb; color:#b45309; border:1px solid #fef3c7; }
        .status-3 { background:#f0fdf4; color:#15803d; border:1px solid #dcfce7; }
        .status-4 { background:#ecfdf5; color:#047857; border:1px solid #d1fae5; }
        .status-9 { background:#fef2f2; color:#b91c1c; border:1px solid #fee2e2; }

        .empty-box { text-align:center; padding:3rem 1rem; color:var(--muted); }
        .empty-box i { font-size:2.5rem; margin-bottom:0.75rem; display:block; color:#cbd5e1; }

        /* ── Stats row ── */
        .stats-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(130px,1fr)); gap:0.75rem; margin-bottom:1.5rem; }
        .stat-box { background:var(--card); border-radius:1rem; padding:1rem; text-align:center; box-shadow:0 4px 12px -4px rgba(0,0,0,0.05); }
        .stat-num { font-size:1.6rem; font-weight:700; }
        .stat-label { font-size:0.75rem; color:var(--muted); margin-top:0.15rem; }

        @keyframes fadeIn { from{opacity:0;} to{opacity:1;} }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<!-- Hero + Tabs -->
<div class="profile-hero">
    <div class="text-center px-3">
        <div class="avatar-circle"><i class="fas fa-user-graduate"></i></div>
        <h1 class="hero-name"><?= htmlspecialchars($student['stdname'] ?? '-') ?></h1>
        <p class="hero-id"><i class="fas fa-id-card me-1"></i><?= htmlspecialchars($student['stdid'] ?? '') ?></p>
    </div>

    <!-- Tab bar -->
    <div class="hero-tabs">
        <button class="hero-tab active" onclick="switchTab('profile', this)">
            <i class="fas fa-user"></i> ข้อมูลส่วนตัว
        </button>
        <button class="hero-tab" onclick="switchTab('internship', this)">
            <i class="fas fa-briefcase"></i> ประวัติฝึกงาน
            <?php if (count($requests)): ?>
            <span style="background:rgba(255,255,255,0.3);padding:0 6px;border-radius:50px;font-size:0.75rem;">
                <?= count($requests) ?>
            </span>
            <?php endif; ?>
        </button>
    </div>
</div>

<div class="modern-container mb-5">

    <!-- ── Tab: ข้อมูลส่วนตัว ── -->
    <div id="tab-profile" class="tab-pane active">
        <div class="info-card">
            <p class="section-title"><i class="fas fa-id-card"></i> ข้อมูลส่วนตัว</p>
            <div class="info-row">
                <span class="info-label">รหัสนิสิต</span>
                <span class="info-value"><?= htmlspecialchars($student['stdid'] ?? '-') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">ชื่อ-นามสกุล</span>
                <span class="info-value"><?= htmlspecialchars($student['stdname'] ?? '-') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">อีเมล</span>
                <span class="info-value"><?= htmlspecialchars($student['email'] ?? '-') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">เบอร์โทร</span>
                <span class="info-value"><?= htmlspecialchars($student['phone'] ?? '-') ?></span>
            </div>
        </div>
    </div>

    <!-- ── Tab: ประวัติฝึกงาน ── -->
    <div id="tab-internship" class="tab-pane">

        <?php
        // สถิติ
        $counts = ['ทั้งหมด'=>count($requests),'อนุมัติ'=>0,'กำลังดำเนินการ'=>0,'ปฏิเสธ'=>0,'เสร็จสิ้น'=>0];
        foreach ($requests as $r) {
            if (isset($counts[$r['status']])) $counts[$r['status']]++;
        }
        $statColors = ['ทั้งหมด'=>['#4338ca','#eef2ff'],'อนุมัติ'=>['#b45309','#fffbeb'],'กำลังดำเนินการ'=>['#0369a1','#e0f2fe'],'ปฏิเสธ'=>['#b91c1c','#fef2f2'],'เสร็จสิ้น'=>['#047857','#ecfdf5']];
        ?>
        <div class="stats-row">
            <?php foreach ($counts as $label => $num):
                [$color,$bg] = $statColors[$label] ?? ['#64748b','#f1f5f9']; ?>
            <div class="stat-box">
                <div class="stat-num" style="color:<?= $color ?>"><?= $num ?></div>
                <div class="stat-label"><?= $label ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($requests): ?>
            <?php foreach ($requests as $r):
                $sc = $statusMap[$r['status']] ?? ['class'=>'status-9','icon'=>'fa-question-circle'];
            ?>
            <div class="req-card">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <div class="req-company">
                            <i class="far fa-building me-1 text-muted"></i>
                            <?= htmlspecialchars($r['companyname'] ?? '-') ?>
                        </div>
                        <div class="req-position">
                            <?= htmlspecialchars($r['company_position'] ?? '') ?>
                            <?= $r['internship_type'] ? ' · '.$r['internship_type'] : '' ?>
                        </div>
                        <div class="req-meta">
                            <?php if ($r['start_date']): ?>
                            <span>
                                <i class="fas fa-calendar-alt"></i>
                                <?= date('d/m/Y', strtotime($r['start_date'])) ?>
                                — <?= $r['end_date'] ? date('d/m/Y', strtotime($r['end_date'])) : '?' ?>
                            </span>
                            <?php endif; ?>
                            <?php if (!empty($r['tchname'])): ?>
                            <span><i class="fas fa-chalkboard-teacher"></i> <?= htmlspecialchars($r['tchname']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <span class="status-badge <?= $sc['class'] ?>">
                        <i class="fas <?= $sc['icon'] ?>"></i>
                        <?= htmlspecialchars($r['status']) ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-box">
                <i class="fas fa-folder-open"></i>
                ยังไม่มีประวัติการยื่นคำขอฝึกงาน
            </div>
        <?php endif; ?>
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
</script>

<?php include 'footer.php'; ?>
</body>
</html>