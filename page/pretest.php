<?php
// page/pretest.php

// Ensure DB connection
require_once __DIR__ . '/../config/connect.php';

$lesson_id = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 0;
$lesson_title = "";

// Fetch Lesson Info
if ($lesson_id > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM tb_content WHERE id = ?");
    $stmt->bind_param("i", $lesson_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $lesson_title = $row['name']; // or 'title' if exists, schema says 'name' usually, content.php uses name/title
        // content.php: 'title' => $lesson['title'] ?? $lesson['name'],
        if (empty($lesson_title) && isset($row['title'])) {
            $lesson_title = $row['title'];
        }
    }
    $stmt->close();
}

// Fetch Questions (Pre-test -> test_type = 'TEST')
$questions = [];
if ($lesson_id > 0) {
    // Assuming 'TEST' is used for Pre/Post tests. 
    // If we wanted specific Pre-test vs Post-test, we'd need another field, but sticking to prompt 'tb_test'.
    $stmt = $mysqli->prepare("SELECT * FROM tb_test WHERE lesson_id = ? AND test_type = 'TEST' ORDER BY test_order ASC, id ASC");
    $stmt->bind_param("i", $lesson_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $questions[] = $row;
    }
    $stmt->close();
}
?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">หน้าแรก</a></li>
            <li class="breadcrumb-item active" aria-current="page">แบบทดสอบก่อนเรียน</li>
        </ol>
    </nav>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <h2 class="mb-4">แบบทดสอบก่อนเรียน: <?= htmlspecialchars($lesson_title) ?></h2>
            
            <?php if (empty($questions)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i> ยังไม่มีแบบทดสอบสำหรับบทเรียนนี้
                </div>
                <a href="index.php" class="btn btn-secondary">กลับหน้าหลัก</a>
            <?php else: ?>
                <form id="pretestForm">
                    <?php foreach ($questions as $index => $q): ?>
                        <div class="mb-4 question-block" data-id="<?= $q['id'] ?>" data-correct="<?= $q['correct'] ?>">
                            <h5 class="fw-bold text-primary mb-3"><?= ($index + 1) ?>. <?= htmlspecialchars($q['question']) ?></h5>
                            
                            <div class="list-group">
                                <?php 
                                $choices = [
                                    'A' => $q['choice_a'],
                                    'B' => $q['choice_b'],
                                    'C' => $q['choice_c'],
                                    'D' => $q['choice_d']
                                ];
                                foreach ($choices as $key => $choice): 
                                ?>
                                    <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 cur-pointer">
                                        <input class="form-check-input flex-shrink-0" type="radio" name="q_<?= $q['id'] ?>" value="<?= $key ?>">
                                        <span>
                                            <span class="fw-bold badge bg-light text-dark border me-2"><?= $key ?></span>
                                            <?= htmlspecialchars($choice) ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <div class="feedback mt-2" style="display:none;"></div>
                        </div>
                    <?php endforeach; ?>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5">
                        <button type="button" class="btn btn-primary btn-lg px-5 rounded-pill" onclick="submitPretest()">
                            <i class="bi bi-check-circle-fill me-2"></i> ส่งคำตอบ
                        </button>
                    </div>
                </form>

                <!-- Result Card (Hidden Initially) -->
                <div id="resultCard" class="card bg-light border-success mt-4" style="display:none;">
                    <div class="card-body text-center p-5">
                        <h3 class="text-success fw-bold display-6 mb-3">ผลการทดสอบ</h3>
                        <div class="display-1 fw-bold mb-3" id="scoreDisplay">0/0</div>
                        <p class="lead text-muted" id="scoreText"></p>
                        <hr>
                        <a href="index.php" class="btn btn-outline-primary mt-3">กลับหน้าหลัก</a>
                        <a href="index.php?page=content&chapter=<?= $lesson_id ?>" class="btn btn-primary mt-3 ms-2">เข้าสู่บทเรียน</a>
                    </div>
                </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function submitPretest() {
    let score = 0;
    let total = 0;
    const questions = document.querySelectorAll('.question-block');
    let allAnswered = true;

    // Reset styles
    document.querySelectorAll('.list-group-item').forEach(el => {
        el.classList.remove('list-group-item-success', 'list-group-item-danger');
    });
    document.querySelectorAll('.feedback').forEach(el => el.style.display = 'none');

    questions.forEach(q => {
        total++;
        const id = q.getAttribute('data-id');
        const correct = q.getAttribute('data-correct');
        const selected = document.querySelector(`input[name="q_${id}"]:checked`);

        if (!selected) {
            allAnswered = false;
        }
    });

    if (!allAnswered) {
        alert('กรุณาทำข้อสอบให้ครบทุกข้อก่อนส่งคำตอบ');
        return;
    }

    questions.forEach(q => {
        const id = q.getAttribute('data-id');
        const correct = q.getAttribute('data-correct');
        const inputs = q.querySelectorAll(`input[name="q_${id}"]`);
        const selected = document.querySelector(`input[name="q_${id}"]:checked`);
        const feedback = q.querySelector('.feedback');

        // Disable inputs
        inputs.forEach(input => input.disabled = true);

        let isCorrect = false;
        if (selected && selected.value === correct) {
            score++;
            isCorrect = true;
            selected.closest('.list-group-item').classList.add('list-group-item-success');
            feedback.innerHTML = '<span class="text-success"><i class="bi bi-check-lg"></i> ถูกต้อง</span>';
        } else {
            if (selected) {
                selected.closest('.list-group-item').classList.add('list-group-item-danger');
                feedback.innerHTML = `<span class="text-danger"><i class="bi bi-x-lg"></i> ผิด</span>`;
            } else {
                // Not answered
                feedback.innerHTML = `<span class="text-danger"><i class="bi bi-x-lg"></i> ยังไม่ได้ตอบ</span>`;
            }
        }
        feedback.style.display = 'block';
    });

    // Show Result
    const resultCard = document.getElementById('resultCard');
    const scoreDisplay = document.getElementById('scoreDisplay');
    const scoreText = document.getElementById('scoreText');
    const formBtn = document.querySelector('#pretestForm button');

    scoreDisplay.textContent = `${score}/${total}`;
    
    const percentage = (score / total) * 100;
    if (percentage >= 80) scoreText.textContent = "ยอดเยี่ยม! คุณมีความรู้พื้นฐานดีมาก";
    else if (percentage >= 50) scoreText.textContent = "ผ่านเกณฑ์! มีพื้นฐานพอสมควร";
    else scoreText.textContent = "ควรทบทวนบทเรียนเพิ่มเติม";

    // Save Score via AJAX
    const formData = new FormData();
    formData.append('lesson_id', <?= $lesson_id ?>);
    formData.append('score', score);
    formData.append('test_type', 'PRE');

    fetch('page/save_score.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log('Score saved:', data);
    })
    .catch(error => {
        console.error('Error saving score:', error);
    });

    resultCard.style.display = 'block';
    formBtn.style.display = 'none'; // Hide submit button
    
    // Check if we should save score logic here if needed, 
    // but for now strictly follows 'display' requirement. 
    // If User wants to save, we can add AJAX to save_score.php
}
</script>

<style>
.cur-pointer { cursor: pointer; }
.list-group-item:hover { background-color: #f8f9fa; }
.question-block {
    background: #fff;
    padding: 1.5rem;
    border-radius: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    border: 1px solid #eee;
}
</style>
