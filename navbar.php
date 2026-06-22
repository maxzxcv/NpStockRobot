<?php 
include('connection.php'); // เชื่อมต่อกับฐานข้อมูล
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['username']) && isset($_SESSION['permission'])) {
    // ตรวจสอบว่า permission เป็น 'user' หรือ 'admin'
    $username = $_SESSION['username'];
    $permission = $_SESSION['permission'];
  
  }else{
    header("Location: index.php");
    exit;
  }
    $result3 = mysqli_query($con, "SELECT * FROM Employee WHERE username = '$username'");
    $user = mysqli_fetch_assoc($result3);
    if (!$user) {
      die("User not found.");
    }

// คำสั่ง SQL สำหรับการตรวจสอบเงื่อนไข
$result1 = mysqli_query($con, "SELECT id FROM Stock_Main WHERE itemoutstock >= Amount");
$result2 = mysqli_query($con, "SELECT id FROM Stock_Main2_inroom WHERE itemoutstock >= Amount");
$result4 = mysqli_query($con, "SELECT id FROM Stock_Tools WHERE itemoutstock >= Amount");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<head>
    <link rel="manifest" href="manifest.json">
    <meta name="apple-mobile-web-app-title" content="N.P. Robotics">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="apple-touch-icon" href="image/NPPP-192x192.png">
    <link rel="manifest" href="/docs/5.3/assets/img/favicons/manifest.json">
    <link rel="icon" type="image/icon" href="image/NPPP.ico">
    
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@500&display=swap" rel="stylesheet">
</head>
<style>
    body {
        font-family: 'Kanit', sans-serif;
        scroll-behavior: smooth;
    }

    /* Scrollbar */
    ::-webkit-scrollbar {
        width: 13px;
        /* ความกว้างของ Scrollbar */
        height: 10px;
        /* ความสูงของ Scrollbar (สำหรับ Scroll แนวนอน) */
    }

    /* Track */
    ::-webkit-scrollbar-track {
        background: #f0f0f0;
        /* สีพื้นหลังของ Track */
        border-radius: 10px;
        /* มุมโค้ง */
    }

    /* Thumb */
    ::-webkit-scrollbar-thumb {
        background: linear-gradient(45deg, #6a11cb, #2575fc);
        /* ไล่สี */
        border-radius: 10px;
        /* มุมโค้ง */
    }

    /* Hover effect */
    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(45deg, #4e0db1, #1d4ed8);
        /* สีเมื่อชี้เมาส์ */
    }

    /* Active effect */
    ::-webkit-scrollbar-thumb:active {
        background: linear-gradient(45deg, #3a0a8e, #163ead);
        /* สีเมื่อคลิก Scrollbar */
    }

    /* Optional: ซ่อน Scrollbar บนมือถือ */
    @media (max-width: 768px) {
        ::-webkit-scrollbar {
            display: none;
            /* ซ่อน Scrollbar */
        }
    }

    .navbar {
        transition: background-color 0.3s ease;
    }

    .sticky-top {
        z-index: 1030;
    }
</style>
<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
    <symbol id="check-circle-fill" viewBox="0 0 16 16">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
    </symbol>
    <symbol id="info-fill" viewBox="0 0 16 16">
        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
    </symbol>
    <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
    </symbol>
</svg>
<header class="sticky-top">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <a href="mainsystem.php" class="navbar-brand d-flex align-items-center">
                <img src="image/NPPP.png" alt="N.P. Robotics Logo" width="40" height="40" class="me-2">
                <strong>Stock N.P. Robotics</strong>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php
                    // ตรวจสอบสิทธิ์ก่อนที่จะแสดงผล HTML
                    if ($permission === 'admin') {
                    ?>

                        <li class="nav-item">
                            <a class="nav-link" href="importItem.php">เอาของเข้า</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="exportItem.php">เอาของออก</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manageUser.php">เพิ่มผู้ใช้ใหม่</a>
                        </li>

                    <?php
                    }
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="history.php">ประวัติ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="stock_tools.php">ห้องอุปกรณ์</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="stock.php">สต็อก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="stock_controller.php">สต็อก (ไดร์ฟ)</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center dropdrown">
                    <div class="dropdown position-relative me-3">
                        <!-- ไอคอนกระดิ่ง -->
                        <i class="fas fa-bell text-white" style="font-size: 24px; cursor: pointer;" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false"></i>
                        <!-- Badge แจ้งเตือน -->
                         
                        <?php
                        if ($result1->num_rows > 0 || $result2->num_rows > 0 || $result4->num_rows > 0) {
                        echo '<span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
                            <span class="visually-hidden">New alerts</span>
                        </span>';
                        }
                    ?>
                        <!-- เมนูแจ้งเตือน -->
                        <?php
                        if ($result1->num_rows > 0 || $result2->num_rows > 0 || $result4->num_rows > 0) {
                            echo '<ul class="dropdown-menu bg-light-subtle">
                                    <li><a class="dropdown-item" href="outofstock.php">สต็อกบางชิ้นกำลังจะหมด</a></li>
                                  </ul>
                                  <span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
                                    <span class="visually-hidden">New alerts</span>
                                  </span>';
                        } else {
                            echo '<ul class="dropdown-menu bg-light-subtle">
                                    <li><a class="dropdown-item disabled" href="#" >ไม่มีการแจ้งเตือน</a></li>
                                  </ul>';
                        }
                        ?>
                    </div>
                    <span class="navbar-text text-white me-3">
                        <?php echo htmlspecialchars($user['firstname'] . ' ' . $user['surname']); ?>
                    </span>
                    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
                </div>
            </div>
        </div>
    </nav>
</header>

<?php
if ($result1->num_rows > 0 || $result2->num_rows > 0 || $result4->num_rows > 0) {
    echo '<div class="alert alert-warning d-flex align-items-center justify-content-center text-center text-dark"
        role="alert"
        style="height: 60px; background-color: #ffc107; opacity: 1; border: 1px solid #ffa000;">
        <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Warning:" width="16" height="16">
            <use xlink:href="#exclamation-triangle-fill" />
        </svg>
        <div>
            แจ้งเตือนของในสต็อกบางชิ้นกำลังจะหมด
            <button type="button" class="btn btn-secondary"
            style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;"
            onclick="window.location.href=\'outofstock.php\';">
        Click
    </button>
            เพื่อดูของที่กำลังจะหมด
        </div>
    </div>';
}
?>
