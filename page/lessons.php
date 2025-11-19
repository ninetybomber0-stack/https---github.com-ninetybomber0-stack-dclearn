<?php
if (!isset($mysqli)) require_once __DIR__ . '/../config/connect.php';
// ดึงข้อมูลบทเรียนทั้งหมด
$lessons = [];
$sql = "SELECT id, name, title FROM tb_content ORDER BY lesson_order ASC, id ASC";
$result = $mysqli->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $lessons[] = $row;
    }
}
?>

<div class="card">
    <div class="card-header bg-white">
        <h2 class="h4 mb-0"><i class="bi bi-collection-play-fill me-2"></i>หน่วยการเรียนรู้ทั้งหมด</h2>
    </div>
    <div class="card-body">
        <?php if (empty($lessons)): ?>
            <div class="alert alert-warning">ยังไม่มีบทเรียนในระบบ</div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($lessons as $lesson): ?>
                    <a href="index.php?page=content&chapter=<?= $lesson['id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1"><?= htmlspecialchars($lesson['name'], ENT_QUOTES, 'UTF-8') ?></h5>
                            <p class="mb-1 text-muted small"><?= htmlspecialchars($lesson['title'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>