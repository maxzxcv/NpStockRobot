<?php
include('connection.php');
session_start();

// ตรวจสอบสิทธิ์การเข้าใช้งาน
if (!isset($_SESSION['username']) || !isset($_SESSION['permission'])) {
    header("Location: logout.php");
    exit;
}

$username = $_SESSION['username'];
$permission = $_SESSION['permission'];

if ($permission != "admin") {
    header("Location: index.php");
    exit;
}

$result = mysqli_query($con, "SELECT * FROM Employee WHERE username = '$username'");
$user = mysqli_fetch_assoc($result);
if (!$user) {
    die("User not found.");
}

$error = isset($_GET['error']) ? $_GET['error'] : '';
$success = isset($_GET['success']) ? $_GET['success'] : '';
?>

<!DOCTYPE html>
<html lang="th">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>เอาของเข้าสต็อก</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="CSS/style_edituser.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.9/dist/sweetalert2.all.min.js"></script>

<style>
body {
    font-family: 'Kanit', sans-serif;
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
            <h3>Add Item to stock</h3>
            <p>สำหรับ เอาของเข้า โดยผู้ดูแลสต็อก</p>
        </div>

        <div class="col-md-9 register-right">
            <h3 class="register-heading">เอาของเข้า</h3>

            <form action="importItem_backend.php" method="post" enctype="multipart/form-data">

                <div class="row register-form">
                    <div class="col-md-6">

                        <div class="form-group">
                            <label class="form-label">โดยผู้ดูแลสต็อก</label>
                            <input type="text" name="firstname" class="form-control"
                                   value="<?php echo htmlspecialchars($user['firstname'] . ' ' . $user['surname']); ?>"
                                   readonly required />
                        </div>

                        <div class="form-group">
                            <label class="form-label">ผู้นำเข้า</label>
                            <select name="user" id="user" class="form-control" required>
                                <option value="" disabled selected>เลือกชื่อผู้นำเข้า</option>
                                <?php
                                $nameQuery = mysqli_query($con, "SELECT firstname, surname, nickname FROM nameTable");
                                while ($row = mysqli_fetch_assoc($nameQuery)) {
                                    $fullname = $row['firstname'] . ' ' . $row['surname'];
                                    $nickname = $row['nickname'];
                                    echo "<option value='$fullname'>$fullname ($nickname)</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">เลือกหมวดหมู่</label>
                            <select name="category" id="category" class="form-control" required>
                                <option value="" disabled selected>เลือกหมวดหมู่</option>
                                <option value="Stock_Main">ห้องประชุม</option>
                                <option value="Stock_Main2_inroom">ห้อง 2</option>
                                <option value="Stock_Main2_Study">ชุดสำหรับอบรม</option>
                                <option value="Stock_Tools">ห้องเครื่องมือ</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">เลือกของที่จะนำเข้า</label>
                            <select name="item" id="item" class="form-control js-example-basic-single" required>
                                <option value="" disabled selected>เลือกของที่จะนำเข้า</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">จำนวนกี่ชิ้น</label>
                            <select name="quantity" id="quantity" class="form-control" required>
                                <option value="" disabled selected>เลือกจำนวน</option>
                                <?php for ($i = 1; $i <= 100; $i++) echo "<option value='$i'>$i</option>"; ?>
                            </select>
                        </div>

                        <input type="hidden" name="item_id" id="item_id">
                        <input type="hidden" name="item_name" id="item_name">

                        <div class="form-group">
                            <label class="form-label">แนบรูปภาพประกอบ</label>
                            <input type="file" name="image" id="image" class="form-control" required />
                        </div>

                        <div class="form-group">
                            <label class="form-label">วันที่เพิ่ม</label>
                            <input type="datetime-local" name="date_added" id="date_added"
                                   class="form-control"
                                   value="<?php echo date('Y-m-d\TH:i'); ?>" required />
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <input type="submit" class="btn btn-success btnRegister w-100" value="ยืนยัน" />
                        </div>

                        <div class="col-md-12">
                            <a href="mainsystem.php" class="btn btn-secondary w-100">ย้อนกลับ</a>
                        </div>
                    </div>

                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {

    $('#user').select2();
    $('.js-example-basic-single').select2();

    $('#category').on('change', function () {

        const selectedCategory = $(this).val();
        const itemSelect = document.getElementById('item');

        if (!selectedCategory) return;

        fetch(`getItemQuantity.php?category=${selectedCategory}`)
            .then(response => response.json())
            .then(data => {

                itemSelect.innerHTML =
                    `<option value="" disabled selected>เลือกของที่จะนำเข้า</option>`;

                if (data.error) {
                    Swal.fire('ข้อมูลไม่พบ', data.error, 'error');
                    return;
                }

                data.forEach(item => {
                    itemSelect.innerHTML += 
                        `<option value="${item.id}" data-name="${item.ItemName}">
                            ${item.id} - ${item.ItemName}
                         </option>`;
                });

                $('#item').trigger('change.select2');
            })

            .catch(err => {
                Swal.fire('ไม่สามารถดึงข้อมูลได้', 'กรุณาลองใหม่', 'error');
                console.error(err);
            });
    });

    // เมื่อเลือก item
    $('#item').on('change', function () {
        const id = $(this).val();
        const name = $(this).find(':selected').data('name');

        $('#item_id').val(id);
        $('#item_name').val(name);
    });

});
</script>

<script>
// SweetAlert แจ้งผล
const url = new URLSearchParams(window.location.search);

if (url.get("success") === "item_added") {
    Swal.fire({
        icon: "success",
        title: "เพิ่มของเข้าสต๊อกแล้ว!",
        text: "ข้อมูลถูกบันทึกสำเร็จ",
        confirmButtonText: "ตกลง"
    }).then(() => {
        window.history.replaceState(null, "", window.location.pathname);
    });
}

if (url.get("error") === "item_not_found") {
    Swal.fire({
        icon: "error",
        title: "ไม่พบสินค้า",
        text: "สินค้าในหมวดนี้ไม่พบ หรือข้อมูลผิดพลาด"
    });
}
</script>

</body>
</html>
