<?php
include "config/connect.php";
$page=$_GET['page'];
?>
<!doctype html>
<html lang="th" data-bs-theme="light">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ระบบเรียนออนไลน์ | Bootstrap 5 Template</title>
  <meta name="description" content="เทมเพลตหน้าเว็บระบบเรียนออนไลน์ด้วย Bootstrap 5" />

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    /* ปรับแต่งเล็กน้อยให้ดูเป็นเอกลักษณ์ */
    .navbar-brand span { font-weight: 800; letter-spacing: 0.3px; }
    .hero {
      background: linear-gradient(180deg, rgba(13,110,253,.08), rgba(13,110,253,.02));
      border-bottom: 1px solid rgba(0,0,0,.06);
    }
    .course-card img { object-fit: cover; height: 160px; }
    .lesson-sidebar {
      height: calc(100vh - 120px);
      overflow: auto;
    }
    .badge-level { text-transform: uppercase; font-size: .7rem; }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg bg-body border-bottom sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="#">
        <i class="bi bi-mortarboard-fill text-primary fs-4"></i>
        <span>LearnX</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNav" aria-controls="offcanvasNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNav" aria-labelledby="offcanvasNavLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="offcanvasNavLabel">เมนู</h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <form class="d-flex me-lg-3 mb-3 mb-lg-0" role="search">
            <input class="form-control" type="search" placeholder="ค้นหาคอร์ส เชิงลึก / พื้นฐาน / React ..." aria-label="Search">
          </form>
          <ul class="navbar-nav ms-lg-3 align-items-lg-center gap-lg-2">
            <li class="nav-item"><a class="nav-link active" href="#courses">คอร์ส</a></li>
             <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle me-1"></i> บทเรียน
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="index.php?page=l1">บทที่1</a></li>
                <li><a class="dropdown-item" href="#adminNote">บทที่2</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><button class="dropdown-item" id="themeToggle" type="button"><i class="bi bi-moon-stars me-2"></i>สลับโหมดสว่าง/มืด</button></li>
              </ul>
            </li>
            <li class="nav-item"><a class="nav-link" href="#paths">เส้นทางการเรียน</a></li>
            <li class="nav-item"><a class="nav-link" href="#pricing">ราคา</a></li>
            <li class="nav-item"><a class="nav-link" href="#faq">คำถามที่พบบ่อย</a></li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle me-1"></i> บัญชีของฉัน
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#authModal">เข้าสู่ระบบ / สมัครสมาชิก</a></li>
                <li><a class="dropdown-item" href="#adminNote">พื้นที่ผู้ดูแล (Admin)</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><button class="dropdown-item" id="themeToggle" type="button"><i class="bi bi-moon-stars me-2"></i>สลับโหมดสว่าง/มืด</button></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
<?php
if($page=="l1"){
    include "page/exercises1.php";
}else{
    include "page/from_gpt.php";
}


?>
  
  <!-- Footer -->
  <footer class="py-4 border-top">
    <div class="container d-flex flex-column flex-lg-row align-items-center justify-content-between gap-3">
      <div class="small">© <span id="year"></span> LearnX — ระบบเรียนออนไลน์</div>
      <ul class="nav small">
        <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">นโยบายความเป็นส่วนตัว</a></li>
        <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">ข้อกำหนดการใช้บริการ</a></li>
        <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">ติดต่อเรา</a></li>
      </ul>
    </div>
  </footer>

  <!-- Video Modal -->
  <div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">ตัวอย่างวิดีโอคอร์ส</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="ratio ratio-16x9">
            <video id="previewVideo" controls poster="https://images.unsplash.com/photo-1555066931-4365d14bab8c?q=80&w=1200&auto=format&fit=crop">
              <source src="https://cdn.coverr.co/videos/coverr-working-on-a-computer-1940/1080p.mp4" type="video/mp4">
              เบราว์เซอร์ของคุณไม่รองรับวิดีโอ HTML5
            </video>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quiz Modal (ตัวอย่างง่าย ๆ) -->
  <div class="modal fade" id="quizModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Quiz 1: Bootstrap Grid</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="mb-2">คำถาม: Bootstrap ใช้ class ใดสำหรับคอลัมน์พื้นฐาน?</p>
          <div class="list-group" id="quizChoices">
            <button class="list-group-item list-group-item-action">.grid-col</button>
            <button class="list-group-item list-group-item-action">.bs-col</button>
            <button class="list-group-item list-group-item-action">.column</button>
            <button class="list-group-item list-group-item-action">.col</button>
          </div>
          <div id="quizFeedback" class="mt-3 small"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Auth Modal -->
  <div class="modal fade" id="authModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">เข้าสู่ระบบ / สมัครสมาชิก</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="login-tab" data-bs-toggle="pill" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="true">เข้าสู่ระบบ</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="register-tab" data-bs-toggle="pill" data-bs-target="#register" type="button" role="tab" aria-controls="register" aria-selected="false">สมัครสมาชิก</button>
            </li>
          </ul>
          <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab" tabindex="0">
<form method="post" action="page/chkmember.php";>
            <div class="vstack gap-3">
                <input type="email" class="form-control" placeholder="อีเมล" name="user_reg">
                <input type="password" class="form-control" placeholder="รหัสผ่าน" name="pass_reg">
                <button class="btn btn-primary">เข้าสู่ระบบ</button>
              </div>
</form>
            </div>
            <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab" tabindex="0">
              <div class="vstack gap-3">
                <input type="text" class="form-control" placeholder="ชื่อ-สกุล">
                <input type="email" class="form-control" placeholder="อีเมล">
                <input type="password" class="form-control" placeholder="รหัสผ่าน">
                <button class="btn btn-success">สมัครสมาชิก</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // ปรับปีลิขสิทธิ์อัตโนมัติ
    document.getElementById('year').textContent = new Date().getFullYear();

    // สลับโหมดสว่าง/มืด
    const toggleBtn = document.getElementById('themeToggle');
    toggleBtn?.addEventListener('click', () => {
      const html = document.documentElement;
      const current = html.getAttribute('data-bs-theme') || 'light';
      const next = current === 'light' ? 'dark' : 'light';
      html.setAttribute('data-bs-theme', next);
      localStorage.setItem('theme', next);
    });
    // โหลดค่า theme จาก localStorage
    (function(){
      const saved = localStorage.getItem('theme');
      if(saved) document.documentElement.setAttribute('data-bs-theme', saved);
    })();

    // รีเซ็ตเฉพาะวิดีโอ (แต่ยังจำคะแนน/สถานะอื่น ๆ ได้)
    function resetOnlyVideo(){
      const iframe = document.querySelector('#courseDetail iframe');
      if(iframe){
        const src = iframe.src;
        iframe.src = src; // reload เฉพาะ iframe
      }
      const preview = document.getElementById('previewVideo');
      if(preview){
        preview.pause();
        preview.currentTime = 0;
      }
    }

    // Quiz แบบง่าย
    const quizChoices = document.getElementById('quizChoices');
    const quizFeedback = document.getElementById('quizFeedback');
    if(quizChoices){
      quizChoices.addEventListener('click', (e) => {
        if(e.target.matches('button')){
          const text = e.target.textContent.trim();
          if(text === '.col'){
            quizFeedback.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i>ถูกต้อง! Class พื้นฐานคือ <code>.col</code></span>';
          } else {
            quizFeedback.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>ยังไม่ถูก ลองทบทวนเรื่อง Grid ดูอีกครั้ง</span>';
          }
        }
      });
    }
  </script>
</body>
</html>
