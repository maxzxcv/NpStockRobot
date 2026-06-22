<?php
include('connection.php');
session_start();

// ถ้า user เข้าสู่ระบบแล้วและมีเซสชัน username
if (isset($_SESSION['username']) && isset($_SESSION['permission'])) {
    $username = $_SESSION['username'];
    $permission = $_SESSION['permission'];
    $result = mysqli_query($con, "SELECT * FROM Employee WHERE username = '$username'");
    $user = mysqli_fetch_assoc($result);
} else {
    header("Location: logout.php");
    exit;
}

// เริ่มต้นค่าการค้นหาเป็นค่าว่าง
$search = '';
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($con, $_GET['search']);  // ป้องกันการโจมตี SQL Injection
}
?>
<html lang="en" data-bs-theme="dark">

<head>



    <script src="/docs/5.3/assets/js/color-modes.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <meta name="mobile-web-app-capable" content="yes">
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
        body {
            font-family: 'Kanit', sans-serif;
            scroll-behavior: smooth;
        }

        a {
            text-decoration: none;
        }



        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
            padding: 20px;
        }

        @media (min-width: 767.98px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }

        }

        .b-example-divider {
            width: 100%;
            height: 3rem;
            background-color: rgba(0, 0, 0, .1);
            border: solid rgba(0, 0, 0, .15);
            border-width: 1px 0;
            box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
        }

        .b-example-vr {
            flex-shrink: 0;
            width: 1.5rem;
            height: 100vh;
        }

        .bi {
            vertical-align: -.125em;
            fill: currentColor;
        }

        .btn-bd-primary {
            --bd-violet-bg: #712cf9;
            --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

            --bs-btn-font-weight: 600;
            --bs-btn-color: var(--bs-white);
            --bs-btn-bg: var(--bd-violet-bg);
            --bs-btn-border-color: var(--bd-violet-bg);
            --bs-btn-hover-color: var(--bs-white);
            --bs-btn-hover-bg: #6528e0;
            --bs-btn-hover-border-color: #6528e0;
            --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
            --bs-btn-active-color: var(--bs-btn-hover-color);
            --bs-btn-active-bg: #5a23c8;
            --bs-btn-active-border-color: #5a23c8;
        }

        .bd-mode-toggle {
            z-index: 1500;
        }

        .bd-mode-toggle .dropdown-menu .active .bi {
            display: block !important;
        }
        
        .card {
    border-radius: 15px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
  }

  /* การกำหนดสีของ Card (พื้นหลังทึบ) */
  .card-green {
    background-color: #28a745; /* สีเขียว */
    border: 2px solid #218838; /* สีกรอบเขียวเข้ม */
  }

  .card-red {
    background-color: #dc3545; /* สีแดง */
    border: 2px solid #c82333; /* สีกรอบแดงเข้ม */
  }

  .card-blue {
    background-color: #007bff; /* สีฟ้า */
    border: 2px solid #0056b3; /* สีกรอบฟ้าเข้ม */
  }

  .card-purple {
    background-color: #663399; /* สีม่วง */
    border: 2px solid #52287e; /* สีกรอบม่วงเข้ม */
  }

  .card-grey {
    background-color: #6c757d; /* สีเทา */
    border: 2px solid #5a6268; /* สีกรอบเทาเข้ม */
  }

  .card-yellow {
    background-color: #ffc107; /* สีเหลือง */
    border: 2px solid #e0a800; /* สีกรอบเหลืองเข้ม */
  }

  .card-teal {
    background-color:#bc6d06; /* สีเขียวน้ำทะเล */
    border: 2px solidrgb(145, 83, 2); /* สีกรอบเขียวน้ำทะเลเข้ม */
  }

  .card:hover {
  transform: translateY(-10px);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3); /* เพิ่มเงา */
  /* ลบ background-color หรือปรับแต่งเพื่อให้สีไม่เปลี่ยน */
}

.card-green:hover,
.card-red:hover,
.card-blue:hover,
.card-purple:hover,
.card-grey:hover,
.card-yellow:hover,
.card-teal:hover {
  background-color: inherit !important; /* คงสีพื้นหลังเดิมไว้ */
  border-color: inherit !important; /* คงสีกรอบเดิม */
}

  /* การจัดการรูปภาพใน Card */
  .card-image-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 225px;
    background-color: #1a1a1a; /* สีพื้นหลังรองรูป */
  }

  .card-image-container img {
    max-width: 150px;
    max-height: 150px;
    object-fit: contain;
  }

  .card-body {
    text-align: center;
    padding: 15px;
  }

  .card-body p {
    margin: 0;
  }

  .bg-success.bg-opacity-75:hover {
    background-color: rgba(40, 167, 69, 0.9) !important;
  }

  .bg-danger.bg-opacity-75:hover {
    background-color: rgba(220, 53, 69, 0.9) !important;
  }

  .bg-primary.bg-opacity-75:hover {
    background-color: rgba(0, 123, 255, 0.9) !important;
  }

  .bg-secondary.bg-opacity-75:hover {
    background-color: rgba(108, 117, 125, 0.9) !important;
  }
        .grid-item:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            background-color: #555;
        }

        .grid-item img {
            transition: transform 0.3s ease;
            width: 100%;
            height: auto;
        }

        .grid-item:hover img {
            transform: scale(1);
        }

        .grid-item p {
            transition: color 0.3s ease;
            margin: 0;
        }

        .grid-item:hover p {
            color: #ffc107;
            /* เปลี่ยนสีข้อความ */
        }

        @media (max-width: 767.98px) {
            .grid-item {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                border-radius: 10px;
                overflow: hidden;
                /* ป้องกันส่วนเกิน */
            }

            .card-body {
                padding: 10px;
            }

        }
    </style>


</head>
<?php include 'navbar.php'; ?>

<body>




    <main>

    <div class="album py-5 bg-body-tertiary">
  <div class="container">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
      <?php
      // ฟังก์ชันสำหรับสร้าง Card พร้อมสีที่แตกต่างกัน
      function createCard($href, $image, $bgClass, $text)
      {
        echo "
        <div class='col text-center'>
          <a href='$href' class='text-decoration-none'>
            <div class='card shadow-sm grid-item $bgClass'>
              <div class='card-image-container'>
                <img class='card-img-top' src='$image' alt='$text' />
              </div>
              <div class='card-body'>
                <p class='card-text fs-5 link-offset-2 link-underline link-underline-opacity-0'>$text</p>
              </div>
            </div>
          </a>
        </div>";
      }

      // ตรวจสอบสิทธิ์ก่อนสร้าง Card
      if ($permission === 'admin') {
        createCard("importItem.php", "image/in-stock1.png", "card-green", "เอาของเข้า");
        createCard("exportItem.php", "image/out-of-stock1.png", "card-red", "เอาของออก");
        createCard("manageUser.php", "image/add-user.png", "card-blue", "เพิ่มผู้ใช้ใหม่");
        createCard("manageAccount.php", "image/new-account.png", "card-blue", "เพิ่ม Account");
      }

      // Card ที่ทุกคนสามารถเข้าถึงได้
      
      createCard("history.php", "image/sheets1.png", "card-grey", "ประวัติการเอาเข้าออก");
      createCard("stock.php", "image/stock.png", "card-teal", "ดูของในสต็อก");
      createCard("stock_controller.php", "image/packages.png", "card-teal", "ดูของในสต็อก ไดร์ฟ");
      createCard("stock_tools.php", "image/tools.png", "card-teal", "ดูของห้องเครื่องมือ");
      ?>
    </div>
  </div>
</div>

    </main>

    <footer class="text-body-secondary py-5 text-center">
        <div class="container">
            <p class="mb-1">©2024 www.np-robotics.com. All rights reserved.</p>
        </div>
    </footer>


    <script src="/docs/5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script>
        document.querySelectorAll('[data-bs-theme-value]').forEach(item => {
            item.addEventListener('click', function() {
                const theme = this.getAttribute('data-bs-theme-value');
                document.documentElement.setAttribute('data-bs-theme', theme);

                document.querySelectorAll('[data-bs-theme-value]').forEach(btn => {
                    btn.classList.remove('active');
                    btn.setAttribute('aria-pressed', 'false');
                });

                this.classList.add('active');
                this.setAttribute('aria-pressed', 'true');
            });
        });
    </script>
    <script>
        // เมื่อผู้ใช้พิมพ์ในช่องค้นหา
        document.getElementById('search').addEventListener('keyup', function() {
            var searchQuery = this.value.trim(); // เอาค่าที่พิมพ์ออกจากช่องค้นหามา
            fetchData(searchQuery);
        });

        // โหลดข้อมูลทั้งหมดเมื่อหน้าเว็บโหลด
        window.addEventListener('load', function() {
            var searchQuery = document.getElementById('search').value.trim();
            fetchData(searchQuery);
        });

        // ฟังก์ชัน fetch ข้อมูล
        function fetchData(query) {
            fetch('search.php?search=' + encodeURIComponent(query))
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.text(); // รับข้อมูลเป็นข้อความ
                })
                .then(data => {
                    document.getElementById('table-body').innerHTML = data;
                    attachClickEvent(); // เพิ่ม Event Listener ให้ div ใหม่
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    document.getElementById('table-body').innerHTML =
                        '<tr><td colspan="100%">ไม่สามารถโหลดข้อมูลได้</td></tr>';
                });
        }

        // เพิ่ม Event Listener ให้กับ div ทุกตัวที่มี class clickable-item
        function attachClickEvent() {
            document.querySelectorAll('.clickable-item').forEach(item => {
                item.removeEventListener('click', handleClick); // ลบ Event เดิมก่อน (ถ้ามี)
                item.addEventListener('click', handleClick);
            });
        }

        function handleClick() {
            const targetHref = this.getAttribute('data-href');
            if (targetHref) {
                window.location.href = targetHref; // เปลี่ยนหน้าไปยังลิงก์ที่กำหนด
            }
        }
    </script>
</body>

</html>