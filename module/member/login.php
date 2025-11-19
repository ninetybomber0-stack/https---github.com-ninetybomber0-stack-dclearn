<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>เข้าสู่ระบบ | e‑Learning วิชาการสื่อสารข้อมูลและเครือข่ายคอมพิวเตอร์</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    :root{--brand:#0d6efd}
    body{
      min-height:100vh;display:flex;align-items:center;justify-content:center;
      background:
        radial-gradient(1200px 700px at 10% 20%, rgba(13,110,253,.08), transparent 60%),
        radial-gradient(1000px 600px at 90% 10%, rgba(25,135,84,.08), transparent 60%),
        linear-gradient(180deg,#f8fbff 0%, #f6f7fb 100%);
    }
    .login-card{border:0;border-radius:1.25rem;box-shadow:0 12px 44px rgba(0,0,0,.08);overflow:hidden;max-width:880px}
    .brand-top{background:linear-gradient(135deg,var(--brand),#3aa9ff);color:#fff}
    .brand-top .logo{width:72px;height:72px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,.8)}
    .btn-brand{background:var(--brand);color:#fff}
    .btn-brand:hover{background:#0b5ed7;color:#fff}
    .form-floating>.form-control:focus~label{color:var(--brand)}
    .small-link{color:#6c757d}
  </style>
  <!--<base href="/dclearn/module/member/">-->
</head>
<body>
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-10">
        <div class="card login-card mx-auto">
          <!-- แถบชื่อรายวิชา + โลโก้ -->
          <div class="brand-top p-4 text-center">
            <img class="logo mb-2" src="../../img/logo-elearn.png" alt="โลโก้ e‑Learning" onerror="this.style.display='none'">
            <div class="fw-bold">ระบบบทเรียนออนไลน์ (e‑Learning)</div>
            <div class="small opacity-75">วิชา: การสื่อสารข้อมูลและเครือข่ายคอมพิวเตอร์</div>
          </div>

          <div class="row g-0">
            <div class="col-md-6 p-4 p-md-5 order-2 order-md-1">
              <h1 class="h4 mb-3">เข้าสู่ระบบ</h1>
              <form id="loginForm" onsubmit="return handleLogin(event)" novalidate>
                <div class="form-floating mb-3">
                  <input type="text" class="form-control" id="username" placeholder="อีเมล/รหัสนักศึกษา" required autofocus>
                  <label for="username"><i class="bi bi-person me-1"></i> รหัสนักศึกษา</label>
                  <div class="invalid-feedback">กรอกรหัสนักศึกษา</div>
                </div>
                <div class="form-floating mb-3 position-relative">
                  <input type="password" class="form-control" id="password" placeholder="รหัสผ่าน" required>
                  <label for="password"><i class="bi bi-key me-1"></i> รหัสผ่าน</label>
                  <button type="button" class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2" style="z-index:5" onclick="togglePw()" aria-label="แสดง/ซ่อนรหัสผ่าน"><i id="pwIcon" class="bi bi-eye"></i></button>
                  <div class="invalid-feedback">กรอกรหัสผ่าน</div>
                </div>

                <div class="row align-items-center mb-3 g-2">
                  <div class="col-7">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="remember">
                      <label class="form-check-label" for="remember">จดจำฉันไว้</label>
                    </div>
                  </div>
                  <div class="col-5 text-end">
                    <a href="#" class="small-link text-decoration-none" onclick="alert('ติดต่อผู้ดูแลระบบเพื่อรีเซ็ตรหัสผ่าน');return false;">ลืมรหัสผ่าน?</a>
                  </div>
                </div>

                <div class="d-grid gap-2">
                  <button class="btn btn-brand btn-lg" id="submitBtn" type="submit">
                    <span class="btn-text"><i class="bi bi-box-arrow-in-right me-1"></i> เข้าสู่ระบบ</span>
                    <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>กำลังตรวจสอบ...</span>
                  </button>
                </div>

                <div class="alert alert-danger d-none mt-3" id="errBox">
                  <i class="bi bi-exclamation-triangle me-1"></i> ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง
                </div>
              </form>
            </div>

            <div class="col-md-6 p-4 p-md-5 order-1 order-md-2 d-flex align-items-center justify-content-center">
              <div class="text-center text-md-start">
                <div class="mb-2 fw-semibold text-secondary">ยินดีต้อนรับสู่คลาสออนไลน์</div>
                <ul class="list-unstyled small text-secondary mb-0">
                  <li class="mb-1"><i class="bi bi-check-circle me-1 text-success"></i> ดูวิดีโอพร้อมแบบทดสอบระหว่างเรียน</li>
                  <li class="mb-1"><i class="bi bi-check-circle me-1 text-success"></i> ดาวน์โหลดสไลด์/เอกสารประกอบ</li>
                  <li class="mb-1"><i class="bi bi-check-circle me-1 text-success"></i> ติดตามความก้าวหน้าได้แบบเรียลไทม์</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="text-center mt-3 small text-muted">© <span id="year"></span> e‑Learning วิชาการสื่อสารข้อมูลและเครือข่ายคอมพิวเตอร์</div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  /* ใช้พาธ absolute ให้ตรงโฟลเดอร์จริง */
  const API_URL = '/dclearn/module/member/login_API.php';
  const AFTER_LOGIN_URL = '/dclearn/index.php';

  async function safeJson(res){ try{ return await res.json(); }catch(_){ return {}; } }

  async function handleLogin(e){
    e.preventDefault();                        // กันรีเฟรชหน้า
    const form   = document.getElementById('loginForm');
    const btn    = document.getElementById('submitBtn');
    const errBox = document.getElementById('errBox');

    form.classList.add('was-validated');
    if (!form.checkValidity()) return false;

    errBox.classList.add('d-none');
    btn.disabled = true;
    btn.querySelector('.btn-text').classList.add('d-none');
    btn.querySelector('.btn-loading').classList.remove('d-none');

    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;

    console.log('API will call:', new URL(API_URL, document.baseURI).href);

    try{
      const res  = await fetch(API_URL, {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        credentials:'same-origin',
        body: JSON.stringify({ username, password })
      });
      const data = await safeJson(res);
      console.log('API response:', res.status, data);

      if (!res.ok){
        errBox.innerHTML = `<i class="bi bi-x-octagon me-1"></i> เรียก API ไม่สำเร็จ (HTTP ${res.status})<br><small>${new URL(API_URL, document.baseURI).href}</small>`;
        errBox.classList.remove('d-none');
        return false;
      }

      if (data && data.ok){
        if (document.getElementById('remember').checked){
          localStorage.setItem('elearn_username', username);
        }else{
          localStorage.removeItem('elearn_username');
        }
        // ใช้ absolute URL กันผลของ <base>
        const to = data.redirect || AFTER_LOGIN_URL;
        window.location.replace(new URL(to, window.location.origin).href);
        return false;
      }

      const msg = (data && data.message) ? data.message : 'เข้าสู่ระบบไม่สำเร็จ';
      errBox.innerHTML = `<i class="bi bi-exclamation-triangle me-1"></i> ${msg}`;
      errBox.classList.remove('d-none');
    }catch(err){
      console.error(err);
      errBox.innerHTML = `<i class="bi bi-wifi-off me-1"></i> ไม่สามารถติดต่อเซิร์ฟเวอร์ได้`;
      errBox.classList.remove('d-none');
    }finally{
      btn.disabled = false;
      btn.querySelector('.btn-text').classList.remove('d-none');
      btn.querySelector('.btn-loading').classList.add('d-none');
    }
    return false; // กัน submit ปกติซ้ำ
  }
</script>


</body>
</html>
