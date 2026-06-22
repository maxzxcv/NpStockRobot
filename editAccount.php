<?php
include('connection.php');
session_start();

if (!isset($_SESSION['username']) && !isset($_SESSION['permission'])) {
    header("Location: index.php");
    exit;
}else{
    $username = $_SESSION['username'];
    $permission = $_SESSION['permission'];
    if($permission != "admin"){
        header("Location: index.php");
    exit;
    }
    $result = mysqli_query($con, "SELECT * FROM Employee WHERE username = '$username'");
    $user = mysqli_fetch_assoc($result);
}

if (isset($_GET['id']) && isset($_GET['table'])) {
    $id = intval($_GET['id']);
    $table = mysqli_real_escape_string($con, $_GET['table']);

    // ดึงข้อมูลปัจจุบันจากฐานข้อมูล
    $query = "SELECT * FROM `$table` WHERE id = $id";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('ไม่พบข้อมูลที่ต้องการแก้ไข'); history.back();</script>";
        exit;
    }
} else {
    echo "<script>alert('ข้อมูลไม่ถูกต้อง'); history.back();</script>";
    exit;
}

// บันทึกข้อมูลที่แก้ไข
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated_values = [];
    foreach ($_POST as $key => $value) {
        $updated_values[] = "$key = '" . mysqli_real_escape_string($con, $value) . "'";
    }

    $update_query = "UPDATE $table SET " . implode(', ', $updated_values) . " WHERE id = $id";
    if (mysqli_query($con, $update_query)) {

        // Debug the values being logged
$action = "แก้ไขข้อมูลในตาราง";
$details = "ผู้แก้ไข: $username, แก้ไขข้อมูล ID: $id, ค่าใหม่: " . implode(', ', $updated_values);
error_log("DEBUG: $details");
$ipAddress = $_SERVER['REMOTE_ADDR']; // Fetch IP Address
$date = date('Y-m-d H:i:s'); // Current time

// Ensure all inputs are escaped
$logQuery = "INSERT INTO admin_logs (action, username, details, ip_address, action_date) 
             VALUES ('" . mysqli_real_escape_string($con, $action) . "', 
                     '" . mysqli_real_escape_string($con, $username) . "', 
                     '" . mysqli_real_escape_string($con, $details) . "', 
                     '" . mysqli_real_escape_string($con, $ipAddress) . "', 
                     '" . mysqli_real_escape_string($con, $date) . "')";

// Execute query and handle errors
if (!mysqli_query($con, $logQuery)) {
    error_log("SQL Error: " . mysqli_error($con)); // Log SQL error
    die("Error logging action: " . mysqli_error($con));
}

        // แจ้งการสำเร็จและกลับไปยังหน้าหลัก
        echo "<script>alert('แก้ไขข้อมูลสำเร็จ'); window.location.href = 'manageAccount.php';</script>";
    } else {
        echo "เกิดข้อผิดพลาด: " . mysqli_error($con);
    }
}
$column_labels = [
    'id' => 'ไอดี',
    'ItemName' => 'ชื่อสินค้า',
    'Amount' => 'จำนวนคงเหลือ',
    'status' => 'สถานะสินค้า',
    'whereItem' => 'เก็บไว้ที่',
    'note' => 'หมายเหตุ',
    'NumberItem' => 'รหัสอุปกรณ์',
    'NumNP' => 'เลขบริษัท',
    'company' => 'บริษัท',
    'user' => 'ผู้นำออก',
    'date' => 'วันที่นำออก',
    'ProductName' => 'ชื่อ',
    'type' => 'ประเภท'
    // เพิ่มคอลัมน์อื่น ๆ ได้ตามต้องการ
];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูล</title>
    <meta charset="utf-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="CSS/style_edituser.css">
  <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<?php include 'navbar.php'; ?>
<body>
    <div class="container mt-5">
        <h2>แก้ไขข้อมูล</h2>
        <form method="POST">
    <?php foreach ($data as $key => $value) : ?>
        <div class="mb-3">
            <!-- ใช้ Mapping Array เพื่อแสดงชื่อคอลัมน์ที่เข้าใจง่าย -->
            <label for="<?= $key ?>" class="form-label">
                <?= isset($column_labels[$key]) ? $column_labels[$key] : $key ?>
            </label>
            
            <?php if ($key === 'status') : ?>
                <!-- Dropdown สำหรับสถานะ -->
    <select class="form-control" id="<?= $key ?>" name="<?= $key ?>">
        <optgroup label="สถานะทั่วไป">
           
            <option value="active" <?= ($value === 'active') ? 'selected' : '' ?>>ปกติ ✅</option>
           
            <option value="wait_test" <?= ($value === 'wait_test') ? 'selected' : '' ?>>กำลังเทส 🔵</option>
        </optgroup>
        <optgroup label="สถานะผิดปกติ">
        <option value="not_active" <?= ($value === 'not_active') ? 'selected' : '' ?>>เสีย ❌</option>
            <option value="repairing" <?= ($value === 'repairing') ? 'selected' : '' ?>>กำลังซ่อม 🟤</option>
        </optgroup>
    </select>
            <?php elseif (in_array($key, ['id', 'ItemName','username','password','permission'])) : ?>
                <!-- Read-only Field -->
                <input type="text" class="form-control" id="<?= $key ?>" name="<?= $key ?>" value="<?= htmlspecialchars($value) ?>" disabled readonly>
            <?php else : ?>
                <!-- Text Field สำหรับคอลัมน์อื่น -->
                <input type="text" class="form-control" id="<?= $key ?>" name="<?= $key ?>" value="<?= htmlspecialchars($value) ?>">
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    <button type="submit" class="btn btn-primary">บันทึก</button>
    <a href="manageAccount.php" class="btn btn-secondary">ยกเลิก</a>
    <a href="#" class="btn btn-danger" onclick="confirmDelete(<?= $id ?>, 'Employee')">ลบผู้ใช้นี้</a>
</form>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    console.log('JavaScript Loaded'); // ตรวจสอบว่าคำสั่งนี้แสดงใน Console หรือไม่
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(event) {
      // ป้องกันการ submit แบบปกติ
      event.preventDefault();

      // แสดงการโหลดกลางหน้าจอ
      Swal.fire({
        title: 'กำลังดำเนินการ...',
        text: 'กรุณารอสักครู่',
        allowOutsideClick: false, // ป้องกันการคลิกนอกหน้าต่าง
        didOpen: () => {
          Swal.showLoading();
        }
      });

      // ใช้ setTimeout เพื่อจำลองการส่งข้อมูล
      // เปลี่ยนเป็นการส่งข้อมูลจริงได้ในขั้นตอนต่อไป
      setTimeout(() => {
        // ส่งข้อมูลจริงไปยัง backend
        form.submit(); // ส่งฟอร์มไปยัง backend
      }, 1000); // ใส่เวลารอ 2 วินาทีเพื่อให้เห็นข้อความ loading
    });
});

function confirmDelete(id, table) {
    Swal.fire({
        title: "แน่ใจหรือไม่?",
        text: "ที่จะลบผู้ใช้ดังกล่าว !",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "ยืนยัน",
        cancelButtonText: "ยกเลิก"
    }).then((result) => {
        if (result.isConfirmed) {
            // ส่งคำขอไปยัง PHP เพื่อทำการลบ
            fetch(`delete_backend.php?id=${id}&table=${table}`)
                .then(response => response.text())
                .then(data => {
                    if (data.includes("ลบข้อมูลสำเร็จ")) {
                        Swal.fire({
                            title: "Deleted!",
                            text: "The user has been deleted.",
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => {
                            // Redirect หลังจากลบสำเร็จ
                            window.location.href = 'manageAccount.php';
                        });
                    } else {
                        // แสดงข้อความผิดพลาดหากเกิดปัญหา
                        Swal.fire({
                            title: "Error!",
                            text: "Failed to delete the user.",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: "Error!",
                        text: "An unexpected error occurred.",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                });
        }
    });
}
console.log('JavaScript Loaded'); // ตรวจสอบว่าคำสั่งนี้แสดงใน Console หรือไม่

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</html>


