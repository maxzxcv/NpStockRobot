<?php
session_start();
// ถ้า user เข้าสู่ระบบแล้วและมีเซสชัน username
if (isset($_SESSION['username']) && isset($_SESSION['permission'])) {
  header("Location: mainsystem.php");
  exit;
  // ตรวจสอบว่า permission เป็น 'user' หรือ 'admin'
}

?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Page</title>
  <link rel="manifest" href="manifest.json">
    <meta name="apple-mobile-web-app-title" content="N.P. Robotics">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="apple-touch-icon" href="image/NPPP-192x192.png">
    <link rel="manifest" href="/docs/5.3/assets/img/favicons/manifest.json">
    <link rel="icon" type="image/icon" href="image/NPPP.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@500&display=swap" rel="stylesheet">

  <style>
    html, body {
      height: 100%;
      font-family: 'Kanit', sans-serif;
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      color: #fff;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .card {
    background: #fff;
    color: #333;
    border-radius: 15px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3); /* เพิ่มเงา */
    transition: transform 0.3s ease, box-shadow 0.3s ease; /* เพิ่ม animation */
  }

  .card:hover {
    transform: translateY(-10px); /* ขยับขึ้นเล็กน้อยเมื่อ hover */
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4); /* เพิ่มเงาเข้มขึ้นเมื่อ hover */
  }

    .card img {
      border-radius: 50%;
    }

    .form-control {
      border: 2px solid #6a11cb;
      border-radius: 10px;
    }

    .form-control:focus {
      box-shadow: none;
      border-color: #2575fc;
    }

    .btn-primary {
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      border: none;
      border-radius: 50px;
      font-weight: bold;
    }

    .btn-primary:hover {
      background: linear-gradient(135deg, #4e0db1, #1d4ed8);
    }

    a {
      color: #2575fc;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="card p-5" style="max-width: 400px; width: 100%;">
      <div class="text-center">
        <img src="image/NPPP.png" height="60" width="60" alt="Logo" class="mb-3">
        <h4 class="mb-4">เข้าสู่ระบบ</h4>
      </div>

      <form name="f1" action="authentication.php" onsubmit="return validation()" method="POST">
        <div class="form-outline mb-4">
          <label class="form-label" for="username">ชื่อผู้ใช้</label>
          <input type="text" id="user" name="user" class="form-control" placeholder="กรอกชื่อผู้ใช้" required />
        </div>
        <div class="form-outline mb-4">
          <label class="form-label" for="password">รหัสผ่าน</label>
          <input type="password" id="pass" name="pass" class="form-control" placeholder="กรอกรหัสผ่าน" required />
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2">เข้าสู่ระบบ</button>
      </form>

      <p class="text-center mt-4">
        <a href="https://www.np-robotics.com/" target="_blank">บริษัท เอ็น.พี. โรโบติกส์ แอนด์ โซลูชั่น จำกัด</a>
      </p>
    </div>
  </div>

  <script>
    function validation() {
      var id = document.f1.user.value;
      var ps = document.f1.pass.value;
      if (!id || !ps) {
        alert("กรุณากรอกชื่อผู้ใช้และรหัสผ่าน");
        return false;
      }
      return true;
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

