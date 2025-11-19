<?php

// ------- ดึงวิดีโอทั้งหมดสำหรับเพลย์ลิสต์ -------
$videos = [];
$vres = $mysqli->query("SELECT * FROM exercises1_video ORDER BY id DESC");
if ($vres) {
  while ($r = $vres->fetch_assoc()) $videos[] = $r;
}

/* วิดีโอล่าสุดเป็นค่าเริ่มต้น
$firstVideo = $videos[0] ?? null;
$video_path = "videos/" . ($firstVideo ? htmlspecialchars($firstVideo['filename']) : 'default.mp4');
*/

// สมมติว่ามีตัวแปร $mysqli อยู่แล้วจาก connect.php

// รับค่า chapter_id จาก GET/POST (ถ้าไม่มี ให้เป็น 0 เพื่อโหลดวิดีโอล่าสุด)
$chapter_id = isset($_GET['idc']) ? (int)$_GET['idc'] : 0;

// เตรียมค่าเริ่มต้น
$firstVideo = null;

// ถ้าระบุ chapter_id มา ให้ดึงวิดีโอของบทนั้น
if ($chapter_id > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM exercises1_video WHERE id_c = ? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("i", $chapter_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $firstVideo = $res->fetch_assoc() ?: null;
    $stmt->close();
}

// ถ้าไม่เจอวิดีโอจาก chapter_id (หรือไม่ระบุ chapter_id) ให้ดึงวิดีโอล่าสุดมาแสดง
if (!$firstVideo) {
    $vres = $mysqli->query("SELECT * FROM exercises1_video ORDER BY id DESC LIMIT 1");
    if ($vres) {
        $firstVideo = $vres->fetch_assoc() ?: null;
        // อัปเดต chapter_id ให้ตรงกับวิดีโอที่โหลดมา
        if ($firstVideo) {
            $chapter_id = $firstVideo['id_c'];
        }
    }
}

// สร้าง path ของไฟล์วิดีโอ
$video_path = "videos/" . ($firstVideo ? htmlspecialchars($firstVideo['filename']) : 'default.mp4');

// ------- ดึงคำถามสำหรับบทเรียนปัจจุบันเท่านั้น -------
$questions = [];
if ($chapter_id > 0 && $firstVideo) {
    // ใช้ chapter_id ที่ถูกต้องในการดึงคำถาม
    $stmt = $mysqli->prepare("SELECT * FROM exercises1_questions WHERE id_c = ? ORDER BY timecode ASC");
    $stmt->bind_param("i", $chapter_id);
    $stmt->execute();
    $qres = $stmt->get_result();
    if ($qres) {
        while ($row = $qres->fetch_assoc()) {
            $questions[] = $row;
        }
    }
    $stmt->close();
}
?>

<style>
  :root{
    --bg:#f6f7fb; --card:#ffffff; --muted:#6b7280;
    --primary:#2563eb; --primary-weak:#e7f1ff;
    --radius:14px; --shadow:0 10px 30px rgba(0,0,0,.06);
    --shadow-sm:0 6px 18px rgba(0,0,0,.05);
  }
  body{background:var(--bg);font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;margin:24px;color:#0f172a}
  h1{margin:0 0 4px} p{margin:0 0 16px;color:var(--muted)}

  .player-wrapper{display:flex;gap:16px;align-items:flex-start}
  .video-pane{position:relative;flex:2 1 640px}
  .video-pane video{width:100%;max-width:960px;border-radius:16px;box-shadow:var(--shadow)}

  #question-box{
    display:none;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
    background:rgba(0,0,0,.85);color:#fff;padding:20px;border-radius:12px;width:80%;max-width:420px;
    text-align:left;z-index:5;box-shadow:0 4px 15px rgba(0,0,0,.5)
  }
  #question-box button{background:#22c55e;border:none;color:#fff;padding:10px 14px;border-radius:10px;cursor:pointer}
  #question-box button:hover{filter:brightness(.95)}

  .playlist{
    flex:1 1 300px;background:var(--card);border-radius:var(--radius);box-shadow:var(--shadow-sm);padding:10px;
    max-height:560px;overflow:auto
  }
  .playlist-header{display:flex;justify-content:space-between;align-items:center;padding:6px 10px 10px}
  .playlist-header h3{margin:0;font-size:18px}
  .playlist-list{list-style:none;padding:0;margin:0}
  .playlist-item{
    display:flex;gap:10px;align-items:center;padding:10px;border-radius:12px;cursor:pointer;
    transition:transform .05s ease, background .2s ease
  }
  .playlist-item:hover{background:#f3f4f6}
  .playlist-item.active{background:var(--primary-weak)}
  .thumb{
    flex:0 0 72px;height:42px;border-radius:8px;background:#000;
    display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px
  }
  .meta{min-width:0}
  .title{font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .sub{font-size:12px;color:var(--muted)}
  .badge{font-size:11px;background:#eef2ff;color:#3730a3;border-radius:999px;padding:2px 8px;margin-left:6px}

  #finished-box{
    display:none;margin-top:16px;border:1px solid #4CAF50;padding:15px;background-color:#f0fff0;border-radius:12px
  }
  @media (max-width: 980px){ .player-wrapper{flex-direction:column} }

  /* อนุญาตให้ย่อใน flex ได้จริง */
.video-pane,
.playlist { min-width: 0; }

/* ถ้าพื้นที่ไม่พอ ให้ wrap ได้ (กันล้นแนวนอน) */
.player-wrapper { flex-wrap: wrap; }

/* มือถือ/จอแคบ: สลับเป็นแนวตั้ง และให้เพลย์ลิสต์เต็มความกว้าง */
@media (max-width: 980px) {
  .player-wrapper { flex-direction: column; }
  .playlist { width: 100%; max-height: none; } /* ยกเลิกเพดานความสูงตอนเรียงใต้กัน */
}
/* ให้ย่อได้จริงใน flex */
.video-pane, .playlist { min-width: 0; }

/* หน้าจอเล็ก: จัดเป็นคอลัมน์ และยกเลิก flex-basis 640px */
@media (max-width: 980px){
  .player-wrapper { flex-direction: column; }
  .video-pane { flex: 0 1 auto; }   /* <-- ตัด 640px ออก ให้สูงเท่าคอนเทนต์ */
  .playlist   { width: 100%; max-height: none; margin-top: 12px; }
  .video-pane video { display:block; width:100%; height:auto; } /* กันวิดีโอเป็น inline แล้วมีช่องว่าง */
}

</style>


<h1>แบบฝึกหัดบทที่ <?=$chapter_id?></h1>
<p>ชมวิดีโอและตอบคำถามระหว่างเล่น</p>

<div class="player-wrapper">
  <!-- วิดีโอ + คำถาม -->
  <div class="video-pane" id="video-pane">
    <video id="video" controls>
      <source id="video-source" src="<?= $video_path ?>" type="video/mp4">
      เบราว์เซอร์ของคุณไม่รองรับวิดีโอ
    </video>

    <!-- ป๊อบอัปคำถาม -->
    <div id="question-box">
      <h3 id="question-text"></h3>
      <form id="answer-form">
        <div><input type="radio" name="answer" id="ans1" value="1"> <label for="ans1" id="opt1"></label></div>
        <div><input type="radio" name="answer" id="ans2" value="2"> <label for="ans2" id="opt2"></label></div>
        <div><input type="radio" name="answer" id="ans3" value="3"> <label for="ans3" id="opt3"></label></div>
        <div><input type="radio" name="answer" id="ans4" value="4"> <label for="ans4" id="opt4"></label></div>
        <br>
        <button type="button" id="submit-answer">ส่งคำตอบ</button>
      </form>
    </div>

    <!-- กล่องคะแนน -->
    <div id="finished-box">
      <h2>ทำแบบทดสอบเสร็จแล้ว!</h2>
      <p>คุณได้คะแนน: <span id="final-score">0</span> / <?= count($questions) ?></p>
      <!-- จะบันทึกอัตโนมัติ แต่คงปุ่มไว้เป็น fallback -->
      <form id="score-form" method="post" action="page/save_score.php">
        <input type="hidden" name="student_id" value="<?=$id_std?>">
        <input type="hidden" name="lesson_id" value="<?=$chapter_id?>">
        <input type="hidden" id="score_to_save" name="score" value="">
        <input type="hidden" name="total_questions" value="<?= count($questions) ?>">
        <button type="submit" style="display:none">บันทึกคะแนน</button>
      </form>
      <p id="save-status"></p>
    </div>
  </div>

  <!-- เพลย์ลิสต์ -->
  <aside class="playlist" id="playlist">
    <div class="playlist-header">
      <h3>เพลย์ลิสต์</h3>
      <span class="badge"><?= count($videos) ?> วิดีโอ</span>
    </div>
    <ul class="playlist-list" id="playlist-list">
      <?php if (count($videos) === 0): ?>
        <li class="playlist-item"><div class="meta"><div class="title">ยังไม่มีวิดีโอ</div></div></li>
      <?php else: ?>
        <?php foreach ($videos as $i => $v):
          $fname = htmlspecialchars($v['filename']);
          $title = htmlspecialchars(($v['title'] ?? '') ?: pathinfo($v['filename'], PATHINFO_FILENAME));
          $dur   = htmlspecialchars($v['duration'] ?? '');
          $active = $i === 0 ? 'active' : '';
        ?>
        <li class="playlist-item <?= $active ?>" data-index="<?= $i ?>" data-src="videos/<?= $fname ?>">
          <div class="thumb">VID</div>
          <div class="meta">
            <div class="title"><?= $title ?></div>
            <div class="sub"><?= $fname ?> <?= $dur ? "• $dur" : "" ?></div>
          </div>
        </li>
        <?php endforeach; ?>
      <?php endif; ?>
    </ul>
  </aside>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {

  /* ---------- ยูทิล: แปลง timecode → วินาที (รองรับ hh:mm:ss / mm:ss / ss) ---------- */
  function toSec(tc) {
    if (tc == null) return Infinity;
    if (typeof tc === 'number') return tc;
    const s = String(tc).trim();
    if (!s.includes(':')) return Number(s) || 0;
    const parts = s.split(':').map(Number);
    if (parts.some(n => Number.isNaN(n))) return 0;
    return parts.reduce((acc, n) => acc * 60 + n, 0);
  }

  /* ---------- ตัวแปรสถานะ ---------- */
  const questions = <?= json_encode($questions, JSON_UNESCAPED_UNICODE); ?>;
  const chapterId = <?= json_encode($chapter_id); ?>;
  console.log('Chapter ID:', chapterId);
  console.log('Loaded Questions:', questions);
  let currentQ = 0;
  let score = 0;
  let scoreSaved = false; // กันบันทึกซ้ำ
  let answerTimeout;

  /* ---------- อ้างอิง DOM ---------- */
  const video = document.getElementById('video');
  const questionBox = document.getElementById('question-box');
  const playlistEl = document.getElementById('playlist-list');

  // เริ่มเล่นวิดีโออัตโนมัติเมื่อหน้าพร้อม
  if (video) {
    video.play();
  }


  /* ---------- In-Video Question Logic ---------- */
  video.addEventListener('timeupdate', function () {
    if (currentQ >= questions.length) return;
    const nextQuestionTime = toSec(questions[currentQ].timecode);
    if (video.currentTime >= nextQuestionTime && getComputedStyle(questionBox).display === 'none') {
      video.pause();
      showInVideoQuestion(questions[currentQ]);
    }
  });

  function showInVideoQuestion(q) {
    document.querySelectorAll('input[name="answer"]').forEach(r => r.checked = false);
    questionBox.style.display = 'block';
    document.getElementById('question-text').innerText = q.question ?? '';
    document.getElementById('opt1').innerText = q.option1 ?? '';
    document.getElementById('opt2').innerText = q.option2 ?? '';
    document.getElementById('opt3').innerText = q.option3 ?? '';
    document.getElementById('opt4').innerText = q.option4 ?? '';

    clearTimeout(answerTimeout);
    answerTimeout = setTimeout(() => {
      alert("หมดเวลา! ระบบจะทำการเริ่มบทเรียนนี้ใหม่ทั้งหมด");
      location.reload();
    }, 15000);
  }

  document.getElementById('submit-answer').addEventListener('click', function() {
    const selected = document.querySelector('input[name="answer"]:checked');
    if (selected) {
      clearTimeout(answerTimeout);
      if (Number(selected.value) === Number(questions[currentQ].answer)) score++;
      currentQ++;
      questionBox.style.display = 'none';
      selected.checked = false;
      video.play();
    } else {
      alert("กรุณาเลือกคำตอบก่อนส่ง");
    }
  });

  /* ---------- Video End Logic ---------- */
  video.addEventListener('ended', function () {
    document.getElementById('finished-box').style.display = 'block';
    document.getElementById('final-score').innerText = score;
    document.getElementById('score_to_save').value = score;

    const btn = document.querySelector('#score-form button[type="submit"]');
    if (btn) btn.style.display = 'none';

    saveScore('ended');

  });

  /* ---------- Score Saving Logic ---------- */
  function saveScore(reason = 'auto') {
    if (scoreSaved) return;

    const form = document.getElementById('score-form');
    const statusP = document.getElementById('save-status');
    const btn = form.querySelector('button[type="submit"]');

    statusP.innerText = 'กำลังบันทึก...';
    statusP.style.color = '';
    document.getElementById('score_to_save').value = String(score);
    const formData = new FormData(form);
    formData.set('score', String(score));
    formData.append('save_reason', reason);

    fetch(form.action, { method: 'POST', body: formData })
      .then(async (resp) => {
        const text = await resp.text();
        if (!resp.ok) throw new Error(text || ('HTTP ' + resp.status));
        return text;
      })
      .then((text) => {
        scoreSaved = true;
        statusP.innerText = text || 'บันทึกคะแนนสำเร็จ';
        statusP.style.color = 'green';
        if (btn) btn.style.display = 'none';
      })
      .catch((err) => {
        statusP.innerText = 'บันทึกไม่สำเร็จ: ' + err.message;
        statusP.style.color = 'red';
        if (btn) btn.style.display = 'inline-block';
      });
  }

  document.getElementById('score-form').addEventListener('submit', function (e) {
    e.preventDefault();
    saveScore('manual');
  });

  /* ---------- Playlist Logic ---------- */
  playlistEl.addEventListener('click', (e) => {
    const li = e.target.closest('.playlist-item');
    if (!li || li.classList.contains('active')) return;

    const src = li.getAttribute('data-src');
    if (!src) return;

    video.pause();
    document.getElementById('finished-box').style.display = 'none';
    questionBox.style.display = 'none';

    // Reset quiz state for the new video
    currentQ = 0;
    score = 0;
    scoreSaved = false;
    clearTimeout(answerTimeout);

    video.querySelector('source').src = src;
    video.load();
    
    // เล่นวิดีโอใหม่ทันที
    video.play();

    document.querySelectorAll('.playlist-item').forEach(el => el.classList.remove('active'));
    li.classList.add('active');
    li.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
  });

});
</script>
