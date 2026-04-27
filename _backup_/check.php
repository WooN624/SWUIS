<?php
/**
 * check.php — รับค่าจากฟอร์ม login แล้วตรวจสอบกับ DB
 */
require_once 'auth.php';
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['user'] ?? '');
$password = $_POST['pass'] ?? '';

if ($username === '' || $password === '') {
    header('Location: login.php?error=1');
    exit;
}

$queries = [
    'student' => 'SELECT stdid AS uid, stdname AS display_name, email FROM student WHERE email = ? LIMIT 1',
    'teacher' => 'SELECT tchID AS uid, tchname AS display_name, email FROM teacher WHERE email = ? LIMIT 1',
    'admin'   => 'SELECT staffid AS uid, staffname AS display_name, email FROM staff   WHERE email = ? LIMIT 1',
];

try {
    $pdo = $conn; //
    $found = null;
    $role  = null;

    foreach ($queries as $r => $sql) {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);
        $row = $stmt->fetch();

        if ($row && $password === $row['email']) {
            $found = $row;
            $role  = $r;
            break;
        }
    }

    if ($found) {
        session_regenerate_id(true);

        $_SESSION['user_id']      = $found['uid'];
        $_SESSION['display_name'] = $found['display_name'];
        $_SESSION['role']         = $role;

        match ($role) {
        'teacher' => header('Location: t_dashboard.php'),
        'admin'   => header('Location: staff_dashboard.php'),
        default   => header('Location: index.php'),
        };
    } else {
        header('Location: login.php?error=1');
    }
} catch (PDOException $e) {
    error_log('DB Error: ' . $e->getMessage());
    header('Location: login.php?error=1');
}
exit;