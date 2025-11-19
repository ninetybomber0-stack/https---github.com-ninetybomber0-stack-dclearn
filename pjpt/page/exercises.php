<?php include "header.php"; ?>
<?php include "config/connect.php"; ?>

<h1>แบบฝึกหัด</h1>
<p>เลือกทำแบบฝึกหัดในแต่ละบทเรียน</p>
<ul>
    <?php
    $result = $conn->query("SELECT * FROM lessons ORDER BY id ASC");
    while($row = $result->fetch_assoc()):
        // ในตัวอย่างนี้จะลิงก์ไปแค่ exercises1.php ก่อน
        // คุณสามารถสร้างตรรกะเพิ่มเติมเพื่อลิงก์ไปยังแบบฝึกหัดของบทอื่นๆ ได้
        if ($row['id'] == 1) {
            echo '<li><a href="exercises1.php">แบบฝึกหัดสำหรับ ' . htmlspecialchars($row['title']) . '</a></li>';
        } else {
             echo '<li><a href="#">แบบฝึกหัดสำหรับ ' . htmlspecialchars($row['title']) . ' (ยังไม่พร้อมใช้งาน)</a></li>';
        }
    endwhile;
    ?>
</ul>

<?php include "footer.php"; ?>