<?php
include('connection.php');
session_start();

// ─────────────────────────────────────────────
// ตรวจสอบสิทธิ์
// ─────────────────────────────────────────────
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

// ─────────────────────────────────────────────
// เปิด error for debug
// ─────────────────────────────────────────────
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ─────────────────────────────────────────────
// ตรวจสอบข้อมูลจากฟอร์ม
// ─────────────────────────────────────────────
if (
    empty($_POST['firstname']) ||
    empty($_POST['category']) ||
    empty($_POST['user']) ||
    empty($_POST['item_id']) ||
    empty($_POST['item_name']) ||
    empty($_POST['quantity']) ||
    empty($_POST['date_added'])
) {
    die("ข้อมูลไม่ครบ");
}

$firstname  = mysqli_real_escape_string($con, $_POST['firstname']);
$user       = mysqli_real_escape_string($con, $_POST['user']);
$category   = mysqli_real_escape_string($con, $_POST['category']);
$itemID     = mysqli_real_escape_string($con, $_POST['item_id']);
$item       = mysqli_real_escape_string($con, $_POST['item_name']);
$quantity   = intval($_POST['quantity']);
$dateImport = mysqli_real_escape_string($con, $_POST['date_added']);

// ─────────────────────────────────────────────
// เฉพาะ Stock_Main2_Study (มี package)
// ─────────────────────────────────────────────
if ($category == "Stock_Main2_Study") {
    if ($itemID == 1) $package = 1;
    elseif ($itemID == 2) $package = 2;
    elseif ($itemID == 3) $package = 3;
    elseif ($itemID == 4) $package = 4;
}

// ─────────────────────────────────────────────
// ตรวจสอบจำนวนปัจจุบัน
// ─────────────────────────────────────────────
$query = "SELECT Amount FROM `$category` WHERE id = '$itemID'";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Error SELECT: " . mysqli_error($con));
}

if (mysqli_num_rows($result) == 0) {
    header("Location: importItem.php?error=item_not_found");
    exit;
}

$row = mysqli_fetch_assoc($result);
$currentAmount = $row['Amount'];
$newAmount = $currentAmount + $quantity;

// ─────────────────────────────────────────────
// อัปเดตจำนวนในสต๊อก
// ─────────────────────────────────────────────
if ($category == "Stock_Main2_Study") {
    $updateQuery = "UPDATE `$category` SET Amount = '$newAmount' WHERE package = '$package'";
} else {
    $updateQuery = "UPDATE `$category` SET Amount = '$newAmount' WHERE id = '$itemID'";
}

if (!mysqli_query($con, $updateQuery)) {
    die("Error UPDATE: " . mysqli_error($con));
}

// ─────────────────────────────────────────────
// เพิ่มข้อมูลลง Stock_Import
// ─────────────────────────────────────────────
if ($category == "Stock_Main2_Study") {
    $packageName = [
        1 => "ชุดอบรม KUKA 1",
        2 => "ชุดอบรม KUKA 2",
        3 => "ชุดอบรม ABB 1",
        4 => "ชุดอบรม ABB 2"
    ][$package];

    $insertQuery = "INSERT INTO Stock_Import (username, user, ItemName, Amount, Date)
                    VALUES ('$username', '$user', '$packageName', '$quantity', '$dateImport')";

} else {
    $insertQuery = "INSERT INTO Stock_Import (username, user, ItemName, Amount, Date)
                    VALUES ('$username', '$user', '$item', '$quantity', '$dateImport')";
}

if (!mysqli_query($con, $insertQuery)) {
    die("Error INSERT: " . mysqli_error($con));
}

$lastID = mysqli_insert_id($con);

// ─────────────────────────────────────────────
// อัปโหลดรูปภาพ (ถ้ามี)
// ─────────────────────────────────────────────
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

    $imageName = $_FILES['image']['name'];
    $imageTmp  = $_FILES['image']['tmp_name'];

    $ext = pathinfo($imageName, PATHINFO_EXTENSION);
    $newFileName = $lastID . "_" . $username . "_" . $item . "_" . $quantity . "_" . $dateImport . "." . $ext;

    $uploadDir = "historys/import/";
    $uploadPath = $uploadDir . $newFileName;

    if (move_uploaded_file($imageTmp, $uploadPath)) {
        mysqli_query($con, "UPDATE Stock_Import SET Image='$uploadPath' WHERE id='$lastID'");
    }
}

// ─────────────────────────────────────────────
// Log การกระทำ admin
// ─────────────────────────────────────────────
$ipAddress = $_SERVER['REMOTE_ADDR'];
$dateNow  = date('Y-m-d H:i:s');

$details = "เพิ่มสินค้า: $item, จำนวน: $quantity, ห้อง: $category, ผู้นำเข้า: $user";
mysqli_query($con,
    "INSERT INTO admin_logs (action, username, details, ip_address, action_date)
     VALUES ('นำของเข้าสต๊อก', '$username', '$details', '$ipAddress', '$dateNow')"
);

// ─────────────────────────────────────────────
// สำเร็จ → กลับหน้า importItem พร้อม alert
// ─────────────────────────────────────────────
header("Location: importItem.php?success=1");
exit;
?>
