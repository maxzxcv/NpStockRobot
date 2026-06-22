<?php
include('connection.php');
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['permission'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['table'])) {
    $id = intval($_GET['id']);
    $table = mysqli_real_escape_string($con, $_GET['table']);

    $delete_query = "DELETE FROM $table WHERE id = $id";
    if (mysqli_query($con, $delete_query)) {
        $username = $_SESSION['username'];
        $action = "ลบข้อมูล";
        $details = "ผู้ใช้: $username ลบข้อมูล ID: $id จากตาราง: $table";
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $date = date('Y-m-d H:i:s');

        $log_query = "INSERT INTO admin_logs (action, username, details, ip_address, action_date) 
                      VALUES ('$action', '$username', '$details', '$ipAddress', '$date')";
        mysqli_query($con, $log_query);

        echo "ลบข้อมูลสำเร็จ";
    } else {
        echo "เกิดข้อผิดพลาด: " . mysqli_error($con);
    }
} else {
    echo "ข้อมูลไม่ถูกต้อง";
}
?>
