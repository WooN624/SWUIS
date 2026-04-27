<?php
/**
 * staff_dashboard.php — Dashboard สำหรับ Staff/Teacher
 */
require_once 'auth.php';
require_once 'db_connect.php';

require_role('admin', 'login.php');

$message = "";

// อัปเดตสถานะ
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_status'])) {
    $request_id = $_POST['request_id'];
    $new_status  = $_POST['status'];
    try {
        $stmt_update = $conn->prepare(
            "UPDATE internship_request SET status = :status WHERE requestid = :id"
        );
        $stmt_update->execute([':status' => $new_status, ':id' => $request_id]);

        // ✅ เพิ่ม: บันทึก status_log ทุกครั้งที่มีการเปลี่ยนสถานะ
        $staffID = $_SESSION['user_id'] ?? null;
        $stmt_log = $conn->prepare(
            "INSERT INTO status_log (requestid, stdid, status, staffID)
             SELECT :requestid, stdid, :status, :staffID
             FROM internship_request WHERE requestid = :requestid2"
        );
        $stmt_log->execute([
            ':requestid'  => $request_id,
            ':status'     => $new_status,
            ':staffID'    => $staffID,
            ':requestid2' => $request_id,
        ]);

        $message = "<div class='message success mb-4'>
                        <i class='fas fa-check-circle fs-5'></i>
                        อัปเดตสถานะคำขอ ID: " . htmlspecialchars($request_id) . " เป็น \"" . htmlspecialchars($new_status) . "\" สำเร็จ!
                    </div>";
    } catch (PDOException $e) {
        $message = "<div class='message error mb-4'>
                        <i class='fas fa-exclamation-circle fs-5'></i>
                        เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage()) . "
                    </div>";
    }
}

// ดึงข้อมูลทั้งหมดพร้อม JOIN
try {
    $all_requests = $conn->query(
        "SELECT 
            r.requestid,
            r.stdid,
            s.stdname,
            c.companyname,
            r.company_position,
            r.start_date,
            r.end_date,
            r.internship_type,
            r.status
         FROM internship_request r
         LEFT JOIN student s  ON r.stdid     = s.stdid
         LEFT JOIN company c  ON r.companyid = c.companyid
         ORDER BY r.requestid DESC"
    )->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("เกิดข้อผิดพลาด: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการฝึกงาน (Staff Dashboard)</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --swu-red:       #c8102e;
            --swu-dark-red:  #9e0b23;
            --swu-light-red: #fde8eb;
            --bg-color:      #f8fafc;
            --card-bg:       rgba(255, 255, 255, 0.98);
            --text-main:     #1e293b;
            --text-muted:    #64748b;
        }

        body {
            font-family: 'Prompt', 'Inter', sans-serif;
            background-color: var(--bg-color);
            background-image:
                radial-gradient(at 0%   0%,   rgba(200,16,46,0.03) 0, transparent 40%),
                radial-gradient(at 100% 100%,  rgba(200,16,46,0.03) 0, transparent 40%);
            background-attachment: fixed;
            color: var(--text-main);
            min-height: 100vh;
            line-height: 1.6;
        }

        .admin-header {
            background: linear-gradient(135deg, var(--swu-dark-red) 0%, var(--swu-red) 100%);
            color: white;
            padding: 3rem 0 5rem;
            border-radius: 0 0 2rem 2rem;
            box-shadow: 0 20px 40px -10px rgba(200,16,46,0.2);
            margin-bottom: -3rem;
            position: relative;
            z-index: 1;
        }

        .header-title {
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            letter-spacing: 0.5px;
        }

        .header-icon {
            background: rgba(255,255,255,0.15);
            padding: 1rem;
            border-radius: 1rem;
            backdrop-filter: blur(5px);
        }

        .modern-container {
            max-width: 1320px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            padding: 0 1.5rem;
        }

        .modern-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.9);
            border-radius: 1.5rem;
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.05);
            padding: 3rem;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .message {
            padding: 1.25rem 1.5rem;
            border-radius: 1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: fadeIn 0.4s ease;
        }
        .message.success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .message.error   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        /* ── Status badge (read-only display) ── */
        .status-badge {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 0.35rem 0.85rem; border-radius: 50px;
            font-size: 0.82rem; font-weight: 600; letter-spacing: 0.3px;
        }
        .status-1       { background: #eef2ff; color: #4338ca; border: 1px solid #e0e7ff; }
        .status-2       { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
        .status-3       { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
        .status-4       { background: #ecfdf5; color: #047857; border: 1px solid #d1fae5; }
        .status-9       { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }
        .status-unknown { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }

        .table {
            border-collapse: separate;
            border-spacing: 0 12px;
            margin-top: -1rem;
            width: 100%;
        }

        .table thead th {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1.5rem 1.25rem 1rem;
            border-bottom: 2px solid #f1f5f9;
            text-align: center;
            vertical-align: middle;
        }

        .table tbody tr {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -8px rgba(0,0,0,0.06);
        }

        .table tbody td {
            border: none;
            padding: 1.25rem 1.25rem;
            vertical-align: middle;
            text-align: center;
        }

        .table tbody td:first-child  { border-radius: 1rem 0 0 1rem; }
        .table tbody td:last-child   { border-radius: 0 1rem 1rem 0; }
        .text-start-td { text-align: left !important; }

        .status-form {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            margin: 0;
        }

        .custom-select {
            padding: 0.55rem 2.25rem 0.55rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            background: #f8fafc url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") no-repeat right 0.75rem center / 0.9rem;
            color: var(--text-main);
            font-family: 'Prompt', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            appearance: none;
            min-width: 190px;
            transition: all 0.2s;
        }

        .custom-select:focus {
            outline: none;
            border-color: var(--swu-red);
            box-shadow: 0 0 0 3px var(--swu-light-red);
            background-color: white;
        }

        .btn-save {
            padding: 0.55rem 1.25rem;
            background: var(--swu-red);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-family: 'Prompt', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-save:hover {
            background: var(--swu-dark-red);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(200,16,46,0.25);
        }

        .student-name { font-weight: 500; font-size: 1rem; }
        .sub-text     { font-weight: 400; color: #94a3b8; font-size: 0.82rem; margin-top: 0.2rem; }

        .student-id {
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            color: var(--swu-red);
            background: var(--swu-light-red);
            padding: 0.3rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.88rem;
            letter-spacing: 0.5px;
        }
        .req-id {
            font-family: 'Inter', sans-serif;
            color: var(--text-muted);
            font-size: 0.88rem;
            font-weight: 600;
            background: #f1f5f9;
            padding: 0.3rem 0.6rem;
            border-radius: 0.4rem;
        }
        .type-badge {
            font-size: 0.75rem;
            padding: 0.2rem 0.6rem;
            border-radius: 50px;
            background: #f1f5f9;
            color: #475569;
            font-weight: 500;
        }

        .empty-state { padding: 5rem 2rem; text-align: center; }
        .empty-icon  { font-size: 3.5rem; color: #cbd5e1; margin-bottom: 1.5rem; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0);    }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        @media (max-width: 992px) {
            .modern-card  { padding: 1.5rem; }
            .status-form  { flex-direction: column; width: 100%; gap: 0.5rem; }
            .custom-select, .btn-save { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>   <!-- ✅ ย้ายมาอยู่ใน <body> แล้ว -->

    <div class="admin-header">
        <div class="container text-center">
            <h1 class="header-title">
                <span class="header-icon"><i class="fas fa-user-shield"></i></span>
                ระบบจัดการฝึกงาน
            </h1>
            <p class="mt-2 mb-0" style="opacity:0.75; font-size:0.95rem;">
                จัดการสถานะคำขอฝึกงานทั้งหมดของนิสิต
            </p>
        </div>
    </div>

    <div class="modern-container mb-5 pt-5">
        <div class="modern-card">

            <?php echo $message; ?>

            <!-- สถิติด่วน -->
            <?php
            $counts = ['กำลังดำเนินการ'=>0,'อนุมัติ'=>0,'ออกใบส่งตัว'=>0,'เสร็จสิ้น'=>0,'ปฏิเสธ'=>0];
            foreach ($all_requests as $r) {
                if (isset($counts[$r['status']])) $counts[$r['status']]++;
            }
            ?>
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3 col-lg">
                    <div class="p-3 rounded-3 text-center" style="background:#eef2ff; border:1px solid #e0e7ff;">
                        <div style="font-size:1.5rem;font-weight:700;color:#4338ca"><?= $counts['กำลังดำเนินการ'] ?></div>
                        <div style="font-size:0.8rem;color:#4338ca;font-weight:500">กำลังดำเนินการ</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg">
                    <div class="p-3 rounded-3 text-center" style="background:#fffbeb; border:1px solid #fef3c7;">
                        <div style="font-size:1.5rem;font-weight:700;color:#b45309"><?= $counts['อนุมัติ'] ?></div>
                        <div style="font-size:0.8rem;color:#b45309;font-weight:500">อนุมัติแล้ว</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg">
                    <div class="p-3 rounded-3 text-center" style="background:#f0fdf4; border:1px solid #dcfce7;">
                        <div style="font-size:1.5rem;font-weight:700;color:#15803d"><?= $counts['ออกใบส่งตัว'] ?></div>
                        <div style="font-size:0.8rem;color:#15803d;font-weight:500">ออกใบส่งตัว</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg">
                    <div class="p-3 rounded-3 text-center" style="background:#ecfdf5; border:1px solid #d1fae5;">
                        <div style="font-size:1.5rem;font-weight:700;color:#047857"><?= $counts['เสร็จสิ้น'] ?></div>
                        <div style="font-size:0.8rem;color:#047857;font-weight:500">เสร็จสิ้น</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg">
                    <div class="p-3 rounded-3 text-center" style="background:#fef2f2; border:1px solid #fee2e2;">
                        <div style="font-size:1.5rem;font-weight:700;color:#b91c1c"><?= $counts['ปฏิเสธ'] ?></div>
                        <div style="font-size:0.8rem;color:#b91c1c;font-weight:500">ปฏิเสธ</div>
                    </div>
                </div>
            </div>

            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="8%">ID</th>
                            <th width="13%">รหัสนิสิต</th>
                            <th width="18%" class="text-start-td">ชื่อ-นามสกุล</th>
                            <th width="20%" class="text-start-td">หน่วยงาน/บริษัท</th>
                            <th width="10%">ช่วงเวลา</th>
                            <th width="31%">จัดการสถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($all_requests)): ?>
                        <?php foreach ($all_requests as $row): 
                            $statusMap = [
                                'กำลังดำเนินการ' => 'status-1',
                                'อนุมัติ'        => 'status-2',
                                'ออกใบส่งตัว'    => 'status-3',
                                'เสร็จสิ้น'      => 'status-4',
                                'ปฏิเสธ'         => 'status-9',
                            ];
                            $sc = $statusMap[$row['status']] ?? 'status-unknown';
                        ?>
                        <tr>
                            <td><span class="req-id">#<?= htmlspecialchars($row['requestid'] ?? '') ?></span></td>
                            <td><span class="student-id"><?= htmlspecialchars($row['stdid'] ?? '') ?></span></td>
                            <td class="text-start-td">
                                <div class="student-name"><?= htmlspecialchars($row['stdname'] ?? '-') ?></div>
                                <div class="sub-text"><?= htmlspecialchars($row['internship_type'] ?? '') ?></div>
                            </td>
                            <td class="text-start-td">
                                <div style="font-weight:500; font-size:0.95rem;">
                                    <i class="far fa-building me-1 text-muted"></i>
                                    <?= htmlspecialchars($row['companyname'] ?? '-') ?>
                                </div>
                                <div class="sub-text"><?= htmlspecialchars($row['company_position'] ?? '') ?></div>
                            </td>
                            <td>
                                <?php if (!empty($row['start_date'])): ?>
                                <div style="font-size:0.82rem; color:#475569; font-family:'Inter',sans-serif;">
                                    <?= date('d/m/y', strtotime($row['start_date'])) ?>
                                    <br>→ <?= !empty($row['end_date']) ? date('d/m/y', strtotime($row['end_date'])) : '-' ?>
                                </div>
                                <?php else: ?><span class="text-muted">-</span><?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" class="status-form">
                                    <input type="hidden" name="request_id" value="<?= htmlspecialchars($row['requestid'] ?? '') ?>">
                                    <select name="status" class="custom-select">
                                        <?php
                                        $options = [
                                            'กำลังดำเนินการ' => '1: กำลังดำเนินการ',
                                            'อนุมัติ'        => '2: อนุมัติ',
                                            'ออกใบส่งตัว'    => '3: ออกใบส่งตัวแล้ว',
                                            'เสร็จสิ้น'      => '4: ฝึกงานเสร็จสิ้น',
                                            'ปฏิเสธ'         => '9: ปฏิเสธ',
                                        ];
                                        foreach ($options as $val => $label):
                                            $sel = (isset($row['status']) && $row['status'] === $val) ? 'selected' : '';
                                        ?>
                                            <option value="<?= $val ?>" <?= $sel ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="update_status" class="btn-save">
                                        <i class="fas fa-save"></i> บันทึก
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="p-0">
                                <div class="empty-state">
                                    <i class="fas fa-inbox empty-icon"></i>
                                    <h5 class="fw-bold text-muted mt-3">ยังไม่มีข้อมูลคำขอฝึกงานในระบบ</h5>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'footer.php'; ?>
</body>
</html>