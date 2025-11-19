<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (empty($_SESSION['sess_userid']) || (int)$_SESSION['sess_userid'] <= 0) {
  // กันโหมด override (-1) และผู้ที่ยังไม่ล็อกอิน
  echo '<div class="alert alert-warning">กรุณาล็อกอินก่อนใช้งาน</div>';
  return;
}
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];
?>
<section class="mt-3">
  <div class="card">
    <div class="card-header bg-white"><i class="bi bi-shield-lock me-1"></i> เปลี่ยนรหัสผ่าน</div>
    <div class="card-body">
      <form id="pwForm" class="row g-3" autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf,ENT_QUOTES,'UTF-8') ?>">
        <div class="col-12 col-md-6">
          <label class="form-label">รหัสผ่านเดิม</label>
          <div class="input-group">
            <input type="password" class="form-control" name="current_password" required>
            <button class="btn btn-outline-secondary toggle" type="button" aria-label="แสดง/ซ่อน"><i class="bi bi-eye"></i></button>
          </div>
        </div>
        <div class="col-12 col-md-6"></div>

        <div class="col-12 col-md-6">
          <label class="form-label">รหัสผ่านใหม่</label>
          <div class="input-group">
            <input id="newpw" type="password" class="form-control" name="new_password" required
                   minlength="8" pattern="(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d\S]{8,}">
            <button class="btn btn-outline-secondary toggle" type="button"><i class="bi bi-eye"></i></button>
          </div>
          <div class="form-text">อย่างน้อย 8 ตัวอักษร และมีทั้งตัวอักษรกับตัวเลข</div>
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
          <div class="input-group">
            <input id="confpw" type="password" class="form-control" required>
            <button class="btn btn-outline-secondary toggle" type="button"><i class="bi bi-eye"></i></button>
          </div>
          <div id="matchHelp" class="form-text"></div>
        </div>

        <div class="col-12 d-flex gap-2">
          <button class="btn btn-primary" type="submit"><i class="bi bi-arrow-repeat me-1"></i> เปลี่ยนรหัสผ่าน</button>
          <span id="pwStatus" class="align-self-center small text-muted"></span>
        </div>
      </form>
    </div>
  </div>
</section>

<script>
(() => {
  // toggle show/hide
  document.querySelectorAll('#pwForm .toggle').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const input = btn.parentElement.querySelector('input');
      input.type = input.type === 'password' ? 'text' : 'password';
      btn.querySelector('i').classList.toggle('bi-eye');
      btn.querySelector('i').classList.toggle('bi-eye-slash');
    });
  });

  // confirm match helper
  const newpw = document.getElementById('newpw');
  const conf  = document.getElementById('confpw');
  const help  = document.getElementById('matchHelp');
  function checkMatch(){
    if (!conf.value) { help.textContent=''; return; }
    if (conf.value !== newpw.value) {
      help.textContent = 'รหัสผ่านยืนยันไม่ตรงกัน';
      help.className = 'form-text text-danger';
    } else {
      help.textContent = 'รหัสผ่านตรงกัน';
      help.className = 'form-text text-success';
    }
  }
  newpw.addEventListener('input', checkMatch);
  conf.addEventListener('input', checkMatch);

  // submit
  const form = document.getElementById('pwForm');
  const statusEl = document.getElementById('pwStatus');
  form.addEventListener('submit', async (e)=>{
    e.preventDefault();
    if (conf.value !== newpw.value) { checkMatch(); conf.focus(); return; }

    statusEl.textContent = 'กำลังบันทึก…';
    const fd = new FormData(form);
    try{
      const resp = await fetch('module/member/change_password_api.php', { method:'POST', body: fd });
      const ct = resp.headers.get('content-type')||'';
      const data = ct.includes('application/json') ? await resp.json() : { ok:false, message:'Invalid response' };
      if(!resp.ok || !data.ok) throw new Error(data.message || 'เปลี่ยนรหัสผ่านไม่สำเร็จ');
      statusEl.textContent = 'เปลี่ยนรหัสผ่านสำเร็จ';
      form.reset(); checkMatch();
    }catch(err){
      statusEl.textContent = 'ผิดพลาด: ' + err.message;
      console.error(err);
    }
  });
})();
</script>
