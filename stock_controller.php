<?php
include('connection.php'); // เชื่อมต่อกับฐานข้อมูล
session_start(); // ใช้ session สำหรับตรวจสอบผู้ใช้ที่ล็อกอิน

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['username']) && !isset($_SESSION['permission'])) {
    header("Location: index.php"); // หากไม่ได้ล็อกอิน ให้ไปหน้า login
    exit;
}

// รับค่าชื่อผู้ใช้จาก session
$username = $_SESSION['username'];
$permission = $_SESSION['permission'];
$result = mysqli_query($con, "SELECT * FROM Employee WHERE username = '$username'");
  $user = mysqli_fetch_assoc($result);
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ดูสินค้าคงคลังในสต็อก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .background-image {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('your-image.jpg');
            background-size: cover;
            background-position: center;
            z-index: -1;
            opacity: 0.5;
        }
        .category {
    position: relative;
    top: 0; /* กำหนดให้หมวดหมู่เริ่มต้นที่ตำแหน่งนี้ */
}
    </style>
</head>
<?php include 'dataTableLink_Rel.php'; ?>
<?php include 'navbar.php'; ?>

<body>
    <div class="background-image"></div>
    <div class="container mt-4">
        <h2 class="text-center">สินค้าคงคลังในสต็อก (ไดร์ฟ)</h2>
<?php
                    // ตรวจสอบสิทธิ์ก่อนที่จะแสดงผล HTML
                    if ($permission === 'admin') {
                    ?>
        <a href="addItem.php" class="btn btn-success mt-3">เพิ่มสินค้า</a>
        <?php
                    }
                    ?>
        <a href="history_change_controller.php" class="btn btn-primary mt-3">ประวัติ</a>
        <a href="mainsystem.php" class="btn btn-primary mt-3">ย้อนกลับ</a>

        <!-- ฟอร์มค้นหา -->
        <form id="searchForm" class="mt-3" method="post">
            
            <!-- Dropdown สำหรับเลือกหมวดหมู่ -->
            <div class="mt-3">
                <label for="category" class="form-label">เลือกหมวดหมู่</label>
                <select name="table" id="category" class="form-select">
    <option value="all" selected>ภาพรวม</option>
    <option value="drive">ไดร์ฟ</option>
    <option value="การ์ดจอ">การ์ดจอ</option>
    <option value="Safety">เซฟตี้</option>
    <option value="RDW">RDW</option>
    <option value="Power Supply">Power Supply</option>
    <option value="MFC">MFC</option>
    <option value="KPS">KPS</option>
    <option value="DSE">DSE</option>
    
</select>
            </div>
        </form>

        <!-- แสดงตารางที่ค้นหา -->
        <div id="stockTable"></div>
    </div>

    <script>

$(document).ready(function() {
    function loadStockData(table = 'all', search = '') {
        $.ajax({
            url: "search_stock_controller.php",
            method: "POST",
            data: { search: search, table: table },
            success: function(response) {
                $("#stockTable").html(response);
            }
        });
    }

    // เมื่อเลือกหมวดหมู่หรือพิมพ์ในช่องค้นหา
     $("#search, #category").on("change input", function() {
        var search = $("#search").val();
        var category = $("#category").val();
        loadStockData(category, search);
    });

    // โหลดข้อมูลตามค่าเริ่มต้นเมื่อโหลดหน้า
    loadStockData('all', ''); // ค่าเริ่มต้นคือ "all"
});
    </script>

    
</body>
</html>
