<?php
include('connection.php');
session_start();

// ตรวจสอบสิทธิ์
if (!isset($_SESSION['username'], $_SESSION['permission']) || $_SESSION['permission'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: stock.php");
    exit;
}

/* =========================
   1) whitelist table
========================= */
$allowedRooms = [
    'Stock_Main',
    'Stock_Main2_inroom',
    'Stock_Tools'
];

$room = $_POST['room'] ?? '';

if (!in_array($room, $allowedRooms, true)) {
    die('Invalid room');
}

/* =========================
   2) รับค่าพื้นฐาน
========================= */
$Amount        = (int)$_POST['Amount'];
$whereItem     = trim($_POST['whereItem']);
$status        = trim($_POST['status']);
$itemoutstock  = (int)$_POST['itemoutstock'];

/* =========================
   3) แยก logic Stock_Tools
========================= */
if ($room === 'Stock_Tools') {

    $ItemName = trim($_POST['tool_name']);

    if ($ItemName === '') {
        die('กรุณากรอกชื่ออุปกรณ์');
    }

} else {

    $ItemName = trim($_POST['ItemName']);

    if ($ItemName === '') {
        die('กรุณากรอกชื่ออุปกรณ์');
    }
}

/* =========================
   4) INSERT (ครั้งเดียว)
========================= */
$sql = "INSERT INTO `$room` (ItemName, Amount, whereItem, status, itemoutstock)
        VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param(
    $stmt,
    "sisss",
    $ItemName,
    $Amount,
    $whereItem,
    $status,
    $itemoutstock
);
mysqli_stmt_execute($stmt);

/* =========================
   5) log การทำงาน
========================= */
$username = $_SESSION['username'];
$details = "room=$room, ItemName=$ItemName, Amount=$Amount";

$logStmt = mysqli_prepare(
    $con,
    "INSERT INTO admin_logs (action, username, details, ip_address, action_date)
     VALUES ('เพิ่มของในสต็อก', ?, ?, ?, NOW())"
);
$ip = $_SERVER['REMOTE_ADDR'];
mysqli_stmt_bind_param($logStmt, "sss", $username, $details, $ip);
mysqli_stmt_execute($logStmt);

/* =========================
   6) redirect
========================= */
header("Location: stock.php?success=1");
exit;
