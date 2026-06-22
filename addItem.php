<?php
include('connection.php');
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['permission'])) {

    header("Location: index.php");
    exit;
}

    $username = $_SESSION['username'];
    $permission = $_SESSION['permission'];
    if($permission != "admin"){
        header("Location: index.php");
    exit;
    }
    $result = mysqli_query($con, "SELECT * FROM Employee WHERE username = '$username'");
    $user = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสินค้าลงสต็อก ไดร์ฟ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="CSS/style_edituser.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.9/dist/sweetalert2.all.min.js"></script>
  
</head>
<?php include 'navbar.php'; ?>
<body>


<div class="container mt-5">
    <h2>เพิ่มสินค้าใหม่</h2>
    <form method="post" action="addItem_controller.php">
        <div class="form-group">
            <label for="ItemName">ชื่ออุปกรณ์:</label>
            <select class="form-control" id="ItemName" name="ItemName" required>
            <option value="" disabled selected>เลือกอุปกรณ์</option>
                <option value="DRIVE 08">DRIVE 08</option>
                <option value="DRIVE 16">DRIVE 16</option>
                <option value="DRIVE 32">DRIVE 32</option>
                <option value="DRIVE 48">DRIVE 48</option>
                <option value="การ์ดจอ 95">การ์ดจอ 95</option>
                <option value="การ์ดจอ XP">การ์ดจอ XP</option>
                <option value="Safety 95">เซฟตี้ 95</option>
                <option value="Safety XP">เซฟตี้ XP</option>
                <option value="RDW">RDW</option>
                <option value="MFC">MFC</option>
                <option value="DSE">DSE</option>
                <option value="KPS">KPS</option>
            </select>
        </div>
        <div class="form-group">
            <label for="category">หมวด / ห้อง</label>
            <select class="form-control" id="category" name="category" required>
            <option value="" disabled selected>เลือกหมวด</option>
            <option value="Stock_Main">ห้องประชุม</option>
            <option value="Stock_Main2_inroom">ห้อง 2</option>
            <option value="Stock_Tools">ห้องเครื่องมือ</option>
            </select>
        </div>
        <div class="form-group">
            <label for="SerialNumber">Serial Number:</label>
            <input type="text" class="form-control" id="SerialNumber" name="SerialNumber" required readonly>
        </div>
        <div class="form-group">
            <label for="Type">ประเภท:</label>
            <input type="text" class="form-control" id="Type" name="Type" required readonly>
            </select>
        </div>
        <div class="form-group">
            <label for="WhereItem">อยู่ที่ไหน:</label>
            <input type="text" class="form-control" id="WhereItem" name="WhereItem" required>
        </div>
        <div class="form-group">
            <label for="Status">สถานะ:</label>
            <select class="form-control" id="Status" name="Status" required>
            <option value="" disabled selected>เลือกสถานะ</option>
            <optgroup label="สถานะปกติ">
                <option value="active">ปกติ ✅</option>
                <option value="wait_test">กำลังเทส 🔵</option>
                </optgroup>
                <optgroup label="สถานะผิดปกติ">
                <option value="not_active">เสีย ❌</option>
                <option value="repairing">กำลังซ่อม 🟤</option>
                </optgroup>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">เพิ่ม</button>
        <a href="stock_controller.php" class="btn btn-secondary">ย้อนกลับ</a>
    </form>
</div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script>
$(document).ready(function() {

    $('#ItemName').change(function() {

        var itemName = $(this).val();
        var category = $('#category').val(); // ✅ ดึง category ให้ถูกที่

        if (!itemName || !category) {
            return;
        }

        $.ajax({
            url: 'check_stock.php',
            type: 'POST',
            data: {
                ItemName: itemName,
                category: category
            },
            dataType: 'json',
            success: function(response) {

                // ถ้า backend ส่ง error มา
                if (response.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: response.error
                    });
                    return;
                }

                // ใส่ Serial Number อัตโนมัติ
                $('#SerialNumber').val(response.serial);

                // ตั้งค่าประเภท
                var type = '';
                if (itemName.includes('DRIVE')) {
                    type = 'drive';
                } else if (itemName.includes('การ์ดจอ')) {
                    type = 'การ์ดจอ';
                } else if (itemName.includes('Safety')) {
                    type = 'Safety';
                } else if (['KPS','MFC','DSE','RDW'].includes(itemName)) {
                    type = itemName;
                }

                $('#Type').val(type);
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่สามารถดึงข้อมูลได้',
                    text: 'กรุณาลองใหม่อีกครั้ง'
                });
            }
        });
    });

});
</script>

  </html>
