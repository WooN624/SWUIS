<?php
/**
 * auth.php — ตัวช่วยระบบสิทธิ์การเข้าถึง
 * 
 * บทบาท (role) ที่รองรับ:
 *   guest    = ไม่ได้ล็อกอิน
 *   student  = นักเรียน/นิสิต
 *   teacher  = อาจารย์
 *   admin    = แอดมิน (เห็นทุกอย่าง)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ดึง role ปัจจุบัน (ถ้าไม่ได้ล็อกอินให้ถือว่าเป็น guest)
function get_role(): string {
    return $_SESSION['role'] ?? 'guest';
}

// ตรวจว่าล็อกอินอยู่ไหม
function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

// ตรวจว่ามีสิทธิ์ตาม role ที่กำหนดไหม
// ลำดับสิทธิ์: admin > teacher > student > guest
function has_role(string $required_role): bool {
    $hierarchy = ['guest' => 0, 'student' => 1, 'teacher' => 2, 'admin' => 3];
    $current   = $hierarchy[get_role()] ?? 0;
    $required  = $hierarchy[$required_role] ?? 0;
    return $current >= $required;
}

// บังคับ redirect ถ้าไม่มีสิทธิ์
function require_role(string $required_role, string $redirect = 'login.php'): void {
    if (!has_role($required_role)) {
        header("Location: $redirect");
        exit;
    }
}

$queries = [
    'student' => [
        'sql' => 'SELECT stdid AS uid, stdname AS display_name, password FROM student WHERE email = ? LIMIT 1',
    ],
    'teacher' => [
        'sql' => 'SELECT tchID AS uid, tchname AS display_name, password FROM teacher WHERE email = ? LIMIT 1',
    ],
    'admin' => [
        'sql' => 'SELECT staffid AS uid, staffname AS display_name, password FROM staff WHERE email = ? LIMIT 1',
    ],
];
