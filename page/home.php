<?php
// ---- ดึงรายการประกาศ (ล่าสุดก่อน) ----
$notices = [];
$res = $mysqli->query("SELECT id, message, `time` FROM tb_notice ORDER BY `time` DESC LIMIT 100");
if ($res) { $notices = $res->fetch_all(MYSQLI_ASSOC); }

function is_new($timestamp, $days = 7) {
  return strtotime($timestamp) >= strtotime("-{$days} days");
}
?>

<!-- COURSE DESCRIPTION -->
<section class="mb-4">
  <div class="card shadow-sm border-0">
    <div class="card-body p-4">
      <h4 class="card-title fw-bold text-primary mb-3"><i class="bi bi-book-half me-2"></i>คำอธิบายรายวิชา</h4>
      <div class="fs-5">
        <p class="mb-2"><strong>ชื่อหลักสูตร (ภาษาไทย):</strong> หลักสูตรบริหารธุรกิจบัณฑิต สาขาวิชาเทคโนโลยีธุรกิจดิจิทัล (ต่อเนื่อง)</p>
        <p class="mb-2"><strong>ชื่อหลักสูตร (ภาษาอังกฤษ):</strong> Bachelor of Business Administration Program in Digital Business Technology (Continuing Program)</p>
        <p class="mb-0"><strong>รหัสหลักสูตร:</strong> 25481991103817</p>
      </div>
    </div>
  </div>
</section>

<!-- ANNOUNCEMENTS & LIVE -->
<div class="row g-3">
  <!-- ซ้าย: ประกาศ -->
  <section id="ann" class="col-lg-6 d-flex"><!-- d-flex ที่คอลัมน์ -->
    <div class="card h-100 flex-fill w-100"><!-- h-100 + flex-fill ให้สูงเท่าฝั่งขวา -->
      <div class="card-header bg-white">
        <i class="bi bi-megaphone me-1"></i> ประกาศ
      </div>
      <div class="list-group list-group-flush" id="annList">
        <?php if (empty($notices)): ?>
          <div class="list-group-item text-muted">ยังไม่มีประกาศ</div>
        <?php else: ?>
          <?php foreach ($notices as $n): ?>
            <a class="list-group-item list-group-item-action" href="#">
              <?php if (is_new($n['time'])): ?>
                <span class="badge text-bg-success me-2">ใหม่</span>
              <?php endif; ?>
              <?= htmlspecialchars($n['message'], ENT_QUOTES, 'UTF-8') ?>
              <div class="small text-muted mt-1">
                <?= (new DateTime($n['time']))->format('d M Y H:i') ?>
              </div>
            </a>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </section>

</div>
