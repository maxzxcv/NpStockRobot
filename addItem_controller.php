<?php
include('connection.php'); // เชื่อมต่อฐานข้อมูล
session_start(); // ใช้ session สำหรับการล็อกอิน

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['username']) || !isset($_SESSION['permission'])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'];
$permission = $_SESSION['permission'];

if ($permission != "admin") {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ItemName = mysqli_real_escape_string($con, $_POST['ItemName']);
    $SerialNumber = mysqli_real_escape_string($con, $_POST['SerialNumber']);
    $Type = mysqli_real_escape_string($con, $_POST['Type']);
    $WhereItem = mysqli_real_escape_string($con, $_POST['WhereItem']);
    $Status = mysqli_real_escape_string($con, $_POST['Status']);

    // 🔹 เพิ่มข้อมูลใหม่เข้า Stock_Main2_KPS
    $query = "INSERT INTO Stock_Main2_KPS (ItemName, SerialNumber, type, whereitem, status)
              VALUES ('$ItemName', '$SerialNumber', '$Type', '$WhereItem', '$Status')";

    if (mysqli_query($con, $query)) {
        $action = "เพิ่มของในสต็อก";
        $details = "ผู้ใช้: $username เปลี่ยนข้อมูล SerialNumber: $SerialNumberOld → $SerialNumber, 
                    ItemName: $ItemNameOld → $ItemName, 
                    Status: $StatusOld → $Status, 
                    WhereItem: $WhereItemOld → $WhereItem";
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $date = date('Y-m-d H:i:s');

        // 🔹 บันทึกการเปลี่ยนแปลงลง admin_logs
        $log_query = "INSERT INTO admin_logs (action, username, details, ip_address, action_date) 
                      VALUES ('$action', '$username', '$details', '$ipAddress', '$date')";
        mysqli_query($con, $log_query);

        header("Location: stock_controller.php?success=1");
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>
