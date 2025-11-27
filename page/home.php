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
        
        <div class="text-start mt-2">
          <a class="text-decoration-none fw-bold" data-bs-toggle="collapse" href="#courseDetailCollapse" role="button" aria-expanded="false" aria-controls="courseDetailCollapse" onclick="toggleArrow(this)">
            <i class="bi bi-chevron-down" id="courseArrow"></i> <span id="courseToggleText">ดูรายละเอียดเพิ่มเติม</span>
          </a>
        </div>

        <div class="collapse" id="courseDetailCollapse">
          <hr class="my-3">
          <p class="mb-2"><strong>รหัสวิชา:</strong> 50-406-081-302</p>
          <p class="mb-2"><strong>ชื่อวิชา:</strong> การสื่อสารข้อมูลและเครือข่ายคอมพิวเตอร์ (Data Communications and Computer Networks)</p>
          <p class="mb-2"><strong>หน่วยกิต:</strong> 3(2-2-5)</p>
          <p class="mb-2"><strong>คำอธิบายรายวิชา:</strong> หลักการสื่อสารข้อมูลทางคอมพิวเตอร์ สถาปัตยกรรมการสื่อสารข้อมูล แบบจำลองโอเอสไอ แบบจำลองอินเทอร์เน็ต เครือข่ายคอมพิวเตอร์ท้องถิ่น <br><small class="text-muted">(Principles of computer data communication; communication architecture; OSI model; internet model; local area networks)</small></p>
          <p class="mb-2"><strong>ผลลัพธ์การเรียนรู้ของรายวิชา (CLOs):</strong></p>
          <ul class="list-unstyled mb-0 ps-3">
            <li>CLO1 : เข้าใจเรื่องความซื่อสัตย์ สุจริต และการมีจรรยาบรรณในวิชาชีพ</li>
            <li>CLO2 : เข้าใจเรื่องการมีความรับผิดชอบและความตรงต่อเวลา</li>
            <li>CLO3 : อธิบายหลักการการการสื่อสารข้อมูลและเครือข่ายคอมพิวเตอร์ได้</li>
          </ul>
        </div>

        <script>
        function toggleArrow(element) {
          const arrow = document.getElementById('courseArrow');
          const text = document.getElementById('courseToggleText');
          if (arrow.classList.contains('bi-chevron-down')) {
            arrow.classList.remove('bi-chevron-down');
            arrow.classList.add('bi-chevron-up');
            text.innerText = "ซ่อนรายละเอียด";
          } else {
            arrow.classList.remove('bi-chevron-up');
            arrow.classList.add('bi-chevron-down');
            text.innerText = "ดูรายละเอียดเพิ่มเติม";
          }
        }
        </script>
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
