<?php
require_once '../../includes/auth.php';
require_once '../../includes/db_connect.php';

require_role('student', 'login.php');

$student_id = $_SESSION['user_id'] ?? '';
// ✅ ลบ debug die() ออกแล้ว — ไฟล์จะแสดงตารางได้ปกติ

try {
    $stmt = $conn->prepare(
        "SELECT 
            r.requestid,
            r.stdid,
            COALESCE(r.company_name, c.companyname) AS companyname,
            r.company_position,
            r.internship_type,
            r.start_date,
            r.end_date,
            r.status
         FROM internship_request r
         LEFT JOIN company c ON r.companyid = c.companyid
         WHERE r.stdid = :student_id
         ORDER BY r.requestid DESC"
    );
    $stmt->execute([':student_id' => $student_id]);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("เกิดข้อผิดพลาด: " . $e->getMessage());
}

function getStatusBadge(string $status): string {
    $map = [
        'กำลังดำเนินการ' => ['class' => 'status-1', 'icon' => 'fa-paper-plane',        'label' => 'กำลังดำเนินการ'],
        'อนุมัติ'        => ['class' => 'status-2', 'icon' => 'fa-user-check',         'label' => 'อาจารย์อนุมัติ'],
        'ออกใบส่งตัว'    => ['class' => 'status-3', 'icon' => 'fa-envelope-open-text', 'label' => 'ออกใบส่งตัวแล้ว'],
        'เสร็จสิ้น'      => ['class' => 'status-4', 'icon' => 'fa-check-circle',       'label' => 'ฝึกงานเสร็จสิ้น'],
        'ปฏิเสธ'         => ['class' => 'status-9', 'icon' => 'fa-times-circle',       'label' => 'ปฏิเสธ'],
    ];
    $s = $map[$status] ?? ['class' => 'status-unknown', 'icon' => 'fa-question-circle', 'label' => 'ไม่ทราบสถานะ'];
    return "<span class='status-badge {$s['class']}'><i class='fas {$s['icon']} me-1'></i>{$s['label']}</span>";
}

// ✅ เพิ่ม: ฟังก์ชัน Step Tracker แสดงขั้นตอนการดำเนินงาน
function getProgressSteps(string $status): string {
    $steps = [
        ['key' => 'กำลังดำเนินการ', 'label' => 'ยื่นคำขอ',     'icon' => 'fa-paper-plane'],
        ['key' => 'อนุมัติ',        'label' => 'อาจารย์อนุมัติ','icon' => 'fa-user-check'],
        ['key' => 'ออกใบส่งตัว',   'label' => 'ออกใบส่งตัว',   'icon' => 'fa-envelope-open-text'],
        ['key' => 'เสร็จสิ้น',     'label' => 'เสร็จสิ้น',     'icon' => 'fa-check-circle'],
    ];

    // ถ้าปฏิเสธ ให้แสดงแค่ badge พิเศษ
    if ($status === 'ปฏิเสธ') {
        return "<span class='status-badge status-9'><i class='fas fa-times-circle me-1'></i>ปฏิเสธ</span>";
    }

    $order = ['กำลังดำเนินการ' => 0, 'อนุมัติ' => 1, 'ออกใบส่งตัว' => 2, 'เสร็จสิ้น' => 3];
    $currentIdx = $order[$status] ?? 0;

    $html = "<div class='progress-steps'>";
    foreach ($steps as $i => $step) {
        if ($i > 0) $html .= "<div class='step-line " . ($i <= $currentIdx ? 'done' : '') . "'></div>";
        $cls = $i < $currentIdx ? 'done' : ($i === $currentIdx ? 'active' : 'pending');
        $html .= "<div class='step-dot $cls' title='{$step['label']}'>
                    <i class='fas {$step['icon']}'></i>
                  </div>";
    }
    $html .= "</div>";
    $html .= "<div class='mt-1'>" . getStatusBadge($status) . "</div>";
    return $html;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการฝึกงาน</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary:       #d80d0d;
            --primary-hover: #ff0000;
            --bg-color:      #f8fafc;
            --card-bg:       rgba(255,255,255,0.95);
            --text-main:     #0f172a;
            --text-muted:    #64748b;
        }

        body {
            font-family: 'Prompt', 'Inter', sans-serif;
            background-color: var(--bg-color);
            background-image:
                radial-gradient(at 0%   0%, hsla(253,16%,7%,0.05) 0, transparent 50%),
                radial-gradient(at 50%  0%, hsla(225,39%,30%,0.03) 0, transparent 50%),
                radial-gradient(at 100% 0%, hsla(339,49%,30%,0.05) 0, transparent 50%);
            background-attachment: fixed;
            color: var(--text-main);
            min-height: 100vh;
        }

        .page-header { padding: 3.5rem 0 4rem; }

        .page-title {
            font-weight: 700;
            font-size: 2.2rem;
            background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .student-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: white;
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-muted);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            border: 1px solid rgba(226,232,240,0.8);
        }
        .student-badge i { color: var(--primary); }

        .modern-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.4);
            border-radius: 24px;
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.05);
            padding: 2rem;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .table { margin-bottom: 0; border-collapse: separate; border-spacing: 0 12px; }
        .table thead th {
            background: transparent; border: none; color: var(--text-muted);
            font-size: 0.85rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: 0.5px; padding: 0 1.5rem 0.5rem;
        }
        .table tbody tr {
            background: white; border-radius: 16px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s ease;
        }
        .table tbody tr:hover { transform: translateY(-2px) scale(1.005); box-shadow: 0 12px 20px -8px rgba(0,0,0,0.08); }
        .table tbody td { border: none; padding: 1.25rem 1.5rem; vertical-align: middle; }
        .table tbody td:first-child { border-radius: 16px 0 0 16px; }
        .table tbody td:last-child  { border-radius: 0 16px 16px 0; }

        /* Status badge */
        .status-badge {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 0.4rem 0.9rem; border-radius: 50px;
            font-size: 0.82rem; font-weight: 600; letter-spacing: 0.3px;
        }
        .status-1       { background: #eef2ff; color: #4338ca; border: 1px solid #e0e7ff; }
        .status-2       { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
        .status-3       { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
        .status-4       { background: #ecfdf5; color: #047857; border: 1px solid #d1fae5; }
        .status-9       { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }
        .status-unknown { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }

        /* ✅ Progress Steps */
        .progress-steps {
            display: flex;
            align-items: center;
            gap: 0;
        }
        .step-dot {
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem;
            flex-shrink: 0;
            transition: all 0.2s;
        }
        .step-dot.done    { background: #d1fae5; color: #047857; border: 2px solid #6ee7b7; }
        .step-dot.active  { background: #4338ca; color: white;   border: 2px solid #4338ca; box-shadow: 0 0 0 4px rgba(67,56,202,0.15); }
        .step-dot.pending { background: #f1f5f9; color: #cbd5e1; border: 2px solid #e2e8f0; }
        .step-line {
            flex: 1; height: 2px; background: #e2e8f0; min-width: 16px; max-width: 28px;
        }
        .step-line.done { background: #6ee7b7; }

        .company-name { font-weight: 600; font-size: 1rem; }
        .sub-text     { color: var(--text-muted); font-size: 0.82rem; margin-top: 0.15rem; }
        .date-text    { color: var(--text-muted); font-weight: 500; font-size: 0.9rem; font-family: 'Inter', sans-serif; }

        .empty-state { padding: 4rem 2rem; text-align: center; }
        .empty-icon  { width: 80px; height: 80px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: #94a3b8; font-size: 2rem; }

        .btn-modern { background: var(--primary); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 500; transition: all 0.2s; text-decoration: none; display: inline-block; }
        .btn-modern:hover { background: var(--primary-hover); transform: translateY(-2px); color: white; }

        .footer-text { color: #94a3b8; font-size: 0.85rem; margin-top: 2rem; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0);    }
        }
    </style>
</head>
<body>

<?php include '../../includes/header.php'; ?>

    <div class="page-header">
        <div class="container text-center">
            <h1 class="page-title">ประวัติคำขอฝึกงาน</h1>
            <div class="mt-3 d-flex align-items-center justify-content-center gap-3 flex-wrap">
                <span class="student-badge">
                    <i class="fas fa-user-graduate"></i>
                    รหัสนิสิต: <?= htmlspecialchars($student_id) ?>
                </span>
                <a href="/SWUIS/form.php" class="btn-modern" style="padding:0.5rem 1.25rem; font-size:0.9rem;">
                    <i class="fas fa-plus me-1"></i> ยื่นคำขอใหม่
                </a>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="modern-card">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>บริษัท/สถานที่ฝึกงาน</th>
                                    <th>ช่วงเวลา</th>
                                    <th class="text-center">ขั้นตอนการดำเนินงาน</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($requests)): ?>
                                <?php foreach ($requests as $row): ?>
                                <tr>
                                    <td>
                                        <div class="company-name"><?= htmlspecialchars($row['companyname'] ?? '-') ?></div>
                                        <div class="sub-text">
                                            <?= htmlspecialchars($row['company_position'] ?? '') ?>
                                            <?php if (!empty($row['internship_type'])): ?>
                                            · <span><?= htmlspecialchars($row['internship_type']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="date-text">
                                            <i class="fas fa-calendar-alt text-muted me-1 small"></i>
                                            <?= !empty($row['start_date']) ? date('d/m/Y', strtotime($row['start_date'])) : '-' ?>
                                        </span>
                                        <?php if (!empty($row['end_date'])): ?>
                                        <br>
                                        <span class="date-text" style="font-size:0.8rem;">
                                            <i class="fas fa-flag-checkered text-muted me-1 small"></i>
                                            <?= date('d/m/Y', strtotime($row['end_date'])) ?>
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?= getProgressSteps($row['status'] ?? '') ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="p-0">
                                        <div class="empty-state">
                                            <div class="empty-icon"><i class="fas fa-folder-open"></i></div>
                                            <h5 class="fw-bold mb-2">ยังไม่มีประวัติการยื่นขอฝึกงาน</h5>
                                            <p class="text-muted mb-4">เริ่มต้นการยื่นขอฝึกงานครั้งแรกของคุณได้ที่นี่</p>
                                            <a href="/SWUIS/form.php" class="btn-modern">
                                                <i class="fas fa-plus me-2"></i>สร้างคำขอใหม่
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <p class="text-center footer-text">
                    © 2026 ระบบจัดการนิสิตฝึกงาน &middot; มหาวิทยาลัยศรีนครินทรวิโรฒ
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php include '../../includes/footer.php'; ?>
</body>
</html>