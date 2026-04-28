<?php
require_once 'auth.php';
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /SWUIS/login.php');
    exit;
}

$username = trim($_POST['user'] ?? '');
$password = $_POST['pass'] ?? '';

if ($username === '' || $password === '') {
    header('Location: /SWUIS/login.php?error=1');
    exit;
}

$queries = [
    'student' => 'SELECT stdid AS uid, stdname AS display_name, password FROM student WHERE email = ? LIMIT 1',
    'teacher' => 'SELECT tchID AS uid, tchname AS display_name, password FROM teacher WHERE email = ? LIMIT 1',
    'admin'   => 'SELECT staffid AS uid, staffname AS display_name, password FROM staff   WHERE email = ? LIMIT 1',
];

try {
    $found = null;
    $role  = null;

    foreach ($queries as $r => $sql) {
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $storedPass = $row['password'] ?? '';
            // รองรับทั้ง plain text (1234) และ hash
            $match = ($password === $storedPass) || password_verify($password, $storedPass);
            if ($match) {
                $found = $row;
                $role  = $r;
                break;
            }
        }
    }

    if ($found) {
        session_regenerate_id(true);

        $_SESSION['user_id']      = $found['uid'];
        $_SESSION['display_name'] = $found['display_name'];
        $_SESSION['role']         = $role;

        match ($role) {
            'teacher' => header('Location: /SWUIS/pages/teacher/T_dashboard.php'),
            'admin'   => header('Location: /SWUIS/pages/admin/staff_dashboard.php'),
            default   => header('Location: /SWUIS/index.php'),
        };
    } else {
        header('Location: /SWUIS/login.php?error=1');
    }

} catch (PDOException $e) {
    error_log('DB Error: ' . $e->getMessage());
    header('Location: /SWUIS/login.php?error=1');
}
exit;