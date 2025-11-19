<?php
// user_list.php

// Check if the user is the admin/teacher.
if (!isset($_SESSION['sess_username']) || $_SESSION['sess_username'] !== 'kamol') {
    // If not admin, show access denied message
?>
<div class="row g-3">
  <section class="col-12">
    <div class="alert alert-danger text-center" role="alert">
      <h4 class="alert-heading">Access Denied</h4>
      <p>You do not have permission to view this page. This page is for administrators only.</p>
      <hr>
      <p class="mb-0">Please contact the system administrator if you believe this is an error.</p>
    </div>
  </section>
</div>
<?php
} else {
    // If user is admin, fetch data and show the user list with scores.
    // This logic is moved from the original work.php

    // We assume $mysqli is available from index.php
    
    // --- Data Fetching for Teacher's View ---
    $students = [];
    $lessons = [];
    $assignments = [];
    $scores_data = [];
    $submissions_data = [];
    $total_points = [];

    // 1. Get all students (non-admin)
    $student_result = $mysqli->query("SELECT id, id_std, fullname, class FROM tb_member WHERE id_std != 'kamol' ORDER BY class, fullname ASC");
    if ($student_result) {
        while ($row = $student_result->fetch_assoc()) {
            $students[] = $row;
        }
    }

    // 2. Get all lessons from tb_content
    $lesson_result = $mysqli->query("SELECT id, name FROM tb_content ORDER BY id ASC");
    if ($lesson_result) {
        while ($row = $lesson_result->fetch_assoc()) {
            $lessons[$row['id']] = $row['name'];
        }
    }
    
    // 3. Get all assignments from tb_work
    $assignment_result = $mysqli->query("SELECT id, title, due_date FROM tb_work ORDER BY id ASC");
    if ($assignment_result) {
        while ($row = $assignment_result->fetch_assoc()) {
            $assignments[$row['id']] = $row;
        }
    }

    // 4. Get total points for each lesson's quiz from tb_test
    $total_p_result = $mysqli->query("SELECT lesson_id, SUM(points) as total FROM tb_test GROUP BY lesson_id");
    if ($total_p_result) {
        while ($row = $total_p_result->fetch_assoc()) {
            $total_points[$row['lesson_id']] = $row['total'];
        }
    }
    
    // 5. Get all quiz scores for all students
    $score_result = $mysqli->query(
        "SELECT student_id, lesson_id, MAX(score) as max_score FROM tb_scores GROUP BY student_id, lesson_id"
    );
    if ($score_result) {
        while ($row = $score_result->fetch_assoc()) {
            $scores_data[$row['student_id']][$row['lesson_id']] = $row['max_score'];
        }
    }

    // 6. Get all work submissions for all students
    $submission_result = $mysqli->query(
        "SELECT student_id, work_id, file_path, submitted_at FROM tb_work_submissions"
    );
    if ($submission_result) {
        while ($row = $submission_result->fetch_assoc()) {
            // Store the latest submission for each work
            $submissions_data[$row['student_id']][$row['work_id']] = $row;
        }
    }
?>

<div class="row g-3">
    <section class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <i class="bi bi-people me-1"></i> รายชื่อนักศึกษาและสรุปผลการเรียน
            </div>
            <div class="card-body">
                <h5 class="card-title">ภาพรวมความคืบหน้าของนักศึกษา</h5>
                <p>คลิกที่ชื่อนักศึกษาเพื่อดูรายละเอียดคะแนนและการส่งงาน</p>

                <div class="accordion mt-3" id="studentAccordion">
                    <?php if (empty($students)): ?>
                        <div class="text-center text-muted p-3">ยังไม่มีข้อมูลนักศึกษาในระบบ</div>
                    <?php else: ?>
                        <?php foreach ($students as $index => $student): 
                            $student_std_id = $student['id_std'];
                            $collapse_id = "collapse-student-" . $student['id'];
                            $header_id = "header-student-" . $student['id'];
                        ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="<?= $header_id ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $collapse_id ?>" aria-expanded="false" aria-controls="<?= $collapse_id ?>">
                                        <span class="fw-bold me-2"><?= htmlspecialchars($student['fullname']) ?></span>
                                        <span class="text-muted small">(<?= htmlspecialchars($student['id_std']) ?> | ห้อง: <?= htmlspecialchars($student['class']) ?>)</span>
                                    </button>
                                </h2>
                                <div id="<?= $collapse_id ?>" class="accordion-collapse collapse" aria-labelledby="<?= $header_id ?>" data-bs-parent="#studentAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <!-- Quiz Scores Table -->
                                            <div class="col-md-6 mb-3 mb-md-0">
                                                <h6><i class="bi bi-card-checklist me-1"></i>คะแนนแบบทดสอบท้ายบท</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>บทเรียน</th>
                                                                <th class="text-center">คะแนน (เต็ม 100)</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (empty($lessons)): ?>
                                                                <tr><td colspan="2" class="text-center text-muted">ไม่มีบทเรียน</td></tr>
                                                            <?php else: ?>
                                                                <?php foreach ($lessons as $lesson_id => $lesson_name): ?>
                                                                    <tr>
                                                                        <td>บทที่ <?= htmlspecialchars($lesson_id) ?>: <?= htmlspecialchars($lesson_name) ?></td>
                                                                        <td class="text-center">
                                                                            <?php
                                                                            if (isset($scores_data[$student_std_id]) && isset($scores_data[$student_std_id][$lesson_id])) {
                                                                                $score = (int)$scores_data[$student_std_id][$lesson_id];
                                                                                $total = isset($total_points[$lesson_id]) ? (int)$total_points[$lesson_id] : 0;
                                                                                $percentage = ($total > 0) ? round(($score / $total) * 100) : 0;
                                                                                echo '<span class="text-success fw-bold">✓ ' . $percentage . '</span>';
                                                                            } else {
                                                                                echo '<span class="text-muted">-</span>';
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Work Submissions Table -->
                                            <div class="col-md-6">
                                                <h6><i class="bi bi-file-earmark-arrow-up me-1"></i>การส่งงาน</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>ชื่องาน</th>
                                                                <th class="text-center">สถานะ</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (empty($assignments)): ?>
                                                                <tr><td colspan="2" class="text-center text-muted">ยังไม่มีงานที่มอบหมาย</td></tr>
                                                            <?php else: ?>
                                                                <?php foreach ($assignments as $work_id => $work_details): ?>
                                                                    <tr>
                                                                        <td>
                                                                            <?= htmlspecialchars($work_details['title']) ?>
                                                                            <div class="small text-muted">กำหนดส่ง: <?= (new DateTime($work_details['due_date']))->format('d/m/Y') ?></div>
                                                                        </td>
                                                                        <td class="text-center align-middle">
                                                                            <?php
                                                                            if (isset($submissions_data[$student_std_id]) && isset($submissions_data[$student_std_id][$work_id])) {
                                                                                $submission = $submissions_data[$student_std_id][$work_id];
                                                                                $submitted_time = new DateTime($submission['submitted_at']);
                                                                                echo '<span class="badge bg-success" title="ส่งเมื่อ: ' . $submitted_time->format('d/m/Y H:i') . '">ส่งแล้ว</span>';
                                                                            } else {
                                                                                echo '<span class="badge bg-danger">ยังไม่ส่ง</span>';
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
} // End of admin check
?>
