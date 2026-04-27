<?php
require_once 'includes/auth.php';
require_once 'includes/db_connect.php';

// ดึง stdid จาก session อัตโนมัติ
$student_id = $_SESSION['user_id'] ?? '';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_year  = $_POST['student_year'] ?? '';
    $student_major = $_POST['student_major'] ?? '';
    $internship_type  = $_POST['internship_type'] ?? '';
    $company_position = $_POST['company_position'] ?? '';
    $company_name     = trim($_POST['company_name'] ?? '');
    $coordinator_name = $_POST['coordinator_name'] ?? '';
    $company_tel   = $_POST['company_tel'] ?? '';
    $start_date    = $_POST['start_date'] ?? '';
    $end_date      = $_POST['end_date'] ?? '';
    $status = 'กำลังดำเนินการ';

    if (empty($student_id) || empty($company_position) || empty($start_date)) {
        $message = "<div class='message error'>กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน</div>";
    } else {
        try {
            // Generate requestid ไม่ซ้ำ
            do {
                $requestid = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
                $chk = $conn->prepare('SELECT requestid FROM internship_request WHERE requestid = ?');
                $chk->execute([$requestid]);
            } while ($chk->fetch());

            $sql = "INSERT INTO internship_request (
                        requestid, stdid, student_year, student_major, internship_type,
                        company_position, company_name, coordinator_name, company_tel,
                        start_date, end_date, status
                    ) VALUES (
                        :requestid, :stdid, :student_year, :student_major, :internship_type,
                        :company_position, :company_name, :coordinator_name, :company_tel,
                        :start_date, :end_date, :status
                    )";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':requestid',        $requestid);
            $stmt->bindParam(':stdid',            $student_id);
            $stmt->bindParam(':student_year',     $student_year);
            $stmt->bindParam(':student_major',    $student_major);
            $stmt->bindParam(':internship_type',  $internship_type);
            $stmt->bindParam(':company_position', $company_position);
            $stmt->bindParam(':company_name',     $company_name);
            $stmt->bindParam(':coordinator_name', $coordinator_name);
            $stmt->bindParam(':company_tel',      $company_tel);
            $stmt->bindParam(':start_date',       $start_date);
            $stmt->bindParam(':end_date',         $end_date);
            $stmt->bindParam(':status',           $status);

            if ($stmt->execute()) {
                $message = "<div class='message success'>บันทึกคำขอฝึกงานสำเร็จ!</div>";
            }
        } catch (PDOException $e) {
            $message = "<div class='message error'>เกิดข้อผิดพลาด: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
    .form-page-wrapper {
        background-color: #f4f5f7;
        padding: 40px 20px;
        min-height: calc(100vh - 160px);
    }
    .form-container {
        max-width: 800px;
        margin: 0 auto;
        background: #ffffff;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        border-top: 6px solid #c8102e;
    }
    .form-container h2 {
        color: #c8102e;
        text-align: center;
        font-size: 28px;
        margin-top: 0;
        margin-bottom: 30px;
        font-weight: 600;
        font-family: 'Prompt', sans-serif;
    }
    .form-section-title {
        margin-top: 40px;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 10px;
        color: #63666a;
        font-size: 20px;
        font-weight: 500;
        font-family: 'Prompt', sans-serif;
    }
    .form-group { margin-bottom: 20px; }
    .form-group label {
        display: block;
        font-weight: 500;
        margin-bottom: 8px;
        color: #4b5563;
        font-size: 15px;
        font-family: 'Prompt', sans-serif;
    }
    .form-group input,
    .form-group select {
        width: 100%;
        padding: 12px 15px;
        box-sizing: border-box;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-family: 'Prompt', sans-serif;
        font-size: 15px;
        background-color: #fafafa;
        transition: all 0.3s ease;
    }
    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #c8102e;
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(200, 16, 46, 0.15);
    }
    .form-group input[readonly] {
        background-color: #f3f4f6;
        color: #6b7280;
        cursor: not-allowed;
    }
    .message {
        padding: 15px;
        margin-bottom: 25px;
        border-radius: 8px;
        font-weight: 500;
        text-align: center;
        font-family: 'Prompt', sans-serif;
    }
    .message.success { background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .message.error   { background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .form-container button[type="submit"] {
        width: 100%;
        padding: 14px 20px;
        background: linear-gradient(135deg, #c8102e, #9e0b23);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 18px;
        font-family: 'Prompt', sans-serif;
        font-weight: 600;
        margin-top: 20px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .form-container button[type="submit"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(200, 16, 46, 0.3);
    }
    @media (max-width: 600px) {
        .form-container { padding: 25px; }
        .form-container h2 { font-size: 24px; }
    }
</style>

<div class="form-page-wrapper">
    <div class="form-container">
        <h2>แบบฟอร์มขอฝึกงาน / สหกิจศึกษา</h2>

        <?php echo $message; ?>

        <form action="form.php" method="POST">
            <h3 class="form-section-title">ข้อมูลนิสิต</h3>

            <div class="form-group">
                <label>รหัสนิสิต:</label>
                <input type="text" value="<?php echo htmlspecialchars($student_id); ?>" readonly>
            </div>

            <div class="form-group">
                <label>ชื่อ - นามสกุล:</label>
                <input type="text" value="<?php echo htmlspecialchars($_SESSION['display_name'] ?? ''); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="student_year">ชั้นปี:</label>
                <input type="number" id="student_year" name="student_year" placeholder="ระบุชั้นปี" min="1" max="5" required>
            </div>

            <div class="form-group">
                <label for="student_major">สาขาวิชา:</label>
                <select id="student_major" name="student_major" required>
                    <option value="">-- กรุณาเลือกสาขาวิชา --</option>
                    <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาจิตวิทยา">สาขาวิชาจิตวิทยา</option>
                    <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาภาษาฝรั่งเศส">สาขาวิชาภาษาฝรั่งเศส</option>
                    <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาภาษาอังกฤษ">สาขาวิชาภาษาอังกฤษ</option>
                    <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาภาษาไทย">สาขาวิชาภาษาไทย</option>
                    <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาวรรณกรรมสำหรับเด็ก">สาขาวิชาวรรณกรรมสำหรับเด็ก</option>
                    <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาปรัชญาและศาสนา">สาขาวิชาปรัชญาและศาสนา</option>
                    <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาภาษาเพื่ออาชีพ (หลักสูตรนานาชาติ)">สาขาวิชาภาษาเพื่ออาชีพ (หลักสูตรนานาชาติ)</option>
                    <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาภาษาไทย (5 ปี)">สาขาวิชาภาษาไทย (5 ปี)</option>
                    <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาภาษาอังกฤษ (5 ปี)">สาขาวิชาภาษาอังกฤษ (5 ปี)</option>
                    <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา">สาขาวิชาสารสนเทศศึกษา</option>
                    <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาภาษาตะวันออก">สาขาวิชาภาษาตะวันออก</option>
                    <option value="หลักสูตรวิทยาศาสตรบัณฑิต สาขาวิชาภาษาเพื่อการสื่อสาร (หลักสูตรนานาชาติ)">สาขาวิชาภาษาเพื่อการสื่อสาร (หลักสูตรนานาชาติ)</option>
                    <option value="หลักสูตรศิลปศาสตรบัณฑิต สาขาวิชาภาษาและวัฒนธรรมอาเซียน">สาขาวิชาภาษาและวัฒนธรรมอาเซียน</option>
                </select>
            </div>

            <div class="form-group">
                <label for="internship_type">รูปแบบการขอฝึกประสบการณ์:</label>
                <select id="internship_type" name="internship_type" required>
                    <option value="">-- กรุณาเลือกรูปแบบ --</option>
                    <option value="ฝึกงานรายวิชา">ฝึกงานรายวิชา</option>
                    <option value="สหกิจศึกษา">สหกิจศึกษา</option>
                    <option value="ฝึกตามความต้องการนอกหลักสูตร">ฝึกตามความต้องการนอกหลักสูตร</option>
                </select>
            </div>

            <h3 class="form-section-title">ข้อมูลของหน่วยงานที่ขอฝึกงาน</h3>

            <div class="form-group">
                <label for="company_name">ชื่อบริษัท/หน่วยงาน: <span style="color:#c8102e">*</span></label>
                <input type="text" id="company_name" name="company_name" placeholder="เช่น บริษัท ABC จำกัด" required>
            </div>

            <div class="form-group">
                <label for="company_position">ตำแหน่งฝึกงาน:</label>
                <input type="text" id="company_position" name="company_position" placeholder="เช่น ผู้ช่วยนักการตลาด, นักพัฒนาซอฟต์แวร์" required>
            </div>

            <div class="form-group">
                <label for="coordinator_name">ชื่อผู้ประสานงาน (ฝ่าย HR หรือบุคคลที่ติดต่อด้วย):</label>
                <input type="text" id="coordinator_name" name="coordinator_name" placeholder="ชื่อผู้ประสานงาน" required>
            </div>

            <div class="form-group">
                <label for="company_tel">หมายเลขโทรศัพท์ (หน่วยงาน):</label>
                <input type="tel" id="company_tel" name="company_tel" placeholder="เบอร์โทรศัพท์ติดต่อหน่วยงาน" required>
            </div>

            <div class="form-group">
                <label for="start_date">วันที่เริ่มฝึกงาน:</label>
                <input type="date" id="start_date" name="start_date" required>
            </div>

            <div class="form-group">
                <label for="end_date">วันที่สิ้นสุดของการฝึกงาน:</label>
                <input type="date" id="end_date" name="end_date" required>
            </div>

            <button type="submit">ส่งคำขอฝึกงาน</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>