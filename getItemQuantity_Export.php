<?php
include('connection.php'); // เชื่อมต่อฐานข้อมูล

if (isset($_GET['category'], $_GET['item_id'])) {
    $category = mysqli_real_escape_string($con, $_GET['category']);
    $item_id = mysqli_real_escape_string($con, $_GET['item_id']);

    $allowedTables = ['Stock_Main', 'Stock_Main2', 'Stock_Main2_inroom', 'Stock_Main2_Study', 'Stock_Main4_VR'];

    if (!in_array($category, $allowedTables)) {
        echo json_encode(['error' => 'หมวดหมู่ไม่ถูกต้อง']);
        exit;
    }

    $query = "SELECT Amount FROM $category WHERE id = '$item_id'";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $amount = (int)$row['Amount'];

        if ($amount > 0) {
            $quantities = range(1, $amount);
            echo json_encode($quantities);
        } else {
            echo json_encode(['error' => 'สินค้าหมดในคลัง']);
        }
    } else {
        echo json_encode(['error' => 'ไม่พบสินค้าที่ระบุในคลัง']);
    }
} else {
    echo json_encode(['error' => 'พารามิเตอร์ไม่ครบ']);
}
?>
