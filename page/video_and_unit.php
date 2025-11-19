<?php
// ดึงข้อมูลบทเรียนทั้งหมดเพื่อสร้างเมนู
$lessons_for_nav = [];
$sql_nav = "SELECT id, name FROM tb_content ORDER BY lesson_order ASC, id ASC";
$result_nav = $mysqli->query($sql_nav);
if ($result_nav) {
    while ($row = $result_nav->fetch_assoc()) {
        $lessons_for_nav[] = $row;
    }
}
?>

<!-- Dropdown สำหรับหน่วยการเรียนรู้ -->
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-collection-play-fill me-1"></i> หน่วยการเรียนรู้
    </a>
    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
        <?php if (empty($lessons_for_nav)): ?>
            <li><a class="dropdown-item" href="#">ไม่มีบทเรียน</a></li>
        <?php else: ?>
            <?php foreach ($lessons_for_nav as $lesson): ?>
                <li><a class="dropdown-item" href="index.php?page=content&chapter=<?= $lesson['id'] ?>"><?= htmlspecialchars($lesson['name'], ENT_QUOTES, 'UTF-8') ?></a></li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</li>