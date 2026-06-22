<?php
include('connection.php');

// ตั้งค่าเขตเวลาเป็นประเทศไทย
date_default_timezone_set('Asia/Bangkok');

// รับค่าจากฟอร์ม
$username1 = mysqli_real_escape_string($con, $_POST['username']);
$permission = mysqli_real_escape_string($con, $_POST['permission']);
$password = mysqli_real_escape_string($con, $_POST['password']);
$confirm_password = mysqli_real_escape_string($con, $_POST['confirm_password']);
$firstname = mysqli_real_escape_string($con, $_POST['firstname']);
$surname = mysqli_real_escape_string($con, $_POST['surname']);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ตรวจสอบว่า username เป็นภาษาอังกฤษและมีความยาวไม่น้อยกว่า 6 ตัว
if (!preg_match("/^[a-zA-Z0-9]*$/", $username1) || strlen($username1) < 4) {
    header("Location: newAccount.php?error=invalid_username");
    exit;
}

// ตรวจสอบว่า username นี้มีอยู่ในฐานข้อมูลหรือไม่
$sql_check = "SELECT * FROM Employee WHERE username = '$username1'";
$result_check = mysqli_query($con, $sql_check);
if (mysqli_num_rows($result_check) > 0) {
    header("Location: newAccount.php?error=username_exists");
    exit;
}

// ตรวจสอบว่ารหัสผ่านตรงกันหรือไม่
if ($password !== $confirm_password) {
    header("Location: newAccount.php?error=passwords_do_not_match");
    exit;
}

// เข้ารหัสรหัสผ่าน
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// เตรียม SQL Query สำหรับการบันทึกข้อมูล
$sql = "INSERT INTO Employee (username, password, firstname, surname, permission) 
        VALUES ('$username1', '$hashed_password', '$firstname', '$surname', '$permission')";

// ตรวจสอบว่า SQL Query ทำงานสำเร็จหรือไม่
if (mysqli_query($con, $sql)) {

    $username = $_SESSION['username'];
    $action = "เพิ่ม Account";
    $details = "ผู้ใช้: $username | เพิ่มผู้ใช้: $username1 | firstname: $firstname | surname: $surname | permission: $permission";
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $date = date('Y-m-d H:i:s');
    $log_query = "INSERT INTO admin_logs (action, username, details, ip_address, action_date) 
                  VALUES ('$action', '$username', '$details', '$ipAddress', '$date')";
    mysqli_query($con, $log_query);


    header("Location: newAccount.php?success=user_added");
    exit;
} else {
    echo "Error adding record: " . mysqli_error($con);
}
?>
