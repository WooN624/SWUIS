<?php
/**
 * Internship.php — หน้ารวมลิงก์และเมนูฝึกงาน (ฉบับปรับปรุงดีไซน์)
 */
include 'includes/header.php';
?>

<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --swu-red: #8B0000;
        --swu-blue: #0056b3;
        --bg-light: #ebedee;
        --text-dark: #2c3e50;
        --shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    body {
        font-family: 'Sarabun', sans-serif;
        background-color: var(--bg-light);
        margin: 0;
        color: #b5b1b4;
        line-height: 1.6;
    }

    /* แก้ปัญหาเนื้อหาตีกับ Header */
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 100px 20px 60px 20px; /* เพิ่ม Padding ด้านบนให้พ้น Header */
    }

    /* หัวข้อหน้าเว็บ */
    .page-header {
        text-align: center;
        margin-bottom: 50px;
        position: relative;
    }

    .page-header h1 {
        color: var(--swu-red);
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 15px;
        letter-spacing: -0.5px;
    }

    .page-header p {
        color: #666;
        font-size: 1.1rem;
        max-width: 700px;
        margin: 0 auto;
    }

    .page-header::after {
        content: '';
        display: block;
        width: 60px;
        height: 4px;
        background: var(--swu-red);
        margin: 20px auto 0;
        border-radius: 2px;
    }

    /* ระบบ Card Grid */
    .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
    }

    .card {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        padding: 35px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        position: relative;
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow);
    }

    .card-content {
        text-align: center;
        margin-bottom: 25px;
        z-index: 1;
    }

    .icon {
        font-size: 50px;
        display: inline-block;
        margin-bottom: 20px;
        filter: drop-shadow(0 5px 15px rgba(0,0,0,0.1));
    }

    .card h3 {
        margin: 10px 0;
        font-size: 1.4rem;
        color: var(--text-dark);
        font-weight: 600;
    }

    .card p {
        font-size: 1rem;
        color: #7f8c8d;
    }

    /* ปุ่มกด */
    .btn {
        display: block;
        padding: 14px;
        text-align: center;
        text-decoration: none;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-red  { background-color: var(--swu-red); color: white; }
    .btn-blue { background-color: var(--swu-blue); color: white; }
    
    .btn:hover { 
        filter: brightness(1.2);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    /* ตกแต่งพิเศษสำหรับ Card หลัก */
    .primary-card { 
        background: linear-gradient(145deg, #ffffff, #f0f7ff);
        border: 1px solid rgba(175, 179, 175, 0.1);
    }
    .primary-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 5px;
        background: var(--swu-blue);
    }

    /* Popup/Section ส่วนขยาย */
    .expand-section {
        display: none; 
        margin-top: 40px; 
        background: #fff; 
        border-radius: 20px; 
        padding: 40px;
        box-shadow: var(--shadow);
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .advice-item {
        border-left: 5px solid var(--swu-red); 
        padding: 15px 20px; 
        margin-bottom: 15px; 
        background: #fff5f5; 
        border-radius: 0 12px 12px 0;
        transition: transform 0.2s;
    }
    .advice-item:hover { transform: translateX(5px); }

    .job-item {
        display: flex; 
        align-items: center; 
        justify-content: space-between; 
        padding: 20px; 
        margin-bottom: 15px; 
        background: #fff; 
        border: 1px solid #eee; 
        border-radius: 12px;
    }
</style>

<main class="container">
    <header class="page-header">
        <h1>ระบบจัดการการฝึกงานและสหกิจศึกษา</h1>
        <p>ภาควิชาสารสนเทศศึกษา คณะมนุษยศาสตร์ มหาวิทยาลัยศรีนครินทรวิโรฒ</p>
    </header>

    <div class="card-grid">
        <div class="card">
            <div class="card-content">
                <span class="icon">📘</span>
                <h3>คู่มือสหกิจศึกษา</h3>
                <p>ข้อมูลสำคัญและแนวทางปฏิบัติสำหรับนิสิต</p>
            </div>
            <a href="https://hpd.hu.swu.ac.th/Portals/12/68/manual190368.pdf"
               class="btn btn-red" target="_blank">ดาวน์โหลดคู่มือ</a>
        </div>

        <div class="card">
            <div class="card-content">
                <span class="icon">📋</span>
                <h3>ขั้นตอนการฝึกงาน</h3>
                <p>ลำดับขั้นตอนและเอกสารที่ต้องเตรียม</p>
            </div>
            <a href="https://drive.google.com/file/d/1o4Z9JTbRhPV13Lgg8DGTI-sw7FfVjtIs/view"
               class="btn btn-red" target="_blank">ดูรายละเอียด</a>
        </div>

        <div class="card">
            <div class="card-content">
                <span class="icon">💬</span>
                <h3>คำแนะนำจากรุ่นพี่</h3>
                <p>ประสบการณ์จริงจากรุ่นพี่ที่ผ่านการฝึกงานมาแล้ว</p>
            </div>
            <a href="#senior-advice" class="btn btn-red" onclick="showSection('senior-advice')">อ่านคำแนะนำ</a>
        </div>

        <?php if (has_role('student')): ?>
        <div class="card primary-card">
            <div class="card-content">
                <span class="icon">📤</span>
                <h3>ยื่นเรื่องฝึกงาน</h3>
                <p>กรอกแบบฟอร์มและแนบไฟล์คำร้อง</p>
            </div>
            <a href="./form.php" class="btn btn-blue">เริ่มต้นยื่นคำร้อง</a>
        </div>

        <div class="card">
            <div class="card-content">
                <span class="icon">🔍</span>
                <h3>ติดตามสถานะ</h3>
                <p>เช็คสถานะการอนุมัติและผลการพิจารณา</p>
            </div>
            <a href="./pages/admin/student_list.php" class="btn btn-blue">ตรวจสอบความคืบหน้า</a>
        </div>

        <div class="card">
            <div class="card-content">
                <span class="icon">🔎</span>
                <h3>แอปหางานฝึกงาน</h3>
                <p>รวมแพลตฟอร์มหางานที่แนะนำสำหรับนิสิต</p>
            </div>
            <a href="#job-apps" onclick="showSection('job-apps')" class="btn btn-blue">ดูแพลตฟอร์ม</a>
        </div>
        <?php endif; ?>
    </div>

    <div id="senior-advice" class="expand-section">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
            <h3 style="color:var(--swu-red); margin:0;">💬 คำแนะนำจากรุ่นพี่</h3>
            <button onclick="this.parentElement.parentElement.style.display='none'" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>
        <?php
        $advices = [
            ['name' => 'พิมพ์ชนก', 'text' => 'ไปฝึกงานวันแรกไม่ต้องเกร็งจนตัวแข็ง พี่ๆ เขาไม่ได้กะให้เราเก่งเทพตั้งแต่วันแรกหรอก แต่เขาดู "ทัศนคติ" เรามากกว่า...'],
            ['name' => 'ณัฐวุฒิ',  'text' => 'ไม่รู้ให้ถาม ไม่มั่นใจให้เช็ก อย่าเดาเอง การถามไม่ได้แปลว่าเราโง่ แต่มันแปลว่าเราไม่อยากให้งานเสีย'],
            ['name' => 'สุภาพร',   'text' => 'ชีวิตจริงมันเร็วกว่าในมหา\'ลัยเยอะค่ะ เคล็ดลับคือ "จดทุกอย่าง" แม้แต่เรื่องเล็กๆ อย่างวิธีส่งเมล...'],
            ['name' => 'กฤษฎา',   'text' => 'ถ้างานเล็กๆ เรายังตั้งใจทำจนเป๊ะ เดี๋ยวงานใหญ่ที่ท้าทายกว่าเดิมจะตามมาเอง'],
            ['name' => 'ธัญญารัตน์','text' => 'อย่ามัวแต่ก้มหน้าก้มตาทำงานจนลืมผูกมิตรนะ การทักทายหรือชวนคุยช่วงพักเที่ยงมันสร้าง Connection ได้ดีมาก'],
            ['name' => 'วรายุทธ',  'text' => 'รับฟีดแบคให้ไม่อยู่ รับฟัง ขอบคุณ แล้วเอาไปแก้ ห้ามหน้าตึงใส่เด็ดขาด'],
            ['name' => 'นภสร',     'text' => 'พยายามพูดให้กระชับ เข้าประเด็นให้ไว ใครอยากโดดเด่น ลองฝึกการพูดหรือการเขียนสรุปงานแบบมืออาชีพไว้ก่อนเลย'],
            ['name' => 'ปิยะพงษ์', 'text' => 'วันแรกให้ทำตัวเป็นนักสืบ สังเกตว่าเขาคุยกันยังไง แต่งตัวแบบไหน ส่งงานทางไหน แล้วเนียนตัวให้กลมกลืนกับวัฒนธรรมเขาให้ไวที่สุด'],
            ['name' => 'อารียา',   'text' => 'การฝึกงานไม่ได้น่ากลัวอย่างที่คิดนะ มันคือสนามจำลองให้เราลองเป็น "มืออาชีพ" ครั้งแรก ผิดได้ พลาดได้ แต่อย่าหยุดเรียนรู้'],
        ];
        foreach ($advices as $a): ?>
        <div class="advice-item">
            <strong style="color:var(--swu-red);"><?= $a['name'] ?></strong>
            <p style="margin:5px 0 0; color:#555;"><?= $a['text'] ?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (has_role('student')): ?>
    <div id="job-apps" class="expand-section">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
            <h3 style="color:var(--swu-blue); margin:0;">🔎 แพลตฟอร์มหางานฝึกงาน</h3>
            <button onclick="this.parentElement.parentElement.style.display='none'" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>
        <?php
        $jobs = [
            ['name' => 'JobsDB',       'url' => 'https://th.jobsdb.com',         'desc' => 'แหล่งรวมงานบริษัทชั้นนำ มีตัวกรองละเอียด'],
            ['name' => 'LinkedIn',     'url' => 'https://www.linkedin.com',       'desc' => 'เน้นสร้างโปรไฟล์แบบมืออาชีพและสร้างเครือข่าย Connection'],
            ['name' => 'JobThai',      'url' => 'https://www.jobthai.com',        'desc' => 'ใช้งานง่าย มีฟีเจอร์ "งานใกล้ตัว" ดูงานผ่านแผนที่'],
            ['name' => 'Blognone Jobs','url' => 'https://jobs.blognone.com',      'desc' => 'สวรรค์ของสาย IT, Developer และ Graphic Design'],
            ['name' => 'JobBKK',       'url' => 'https://www.jobbkk.com',        'desc' => 'รวมงานครอบคลุมทุกสาขาอาชีพทั่วไทย'],
            ['name' => 'GrabJobs',     'url' => 'https://grabjobs.co/thailand',   'desc' => 'สมัครงานได้รวดเร็ว เน้นงานบริการและงานที่ต้องการคนด่วน'],
            ['name' => 'Helpster',     'url' => 'https://www.helpster.asia',      'desc' => 'เน้นงาน Part-time หรืองานรายวัน'],
            ['name' => 'Fastwork',     'url' => 'https://fastwork.co',            'desc' => 'แหล่งรวมงาน Freelance สำหรับคนอยากรับงานเป็นโปรเจกต์'],
            ['name' => 'WorkVenture',  'url' => 'https://www.workventure.com',    'desc' => 'เน้นรีวิวบริษัทให้เห็นภาพรวมวัฒนธรรมองค์กร'],
        ];
        foreach ($jobs as $j): ?>
        <div class="job-item">
            <div>
                <strong style="color:var(--swu-blue);"><?= $j['name'] ?></strong>
                <p style="margin:5px 0 0; color:#666; font-size:0.9rem;"><?= $j['desc'] ?></p>
            </div>
            <a href="<?= $j['url'] ?>" target="_blank" class="btn btn-blue" style="padding:10px 20px;">เปิดเว็บ</a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</main>

<script>
function showSection(id) {
    // ปิด section อื่นก่อน (ถ้ามี)
    document.querySelectorAll('.expand-section').forEach(sec => sec.style.display = 'none');
    // เปิด section ที่เลือก
    const target = document.getElementById(id);
    target.style.display = 'block';
    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
</script>

<?php include 'includes/footer.php'; ?>