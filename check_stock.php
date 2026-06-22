<?php
include('connection.php');

if (isset($_POST['ItemName'])) {
    $ItemName = $_POST['ItemName'];

    // ค้นหาจำนวนที่ตรงกับ ItemName
    $query = "SELECT COUNT(*) AS total FROM Stock_Main2_KPS WHERE ItemName = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $ItemName);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['total'] + 1; // จำนวนที่มีอยู่ +1

        // เช็คว่าชื่อ Item เป็น DRIVE หรือไม่
        if (strpos($ItemName, 'DRIVE') !== false) {
            // ดึงหมายเลขของ DRIVE (08, 16, 32, 48)
            preg_match('/\d+/', $ItemName, $matches);
            $driveNumber = $matches[0]; // ได้ค่าเป็น 08, 16, 32, หรือ 48
        }

    // ตรวจสอบว่าเป็น DRIVE หรือไม่ (ใช้ Dr. ตามด้วยจำนวน)
    if (strpos($ItemName, 'DRIVE') !== false) {
        $newSerial = "Dr." . $driveNumber . "." . $count;
    } 
    // ตรวจสอบว่าเป็น KPS หรือไม่
    elseif ($ItemName === "KPS") {
        $newSerial = "KPS." . $count;
    } 
    // ตรวจสอบการ์ดจอ XP และ 95
    elseif ($ItemName === "การ์ดจอ XP") {
        $newSerial = "VGA.XP." . $count;
    } 
    elseif ($ItemName === "การ์ดจอ 95") {
        $newSerial = "VGA.95." . $count;
    }
    elseif ($ItemName === "Safety 95") {
        $newSerial = "ST." . $count . ".95";
    }
    elseif ($ItemName === "Safety XP") {
        $newSerial = "ST." . $count . ".XP";
    }
    // กรณีอื่นๆ ใช้รูปแบบ "ชื่อ.จำนวน"
    else {
        $ItemNameFormatted = str_replace(' ', '', $ItemName);
        $newSerial = $ItemNameFormatted . "." . $count;
    }

    echo json_encode(["count" => $count, "serial" => $newSerial]);
}
?>
