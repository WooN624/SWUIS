<?php
require_once '../../includes/auth.php';
require_role('teacher');
?>
<?php
session_start();
require_once '../../includes/db_connect.php';

// TODO: เปลี่ยนเป็น session จริงเมื่อมีระบบ login
$_SESSION['logged_in_teacher_id'] = '123';
$teacher_id = $_SESSION['logged_in_teacher_id'];

$message = "";

// อนุมัติ
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approve'])) {
    $id = $_POST['request_id'];
    try {
        $stmt = $conn->prepare("UPDATE internship_request SET status = 'อนุมัติ' WHERE requestid = :id");
        $stmt->execute([':id' => $id]);
        $message = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <i class='fas fa-check-circle me-2'></i>อนุมัติคำขอเรียบร้อยแล้ว
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>";
    } catch(PDOException $e) {
        $message = "<div class='alert alert-danger'>เกิดข้อผิดพลาด: " . $e->getMessage() . "</div>";
    }
}

// ไม่อนุมัติ
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reject'])) {
    $id = $_POST['request_id'];
    try {
        $stmt = $conn->prepare("UPDATE internship_request SET status = 'ปฏิเสธ' WHERE requestid = :id");
        $stmt->execute([':id' => $id]);
        $message = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                        <i class='fas fa-times-circle me-2'></i>ไม่อนุมัติคำขอเรียบร้อยแล้ว
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>";
    } catch(PDOException $e) {
        $message = "<div class='alert alert-danger'>เกิดข้อผิดพลาด: " . $e->getMessage() . "</div>";
    }
}

// ดึงข้อมูลทั้งหมดพร้อม JOIN
try {
    $stmt = $conn->prepare("
        SELECT 
            r.requestid,
            r.stdid,
            s.stdname,
            s.email       AS student_email,
            s.phone       AS student_tel,
            r.companyid,
            COALESCE(r.company_name, c.companyname) AS companyname,
            c.email       AS company_email,
            c.address     AS company_address,
            r.duration,
            r.status,
            r.company_position,
            r.coordinator_name,
            r.company_tel,
            r.start_date,
            r.end_date,
            r.internship_type,
            r.student_year,
            r.student_major
        FROM internship_request r
        LEFT JOIN student s  ON r.stdid     = s.stdid
        LEFT JOIN company c  ON r.companyid = c.companyid
        ORDER BY r.requestid DESC
    ");
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("เกิดข้อผิดพลาด: " . $e->getMessage());
}

function getStatusBadge($status) {
    $map = [
        'กำลังดำเนินการ' => ['class' => 'status-1', 'icon' => 'fa-paper-plane',        'label' => 'กำลังดำเนินการ'],
        'อนุมัติ'        => ['class' => 'status-2', 'icon' => 'fa-user-check',         'label' => 'อนุมัติ'],
        'ออกใบส่งตัว'    => ['class' => 'status-3', 'icon' => 'fa-envelope-open-text', 'label' => 'ออกใบส่งตัวแล้ว'],
        'เสร็จสิ้น'      => ['class' => 'status-4', 'icon' => 'fa-check-circle',       'label' => 'ฝึกงานเสร็จสิ้น'],
        'ปฏิเสธ'         => ['class' => 'status-9', 'icon' => 'fa-times-circle',       'label' => 'ปฏิเสธ'],
    ];
    $s = $map[$status] ?? ['class' => 'status-9', 'icon' => 'fa-question-circle', 'label' => htmlspecialchars($status)];
    return "<span class='status-badge {$s['class']}'><i class='fas {$s['icon']} me-1'></i>{$s['label']}</span>";
}
?>

<?php include '../../includes/header.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .dashboard-wrapper {
        background-color: #f8fafc;
        padding: 40px 20px;
        min-height: calc(100vh - 160px);
    }
    .page-title {
        font-weight: 700;
        font-size: 2rem;
        background: linear-gradient(135deg, #9e0b23 0%, #c8102e 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        color: transparent;
        margin-bottom: 0.5rem;
    }
    .modern-card {
        background: rgba(255,255,255,0.95);
        border-radius: 24px;
        box-shadow: 0 20px 40px -15px rgba(0,0,0,0.05);
        padding: 2rem;
    }
    .table { border-collapse: separate; border-spacing: 0 12px; margin-bottom: 0; }
    .table thead th { background: transparent; border: none; color: #63666a; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; padding: 0 1.5rem 0.5rem; }
    .table tbody tr { background: white; border-radius: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s ease; }
    .table tbody tr:hover { transform: translateY(-2px); box-shadow: 0 12px 20px -8px rgba(0,0,0,0.08); }
    .table tbody td { border: none; padding: 1.25rem 1.5rem; vertical-align: middle; }
    .table tbody td:first-child { border-top-left-radius: 16px; border-bottom-left-radius: 16px; }
    .table tbody td:last-child { border-top-right-radius: 16px; border-bottom-right-radius: 16px; }

    .status-badge { display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 1rem; border-radius: 50px; font-size: 0.85rem; font-weight: 600; }
    .status-1 { background: #eef2ff; color: #4338ca; border: 1px solid #e0e7ff; }
    .status-2 { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
    .status-3 { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
    .status-4 { background: #ecfdf5; color: #047857; border: 1px solid #d1fae5; }
    .status-9 { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }

    .student-name { font-weight: 600; color: #c8102e; }
    .company-name { font-weight: 600; color: #0f172a; font-size: 1.05rem; }
    .date-text { color: #63666a; font-weight: 500; font-size: 0.95rem; }

    .btn-outline-modern { background: transparent; color: #0f172a; border: 1px solid #e2e8f0; padding: 0.5rem 1rem; border-radius: 10px; font-weight: 500; font-size: 0.9rem; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; }
    .btn-outline-modern:hover { background: #f8fafc; border-color: #cbd5e1; }
    .btn-success-modern { background: #16a34a; color: white; border: none; padding: 0.5rem 1rem; border-radius: 10px; font-weight: 500; font-size: 0.9rem; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; }
    .btn-success-modern:hover { background: #15803d; transform: translateY(-2px); }
    .btn-danger-modern { background: #dc2626; color: white; border: none; padding: 0.5rem 1rem; border-radius: 10px; font-weight: 500; font-size: 0.9rem; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; }
    .btn-danger-modern:hover { background: #b91c1c; transform: translateY(-2px); color: white; }

    .modal-content { border: none; border-radius: 20px; overflow: hidden; }
    .modal-header { background: #fef2f2; border-bottom: 1px solid #fee2e2; padding: 1.5rem 2rem; }
    .info-group { margin-bottom: 1rem; }
    .info-label { font-size: 0.8rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.25rem; }
    .info-value { font-weight: 500; color: #0f172a; }
</style>

<div class="dashboard-wrapper">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <div>
                <h1 class="page-title">ระบบจัดการฝึกงาน</h1>
            </div>
            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill fs-6">
                <i class="fas fa-chalkboard-teacher text-danger me-2"></i>อาจารย์ที่ปรึกษา
            </span>
        </div>

        <?php if ($message != "") echo $message; ?>

        <div class="modern-card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ข้อมูลนิสิต</th>
                            <th>ข้อมูลการฝึกงาน</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-center" style="width:200px;">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($requests) > 0): ?>
                            <?php foreach ($requests as $row): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-light p-3 rounded-circle text-danger">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <div class="student-name"><?= htmlspecialchars($row['stdname'] ?? '-') ?></div>
                                            <div class="date-text">รหัส: <?= htmlspecialchars($row['stdid'] ?? '-') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="company-name"><?= htmlspecialchars($row['companyname'] ?? '-') ?></div>
                                    <div class="date-text mt-1">
                                        <i class="far fa-calendar-alt text-muted me-1 small"></i>
                                        <?= !empty($row['start_date']) ? date('d/m/Y', strtotime($row['start_date'])) : '-' ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?= getStatusBadge($row['status'] ?? '') ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-2 align-items-center">
                                        <!-- ปุ่มรายละเอียด -->
                                        <button type="button"
                                            class="btn-outline-modern w-100 justify-content-center"
                                            data-bs-toggle="modal"
                                            data-bs-target="#detailModal<?= htmlspecialchars($row['requestid']) ?>">
                                            <i class="fas fa-search"></i> รายละเอียด
                                        </button>
                                        <!-- ปุ่มอนุมัติ/ปฏิเสธ เฉพาะสถานะ กำลังดำเนินการ -->
                                        <?php if ($row['status'] === 'กำลังดำเนินการ'): ?>
                                        <form method="POST" class="w-100 d-flex flex-column gap-2">
                                            <input type="hidden" name="request_id" value="<?= htmlspecialchars($row['requestid']) ?>">
                                            <button type="submit" name="approve" class="btn-success-modern w-100 justify-content-center">
                                                <i class="fas fa-check"></i> อนุมัติ
                                            </button>
                                            <button type="submit" name="reject" class="btn-danger-modern w-100 justify-content-center"
                                                onclick="return confirm('ยืนยันที่จะไม่อนุมัติคำขอนี้ใช่หรือไม่?');">
                                                <i class="fas fa-times"></i> ไม่อนุมัติ
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal รายละเอียด -->
                            <div class="modal fade" id="detailModal<?= htmlspecialchars($row['requestid']) ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-info-circle text-danger me-2"></i>รายละเอียดคำขอฝึกงาน
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="text-danger border-bottom pb-2 mb-3">
                                                        <i class="fas fa-user-graduate me-2"></i>ข้อมูลนิสิต
                                                    </h6>
                                                    <div class="info-group">
                                                        <div class="info-label">รหัสนิสิต</div>
                                                        <div class="info-value"><?= htmlspecialchars($row['stdid'] ?? '-') ?></div>
                                                    </div>
                                                    <div class="info-group">
                                                        <div class="info-label">ชื่อ-นามสกุล</div>
                                                        <div class="info-value"><?= htmlspecialchars($row['stdname'] ?? '-') ?></div>
                                                    </div>
                                                    <div class="info-group">
                                                        <div class="info-label">ชั้นปี / สาขาวิชา</div>
                                                        <div class="info-value">
                                                            ปี <?= htmlspecialchars($row['student_year'] ?? '-') ?>
                                                            - <?= htmlspecialchars($row['student_major'] ?? '-') ?>
                                                        </div>
                                                    </div>
                                                    <div class="info-group">
                                                        <div class="info-label">ติดต่อ</div>
                                                        <div class="info-value">
                                                            <i class="fas fa-phone-alt text-muted me-1"></i><?= htmlspecialchars($row['student_tel'] ?? '-') ?><br>
                                                            <i class="fas fa-envelope text-muted me-1"></i><?= htmlspecialchars($row['student_email'] ?? '-') ?>
                                                        </div>
                                                    </div>
                                                    <div class="info-group">
                                                        <div class="info-label">รูปแบบ</div>
                                                        <div class="info-value"><?= htmlspecialchars($row['internship_type'] ?? '-') ?></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="text-danger border-bottom pb-2 mb-3">
                                                        <i class="fas fa-building me-2"></i>ข้อมูลสถานที่ฝึกงาน
                                                    </h6>
                                                    <div class="info-group">
                                                        <div class="info-label">ชื่อหน่วยงาน</div>
                                                        <div class="info-value"><?= htmlspecialchars($row['companyname'] ?? '-') ?></div>
                                                    </div>
                                                    <div class="info-group">
                                                        <div class="info-label">ตำแหน่ง</div>
                                                        <div class="info-value"><?= htmlspecialchars($row['company_position'] ?? '-') ?></div>
                                                    </div>
                                                    <div class="info-group">
                                                        <div class="info-label">ผู้ประสานงาน</div>
                                                        <div class="info-value"><?= htmlspecialchars($row['coordinator_name'] ?? '-') ?></div>
                                                    </div>
                                                    <div class="info-group">
                                                        <div class="info-label">ติดต่อหน่วยงาน</div>
                                                        <div class="info-value">
                                                            <i class="fas fa-phone-alt text-muted me-1"></i><?= htmlspecialchars($row['company_tel'] ?? '-') ?><br>
                                                            <i class="fas fa-envelope text-muted me-1"></i><?= htmlspecialchars($row['company_email'] ?? '-') ?>
                                                        </div>
                                                    </div>
                                                    <div class="info-group">
                                                        <div class="info-label">ระยะเวลาฝึกงาน</div>
                                                        <div class="info-value">
                                                            <?= !empty($row['start_date']) ? date('d/m/Y', strtotime($row['start_date'])) : '-' ?>
                                                            ถึง
                                                            <?= !empty($row['end_date']) ? date('d/m/Y', strtotime($row['end_date'])) : '-' ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0 bg-light">
                                            <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">ปิด</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="fas fa-folder-open mb-3" style="font-size:3rem; color:#cbd5e1;"></i>
                                    <h5 class="text-muted">ไม่มีข้อมูลคำขอฝึกงาน</h5>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <p class="text-center mt-4" style="color:#94a3b8; font-size:0.85rem;">
            © 2026 ระบบจัดการนิสิตฝึกงาน &middot; มหาวิทยาลัยศรีนครินทรวิโรฒ
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php include '../../includes/footer.php'; ?>