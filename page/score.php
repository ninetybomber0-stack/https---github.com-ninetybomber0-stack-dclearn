<?php
// page/score.php
// Display student's own scores and progress

// 1. Check Login
if (!isset($_SESSION['sess_userid'])) {
    echo '<div class="alert alert-danger">กรุณาเข้าสู่ระบบก่อนดูคะแนน</div>';
    return;
}

$user_id = $_SESSION['sess_userid'];
$user_std_id = $_SESSION['sess_id_std'] ?? ''; // Assuming this is set in index.php

// --- Data Fetching ---

// 1. Get all lessons
$lessons = [];
$lesson_sql = "SELECT id, name FROM tb_content ORDER BY lesson_order ASC, id ASC";
$res_l = $mysqli->query($lesson_sql);
if ($res_l) {
    while ($row = $res_l->fetch_assoc()) {
        $lessons[$row['id']] = $row;
    }
}

// 2. Get Video Question Progress (from tb_test_log)
// Count distinct questions answered correct per lesson
$video_progress = [];
// Note: We need to map 'w1-vid', 'w2-vid' etc from tb_test_log to tb_content.
// However, tb_test_log stores 'lesson' as string (e.g. 'w1-vid'). tb_content has 'lesson_id_text'.
// Let's assume we can match them. 
// OR, simpler: Count questions in tb_test by lesson, and count answers in tb_test_log.

// Strategy:
// A. Get total questions per lesson string from tb_test
$total_questions = [];
$q_sql = "SELECT lesson, COUNT(*) as total FROM tb_test GROUP BY lesson";
$res_q = $mysqli->query($q_sql);
if ($res_q) {
    while ($row = $res_q->fetch_assoc()) {
        $total_questions[$row['lesson']] = $row['total'];
    }
}

// B. Get answered questions by this user
$answered_questions = [];
$ans_sql = "SELECT lesson, COUNT(DISTINCT quiz_id) as answered FROM tb_test_log WHERE user_id = ? GROUP BY lesson";
$stmt = $mysqli->prepare($ans_sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res_ans = $stmt->get_result();
if ($res_ans) {
    while ($row = $res_ans->fetch_assoc()) {
        $answered_questions[$row['lesson']] = $row['answered'];
    }
}

// Map tb_content ID/lesson_id_text to these counts
// We need to know the 'lesson_id_text' for each lesson in $lessons.
// Let's re-fetch lessons with lesson_id_text
$lessons = []; // Clear
$lesson_sql = "SELECT id, name, lesson_id_text, score as max_score FROM tb_content ORDER BY lesson_order ASC, id ASC";
$res_l = $mysqli->query($lesson_sql);
if ($res_l) {
    while ($row = $res_l->fetch_assoc()) {
        $lessons[$row['id']] = $row;
    }
}


// 3. Get Chapter Quiz Scores (tb_scores)
$chapter_scores = [];
$score_sql = "SELECT lesson_id, MAX(score) as score, total FROM tb_scores WHERE member_id = ? GROUP BY lesson_id";
$stmt = $mysqli->prepare($score_sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res_s = $stmt->get_result();
if ($res_s) {
    while ($row = $res_s->fetch_assoc()) {
        $chapter_scores[$row['lesson_id']] = $row;
    }
}

// 4. Get Assignments (tb_work)
$assignments = [];
$work_sql = "SELECT id, title, due_date FROM tb_work ORDER BY id ASC";
$res_w = $mysqli->query($work_sql);
if ($res_w) {
    while ($row = $res_w->fetch_assoc()) {
        $assignments[$row['id']] = $row;
    }
}

// 5. Get Student Submissions (tb_work_submissions)
$submissions = [];
// Assuming tb_work_submissions uses 'student_id' (varchar) OR 'user_id' (int)?
// Checked user_list.php: uses 'student_id' column. AND assumes it matches 'id_std' from session.
if ($user_std_id) {
    $sub_sql = "SELECT work_id, submitted_at FROM tb_work_submissions WHERE student_id = ?";
    $stmt = $mysqli->prepare($sub_sql);
    $stmt->bind_param('s', $user_std_id);
    $stmt->execute();
    $res_sub = $stmt->get_result();
    if ($res_sub) {
        while ($row = $res_sub->fetch_assoc()) {
            $submissions[$row['work_id']] = $row;
        }
    }
}

// 6. Get Exam Scores (tb_exam_scores)
$exam_scores = [];
if ($user_std_id) {
    $ex_sql = "SELECT exam_type, score, full_score FROM tb_exam_scores WHERE student_id = ?";
    $stmt = $mysqli->prepare($ex_sql);
    $stmt->bind_param('s', $user_std_id);
    $stmt->execute();
    $res_ex = $stmt->get_result();
    if ($res_ex) {
        while ($row = $res_ex->fetch_assoc()) {
            $exam_scores[$row['exam_type']] = $row;
        }
    }
}
?>

<div class="row g-4">
  <!-- Header -->
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex align-items-center gap-3 p-4">
        <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
          <i class="bi bi-person-vcard fs-1"></i>
        </div>
        <div>
          <h4 class="mb-1">คะแนนและการส่งงาน</h4>
          <p class="text-muted mb-0">สรุปความคืบหน้าการเรียน คะแนนทดสอบ และสถานะการส่งงานของคุณ</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Lesson Progress & Chapter Scores -->
  <div class="col-lg-8">
    <div class="card shadow-sm h-100">
      <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0"><i class="bi bi-journal-check me-2 text-primary"></i>บทเรียนและคะแนนเก็บ</h5>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width: 40%">บทเรียน</th>
              <th class="text-center">คำถามในคลิป</th>
              <!-- <th class="text-center">แบบทดสอบท้ายบท</th> -->
            </tr>
          </thead>
          <tbody>
            <?php foreach ($lessons as $lid => $l): 
                $lid_text = $l['lesson_id_text'];
                
                // Video Progress
                $total_q = $total_questions[$lid_text] ?? 0;
                $done_q = $answered_questions[$lid_text] ?? 0;
                $vid_percent = ($total_q > 0) ? ($done_q / $total_q) * 100 : 0;
                
                // Chapter Score
                $score_info = $chapter_scores[$lid] ?? null;
                $my_score = $score_info ? $score_info['score'] : 0;
                // Use score from tb_scores logic or fallback? 
                // user_list.php uses tb_test sum points. tb_content has 'score' column too.
                // Let's stick to simple display.
                $max_score = $l['max_score']; // from tb_content
                $score_percent = ($max_score > 0) ? ($my_score / $max_score) * 100 : 0;
            ?>
            <tr>
              <td>
                <div class="fw-semibold text-dark"><?= htmlspecialchars($l['name']) ?></div>
                <div class="small text-muted">บทที่ <?= $lid ?></div>
              </td>
              <td class="text-center">
                <?php if ($total_q == 0): ?>
                  <span class="text-muted small">-</span>
                <?php else: ?>
                    <div class="d-flex flex-column align-items-center">
                        <div class="progress w-75" style="height: 6px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: <?= $vid_percent ?>%"></div>
                        </div>
                        <span class="small mt-1 text-muted"><?= $done_q ?>/<?= $total_q ?> ข้อ</span>
                    </div>
                <?php endif; ?>
              </td>
              <!-- <td class="text-center">
                 <div class="d-flex flex-column align-items-center">
                    <?php if ($my_score > 0): ?>
                        <span class="fw-bold text-success fs-5"><?= $my_score ?></span>
                        <span class="small text-muted">/ <?= $max_score ?> คะแนน</span>
                    <?php else: ?>
                        <span class="text-muted small">ยังไม่มีคะแนน</span>
                    <?php endif; ?>
                 </div>
              </td> -->
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Sidebar: Work & Exams -->
  <div class="col-lg-4">
    <div class="row g-4">
        <!-- Work Submissions -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0"><i class="bi bi-box-seam me-2 text-primary"></i>งานที่ได้รับมอบหมาย</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <?php if (empty($assignments)): ?>
                        <li class="list-group-item text-muted text-center py-3">ไม่มีงานที่ได้รับมอบหมาย</li>
                    <?php else: ?>
                        <?php foreach ($assignments as $wid => $work): 
                            $is_submitted = isset($submissions[$wid]);
                            $sub_date = $is_submitted ? $submissions[$wid]['submitted_at'] : null;
                        ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div style="max-width: 60%">
                                <div class="fw-medium text-truncate" title="<?= htmlspecialchars($work['title']) ?>">
                                    <?= htmlspecialchars($work['title']) ?>
                                </div>
                                <div class="small text-muted">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    กำหนด: <?= (new DateTime($work['due_date']))->format('d/m/y') ?>
                                </div>
                            </div>
                            <div class="text-end">
                                <?php if ($is_submitted): ?>
                                    <span class="badge bg-success mb-1">ส่งแล้ว</span>
                                    <div style="font-size: 0.7em" class="text-muted">
                                        <?= (new DateTime($sub_date))->format('d/m/y') ?>
                                    </div>
                                <?php else: ?>
                                    <span class="badge bg-danger">ยังไม่ส่ง</span>
                                <?php endif; ?>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Exams -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0"><i class="bi bi-trophy me-2 text-primary"></i>คะแนนสอบ</h5>
                </div>
                <div class="card-body">
                    <!-- Pre-test -->
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <div class="fw-semibold">สอบก่อนเรียน</div>
                            <div class="small text-muted">Pre-test</div>
                        </div>
                        <div class="text-end">
                            <?php 
                                $pre = $exam_scores['pre_test'] ?? null;
                            ?>
                            <div class="h4 mb-0 text-primary fw-bold">
                                <?= $pre ? $pre['score'] : '-' ?>
                                <span class="fs-6 text-muted fw-normal">/ <?= $pre ? $pre['full_score'] : '100' ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Post-test -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">สอบหลังเรียน</div>
                            <div class="small text-muted">Post-test</div>
                        </div>
                        <div class="text-end">
                            <?php 
                                $post = $exam_scores['post_test'] ?? null;
                            ?>
                            <div class="h4 mb-0 text-primary fw-bold">
                                <?= $post ? $post['score'] : '-' ?>
                                <span class="fs-6 text-muted fw-normal">/ <?= $post ? $post['full_score'] : '100' ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
