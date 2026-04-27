<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/style_activity.css">
<!-- ส่วนข้อมูลหลักสูตร -->
    <section class="hero" id="home">
        <div class="container hero-content">
            <div class="hero-text">
                <h1>หลักสูตรสารสนเทศศึกษา</h1>
                <p>รหัสหลักสูตร 25520091104002 <br>
                    ภาษาไทย: หลักสูตรศิลปศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา <br>
                    ภาษาอังกฤษ: Bachelor of Arts Program in Information Studies</p>
                <!-- ปุ่มดูเพิ่มเติม -->
               <a href="./curriculum.php" class="btn btn-modern">ดูเพิ่มเติม</a>
            </div>
        </div>
    </section>

    <!-- ส่วนข่าวประชาสัมพันธ์ -->
<!-- icon เอาจากเว็บ fontawesome . com -->
<section class="news-section">
            <div class="container">
                <h2 class="section-title-news">ข่าวประชาสัมพันธ์</h2>
                <div class="news-grid">
                    <div class="news-card">
                        <div class="news-icon-wrapper">
                            <i class="fa-solid fa-bullhorn"></i>
                        </div>
                        <div class="news-info">
                            <span class="news-date">15 มี.ค. 2569</span>
                            <h3>SWU ADMISSIONS</h3>
                            <p>ระบบรับสมัครนิสิตใหม่</p>
                            <a href="https://admission.swu.ac.th/admissions2/" class="read-more">อ่านเพิ่มเติม <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>

                    <div class="news-card">
                        <div class="news-icon-wrapper">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <div class="news-info">
                            <span class="news-date">10 มี.ค. 2569</span>
                            <h3>กำหนดการลงทะเบียนเรียน ภาคเรียนที่ 1/2569</h3>
                            <p>นิสิตทุกชั้นปีสามารถตรวจสอบรายวิชาและวันเวลาลงทะเบียนได้ที่ระบบทะเบียนออนไลน์</p>
                            <a href="https://supreme.swu.ac.th/portal/index.php" class="read-more">อ่านเพิ่มเติม <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>

                    <div class="news-card">
                        <div class="news-icon-wrapper">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <div class="news-info">
                            <span class="news-date">03 มี.ค. 2569</span>
                            <h3>เตรียมความพร้อมก่อนฝึกงาน ชั้นปีที่ 3</h3>
                            <p>ขอเชิญเข้าร่วมฟังการบรรยายพิเศษเรื่อง "การเตรียมตัวสู่โลกการทำงานสารสนเทศในยุคดิจิทัล"</p>
                            <a href="#" class="read-more">อ่านเพิ่มเติม <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


<section class="courses" id="courses">
    <div class="container">
    <h2 class="section-title-news">กิจกรรมของหลักสูตร</h2>
        <div class="courses-grid" id="activity-container"></div>
    </div>
</section>

<div id="mainModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <div class="modal-gallery">
            <img id="modal-main-img" src="" class="main-image">
            <div id="modal-thumbnails" class="thumbnails"></div>
        </div>
        <div class="modal-text-content" id="modal-text"></div>
    </div>
</div>

<!-- ส่วนเส้นทางอาชีพ -->
        <section class="career-section">
            <div class="section-title">
                <div class="badge-top">Opportunities</div>
                <h2>เส้นทางอาชีพหลังจบการศึกษา</h2>
            </div>

            <div class="career-grid">
                <div class="career-item">
                    <i class="fa-solid fa-database"></i>
                    <h4>Data Analyst / Manager</h4>
                    <p>ผู้วิเคราะห์และบริหารจัดการข้อมูลในองค์กรดิจิทัล</p>
                </div>
                <div class="career-item">
                    <i class="fa-solid fa-code"></i>
                    <h4>Content & Web Developer</h4>
                    <p>นักพัฒนาเว็บไซต์และผู้จัดการเนื้อหาบนแพลตฟอร์ม</p>
                </div>
                <div class="career-item">
                    <i class="fa-solid fa-book-bookmark"></i>
                    <h4>Digital Librarian</h4>
                    <p>บรรณารักษ์ยุคใหม่</p>
                </div>
                <div class="career-item">
                    <i class="fa-solid fa-user-gear"></i>
                    <h4>UX/UI Researcher</h4>
                    <p>นักวิจัยประสบการณ์ผู้ใช้ เพื่อออกแบบระบบที่ตอบโจทย์มนุษย์</p>
                </div>
            </div>
        </section>

        <!-- ส่วน skill -->
        <section class="skill-stack">
            <div class="stack-card">
                <h3>IS Skill Stack</h3>
                <div class="tags-container">
                    <span class="tag">Python</span>
                    <span class="tag">SQL</span>
                    <span class="tag">Web Design</span>
                    <span class="tag">Data Analysis</span>
                    <span class="tag">Information Arch</span>
                    <span class="tag">Digital Literacy</span>
                </div>
            </div>
        </section>

        <div class="contact-grid">
                <div class="contact-item">
                    <div class="contact-icon"><i class="fa-solid fa-location-dot"></i></div>
                    <div class="contact-detail">
                        <h4>Location</h4>
                        <p>ภาควิชาบรรณารักษศาสตร์และสารสนเทศศาสตร์ คณะมนุษยศาสตร์ มศว (ประสานมิตร)</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon"><i class="fa-solid fa-paper-plane"></i></div>
                    <div class="contact-detail">
                        <h4>Contact Info</h4>
                        <p>โทร: 02-649-5000 ต่อ 16322</p>
                        <p>อีเมล: is@g.swu.ac.th</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon"><i class="fa-solid fa-share-nodes"></i></div>
                    <div class="contact-detail">
                        <h4>Social Media</h4>
                        <div class="social-links">
                            <a href="https://www.facebook.com/isswuofficial/"><i class="fa-brands fa-facebook"></i></a>
                            <a href="https://swu.ac.th" target="_blank"><i class="fa-solid fa-globe"></i></a>
                            <a href="https://www.instagram.com/is.hmswu/"><i class="fa-brands fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>


<?php include 'includes/footer.php'; ?>
<script src="assets/js/script.js"></script>

