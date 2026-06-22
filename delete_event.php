<?php
include('connection.php');
header("Content-Type: application/json");

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 

// รับค่าจาก POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eventId'])) {
    $eventId = $_POST['eventId'];

    // ลบข้อมูลจากฐานข้อมูล
    $sql = "DELETE FROM schedule WHERE id = ?";

    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param("i", $eventId); // ใช้ i สำหรับ integer
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Record deleted successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error executing query: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Error preparing query: " . $con->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}

// ปิดการเชื่อมต่อ
$con->close();
?>