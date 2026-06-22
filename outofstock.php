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
    <title>ของที่ใกล้หมดสต็อก</title>
    
    <script src="/docs/5.3/assets/js/color-modes.js"></script>

    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.122.0">
    <title>Stock N.P. Robotics</title>
    <link rel="icon" type="image/icon" href="image/NPPP.ico">
    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/album/">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link href="/docs/5.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- mangifie -->
    <link rel="manifest" href="manifest.json">
    <meta name="apple-mobile-web-app-title" content="N.P. Robotics">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <!-- Favicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="apple-touch-icon" href="image/NPPP-192x192.png">

    <link rel="icon" href="/docs/5.3/assets/img/favicons/favicon-32x32.png" sizes="32x32" type="image/png">
    <link rel="icon" href="/docs/5.3/assets/img/favicons/favicon-16x16.png" sizes="16x16" type="image/png">
    <link rel="manifest" href="/docs/5.3/assets/img/favicons/manifest.json">
    <link rel="mask-icon" href="/docs/5.3/assets/img/favicons/safari-pinned-tab.svg" color="#712cf9">
    <link rel="icon" href="/docs/5.3/assets/img/favicons/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@500&display=swap" rel="stylesheet">
    <meta name="theme-color" content="#712cf9">

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
        <h2 class="text-center">ของที่ใกล้หมดสต็อก</h2>
        <a href="mainsystem.php" class="btn btn-primary mt-3">ย้อนกลับ</a>

        <!-- แสดงตารางที่ค้นหา -->



        <div id="stockTable"></div>




    </div>

    <script>
        $(document).ready(function() {
    // ฟังก์ชันที่ใช้ดึงข้อมูลทั้งหมด
    
    function loadStockData(table = 'all', search = '') {
        
        $.ajax({
            url: "outofstock_search.php", // ไฟล์ PHP ที่จะประมวลผลการค้นหา
            method: "POST",
            data: {
                search: search, // ส่งคำค้นหาที่ผู้ใช้กรอก
                table: table // ส่งชื่อของตารางที่ผู้ใช้เลือก
            },
            success: function(response) {
                $("#stockTable").html(response); // แสดงผลลัพธ์ใน div ที่มี id="stockTable"
            }
        });
        
    }
    // เมื่อเลือกหมวดหมู่หรือพิมพ์ในช่องค้นหา
    $("#search, #category").on("change input", function() {
        var search = $("#search").val();
        var category = $("#category").val();
        loadStockData(category, search); // เรียกใช้ฟังก์ชันเพื่อดึงข้อมูล
    });

    // โหลดข้อมูลตามค่าเริ่มต้นเมื่อโหลดหน้า
        loadStockData('all', ''); // ค่าเริ่มต้นคือ "all"
    });
        </script>
    
</body>
</html>
