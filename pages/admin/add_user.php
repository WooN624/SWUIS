<?php
/**
 * add_user.php — เพิ่มข้อมูลนิสิต / อาจารย์ (สำหรับ Admin เท่านั้น)
 */
require_once '../../includes/auth.php';
require_once '../../includes/db_connect.php';

require_role('admin', 'login.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';

    try {
        if ($type === 'student') {
            $stdid   = trim($_POST['stdid']   ?? '');
            $stdname = trim($_POST['stdname'] ?? '');
            $email   = trim($_POST['email']   ?? '');
            $phone   = trim($_POST['phone']   ?? '');

            if (!$stdid || !$stdname || !$email) {
                throw new Exception('กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน');
            }

            $stmt = $conn->prepare(
                "INSERT INTO student (stdid, stdname, email, phone) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$stdid, $stdname, $email, $phone]);
            $message = "<div class='message success'><i class='fas fa-check-circle'></i> เพิ่มข้อมูลนิสิต <strong>" . htmlspecialchars($stdname) . "</strong> สำเร็จ!</div>";

        } elseif ($type === 'teacher') {
            $tchID   = trim($_POST['tchID']   ?? '');
            $tchname = trim($_POST['tchname'] ?? '');
            $email   = trim($_POST['email']   ?? '');
            $phone   = trim($_POST['phone']   ?? '');

            if (!$tchID || !$tchname || !$email) {
                throw new Exception('กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน');
            }

            $stmt = $conn->prepare(
                "INSERT INTO teacher (tchID, tchname, email, phone) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$tchID, $tchname, $email, $phone]);
            $message = "<div class='message success'><i class='fas fa-check-circle'></i> เพิ่มข้อมูลอาจารย์ <strong>" . htmlspecialchars($tchname) . "</strong> สำเร็จ!</div>";
        }

    } catch (PDOException $e) {
        // รหัสซ้ำ
        if ($e->getCode() === '23000') {
            $message = "<div class='message error'><i class='fas fa-exclamation-circle'></i> รหัสนี้มีอยู่ในระบบแล้ว กรุณาตรวจสอบอีกครั้ง</div>";
        } else {
            $message = "<div class='message error'><i class='fas fa-exclamation-circle'></i> เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } catch (Exception $e) {
        $message = "<div class='message error'><i class='fas fa-exclamation-circle'></i> " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// ดึงรายชื่อนิสิตและอาจารย์ทั้งหมด
$students = $conn->query("SELECT * FROM student ORDER BY stdid DESC")->fetchAll(PDO::FETCH_ASSOC);
$teachers = $conn->query("SELECT * FROM teacher ORDER BY tchID DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการบัญชีผู้ใช้ — Information Studies SWU</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --swu-red:       #c8102e;
            --swu-dark-red:  #9e0b23;
            --swu-light-red: #fde8eb;
            --bg-color:      #f8fafc;
            --card-bg:       rgba(255,255,255,0.98);
            --text-main:     #1e293b;
            --text-muted:    #64748b;
        }

        body {
            font-family: 'Prompt', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
        }

        .admin-header {
            background: linear-gradient(135deg, var(--swu-dark-red) 0%, var(--swu-red) 100%);
            color: white;
            padding: 3rem 0 5rem;
            border-radius: 0 0 2rem 2rem;
            margin-bottom: -3rem;
            position: relative;
            z-index: 1;
        }

        .modern-container {
            max-width: 1100px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            padding: 0 1.5rem;
        }

        .modern-card {
            background: var(--card-bg);
            border-radius: 1.5rem;
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.08);
            padding: 2.5rem;
            margin-bottom: 2rem;
            animation: slideUp 0.5s cubic-bezier(0.16,1,0.3,1);
        }

        /* ── Tabs ── */
        .tab-nav {
            display: flex;
            gap: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 2rem;
        }
        .tab-btn {
            padding: 0.6rem 1.4rem;
            border: none;
            background: none;
            font-family: 'Prompt', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-muted);
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .tab-btn.active {
            color: var(--swu-red);
            border-bottom-color: var(--swu-red);
        }
        .tab-pane { display: none; }
        .tab-pane.active { display: block; }

        /* ── Form ── */
        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--swu-dark-red);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.4rem;
        }
        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 0.8rem;
            padding: 0.75rem 1rem;
            font-family: 'Prompt', sans-serif;
            transition: all 0.2s;
        }
        .form-control:focus {
            border-color: var(--swu-red);
            box-shadow: 0 0 0 3px rgba(200,16,46,0.1);
        }
        .required-mark { color: var(--swu-red); }

        .btn-add {
            background: linear-gradient(45deg, var(--swu-dark-red), var(--swu-red));
            color: white;
            border: none;
            border-radius: 0.8rem;
            padding: 0.75rem 2rem;
            font-family: 'Prompt', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            box-shadow: 0 8px 16px rgba(200,16,46,0.3);
            transition: all 0.2s;
        }
        .btn-add:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }

        /* ── Messages ── */
        .message {
            padding: 1rem 1.25rem;
            border-radius: 0.8rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        .message.success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .message.error   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        /* ── Table ── */
        .table thead th {
            background: #f8fafc;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            border-bottom: 2px solid #e2e8f0;
            padding: 1rem;
        }
        .table tbody td { padding: 0.85rem 1rem; vertical-align: middle; font-size: 0.9rem; }
        .table tbody tr:hover { background: #fafafa; }

        .badge-id {
            background: #eef2ff;
            color: #4338ca;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 700;
            font-family: monospace;
        }

        .section-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section-title::before {
            content: '';
            display: block;
            width: 4px;
            height: 1.2rem;
            background: var(--swu-red);
            border-radius: 2px;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <?php include '../../includes/header.php'; ?>

    <div class="admin-header">
        <div class="container text-center">
            <h1 style="font-weight:700; display:flex; align-items:center; justify-content:center; gap:1rem;">
                <span style="background:rgba(255,255,255,0.15); padding:1rem; border-radius:1rem;">
                    <i class="fas fa-user-plus"></i>
                </span>
                จัดการบัญชีผู้ใช้
            </h1>
            <p style="opacity:0.75; margin:0.5rem 0 0;">เพิ่มข้อมูลนิสิตและอาจารย์เข้าสู่ระบบ</p>
        </div>
    </div>

    <div class="modern-container mb-5 pt-5">
        <div class="modern-card">

            <?php if ($message) echo $message; ?>

            <!-- Tabs -->
            <div class="tab-nav">
                <button class="tab-btn active" onclick="switchTab('student', this)">
                    <i class="fas fa-user-graduate me-1"></i> นิสิต
                </button>
                <button class="tab-btn" onclick="switchTab('teacher', this)">
                    <i class="fas fa-chalkboard-teacher me-1"></i> อาจารย์
                </button>
            </div>

            <!-- ── Tab: นิสิต ── -->
            <div id="tab-student" class="tab-pane active">

                <!-- ฟอร์มเพิ่มนิสิต -->
                <p class="section-title">เพิ่มนิสิตใหม่</p>
                <form method="POST">
                    <input type="hidden" name="type" value="student">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">รหัสนิสิต <span class="required-mark">*</span></label>
                            <input type="text" name="stdid" class="form-control" placeholder="เช่น 67101010999" required maxlength="11">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ชื่อ-นามสกุล <span class="required-mark">*</span></label>
                            <input type="text" name="stdname" class="form-control" placeholder="ชื่อ นามสกุล" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">อีเมล <span class="required-mark">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="xxx@g.swu.ac.th" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">เบอร์โทร</label>
                            <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxx" maxlength="15">
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn-add">
                            <i class="fas fa-plus me-1"></i> เพิ่มนิสิต
                        </button>
                    </div>
                </form>

                <hr style="border-color:#f1f5f9; margin: 2rem 0;">

                <!-- ตารางนิสิตทั้งหมด -->
                <p class="section-title">รายชื่อนิสิตทั้งหมด (<?= count($students) ?> คน)</p>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>รหัสนิสิต</th>
                                <th>ชื่อ-นามสกุล</th>
                                <th>อีเมล</th>
                                <th>เบอร์โทร</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($students): ?>
                                <?php foreach ($students as $s): ?>
                                <tr>
                                    <td><span class="badge-id"><?= htmlspecialchars($s['stdid']) ?></span></td>
                                    <td><?= htmlspecialchars($s['stdname']) ?></td>
                                    <td style="color:var(--text-muted); font-size:0.85rem;"><?= htmlspecialchars($s['email'] ?? '-') ?></td>
                                    <td style="color:var(--text-muted); font-size:0.85rem;"><?= htmlspecialchars($s['phone'] ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">ยังไม่มีข้อมูลนิสิต</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ── Tab: อาจารย์ ── -->
            <div id="tab-teacher" class="tab-pane">

                <!-- ฟอร์มเพิ่มอาจารย์ -->
                <p class="section-title">เพิ่มอาจารย์ใหม่</p>
                <form method="POST">
                    <input type="hidden" name="type" value="teacher">
                    <div class="row g-3 mb-3">
                        <div class="col-md-2">
                            <label class="form-label">รหัสอาจารย์ <span class="required-mark">*</span></label>
                            <input type="text" name="tchID" class="form-control" placeholder="เช่น 131" required maxlength="10">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ชื่อ-นามสกุล (พร้อมตำแหน่ง) <span class="required-mark">*</span></label>
                            <input type="text" name="tchname" class="form-control" placeholder="อาจารย์ ดร. ชื่อ นามสกุล" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">อีเมล <span class="required-mark">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="xxx@g.swu.ac.th" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">เบอร์โทร</label>
                            <input type="text" name="phone" class="form-control" placeholder="026495000" maxlength="15">
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn-add">
                            <i class="fas fa-plus me-1"></i> เพิ่มอาจารย์
                        </button>
                    </div>
                </form>

                <hr style="border-color:#f1f5f9; margin: 2rem 0;">

                <!-- ตารางอาจารย์ทั้งหมด -->
                <p class="section-title">รายชื่ออาจารย์ทั้งหมด (<?= count($teachers) ?> คน)</p>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>รหัส</th>
                                <th>ชื่อ-นามสกุล</th>
                                <th>อีเมล</th>
                                <th>เบอร์โทร</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($teachers): ?>
                                <?php foreach ($teachers as $t): ?>
                                <tr>
                                    <td><span class="badge-id"><?= htmlspecialchars($t['tchID']) ?></span></td>
                                    <td><?= htmlspecialchars($t['tchname']) ?></td>
                                    <td style="color:var(--text-muted); font-size:0.85rem;"><?= htmlspecialchars($t['email'] ?? '-') ?></td>
                                    <td style="color:var(--text-muted); font-size:0.85rem;"><?= htmlspecialchars($t['phone'] ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">ยังไม่มีข้อมูลอาจารย์</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function switchTab(tab, btn) {
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('tab-' + tab).classList.add('active');
            btn.classList.add('active');
        }

        // ถ้า submit แล้วให้เปิด tab ที่ถูกต้องอัตโนมัติ
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['type'])): ?>
        document.addEventListener('DOMContentLoaded', () => {
            const type = '<?= $_POST['type'] ?>';
            const btn = document.querySelector(`.tab-btn[onclick*="${type}"]`);
            if (btn) switchTab(type, btn);
        });
        <?php endif; ?>
    </script>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>