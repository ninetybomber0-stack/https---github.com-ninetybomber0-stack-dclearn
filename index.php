<?php
// 1. เริ่ม session และตรวจสอบการล็อกอินก่อน
require_once __DIR__ . '/config/session_boot.php';

// --- ตรวจสอบ Session ก่อนแสดงผล HTML ใดๆ ---
include "module/member/chksession.php";
// 2. เชื่อมต่อฐานข้อมูล (ย้ายขึ้นมาเพื่อให้เมนูใช้งานได้)
include "config/connect.php"; 

// --- ย้ายส่วนดึงข้อมูลผู้ใช้และตั้งค่า Session มาไว้ก่อนการตรวจสอบสิทธิ์ ---
if (isset($_SESSION['sess_username'])) {
    $sess_username = $_SESSION['sess_username'];
    $sql = "SELECT id, id_std, fullname, class, password FROM tb_member WHERE id_std = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $sess_username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $_SESSION['sess_userid'] = $row['id'];
        $_SESSION['sess_id_std'] = $row['id_std'];
        $_SESSION['sess_fullname'] = $row['fullname'];
    }
    $stmt->close();
}

// --- ตรวจสอบและเพิ่มข้อมูลบทเรียนเริ่มต้น (ถ้ายังไม่มี) ---
$check_content_res = $mysqli->query("SELECT COUNT(*) as count FROM tb_content");
if ($check_content_res) {
    $row = $check_content_res->fetch_assoc();
    if ((int)$row['count'] === 0) {
        // ถ้าตารางว่าง ให้เรียกใช้ populate_lessons.php เพื่อเพิ่มข้อมูล
        include_once __DIR__ . '/populate_lessons.php';
    }
}
// ---------------------------------------------------------

$page = $_GET['page'] ?? 'home'; // กำหนดค่า page (ถ้าไม่มีให้เป็น 'home')

// --- ตรวจสอบการทำแบบทดสอบก่อนเรียน (Pre-test) สำหรับบทที่ 1 ---
if ($page === 'content' && isset($_GET['chapter']) && (int)$_GET['chapter'] === 1 && isset($_SESSION['sess_userid'])) {
    $uid_chk_pre = (int)$_SESSION['sess_userid'];
    // ตรวจสอบว่ามีคะแนน Pre-test บทที่ 1 หรือยัง (ไม่สนคะแนน 0 ก็ถือว่าทำแล้ว)
    $chk_pre_score = $mysqli->query("SELECT id FROM tb_scores WHERE member_id = $uid_chk_pre AND lesson_id = 1 AND test_type = 'PRE'");
    if ($chk_pre_score->num_rows === 0) {
        // ยังไม่เคยทำ -> เด้งไปหน้า Pre-test
        header("Location: index.php?page=pretest&lesson_id=1");
        exit;
    }
}
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>บทเรียนออนไลน์ | การสื่อสารข้อมูลและเครือข่ายคอมพิวเตอร์</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    :root{--brand:#0d6efd}
    body{background:#f6f7fb}
    .navbar-brand{font-weight:700}
    .hero{background:linear-gradient(135deg, rgba(13,110,253,.12), rgba(25,135,84,.12)); border-radius:1.25rem; padding:3rem 1.5rem}
    .hero h1{font-weight:800; letter-spacing:.2px}
    .stat{border-radius:1rem; background:#fff; box-shadow:0 6px 20px rgba(0,0,0,.06)}
    .module-card{border:0; border-radius:1rem; box-shadow:0 10px 30px rgba(0,0,0,.06)}
    .module-card .badge{letter-spacing:.2px}
    .pill{border-radius:999px; background:#eef2ff; color:#3743a0; padding:.2rem .6rem; font-size:.75rem}
    .section-title{font-weight:700}
    .link-muted{color:#6c757d; text-decoration:none}
    .link-muted:hover{color:#0d6efd}
    .footer{color:#6c757d}

    .hero{
      /* ไล่สี + รูปพื้นหลัง */
      background:
        linear-gradient(135deg, rgba(13,110,253,.12), rgba(25,135,84,.12)),
        url("img/bg.png") center/cover no-repeat;

      border-radius: 1.25rem;
      padding: 1.25rem 1rem;
      /* กันภาพล้นขอบโค้ง */
      overflow: hidden;
    }
    @media (min-width: 992px){
      .hero{ padding: 1.5rem 1.25rem; }
    }

/* Tablet */
@media (max-width: 1199.98px){
  .hero{
    background:
      linear-gradient(135deg, rgba(13,110,253,.12), rgba(25,135,84,.12)),
      url("img/hero/hero-960.webp") center/cover no-repeat;
  }
}

/* Mobile */
@media (max-width: 575.98px){
  .hero{
    background:
      linear-gradient(135deg, rgba(13,110,253,.12), rgba(25,135,84,.12)),
      url("img/hero/hero-828.webp") center/cover no-repeat;
  }
}
  </style>
</head>
<body>
  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="#">
        <i class="bi bi-hdd-network-fill fs-4 text-primary"></i>
        <span>DataCom & Networks</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="topNav">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" href="index.php">หน้าแรก</a></li>
          <!-- Dropdown สำหรับหน่วยการเรียนรู้ -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-collection-play-fill me-1"></i> หน่วยการเรียนรู้
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
              <?php
                $lesson_query = "SELECT id, name FROM tb_content ORDER BY lesson_order ASC, id ASC";
                $lesson_result = $mysqli->query($lesson_query);
                if ($lesson_result && $lesson_result->num_rows > 0) {
                    while ($lesson_row = $lesson_result->fetch_assoc()) {
                        echo '<li><a class="dropdown-item" href="index.php?page=content&chapter=' . $lesson_row['id'] . '">' . htmlspecialchars($lesson_row['name'], ENT_QUOTES, 'UTF-8') . '</a></li>';
                    }
                } else {
                    echo '<li><a class="dropdown-item" href="index.php?page=content&chapter=1">บทที่ 1: ความรู้เบื้องต้นเกี่ยวกับการสื่อสารข้อมูล</a></li>';
                    echo '<li><a class="dropdown-item" href="index.php?page=content&chapter=2">บทที่ 2: รูปแบบและส่วนประกอบของระบบสื่อสารข้อมูล</a></li>';
                }
              ?>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link" href="index.php?page=work">งานที่มอบหมาย</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?page=notice">ประกาศ</a></li>
          <?php if (isset($_SESSION['sess_username']) && $_SESSION['sess_username'] === 'kamol'): ?>
          <li class="nav-item"><a class="nav-link" href="index.php?page=user">รายชื่อนักศึกษา</a></li>
          <?php endif; ?>
        </ul>
        <!--<div class="d-flex gap-2">
          <a class="btn btn-primary" href="#start"><i class="bi bi-play-circle me-1"></i> เริ่มเรียน</a>
        </div>-->
        <div class="ms-3 dropdown">
          <a class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" href="#"><i class="bi bi-person-circle me-1"></i>
          <?php
          if (isset($_SESSION['sess_username']) && $_SESSION['sess_username'] == "kamol") {
            echo "อาจารย์กมล  ช่วยรักษา";
          } else if (isset($_SESSION['sess_fullname'])) {
            echo htmlspecialchars($_SESSION['sess_fullname']);
          }
          ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="index.php?page=score">คะแนนและการส่งงาน</a></li>
            <li><a class="dropdown-item" href="index.php?page=changepw&id=<?php echo $_SESSION['sess_userid'] ?? ''; ?>">เปลี่ยนรหัสผ่าน</a></li>
            <li><a class="dropdown-item" href="module/member/logout.php">ออกจากระบบ</a></li>
          </ul>
        </div>
      </div>
    </div>
  </nav>

  <main class="container py-4">
    <!-- HERO -->
    <section class="hero mb-4">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <div class="kicker text-primary fw-semibold mb-1">บทเรียนออนไลน์</div>
          <h2 class="display-6 mb-2">การสื่อสารข้อมูลและเครือข่ายคอมพิวเตอร์</h2>
          <p class="text-secondary mb-3">ครบทั้งวิดีโอสอน สไลด์ แบบฝึกหัด และแบบทดสอบ ครอบคลุมตั้งแต่พื้นฐานสัญญาณ สื่อกลาง OSI/TCP‑IP การกำหนดเลข IP จนถึงความปลอดภัยเครือข่าย</p>
          
        </div>
        <div class="col-lg-5">
          <div class="row text-center g-3">
            <div class="col-4">
              <div class="stat p-3">
                <div class="h4 mb-0">10+</div>
                <div class="small text-muted">หน่วยการเรียน</div>
              </div>
            </div>
            <div class="col-4">
              <div class="stat p-3">
                <div class="h4 mb-0">25+</div>
                <div class="small text-muted">แบบทดสอบ</div>
              </div>
            </div>
            <div class="col-4">
              <div class="stat p-3">
                <div class="h4 mb-0">10 ชม.+</div>
                <div class="small text-muted">วิดีโอ</div>
              </div>
            </div>
          </div>
          <div class="mt-3 text-center text-lg-start">
            <span class="pill">พร้อมสอบกลางภาค/ปลายภาค</span>
            <span class="pill ms-1">รองรับมือถือ</span>
          </div>
        </div>
      </div>
    </section>
<?php
if($page=="work"){
    include "page/work.php";
}elseif($page=="add_unit"){
    include "page/add_unit.php";
}elseif($page=="notice"){
    include "page/notice.php";
}elseif($page=="user" || $page=="edit_user"){
    include "page/user_list.php"; // User.php is the edit page, user_list.php is the list
}elseif($page=="content" || $page=="video_lesson"){ // Route to the correct video player
    include "page/content.php";
}elseif($page=="manage_quizzes"){
    include "page/manage_quizzes.php";
}elseif($page=="changepw"){
    include "page/change_password.php";
}elseif($page=="score"){
    include "page/score.php";
}elseif($page=="pretest"){
    include "page/pretest.php";
}else{
    include "page/home.php";
}


?>   
    <!-- INSTRUCTOR -->
<section class="mt-4">
  <div class="card">
    <div class="card-body d-flex flex-wrap align-items-center gap-3">
      <img src="img/portrait_150w.jpg" class="rounded-circle" style="width:72px;height:72px;object-fit:cover" alt="ผู้สอน">
      <div class="flex-fill">
        <div class="fw-semibold">ผู้สอน: อาจารย์กมล ช่วยรักษา</div>
        <div class="text-muted small">อีเมล: kamol.ch@rmuti.ac.th</div>
      </div>

      <!-- ปุ่มติดต่อผู้สอน = เปิด QR กลุ่ม LINE -->
      <div class="d-flex gap-2">
        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#lineQRModal">
          <i class="bi bi-qr-code me-1"></i> ติดต่อผู้สอน (เข้ากลุ่ม LINE)
        </button>
      </div>
    </div>
  </div>
</section>

<!-- Modal: QR กลุ่ม LINE -->
<div class="modal fade" id="lineQRModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-qr-code me-1"></i> เข้ากลุ่ม LINE ติดต่อผู้สอน</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
      </div>
      <div class="modal-body text-center">
        <!-- ใส่ไฟล์ QR ของกลุ่ม LINE ตรงนี้ -->
        <img src="img/line-group-qr.png" alt="QR กลุ่ม LINE"
             class="img-fluid rounded" style="max-width:280px" loading="lazy">
        <div class="text-muted small mt-2">
          สแกนด้วยแอป LINE เพื่อเข้ากลุ่ม
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <div class="small text-muted">หรือเปิดลิงก์เชิญโดยตรง</div>
        <!-- ใส่ลิงก์เชิญจริงของกลุ่ม LINE แทน XXXXXXXX -->
        <a href="https://line.me/ti/g/x9TU2GNfnp" target="_blank" class="btn btn-success">
          <i class="bi bi-box-arrow-up-right me-1"></i> เปิดด้วย LINE
        </a>
      </div>
    </div>
  </div>
</div>

  </main>

  <footer class="border-top bg-white mt-4 py-3 footer">
    <div class="container d-flex flex-wrap justify-content-between small">
      <div>© <span id="year"></span> หลักสูตรเทคโนโลยีธุรกิจดิจิทัล</div>
      <div class="d-flex gap-3">
        <a class="link-muted" href="#">คู่มือผู้เรียน</a>
        <a class="link-muted" href="#">นโยบายความเป็นส่วนตัว</a>
        <a class="link-muted" href="#">ติดต่อ</a>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // ปีปัจจุบัน
    document.getElementById('year').textContent = new Date().getFullYear();


    /*
    // สร้างการ์ดโมดูล
    const grid = document.getElementById('moduleGrid');
    function renderModules(list){
      grid.innerHTML = list.map(m=>`
        <div class="col-sm-6 col-lg-4">
          <div class="card module-card h-100">
            <div class="card-body d-flex flex-column">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <h3 class="h6 mb-0">${m.title}</h3>
                ${m.badge?`<span class="badge text-bg-primary">${m.badge}</span>`:''}
              </div>
              <div class="text-muted small mb-3">${m.tag}</div>
              <div class="mt-auto d-flex justify-content-between align-items-center">
                <div class="small text-muted"><i class="bi bi-clock me-1"></i>${m.minutes} นาที</div>
                <div class="btn-group btn-group-sm">
                  <a href="${m.href}" class="btn btn-outline-primary"><i class="bi bi-play-circle"></i> เรียน</a>
                  <a href="#" class="btn btn-outline-secondary"><i class="bi bi-file-earmark-text"></i> สไลด์</a>
                  <a href="#" class="btn btn-outline-secondary"><i class="bi bi-ui-checks-grid"></i> แบบทดสอบ</a>
                </div>
              </div>
            </div>
          </div>
        </div>`).join('');
    }
    renderModules(modules);

    // ค้นหาโมดูล
    function filterModules(){
      const q = (document.getElementById('q').value||'').toLowerCase();
      const filtered = modules.filter(m=> (m.title+" "+m.tag).toLowerCase().includes(q));
      renderModules(filtered);
    }
    */
  </script>
</body>
</html>
