<?php
// 1. เชื่อมต่อฐานข้อมูล
require_once __DIR__ . '/../config/connect.php';

// --- REFACTORED DATA FETCHING ---
function toSeconds(string $timeStr): int {
    if (strpos($timeStr, ':') === false) return (int)$timeStr;
    $parts = explode(':', $timeStr);
    return count($parts) === 2 ? ((int)$parts[0] * 60) + (int)$parts[1] : 0;
}

function resolveVideoPath($path) {
    $filename = basename($path ?? '');
    // Check if file exists in local videos directory (relative to page/content.php)
    if ($filename && file_exists(__DIR__ . '/../videos/' . $filename)) {
        return 'videos/' . $filename;
    }
    $path = $path ?? '';
    return (strpos($path, 'http') === 0) ? $path : '/videos/' . ltrim($path, '/');
}

$lessons_data = [];
$content_result = $mysqli->query("SELECT * FROM tb_content ORDER BY lesson_order ASC, id ASC");

if ($content_result) {
    while ($lesson = $content_result->fetch_assoc()) {
        $lesson_id = (int)$lesson['id'];
        
        // ดึงคำถามสำหรับบทเรียนนี้
        $questions = [];
        $stmt_q = $mysqli->prepare("SELECT * FROM tb_test WHERE lesson_id = ? AND test_type = 'QUIZ' ORDER BY timeshow ASC");
        $stmt_q->bind_param('i', $lesson_id);
        $stmt_q->execute();
        $questions_result = $stmt_q->get_result();
        while ($q = $questions_result->fetch_assoc()) {
            $questions[] = [
                'id' => $q['id'], 'question' => $q['question'],
                'choices' => ['A' => $q['choice_a'], 'B' => $q['choice_b'], 'C' => $q['choice_c'], 'D' => $q['choice_d']],
                'correct' => $q['correct'], 
                'timeshow' => toSeconds($q['timeshow']), 'shown' => false
            ];
        }
        $stmt_q->close();

        // ดึง Transcript สำหรับบทเรียนนี้
        $transcript = [];
        // Use lesson_id_text as per schema in update_schema.sql
        $stmt_t = $mysqli->prepare("SELECT * FROM tb_transcripts WHERE lesson_id_text = ? ORDER BY cue_time ASC");
        
        if ($stmt_t) {
            $lid_text = $lesson['lesson_id_text'];
            $stmt_t->bind_param('s', $lid_text);
            $stmt_t->execute();
            $transcript_result = $stmt_t->get_result();
            while ($t = $transcript_result->fetch_assoc()) {
                $transcript[] = ['t' => (int)$t['cue_time'], 'text' => $t['text']];
            }
            $stmt_t->close();
        } else {
            // Table might not exist or schema mismatch. Log error to allow debugging without crash.
            error_log("Transcript query failed: " . $mysqli->error);
        }

        // ประกอบข้อมูลสำหรับ JavaScript
        $lessons_data[] = [
            'id' => $lesson['lesson_id_text'] ?? "lesson-{$lesson_id}",
            'db_id' => $lesson_id,
            'title' => $lesson['title'] ?? $lesson['name'],
            'slide' => $lesson['slide_url'] ?? '#',
            'quiz' => $lesson['quiz_url'] ?? '#',
            'sources' => [
                '720' => resolveVideoPath($lesson['video_path_720p']),
                '480' => resolveVideoPath($lesson['video_path_480p'])
            ],
            'poster' => $lesson['poster_path'] ?? '',
            'transcript' => $transcript,
            'questions' => $questions
        ];
    }
}

// --- DEBUG LOGGING ---
echo '<script>';
echo 'console.log("--- DEBUG: Lesson Data for JavaScript ---");';
echo 'console.log(' . json_encode($lessons_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . ');';
echo '</script>';
// ---------------------

?>
<style>
    body{background:#f6f7fb}
    .player-card{border:0;border-radius:1rem;box-shadow:0 10px 30px rgba(0,0,0,.06)}
    .playlist{border-radius:.75rem;background:#fff;box-shadow:0 6px 18px rgba(0,0,0,.05)}
    .playlist .active{background:#e7f1ff}
    .cue{cursor:pointer}
    .cue.active{background:#fff3cd}
    .note-box{min-height:120px}
    .kbd{border:1px solid #dee2e6;border-bottom-width:2px;border-right-width:2px;border-radius:.375rem;padding:.125rem .375rem;font-size:.8rem;background:#fff}
    
    /* Question Overlay Styles */
    #questionOverlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        z-index: 20; /* Higher than controls */
        display: none; /* Hidden by default */
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: white;
        padding: 20px;
        text-align: center;
    }
    #questionOverlay .list-group-item {
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.2);
        margin-bottom: 5px;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    #questionOverlay .list-group-item:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }
    #questionOverlay .list-group-item:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>

<main class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?page=home">หน้าแรก</a></li>
        <li class="breadcrumb-item"><a href="#modules">โมดูล</a></li>
        <li id="bcLesson" class="breadcrumb-item active" aria-current="page">บทเรียนวิดีโอ</li>
        </ol>
    </nav>

    <div class="row g-3 justify-content-center">
        <!-- CENTER: PLAYER & CONTENT -->
        <div class="col-lg-10">
            <div class="card player-card mb-3">
                <div class="ratio ratio-16x9 bg-dark position-relative">
                    <video id="player" playsinline controls preload="metadata" poster="" class="w-100 h-100">
                        <source id="srcMp4" src="" type="video/mp4" />
                    </video>
                    
                    <!-- Question Overlay -->
                    <div id="questionOverlay">
                        <h4 class="mb-4">คำถามทบทวนความเข้าใจ</h4>
                        <p id="questionText" class="lead mb-4"></p>
                        <div id="questionChoices" class="list-group w-75 mb-3" style="max-width: 600px;"></div>
                        <div id="questionFeedback" class="mb-3"></div>
                        <button id="continueBtn" class="btn btn-primary px-4" style="display:none;">ดูวิดีโอต่อ</button>
                    </div>
                </div>
                <div class="card-body">
                <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                    <h1 id="lessonTitle" class="h5 flex-fill mb-0"></h1>
                    <div class="d-flex align-items-center gap-2">
                    <label class="text-muted small">ความเร็ว</label>
                    <select id="speed" class="form-select form-select-sm" style="width:auto">
                        <option value="0.75">0.75×</option>
                        <option value="1" selected>1×</option>
                        <option value="1.25">1.25×</option>
                        <option value="1.5">1.5×</option>
                        <option value="2">2×</option>
                    </select>
                    </div>
                </div>

                <div class="progress" role="progressbar" aria-label="ความก้าวหน้า" aria-valuemin="0" aria-valuemax="100">
                    <div id="prog" class="progress-bar" style="width:0%">0%</div>
                </div>

                <div class="mt-3 d-flex flex-wrap gap-2">
                    <a id="btnSlide" href="#" class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-earmark-text me-1"></i> สไลด์</a>
                    <a id="btnQuiz" href="#" class="btn btn-outline-primary btn-sm"><i class="bi bi-ui-checks-grid me-1"></i> ทำแบบทดสอบท้ายบท</a>
                    <button id="btnComplete" class="btn btn-success btn-sm ms-auto" disabled><i class="bi bi-check2-circle me-1"></i> ทำบทเรียนนี้แล้ว</button>
                </div>
                </div>
            </div>

            <!-- Transcript -->
            <div class="card mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span><i class="bi bi-subtitle me-1"></i> คำบรรยาย/สคริปต์</span>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="autoScroll" checked>
                    <label class="form-check-label small" for="autoScroll">เลื่อนอัตโนมัติ</label>
                </div>
                </div>
                <div id="transcript" class="list-group list-group-flush" style="max-height:260px; overflow:auto"></div>
            </div>

            <!-- PLAYLIST (Moved to bottom) -->
            <div class="p-2 playlist">
                <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="fw-semibold"><i class="bi bi-collection-play me-1"></i> เพลย์ลิสต์บทเรียน</div>
                <div class="small text-muted"><span id="completedCount">0</span>/<span id="totalCount">0</span> เสร็จแล้ว</div>
                </div>
                <div id="list" class="list-group small"></div>
            </div>
        </div>
    </div>
</main>

<!-- Quiz Modal -->
<div class="modal fade" id="quizModal" tabindex="-1" aria-labelledby="quizModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" style="height: 90vh;">
      <div class="modal-header">
        <h5 class="modal-title" id="quizModalLabel"><i class="bi bi-ui-checks-grid me-2"></i>แบบทดสอบท้ายบท</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <iframe id="quizFrame" src="" style="width: 100%; height: 100%; border: 0;"></iframe>
      </div>
    </div>
  </div>
</div>


<script>
    // --- Data (from PHP) ---
    const lessons = <?= json_encode($lessons_data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK); ?>;

    // --- Elements ---
    const player = document.getElementById('player');
    const srcMp4 = document.getElementById('srcMp4');
    const prog = document.getElementById('prog');
    const speed = document.getElementById('speed');
    const transcriptEl = document.getElementById('transcript');
    const btnComplete = document.getElementById('btnComplete');
    const bcLesson = document.getElementById('bcLesson');
    const btnSlide = document.getElementById('btnSlide');
    const btnQuiz = document.getElementById('btnQuiz');
    const list = document.getElementById('list');
    const completedCount = document.getElementById('completedCount');
    const totalCount = document.getElementById('totalCount');

    // --- Overlay Elements ---
    const questionOverlay = document.getElementById('questionOverlay');
    const questionText = document.getElementById('questionText');
    const questionChoices = document.getElementById('questionChoices');
    const questionFeedback = document.getElementById('questionFeedback');
    const continueBtn = document.getElementById('continueBtn');

    let current = 0; // index
    let questionTimeout; // ตัวแปรสำหรับจับเวลาตอนคำถามแสดง

    function renderList(){
        totalCount.textContent = lessons.length;
        list.innerHTML = lessons.map((l,idx)=>{
            const done = localStorage.getItem('done_'+l.id) === '1';
            return `<button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center ${idx===current?'active':''}" onclick="loadLesson(${idx})">
            <span class="text-start">${l.title}</span>
            <span class="badge ${done?'text-bg-success':'text-bg-light'}">${done?'เสร็จแล้ว':'ยังไม่เรียน'}</span>
            </button>`;
        }).join('');
        updateCompletedCounter();
    }

    function updateCompletedCounter(){
        const count = lessons.filter(l=> localStorage.getItem('done_'+l.id)==='1').length;
        completedCount.textContent = count;
    }

    function loadLesson(idx){
        current = idx;
        const l = lessons[idx];
        document.getElementById('lessonTitle').textContent = l.title;
        bcLesson.textContent = l.title;
        if (l.slide && l.slide !== '#' && l.slide.trim() !== '') {
            btnSlide.href = l.slide;
            btnSlide.target = '_blank';
            btnSlide.classList.remove('disabled');
            btnSlide.removeAttribute('aria-disabled');
        } else {
            btnSlide.href = '#';
            btnSlide.removeAttribute('target');
            btnSlide.classList.add('disabled');
            btnSlide.setAttribute('aria-disabled', 'true');
        }
        btnQuiz.href = l.quiz;

        srcMp4.src = l.sources['720'] || Object.values(l.sources)[0];
        player.poster = l.poster || '';
        player.load();

        transcriptEl.innerHTML = l.transcript.map(c=>`<a class="list-group-item list-group-item-action cue" data-t="${c.t}"><span class="text-muted me-2">${fmtTime(c.t)}</span>${c.text}</a>`).join('');
        transcriptEl.querySelectorAll('.cue').forEach(el=>{
            el.addEventListener('click', ()=>{ player.currentTime = parseFloat(el.dataset.t); player.play(); });
        });

        const done = localStorage.getItem('done_'+l.id) === '1';
        btnComplete.disabled = done;
        btnComplete.textContent = done ? 'เรียนจบแล้ว' : 'ทำบทเรียนนี้แล้ว';

        // Reset shown status for questions of the new lesson
        resetQuestionShown();
        
        // Hide overlay if showing
        questionOverlay.style.display = 'none';

        renderList();
    }

    function resetQuestionShown(){
        let lesson = lessons[current];
        lesson.questions.forEach(q => q.shown = false);
    }

    function showQuestion(question) {
        player.pause();
        question.shown = true;

        questionText.textContent = question.question;
        // ล้างตัวเลือกเก่าและ feedback
        questionChoices.innerHTML = '';
        questionFeedback.innerHTML = '';
        for (const key in question.choices) {
            const choice = question.choices[key];
            const button = document.createElement('button');
            button.className = 'list-group-item list-group-item-action text-start';
            button.textContent = `${key}. ${choice}`;
            button.onclick = () => handleAnswer(key, question.correct);
            questionChoices.appendChild(button);
        }
        continueBtn.style.display = 'none';
        
        // Show Overlay
        questionOverlay.style.display = 'flex';
        
        // Exit fullscreen if active, to ensure overlay is visible and usable
        if (document.fullscreenElement) {
            document.exitFullscreen().catch(err => console.log(err));
        }

        // ตั้งเวลา 15 วินาที ถ้าไม่ตอบจะรีเซ็ตวิดีโอ
        clearTimeout(questionTimeout);
        questionTimeout = setTimeout(() => {
            questionOverlay.style.display = 'none';
            alert("คุณไม่ได้ตอบคำถามภายในเวลาที่กำหนด วิดีโอจะเริ่มเล่นใหม่");
            player.currentTime = 0; // รีเซ็ตเวลาวิดีโอ
            resetQuestionShown();
        }, 15000); // 15 วินาที
    }

    function handleAnswer(selected, correct) {
        clearTimeout(questionTimeout); // ยกเลิกการจับเวลาเมื่อผู้ใช้ตอบ
        // Disable all choices
        const buttons = questionChoices.querySelectorAll('button');
        buttons.forEach(b => b.disabled = true);

        if (selected === correct) {
            questionFeedback.innerHTML = '<span class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i>ถูกต้อง!</span>';
            // TODO: Add score via AJAX if needed
        } else {
            questionFeedback.innerHTML = `<span class="text-danger fw-bold"><i class="bi bi-x-circle me-1"></i>ยังไม่ถูก, คำตอบที่ถูกต้องคือ ${correct}</span>`;
        }
        continueBtn.style.display = 'block';
    }

    continueBtn.addEventListener('click', () => {
        questionOverlay.style.display = 'none';
        player.play();
    });

    // 4. จัดการเมื่อผู้ใช้สลับแท็บ
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            player.pause();
            // player.currentTime = 0; // รีเซ็ตวิดีโอเมื่อสลับแท็บ (Optional)
        }
    });

    speed.addEventListener('change', ()=>{ player.playbackRate = parseFloat(speed.value); });

    player.addEventListener('timeupdate', ()=>{
        if(!player.duration) return;
        const p = Math.floor(player.currentTime * 100 / player.duration);
        prog.style.width = p+"%"; prog.textContent = p+"%";

        // Highlight transcript
        const cues = Array.from(transcriptEl.querySelectorAll('.cue'));
        let activeIdx = -1;
        for(let i=0;i<cues.length;i++){
            const t = parseFloat(cues[i].dataset.t);
            const nextT = i<cues.length-1 ? parseFloat(cues[i+1].dataset.t) : 1e9;
            if(player.currentTime>=t && player.currentTime<nextT){ activeIdx=i; break; }
        }
        cues.forEach((el,i)=> el.classList.toggle('active', i===activeIdx));
        if(activeIdx>=0 && document.getElementById('autoScroll').checked){
            cues[activeIdx].scrollIntoView({block:'nearest'});
        }

        // Check for questions
        const lesson = lessons[current];
        if (lesson && lesson.questions) {
            for (const q of lesson.questions) {
                if (!q.shown && player.currentTime >= q.timeshow) {
                    showQuestion(q);
                    break; // Show one question at a time
                }
            }
        }
    });

    btnComplete.addEventListener('click', ()=>{
        const l = lessons[current];
        localStorage.setItem('done_'+l.id, '1');
        btnComplete.disabled = true; btnComplete.textContent = 'เรียนจบแล้ว';
        updateCompletedCounter(); renderList();
    });


    // --- Quiz Modal Logic ---
    document.addEventListener('DOMContentLoaded', () => {
        const quizModal = new bootstrap.Modal(document.getElementById('quizModal'));
        const quizFrame = document.getElementById('quizFrame');

        btnQuiz.addEventListener('click', (e) => {
            e.preventDefault();
            const l = lessons[current];
            if(l.quiz && l.quiz !== '#'){
                 quizFrame.src = l.quiz;
                 quizModal.show();
            } else {
                alert('ไม่มีแบบทดสอบสำหรับบทเรียนนี้');
            }
        });
        
        // Clear iframe when modal is closed to stop audio/video if any
        document.getElementById('quizModal').addEventListener('hidden.bs.modal', () => {
            quizFrame.src = '';
        });
    });

    function fmtTime(s){ const m = Math.floor(s/60); const ss = Math.floor(s%60).toString().padStart(2,'0'); return `${m}:${ss}`; }

    (function init(){
        if (lessons.length > 0) {
            // Check for a lesson id in the URL
            const urlParams = new URLSearchParams(window.location.search);
            const lessonId = urlParams.get('chapter'); // FIX: Read 'chapter' from URL
            let startIdx = 0;
            if (lessonId) {
                const foundIdx = lessons.findIndex(l => l.db_id == lessonId);
                if (foundIdx !== -1) {
                    startIdx = foundIdx;
                }
            }
            renderList();
            loadLesson(startIdx);
        } else {
            document.getElementById('lessonTitle').textContent = "[บทที่1]";
            list.innerHTML = '<div class="list-group-item">กรุณาเพิ่มบทเรียนในหน้า Admin</div>';
        }
    })();
</script>