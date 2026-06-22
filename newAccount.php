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
if($permission != "admin"){
    header("Location: index.php");
exit;
}
$result = mysqli_query($con, "SELECT * FROM Employee WHERE username = '$username'");
  $user = mysqli_fetch_assoc($result);

$error = isset($_GET['error']) ? $_GET['error'] : '';
$success = isset($_GET['success']) ? $_GET['success'] : '';
?>
<!doctype html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add User Information</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="CSS/style_edituser.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.9/dist/sweetalert2.all.min.js"></script>
  <style>
    body {
      font-family: 'Kanit', sans-serif;
      scroll-behavior: smooth;
    }

    .form-label {
      font-size: 0.9em;
      color: #6c757d;
    }

    .btnRegister {
      margin-top: 10px;
      width: 100%;
    }
  </style>
</head>

<?php include 'navbar.php'; ?>
<body class="bg-light">

  <div class="container register">
    <div class="row">
      <div class="col-md-3 register-left">
        <img src="image/NPPP.png" alt="" />
        <h3>User Management</h3>
        <p class="">สำหรับเพิ่มข้อมูลผู้ใช้โปรแกรมถอดประกอบ KR150</p>
      </div>
      <div class="col-md-9 register-right">
        <h3 class="register-heading">เพิ่มข้อมูลผู้ใช้โปรแกรมใหม่</h3>
        <form action="update_new_account.php" method="post">
          <div class="row register-form">
            <div class="col-md-6">
              <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="ชื่อผู้ใช้ *" required />
              </div>
              <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="รหัสผ่าน *" required />
              </div>
              <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="ยืนยันรหัสผ่าน *" required />
              </div>
              <div class="form-group">
                <label for="firstname" class="form-label">ชื่อจริง</label>
                <input type="text" name="firstname" id="firstname" class="form-control" placeholder="ชื่อจริง *" required />
              </div>
              <div class="form-group">
                <label for="surname" class="form-label">นามสกุล</label>
                <input type="text" name="surname" id="surname" class="form-control" placeholder="นามสกุล *" required />
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="permission" class="form-label">สิทธิ์การเข้าถึง</label>
                <select name="permission" id="permission" class="form-control">
  <option value="user" selected>ผู้ใช้ปกติ</option>
</select>
              </div>
            </div>
        
            <input type="submit" class="btn btn-primary btnRegister" value="ยืนยัน" />
          </div>
        </form>
        
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      <?php if ($success == 'user_added'): ?>
        Swal.fire({
          title: 'เพิ่มข้อมูลสำเร็จ',
          text: 'คุณจะถูกเปลี่ยนเส้นทางไปยังระบบหลัก',
          icon: 'success',
          timer: 1000,
          timerProgressBar: true
        }).then(function() {
          window.location = 'mainsystem.php';
        });
      <?php elseif ($error == 'invalid_username'): ?>
      Swal.fire({
        icon: 'error',
        title: 'เกิดข้อผิดพลาด',
        text: 'ชื่อผู้ใช้ต้องเป็นภาษาอังกฤษและมีความยาวอย่างน้อย 6 ตัว',
      }).then(function() {
        window.location = 'newAccount.php';
      });
      <?php elseif ($error == 'username_exists'): ?>
      Swal.fire({
        icon: 'error',
        title: 'เกิดข้อผิดพลาด',
        text: 'ชื่อผู้ใช้นี้มีอยู่ในระบบแล้ว กรุณาเลือกชื่อใหม่',
      }).then(function() {
        window.location = 'newAccount.php';
      });
      <?php elseif ($error == 'passwords_do_not_match'): ?>
      Swal.fire({
        icon: 'error',
        title: 'เกิดข้อผิดพลาด',
        text: 'รหัสผ่านไม่ตรงกัน',
      }).then(function() {
        window.location = 'newAccount.php';
      });
      <?php endif; ?>
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>
