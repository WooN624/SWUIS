<?php
/**
 * login.php — หน้าเข้าสู่ระบบ
 */
require_once 'includes/auth.php';

// ถ้าล็อกอินแล้ว redirect ไปหน้าหลักเลย
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = isset($_GET['error']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ — Information Studies SWU</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Sarabun', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #9e0b23; /* fallback */
            background: linear-gradient(135deg, #830115 0%, #e6042a 100%);
            overflow: hidden;
            position: relative;
        }

        /* 🌌 Animated Background Canvas */
        #bg-canvas {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 1;
        }

        .login-wrapper {
            position: relative;
            width: 100%;
            max-width: 620px;
            z-index: 10;
            padding: 20px;
            animation: slideUp 1s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Logo Area */
        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
            color: white;
        }
        .login-logo img {
            width: 90px;
            filter: drop-shadow(0 0 15px rgba(255,255,255,0.3));
            margin-bottom: 1rem;
            transition: all 0.5s ease;
        }
        .login-logo img:hover {
            transform: rotateY(180deg); /* ลูกเล่นหมุนโลโก้แบบ 3D */
        }
        .login-logo h1 {
            font-size: 1.6rem;
            letter-spacing: 2px;
            font-weight: 700;
            text-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        /* ✨ Glassmorphism Card */
        .login-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            border-radius: 2.5rem;
            padding: 3rem 2.5rem;
            box-shadow: 0 40px 80px rgba(0,0,0,0.4), 
                        inset 0 0 0 1px rgba(255,255,255,0.5);
            transition: transform 0.3s ease;
        }
        .login-card:hover {
            transform: translateY(-5px);
        }

        .login-card h2 {
            font-size: 1.7rem;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
            text-align: center;
        }
        .login-card .subtitle {
            text-align: center;
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 2.5rem;
        }

        /* Modern Inputs */
        .field { margin-bottom: 1.5rem; position: relative; }
        .field label {
            font-size: 0.85rem;
            font-weight: 700;
            color: #9e0b23;
            margin-bottom: 0.6rem;
            display: block;
            text-transform: uppercase;
        }
        .field input {
            width: 100%;
            padding: 1rem 1.2rem;
            border: 2px solid #eee;
            border-radius: 1.2rem;
            font-size: 1rem;
            background: #fdfdfd;
            transition: all 0.3s ease;
        }
        .field input:focus {
            outline: none;
            border-color: #c8102e;
            background: #fff;
            box-shadow: 0 8px 20px rgba(200,16,46,0.15);
        }

        /* 🚀 Glow Button */
        .btn-login {
            width: 100%;
            padding: 1.1rem;
            background: linear-gradient(45deg, #9e0b23, #e63950);
            color: white;
            border: none;
            border-radius: 1.2rem;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(158,11,35,0.4);
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        .btn-login:hover {
            box-shadow: 0 15px 30px rgba(158,11,35,0.6);
            filter: brightness(1.15);
            transform: scale(1.02);
        }

        /* Error Box */
        .error-box {
            background: #fff5f5;
            color: #c53030;
            padding: 1rem;
            border-radius: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            border: 1px solid #feb2b2;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Hint Box */
        .demo-hint {
            margin-top: 2rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 1rem;
            font-size: 0.8rem;
            color: #64748b;
            line-height: 1.6;
        }
        .role-tag {
            padding: 2px 6px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 0.7rem;
        }
        .tag-s { background: #e0e7ff; color: #4338ca; }
        .tag-t { background: #dcfce7; color: #15803d; }
        .tag-a { background: #fef3c7; color: #b45309; }

    </style>
</head>
<body>

    <canvas id="bg-canvas"></canvas>

    <div class="login-wrapper">
        <div class="login-logo">
            <img src="img/Srinakharinwirot_Logo.png" alt="swu logo">
            <h1>INFORMATION STUDIES</h1>
            <p>Srinakharinwirot University</p>
        </div>

        <div class="login-card">
            <h2>เข้าสู่ระบบ</h2>
            <p class="subtitle">ระบบสารสนเทศเพื่อการศึกษา</p>

            <?php if ($error): ?>
                <div class="error-box">
                    <span>⚠️</span> ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง
                </div>
            <?php endif; ?>

            <form action="includes/check.php" method="POST">
                <div class="field">
                    <label>Username</label>
                    <input type="text" name="user" placeholder="ระบุชื่อผู้ใช้งาน" required>
                </div>
                <div class="field">
                    <label>Password</label>
                    <input type="password" name="pass" placeholder="ระบุรหัสผ่าน" required>
                </div>
                <button type="submit" class="btn-login">SIGN IN</button>
            </form>

            <div class="demo-hint">
                <strong>🔑 สำหรับรหัสผ่าน:</strong><br>
                ใช้อีเมลทั้งตรง Username และ Password </span><br>
            </div>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('bg-canvas');
        const ctx = canvas.getContext('2d');
        let particles = [];

        function resize() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }

        window.addEventListener('resize', resize);
        resize();

        class Particle {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.speedX = (Math.random() - 0.5) * 0.5;
                this.speedY = (Math.random() - 0.5) * 0.5;
                this.size = Math.random() * 2;
            }
            update() {
                this.x += this.speedX;
                this.y += this.speedY;
                if (this.x > canvas.width) this.x = 0;
                if (this.x < 0) this.x = canvas.width;
                if (this.y > canvas.height) this.y = 0;
                if (this.y < 0) this.y = canvas.height;
            }
            draw() {
                ctx.fillStyle = 'rgba(255, 255, 255, 0.5)';
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        function init() {
            for (let i = 0; i < 150; i++) particles.push(new Particle());
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach((p, index) => {
                p.update();
                p.draw();
                for (let j = index; j < particles.length; j++) {
                    const dx = p.x - particles[j].x;
                    const dy = p.y - particles[j].y;
                    const dist = Math.sqrt(dx*dx + dy*dy);
                    if (dist < 150) {
                        ctx.strokeStyle = `rgba(255, 255, 255, ${0.15 - dist/1000})`;
                        ctx.lineWidth = 0.5;
                        ctx.beginPath();
                        ctx.moveTo(p.x, p.y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.stroke();
                    }
                }
            });
            requestAnimationFrame(animate);
        }

        init();
        animate();
    </script>
</body>
</html>