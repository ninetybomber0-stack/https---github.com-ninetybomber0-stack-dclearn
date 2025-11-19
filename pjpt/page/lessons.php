<?php include "header.php"; ?>
<?php include "config/connect.php"; // เชื่อมต่อฐานข้อมูล ?>

<h1>บทเรียน</h1>
<ul>
    <?php
    // ดึงข้อมูลบทเรียนทั้งหมดจากตาราง lessons
    $result = $conn->query("SELECT * FROM lessons ORDER BY id ASC");
    while($row = $result->fetch_assoc()):
    ?>
        <li>
            <a href="lesson.php?id=<?= $row['id'] ?>">
                <?= htmlspecialchars($row['title']) ?>
            </a>
        </li>
    <?php endwhile; ?>
</ul>

<?php include "footer.php"; ?>