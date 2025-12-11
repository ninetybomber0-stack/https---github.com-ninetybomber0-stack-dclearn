<?php
// page/score.php
// Display student's own scores and progress

// 1. Check Login
// 1. Check Login
if (!isset($_SESSION['sess_userid'])) {
    echo '<div class="alert alert-danger">กรุณาเข้าสู่ระบบก่อนดูคะแนน</div>';
    return;
}

// DEBUG: Enable Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$user_id = $_SESSION['sess_userid'];
$user_std_id = $_SESSION['sess_id_std'] ?? ''; 

// Check DB Connection
if (!isset($mysqli) || $mysqli->connect_errno) {
    echo '<div class="alert alert-danger">Database connection failed.</div>';
    return;
}

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
// B. Get answered questions by this user
$answered_questions = [];
$ans_sql = "SELECT lesson, COUNT(DISTINCT quiz_id) as answered FROM tb_test_log WHERE user_id = ? GROUP BY lesson";
$stmt = $mysqli->prepare($ans_sql);
if ($stmt) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $res_ans = $stmt->get_result();
    if ($res_ans) {
        while ($row = $res_ans->fetch_assoc()) {
            $answered_questions[$row['lesson']] = $row['answered'];
        }
    }
} else {
    // echo "Error preparing log query: " . $mysqli->error;
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


// 3. Get Scores (PRE, QUIZ, POST) from tb_scores
$course_scores = [];
$score_sql = "SELECT lesson_id, test_type, score, full_score FROM tb_scores WHERE member_id = ?";
$stmt = $mysqli->prepare($score_sql);
if ($stmt) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $res_s = $stmt->get_result();
    if ($res_s) {
        while ($row = $res_s->fetch_assoc()) {
            $lid = $row['lesson_id'];
            $type = strtoupper($row['test_type']);
            if ($type) {
                $course_scores[$lid][$type] = $row;
            }
        }
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
if ($user_std_id) {
    $sub_sql = "SELECT work_id, submitted_at FROM tb_work_submissions WHERE student_id = ?";
    $stmt = $mysqli->prepare($sub_sql);
    if ($stmt) {
        $stmt->bind_param('s', $user_std_id);
        $stmt->execute();
        $res_sub = $stmt->get_result();
        if ($res_sub) {
            while ($row = $res_sub->fetch_assoc()) {
                $submissions[$row['work_id']] = $row;
            }
        }
    } else {
        // echo "Error preparing submission query: " . $mysqli->error;
    }
}

// 6. Get Exam Scores (tb_exam_scores)
$exam_scores = [];
if ($user_std_id) {
    $ex_sql = "SELECT exam_type, score, full_score FROM tb_exam_scores WHERE student_id = ?";
    $stmt = $mysqli->prepare($ex_sql);
    if ($stmt) {
        $stmt->bind_param('s', $user_std_id);
        $stmt->execute();
        $res_ex = $stmt->get_result();
        if ($res_ex) {
            while ($row = $res_ex->fetch_assoc()) {
                $exam_scores[$row['exam_type']] = $row;
            }
        }
    } else {
        // echo "Error preparing exam query: " . $mysqli->error;
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
        <table class="table table-hover align-middle mb-0 text-center">
          <thead class="table-light">
            <tr>
              <th class="text-start" style="width: 30%">บทเรียน</th>
              <th style="width: 20%">ก่อนเรียน (Pre)</th>
              <th style="width: 20%">ระหว่างเรียน (Quiz)</th>
              <th style="width: 20%">หลังเรียน (Post)</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($lessons as $lid => $l): 
                // Get scores for this lesson
                $scores = $course_scores[$lid] ?? [];
                
                $pre = $scores['PRE'] ?? null;
                $quiz = $scores['QUIZ'] ?? null; // Video Pop-up questions
                $post = $scores['POST'] ?? null;
            ?>
            <tr>
              <td class="text-start">
                <div class="fw-semibold text-dark"><?= htmlspecialchars($l['name']) ?></div>
                <div class="small text-muted">บทที่ <?= $lid ?></div>
              </td>
              
              <!-- Pre-test Column -->
              <td>
                <?php if ($pre): ?>
                    <span class="fw-bold text-primary"><?= $pre['score'] ?></span>
                    <span class="text-muted small">/ <?= $pre['full_score'] ?></span>
                <?php else: ?>
                    <span class="text-muted">-</span>
                <?php endif; ?>
              </td>

              <!-- In-lesson Quiz Column -->
              <td>
                <?php if ($quiz): ?>
                    <span class="fw-bold text-success"><?= $quiz['score'] ?></span>
                    <span class="text-muted small">/ <?= $quiz['full_score'] ?></span>
                <?php else: ?>
                    <span class="text-muted">-</span>
                <?php endif; ?>
              </td>

              <!-- Post-test Column -->
              <td>
                <?php if ($post): ?>
                    <span class="fw-bold text-info"><?= $post['score'] ?></span>
                    <span class="text-muted small">/ <?= $post['full_score'] ?></span>
                <?php else: ?>
                    <span class="text-muted">-</span>
                <?php endif; ?>
              </td>

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
                    <div class="d-flex justify-content-between align-items-center mb-4">
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

                    <!-- Total Score Calculation -->
                    <?php
                        // Calculate Total Score (Chapters + Post-test)
                        // 1. Chapters
                        $sum_chapter_score = 0;
                        $sum_chapter_full = 0;
                        foreach ($lessons as $lid => $l) {
                            $scores = $course_scores[$lid] ?? [];
                            $post_ch = $scores['POST'] ?? null;
                            if ($post_ch) {
                                $sum_chapter_score += (int)$post_ch['score'];
                            }
                            $sum_chapter_full += (int)$l['max_score'];
                        }

                        // 2. Post-test
                        $post_score = $post ? (int)$post['score'] : 0;
                        $post_full = $post ? (int)$post['full_score'] : 100;

                        $grand_total_score = $sum_chapter_score + $post_score;
                        $grand_total_full = $sum_chapter_full + $post_full;
                    ?>

                    <!-- Grand Total Display -->
                    <div class="bg-primary bg-opacity-10 rounded p-3 text-center mt-3">
                        <div class="text-primary fw-bold mb-1" style="font-size: 1.1rem;">คะแนนรวมทั้งหมด</div>
                        <div class="display-4 fw-bold text-primary" style="line-height: 1.2;">
                            <?= $grand_total_score ?>
                            <span class="fs-4 text-muted fw-normal">/ <?= $grand_total_full ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
