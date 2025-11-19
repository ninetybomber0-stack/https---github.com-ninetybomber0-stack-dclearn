<?php
// page/plane.php
if (!isset($mysqli)) require_once __DIR__ . '/../config/connect.php';

$q = trim($_GET['q'] ?? '');

// helper: ไฮไลต์คำค้น
function hi($text, $q) {
  if ($q === '') return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
  $safe = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
  $pattern = '/' . preg_quote($q, '/') . '/iu';
  return preg_replace($pattern, '<mark>$0</mark>', $safe);
}
?>

<section id="plan" class="mb-4">
  <?php if ($q !== ''): ?>
    <?php
      // กัน wildcard และค้นด้วย LIKE + prepared statement
      $like = '%' . str_replace(['%','_'], ['\%','\_'], $q) . '%';
      $stmt = $mysqli->prepare("SELECT id, name FROM tb_content WHERE name LIKE ?");
      $stmt->bind_param('s', $like);
      $stmt->execute();
      $res = $stmt->get_result();
      $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
      $stmt->close();
    ?>
    <h2 class="h5 section-title mb-2">
      ผลการค้นหา: "<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>" (<?= count($rows) ?> รายการ)
    </h2>

    <?php if (empty($rows)): ?>
      <div class="alert alert-warning">ไม่พบหัวข้อที่ตรงกับคำค้น</div>
      <a class="btn btn-outline-secondary" href="index.php?page=plane">แสดงแผน 13 สัปดาห์ทั้งหมด</a>
    <?php else: ?>
      <div class="list-group">
        <?php foreach ($rows as $r): ?>
          <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" 
             href="index.php?page=video_lesson&chapter=<?= (int)$r['id'] ?>">
            <span><?= hi($r['name'], $q) ?></span>
            <span class="btn btn-sm btn-outline-primary">เรียน</span>
          </a>
        <?php endforeach; ?>
      </div>
      <div class="mt-3">
        <a class="btn btn-outline-secondary" href="index.php?page=plane">แสดงแผน 13 สัปดาห์ทั้งหมด</a>
      </div>
    <?php endif; ?>

  <?php else: // ไม่มี q → แสดงแผน 13 สัปดาห์ ?>
    <h2 class="h5 section-title mb-2">แผนการจัดการเรียนรู้ (13 สัปดาห์)</h2>
    <?php if (isset($_SESSION['sess_username']) && $_SESSION['sess_username'] === 'kamol'): ?>
    <div class="mb-3">
        <a href="index.php?page=add_unit" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> เพิ่มหน่วยการเรียน</a>
    </div>
    <?php endif; ?>
    <div class="accordion" id="planAcc">
      <?php
      $count=0;
      $result = $mysqli->query('SELECT id, name FROM tb_content ORDER BY id');
      while($r = $result->fetch_assoc()):
        $count++;
        $hid = "w{$count}h";   // heading id
        $cid = "w{$count}";    // collapse id
      ?>
        <div class="accordion-item">
          <h2 class="accordion-header" id="<?= $hid ?>">
            <button class="accordion-button collapsed" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#<?= $cid ?>"
                    aria-expanded="false"
                    aria-controls="<?= $cid ?>">
              สัปดาห์ที่ <?= $count ?> : <?= htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8') ?>
            </button>
          </h2>
          <div id="<?= $cid ?>" class="accordion-collapse collapse"
               aria-labelledby="<?= $hid ?>" data-bs-parent="#planAcc">
            <div class="accordion-body">
              <ul class="mb-0">
                <li><a href="index.php?page=video_lesson&chapter=<?= (int)$r['id'] ?>">เรียน</a></li>
                <li>ใบความรู้</li>
                <li>สอบท้ายบท</li>
              </ul>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>
</section>
