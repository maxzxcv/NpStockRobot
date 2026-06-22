<?php
include('connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $day = mysqli_real_escape_string($con, $_POST['day']);
    $month = mysqli_real_escape_string($con, $_POST['month']);
    $year = mysqli_real_escape_string($con, $_POST['year']);

    // ดึงข้อมูลงานที่เกี่ยวข้องกับวันที่ที่เลือก
    $sql = "SELECT id,event_name, detail_work, employee_ids, time_start, time_end FROM schedule 
            WHERE DAY(date_start) <= '$day' AND DAY(date_end) >= '$day'
            AND MONTH(date_start) <= '$month' AND MONTH(date_end) >= '$month'
            AND YEAR(date_start) <= '$year' AND YEAR(date_end) >= '$year'";

    $result = mysqli_query($con, $sql);
    
    $events = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }

    echo json_encode($events);
}
?>
