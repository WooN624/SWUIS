<?php
/**
 * teacher_students.php — อาจารย์ดูข้อมูลนิสิตแบ่งตามชั้นปี
 */
require_once 'auth.php';
require_once 'db_connect.php';

require_role('teacher', 'login.php');

$students_raw = $conn->query("
    SELECT 
        s.stdid, s.stdname, s.email, s.phone,
        r.internship_type, r.status, c.companyname,
        r.student_year, r.company_position, r.start_date, r.end_date
    FROM student s
    LEFT JOIN internship_request r ON r.stdid = s.stdid
        AND r.requestid = (
            SELECT r2.requestid FROM internship_request r2
            WHERE r2.stdid = s.stdid
            ORDER BY r2.requestid DESC LIMIT 1
        )
    LEFT JOIN company c ON r.companyid = c.companyid
    ORDER BY r.student_year ASC, s.stdname ASC
")->fetchAll(PDO::FETCH_ASSOC);

$by_year = [];
foreach ($students_raw as $s) {
    $year = $s['student_year'] ?? 0;
    $by_year[$year][] = $s;
}
ksort($by_year);

$statusMap = [
    'กำลังดำเนินการ' => ['class'=>'status-1','icon'=>'fa-paper-plane'],
    'อนุมัติ'        => ['class'=>'status-2','icon'=>'fa-user-check'],
    'ออกใบส่งตัว'    => ['class'=>'status-3','icon'=>'fa-envelope-open-text'],
    'เสร็จสิ้น'      => ['class'=>'status-4','icon'=>'fa-check-circle'],
    'ปฏิเสธ'         => ['class'=>'status-9','icon'=>'fa-times-circle'],
];

$year_label = [1=>'ปีที่ 1', 2=>'ปีที่ 2', 3=>'ปีที่ 3', 4=>'ปีที่ 4', 0=>'ไม่ระบุชั้นปี'];

function renderStudentCard(array $s, array $statusMap): string {
    $sc = isset($s['status']) ? ($statusMap[$s['status']] ?? null) : null;
    ob_start(); ?>
    <div class="std-card" data-search="<?= strtolower(htmlspecialchars($s['stdname'].' '.$s['stdid'])) ?>">
        <div class="d-flex align-items-center gap-3">
            <div class="std-avatar"><i class="fas fa-user-graduate"></i></div>
            <div>
                <p class="std-name"><?= htmlspecialchars($s['stdname']) ?></p>
                <p class="std-id"><?= htmlspecialchars($s['stdid']) ?></p>
            </div>
        </div>
        <?php if (!empty($s['email'])): ?>
        <div class="std-email"><i class="fas fa-envelope me-1"></i><?= htmlspecialchars($s['email']) ?></div>
        <?php endif; ?>
        <?php if (!empty($s['companyname'])): ?>
        <div class="company-info">
            <div class="company-name"><i class="far fa-building me-1 text-muted"></i><?= htmlspecialchars($s['companyname']) ?></div>
            <div class="company-pos"><?= htmlspecialchars($s['company_position'] ?? '') ?> · <?= htmlspecialchars($s['internship_type'] ?? '') ?></div>
            <?php if ($sc): ?>
            <span class="status-badge <?= $sc['class'] ?>">
                <i class="fas <?= $sc['icon'] ?>"></i> <?= htmlspecialchars($s['status']) ?>
            </span>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="no-internship"><i class="fas fa-minus-circle me-1"></i>ยังไม่มีคำขอฝึกงาน</div>
        <?php endif; ?>
    </div>
    <?php return ob_get_clean();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลนิสิต — Information Studies SWU</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --swu-red:#c8102e; --swu-dark-red:#9e0b23; --bg:#f8fafc; --card:#fff; --text:#1e293b; --muted:#64748b; }
        body { font-family:'Prompt',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; }

        .page-hero { background:linear-gradient(135deg,var(--swu-dark-red) 0%,var(--swu-red) 100%); padding:3rem 0 5.5rem; border-radius:0 0 2.5rem 2.5rem; margin-bottom:-3.5rem; position:relative; z-index:1; color:white; text-align:center; }
        .page-hero h1 { font-size:1.8rem; font-weight:700; margin:0; display:flex; align-items:center; justify-content:center; gap:0.75rem; }
        .page-hero p  { opacity:0.75; margin:0.5rem 0 0; font-size:0.9rem; }

        .modern-container { max-width:1100px; margin:0 auto; padding:0 1.25rem; position:relative; z-index:2; }
        .main-card { background:var(--card); border-radius:1.5rem; box-shadow:0 20px 40px -15px rgba(0,0,0,0.07); padding:2rem; margin-bottom:2rem; }

        .year-tabs { display:flex; gap:0.5rem; flex-wrap:wrap; margin-bottom:1.5rem; }
        .year-tab { padding:0.5rem 1.2rem; border:2px solid #e2e8f0; border-radius:50px; background:white; font-family:'Prompt',sans-serif; font-size:0.85rem; font-weight:600; color:var(--muted); cursor:pointer; transition:all 0.2s; }
        .year-tab:hover,.year-tab.active { border-color:var(--swu-red); background:var(--swu-red); color:white; }

        .year-section { display:none; animation:fadeIn 0.3s ease; }
        .year-section.active { display:block; }

        .year-header { display:flex; align-items:center; gap:0.75rem; margin-bottom:1.25rem; }
        .year-badge { background:linear-gradient(135deg,var(--swu-dark-red),var(--swu-red)); color:white; padding:0.35rem 1rem; border-radius:50px; font-size:0.85rem; font-weight:700; }
        .year-count { font-size:0.85rem; color:var(--muted); }

        .std-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:1rem; margin-bottom:2rem; }
        .std-card { background:var(--card); border-radius:1.25rem; border:1px solid #f1f5f9; padding:1.25rem 1.5rem; box-shadow:0 4px 12px -4px rgba(0,0,0,0.05); transition:all 0.2s; }
        .std-card:hover { transform:translateY(-3px); box-shadow:0 12px 24px -8px rgba(0,0,0,0.1); }

        .std-avatar { width:44px; height:44px; background:#fde8eb; color:var(--swu-red); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
        .std-name { font-weight:600; font-size:0.95rem; margin:0; }
        .std-id   { font-size:0.78rem; color:var(--muted); margin:0; font-family:monospace; }
        .std-email { font-size:0.8rem; color:var(--muted); margin-top:0.5rem; }

        .company-info { margin-top:0.85rem; padding-top:0.85rem; border-top:1px solid #f1f5f9; font-size:0.82rem; }
        .company-name { font-weight:600; color:var(--text); }
        .company-pos  { color:var(--muted); margin-top:0.15rem; }
        .no-internship { margin-top:0.85rem; padding-top:0.85rem; border-top:1px solid #f1f5f9; font-size:0.82rem; color:#cbd5e1; font-style:italic; }

        .status-badge { display:inline-flex; align-items:center; gap:0.35rem; padding:0.22rem 0.65rem; border-radius:50px; font-size:0.75rem; font-weight:600; margin-top:0.5rem; }
        .status-1 { background:#eef2ff; color:#4338ca; border:1px solid #e0e7ff; }
        .status-2 { background:#fffbeb; color:#b45309; border:1px solid #fef3c7; }
        .status-3 { background:#f0fdf4; color:#15803d; border:1px solid #dcfce7; }
        .status-4 { background:#ecfdf5; color:#047857; border:1px solid #d1fae5; }
        .status-9 { background:#fef2f2; color:#b91c1c; border:1px solid #fee2e2; }

        .search-box { position:relative; margin-bottom:1.5rem; }
        .search-box input { width:100%; padding:0.75rem 1rem 0.75rem 2.75rem; border:2px solid #e2e8f0; border-radius:0.8rem; font-family:'Prompt',sans-serif; font-size:0.9rem; transition:border-color 0.2s; }
        .search-box input:focus { outline:none; border-color:var(--swu-red); }
        .search-box i { position:absolute; left:0.9rem; top:50%; transform:translateY(-50%); color:var(--muted); }

        @keyframes fadeIn { from{opacity:0;} to{opacity:1;} }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="page-hero">
    <h1>
        <span style="background:rgba(255,255,255,0.15);padding:0.8rem;border-radius:0.8rem;">
            <i class="fas fa-users"></i>
        </span>
        ข้อมูลนิสิต
    </h1>
    <p>รายชื่อนิสิตทั้งหมด <?= count($students_raw) ?> คน แบ่งตามชั้นปี</p>
</div>

<div class="modern-container mb-5 pt-5">
    <div class="main-card">

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="ค้นหาชื่อ หรือรหัสนิสิต..." oninput="filterCards()">
        </div>

        <div class="year-tabs">
            <button class="year-tab active" onclick="switchYear('all', this)">
                ทั้งหมด (<?= count($students_raw) ?>)
            </button>
            <?php foreach ($by_year as $year => $list): ?>
            <button class="year-tab" onclick="switchYear('year-<?= $year ?>', this)">
                <?= $year_label[$year] ?? 'ปี '.$year ?> (<?= count($list) ?>)
            </button>
            <?php endforeach; ?>
        </div>

        <div id="section-all" class="year-section active">
            <div class="std-grid">
                <?php foreach ($students_raw as $s): echo renderStudentCard($s, $statusMap); endforeach; ?>
            </div>
        </div>

        <?php foreach ($by_year as $year => $list): ?>
        <div id="section-year-<?= $year ?>" class="year-section">
            <div class="year-header">
                <span class="year-badge"><?= $year_label[$year] ?? 'ปี '.$year ?></span>
                <span class="year-count"><?= count($list) ?> คน</span>
            </div>
            <div class="std-grid">
                <?php foreach ($list as $s): echo renderStudentCard($s, $statusMap); endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let activeSection = 'all';

    function switchYear(id, btn) {
        document.querySelectorAll('.year-section').forEach(s => s.classList.remove('active'));
        document.querySelectorAll('.year-tab').forEach(b => b.classList.remove('active'));
        document.getElementById('section-' + id).classList.add('active');
        btn.classList.add('active');
        activeSection = id;
        filterCards();
    }

    function filterCards() {
        const q = document.getElementById('searchInput').value.toLowerCase().trim();
        document.getElementById('section-' + activeSection)
            .querySelectorAll('.std-card')
            .forEach(card => {
                card.style.display = (!q || card.dataset.search.includes(q)) ? '' : 'none';
            });
    }
</script>

<?php include 'footer.php'; ?>
</body>
</html>