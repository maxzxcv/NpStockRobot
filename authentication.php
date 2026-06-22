<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('connection.php');

/* =========================
   1) กันการเปิดไฟล์ตรง ๆ
========================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

/* =========================
   2) ตรวจว่ามีค่า POST จริง
========================= */
if (!isset($_POST['user'], $_POST['pass'])) {
    header("Location: index.php");
    exit;
}

$username = trim($_POST['user']);
$password = trim($_POST['pass']);

/* =========================
   3) ใช้ Prepared Statement (ปลอดภัย)
========================= */
$sql = "SELECT username, password, permission FROM Employee WHERE username = ?";
$stmt = mysqli_prepare($con, $sql);

if (!$stmt) {
    die("Database error");
}

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

/* =========================
   4) ตรวจรหัสผ่าน
========================= */
if ($row && password_verify($password, $row['password'])) {

    session_regenerate_id(true);
    $_SESSION['username']   = $row['username'];
    $_SESSION['permission'] = $row['permission'];

    echo "
    <link href='https://fonts.googleapis.com/css2?family=Kanit:wght@500&display=swap' rel='stylesheet'>
    <style>
      body { font-family: 'Kanit', sans-serif; }
    </style>

    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'เข้าสู่ระบบสำเร็จ',
                text: 'กำลังเข้าสู่ระบบ',
                icon: 'success',
                timer: 1000,
                showConfirmButton: false
            }).then(() => {
                window.location = 'mainsystem.php';
            });
        });
    </script>";
    exit;
}

/* =========================
   5) login ไม่ผ่าน
========================= */
echo "
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'ไม่สามารถเข้าสู่ระบบได้',
            text: 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'
        }).then(() => {
            window.location = 'index.php';
        });
    });
</script>";
exit;
