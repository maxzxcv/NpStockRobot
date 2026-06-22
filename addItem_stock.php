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
    <title>เพิ่มสินค้าลงสต็อก</title>
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
    <form method="post" action="addItem_stock_backend.php">
    <div class="form-group">
            <label for="room">ห้องที่จะเก็บ</label>
            <select class="form-control" id="room" name="room" required>
            <option value="" disabled selected>เลือกห้อง</option>
                <option value="Stock_Main">ห้องประชุม</option>
                <option value="Stock_Main2_inroom">ห้อง 2</option>
                <option value="Stock_Tools">ห้องเครื่องมือ</option>
            </select>
        </div>

        <div class="form-group d-none" id="toolNameBox">
            <label for="tool_name">ชื่ออุปกรณ์ (ห้องเครื่องมือ)</label>
            <input type="text" class="form-control" id="tool_name" name="tool_name">
        </div>

        <div class="form-group">
            <label for="ItemName">ชื่ออุปกรณ์</label>
            <input type="text" class="form-control" id="ItemName" name="ItemName" required>
        </div>
        <div class="form-group">
    <label for="Amount">จำนวนคงเหลือ</label>
    <input type="number" class="form-control" id="Amount" name="Amount" min="0" step="1" required>
</div>
        
        <div class="form-group">
            <label for="whereItem">เก็บไว้ที่:</label>
            <select class="form-control" id="whereItem" name="whereItem" required>
                <option value="" disabled selected>เลือกตำแหน่ง</option>
            </select>
        </div>

        <div class="form-group">
            <label for="status">สถานะ:</label>
            <select class="form-control" id="status" name="status" required>
            <option value="" disabled selected>เลือกสถานะ</option>
            <optgroup label="สถานะปกติ">
                <option value="">ไม่ระบุ ❔</option>
                <option value="active">ปกติ ✅</option>
                <option value="wait_test">กำลังเทส 🔵</option>
                </optgroup>
                <optgroup label="สถานะผิดปกติ">
                <option value="not_active">เสีย ❌</option>
                <option value="repairing">กำลังซ่อม 🟤</option>
                </optgroup>
            </select>
        </div>
        <div class="form-group">
            <label for="itemoutstock">จำนวนต่ำกว่าเท่าใด ให้แจ้งเตือนของใกล้หมด (ตัวเลขเท่านั้น)</label>
            <input type="number" class="form-control" id="itemoutstock" name="itemoutstock" min="0" step="1" required>
        </div>
        <button type="submit" class="btn btn-primary">เพิ่ม</button>
        <a href="stock.php" class="btn btn-secondary">ย้อนกลับ</a>
    </form>
</div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script>
    $(document).ready(function () {
    $('#room').change(function () {
        var roomValue = $(this).val();
        var whereitemSelect = $('#whereItem');

        whereitemSelect.empty();
        whereitemSelect.append('<option value="" disabled selected>เลือกตำแหน่ง</option>');

        // reset ช่องชื่อ
        $('#toolNameBox').addClass('d-none');
        $('#ItemName').prop('required', true);
        $('#tool_name').prop('required', false);

        if (roomValue === 'Stock_Main') {
            whereitemSelect.append(`
                <optgroup label="ตู้ที่ 1">
                    <option value="ตู้ 1 ชั้นที่ 1">ตู้ 1 ชั้นที่ 1</option>
                    <option value="ตู้ 1 ชั้นที่ 2">ตู้ 1 ชั้นที่ 2</option>
                    <option value="ตู้ 1 ชั้นที่ 3">ตู้ 1 ชั้นที่ 3</option>
                </optgroup>
                <optgroup label="ตู้ที่ 2">
                    <option value="ตู้ 2 ชั้นที่ 1">ตู้ 2 ชั้นที่ 1</option>
                    <option value="ตู้ 2 ชั้นที่ 2">ตู้ 2 ชั้นที่ 2</option>
                    <option value="ตู้ 2 ชั้นที่ 3">ตู้ 2 ชั้นที่ 3</option>
                    <option value="ตู้ 2 ชั้นที่ 4">ตู้ 2 ชั้นที่ 4</option>
                    <option value="ตู้ 2 ชั้นที่ 5">ตู้ 2 ชั้นที่ 5</option>
                </optgroup>
                <optgroup label="ห้องประชุม">
                    <option value="ห้องประชุม">ห้องประชุม</option>
                </optgroup>
            `);
        }
        else if (roomValue === 'Stock_Main2_inroom') {
            whereitemSelect.append(`
                <option value="ห้องเก็บของสแปร์ ห้อง 2">ห้องเก็บของสแปร์ ห้อง 2</option>
                <option value="ห้องเตรียมส่งของซ่อม ห้องของเสีย">ห้องเตรียมส่งของซ่อม ห้องของเสีย</option>
            `);
        }
        else if (roomValue === 'Stock_Tools') {
            // ห้องเครื่องมือ → ไม่ใช้ตำแหน่งตายตัว
            whereitemSelect.append(`
                <option value="ห้องเครื่องมือ">ห้องเครื่องมือ</option>
            `);

            // แสดงช่องกรอกชื่ออุปกรณ์ใหม่
            $('#toolNameBox').removeClass('d-none');
            $('#ItemName').prop('required', false);
            $('#tool_name').prop('required', true);
        }
    });
});

</script>

  </html>
