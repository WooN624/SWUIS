<?php
/**
 * logout.php — ล้าง session แล้ว redirect กลับหน้าหลัก
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION = [];
session_destroy();
header('Location: /SWUIS/index.php');
exit;
