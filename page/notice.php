<?php
// ดึงรายการประกาศ (ล่าสุดก่อน)
$sql = "SELECT id, message, `time` FROM tb_notice ORDER BY `time` DESC LIMIT 100";
$res = $mysqli->query($sql);
$notices = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

// ฟังก์ชันบอกว่าเป็นประกาศ "ใหม่" ภายในกี่วัน
function is_new($timestamp, $days = 7) {
  $t = strtotime($timestamp);
  return $t >= strtotime("-{$days} days");
}
?>

  <!-- Bootstrap 5 -->
  <style>
    .notice-card .card-header {
      display:flex; align-items:center; gap:.5rem;
      font-weight:600;
    }
    .badge-new { background:#16a34a; } /* เขียว */
    .notice-item { padding:.6rem 0; border-bottom:1px solid #eef1f4; }
    .notice-item:last-child { border-bottom:0; }
    .notice-time { color:#6b7280; font-size:.9rem; }
    .notice-text { white-space:pre-wrap; }
  </style>

<div class="container py-4">
<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$sess_username = $_SESSION['sess_username'] ?? null;  // อ่านจาก SESSION
$canDelete = ($sess_username === 'kamol');
?>
<!-- debug: sess_username=<?=htmlspecialchars($sess_username ?? 'NULL', ENT_QUOTES, 'UTF-8')?>, canDelete=<?=$canDelete?'1':'0'?> -->

<!-- กล่องประกาศ -->
<div class="card shadow-sm notice-card mb-4">
  <div class="card-header">
    <span class="me-2">📢</span> ประกาศ
  </div>

  <div class="card-body" id="annBody"><!-- ใส่ id ไว้อ้างอิง -->
    <?php if (empty($notices)): ?>
      <div class="text-muted">ยังไม่มีประกาศ</div>
    <?php else: ?>
      <?php foreach ($notices as $n): ?>
        <div class="notice-item d-flex justify-content-between align-items-start" data-id="<?= (int)$n['id'] ?>">
          <div class="me-2">
            <?php if (is_new($n['time'])): ?>
              <span class="badge badge-new me-2">ใหม่</span>
            <?php endif; ?>
            <span class="notice-text">
              <?= nl2br(htmlspecialchars($n['message'], ENT_QUOTES, 'UTF-8')) ?>
            </span>
            <div class="notice-time">
              <?= (new DateTime($n['time']))->format('d M Y H:i') ?>
            </div>
          </div>

          <?php if ($canDelete): ?>
            <button type="button"
                    class="btn btn-sm btn-outline-danger del-notice"
                    data-id="<?= (int)$n['id'] ?>"
                    title="ลบประกาศ">
              <i class="bi bi-trash"></i>
            </button>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php if ($canDelete): ?>
  <!-- ฟอร์มเพิ่มประกาศ (แสดงเฉพาะ user: kamol) -->
  <div class="card shadow-sm mt-3">
    <div class="card-header bg-white">
      <i class="bi bi-plus-circle me-1"></i> เพิ่มข้อความประกาศ
    </div>
    <div class="card-body">
      <form id="notice-form" class="mb-2" autocomplete="off">
        <div class="mb-2">
          <label for="message" class="form-label">ข้อความประกาศ</label>
          <textarea class="form-control" name="message" id="message" rows="3"
                    maxlength="1000" placeholder="พิมพ์ข้อความประกาศ…" required></textarea>
          <div class="form-text"><span id="char-count">0</span>/1000</div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button type="submit" class="btn btn-primary">บันทึกประกาศ</button>
          <span id="save-status" class="small text-muted"></span>
        </div>
      </form>
      <div class="small text-muted">
        * ระบบจะบันทึกเวลาอัตโนมัติ (timestamp) — ข้อความใหม่ในช่วง 7 วันจะแสดงป้าย “ใหม่”
      </div>
    </div>
  </div>
<?php endif; ?>

<script>
// ตัวนับตัวอักษร (ถ้ามีฟอร์ม)
const ta = document.getElementById('message');
const cc = document.getElementById('char-count');
if (ta && cc) ta.addEventListener('input', () => cc.textContent = ta.value.length);

(function () {
  const form      = document.getElementById('notice-form');
  const list      = document.getElementById('annList') || document.getElementById('annBody') || document.querySelector('.notice-card .card-body');
  const charCount = document.getElementById('char-count');
  const statusEl  = document.getElementById('save-status');

  // บอกสิทธิ์ลบให้ JS รู้ (ฝังจาก PHP)
  const CAN_DELETE = <?php echo $canDelete ? 'true' : 'false'; ?>;

  // --- เพิ่มประกาศแบบ AJAX ---
  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      if (statusEl) statusEl.textContent = 'กำลังบันทึก…';

      try {
        const fd   = new FormData(form);
        const resp = await fetch('page/notice_save.php', { method: 'POST', body: fd });

        const ct = resp.headers.get('content-type') || '';
        if (!ct.includes('application/json')) {
          const text = await resp.text();
          throw new Error(`Unexpected response (${resp.status}): ${ct} — ${text.slice(0,120)}…`);
        }

        const data = await resp.json();
        if (!resp.ok || !data.success) throw new Error(data.error || 'save failed');

        prependNoticeItem(data.row);
        form.reset();
        if (charCount) charCount.textContent = '0';
        if (statusEl) statusEl.textContent = 'บันทึกสำเร็จ';
      } catch (err) {
        if (statusEl) statusEl.textContent = 'บันทึกไม่สำเร็จ: ' + err.message;
        console.error(err);
      }
    });
  }

  // --- ลบประกาศ (เฉพาะสิทธิ์) ---
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.del-notice');
    if (!btn) return;

    const id  = btn.getAttribute('data-id');
    if (!id) return;

    if (!confirm('ยืนยันการลบประกาศนี้?')) return;

    try {
      const resp = await fetch('page/notice_delete.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
        body: new URLSearchParams({ id })
      });

      const ct = resp.headers.get('content-type') || '';
      const data = ct.includes('application/json') ? await resp.json() : { success:false, error:'Invalid response' };

      if (!resp.ok || !data.success) throw new Error(data.error || 'delete failed');

      // ลบ DOM
      const item = document.querySelector(`.notice-item[data-id="${id}"]`);
      if (item) item.remove();

      // ถ้าไม่มีประกาศเหลือ แสดงข้อความว่าง
      if (list && !list.querySelector('.notice-item')) {
        const empty = document.createElement('div');
        empty.className = 'text-muted';
        empty.textContent = 'ยังไม่มีประกาศ';
        list.appendChild(empty);
      }
    } catch (err) {
      alert('ลบไม่สำเร็จ: ' + err.message);
      console.error(err);
    }
  });

  // --- สร้าง DOM ของประกาศใหม่ให้เข้ากับ notice-card ---
  function prependNoticeItem(row) {
    if (!list) return;

    // ลบ “ยังไม่มีประกาศ” ถ้ามี
    const empty = list.querySelector('.text-muted');
    if (empty) empty.remove();

    const wrap = document.createElement('div');
    wrap.className = 'notice-item d-flex justify-content-between align-items-start';
    wrap.setAttribute('data-id', String(row.id || ''));

    const timeStr = new Date(String(row.time || '').replace(' ', 'T'))
      .toLocaleString('th-TH', { year:'numeric', month:'short', day:'2-digit', hour:'2-digit', minute:'2-digit' });

    // ด้านซ้าย: badge + ข้อความ + เวลา
    const left = document.createElement('div');
    left.className = 'me-2';

    const badge = document.createElement('span');
    badge.className = 'badge badge-new me-2';
    badge.textContent = 'ใหม่';

    const text = document.createElement('span');
    text.className = 'notice-text';
    text.textContent = String(row.message || '');

    const time = document.createElement('div');
    time.className = 'notice-time';
    time.textContent = timeStr;

    left.append(badge, text, time);
    wrap.appendChild(left);

    // ด้านขวา: ปุ่มลบ (ถ้ามีสิทธิ์)
    if (CAN_DELETE) {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'btn btn-sm btn-outline-danger del-notice';
      btn.setAttribute('data-id', String(row.id || ''));
      btn.title = 'ลบประกาศ';
      btn.innerHTML = '<i class="bi bi-trash"></i>';
      wrap.appendChild(btn);
    }

    list.prepend(wrap);
  }
})();
</script>

