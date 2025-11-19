<?php
// This is a simplified admin page for managing quizzes.
// In a real application, you would have proper admin authentication.

// Include the database connection
require_once '../config/connect.php';

// --- Handle form submissions (Add/Update/Delete) ---

// Add or Update Question
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_question'])) {
    $lesson_id = $_POST['lesson_id'];
    $question_id = $_POST['question_id'] ?? null;
    $question_text = $_POST['question_text'];
    $time_in_seconds = $_POST['time_in_seconds'];
    $points = $_POST['points'];
    $options = $_POST['options'];
    $correct_option_index = $_POST['correct_option'];

    if ($question_id) {
        // Update existing question
        $stmt = $mysqli->prepare("UPDATE tb_questions SET lesson_id = ?, question_text = ?, time_in_seconds = ?, points = ? WHERE id = ?");
        $stmt->bind_param('isiii', $lesson_id, $question_text, $time_in_seconds, $points, $question_id);
        $stmt->execute();

        // Delete old options
        $stmt = $mysqli->prepare("DELETE FROM tb_question_options WHERE question_id = ?");
        $stmt->bind_param('i', $question_id);
        $stmt->execute();

    } else {
        // Insert new question
        $stmt = $mysqli->prepare("INSERT INTO tb_questions (lesson_id, question_text, time_in_seconds, points) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('isii', $lesson_id, $question_text, $time_in_seconds, $points);
        $stmt->execute();
        $question_id = $mysqli->insert_id;
    }

    // Insert new options
    $stmt = $mysqli->prepare("INSERT INTO tb_question_options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
    foreach ($options as $index => $option_text) {
        $is_correct = ($index == $correct_option_index) ? 1 : 0;
        $stmt->bind_param('isi', $question_id, $option_text, $is_correct);
        $stmt->execute();
    }

    header("Location: admin_questions.php?lesson_id=" . $lesson_id);
    exit;
}

// Delete Question
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_question'])) {
    $question_id = $_POST['question_id'];
    $lesson_id = $_POST['lesson_id'];

    // Also deletes options due to foreign key cascade (if set up)
    $stmt = $mysqli->prepare("DELETE FROM tb_questions WHERE id = ?");
    $stmt->bind_param('i', $question_id);
    $stmt->execute();

    header("Location: admin_questions.php?lesson_id=" . $lesson_id);
    exit;
}


// --- Fetch data for display ---
$selected_lesson_id = $_GET['lesson_id'] ?? 1; // Default to lesson 1

// Fetch all lessons/chapters to populate a dropdown
// Assuming you have a tb_lessons table. If not, we'll just use numbers.
$lessons_result = $mysqli->query("SELECT DISTINCT lesson_id FROM tb_questions ORDER BY lesson_id ASC");
$available_lessons = [];
while($row = $lessons_result->fetch_assoc()) {
    $available_lessons[] = $row['lesson_id'];
}
// Add some default lessons if none exist
if (empty($available_lessons)) {
    $available_lessons = [1, 2, 3];
}


// Fetch questions for the selected lesson
$questions = [];
$stmt = $mysqli->prepare("SELECT * FROM tb_questions WHERE lesson_id = ? ORDER BY time_in_seconds ASC");
$stmt->bind_param('i', $selected_lesson_id);
$stmt->execute();
$result = $stmt->get_result();

while ($question = $result->fetch_assoc()) {
    $options_stmt = $mysqli->prepare("SELECT * FROM tb_question_options WHERE question_id = ? ORDER BY id ASC");
    $options_stmt->bind_param('i', $question['id']);
    $options_stmt->execute();
    $options_result = $options_stmt->get_result();
    $question['options'] = [];
    while($option = $options_result->fetch_assoc()) {
        $question['options'][] = $option;
    }
    $questions[] = $question;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Quizzes</title>
    <!-- Using Bootstrap for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .option-group { margin-bottom: 10px; }
        .correct-option { border-left: 4px solid #198754; }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">จัดการคำถามในวิดีโอ</h1>

    <!-- Lesson Selector -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="admin_questions.php" class="d-flex align-items-center">
                <label for="lesson_id" class="form-label me-3 mb-0">เลือกบทเรียน:</label>
                <select name="lesson_id" id="lesson_id" class="form-select w-auto" onchange="this.form.submit()">
                    <?php foreach (array_unique(array_merge($available_lessons, [1,2,3,4,5])) as $lesson_num): ?>
                        <option value="<?php echo $lesson_num; ?>" <?php echo ($selected_lesson_id == $lesson_num) ? 'selected' : ''; ?>>
                            บทที่ <?php echo $lesson_num; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>

    <!-- Existing Questions -->
    <h2 class="mb-3">คำถามที่มีอยู่ (บทที่ <?php echo $selected_lesson_id; ?>)</h2>
    <?php if (empty($questions)): ?>
        <p>ยังไม่มีคำถามสำหรับบทเรียนนี้</p>
    <?php else: ?>
        <?php foreach ($questions as $q): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title"><?php echo htmlspecialchars($q['question_text']); ?></h5>
                        <div>
                            <!-- Edit Button -->
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#questionModal" data-question-id="<?php echo $q['id']; ?>" data-question-text="<?php echo htmlspecialchars($q['question_text']); ?>" data-time="<?php echo $q['time_in_seconds']; ?>" data-points="<?php echo $q['points']; ?>" data-options='<?php echo json_encode($q['options']); ?>'>
                                <i class="bi bi-pencil"></i> แก้ไข
                            </button>
                            <!-- Delete Form -->
                            <form method="POST" action="admin_questions.php" class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบคำถามนี้?');">
                                <input type="hidden" name="question_id" value="<?php echo $q['id']; ?>">
                                <input type="hidden" name="lesson_id" value="<?php echo $selected_lesson_id; ?>">
                                <button type="submit" name="delete_question" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i> ลบ
                                </button>
                            </form>
                        </div>
                    </div>
                    <p class="card-subtitle mb-2 text-muted">
                        แสดงตอน <span class="fw-bold"><?php echo $q['time_in_seconds']; ?></span> วินาที | <span class="fw-bold"><?php echo $q['points']; ?></span> คะแนน
                    </p>
                    <ul class="list-group">
                        <?php foreach ($q['options'] as $opt): ?>
                            <li class="list-group-item <?php echo $opt['is_correct'] ? 'correct-option fw-bold' : ''; ?>">
                                <?php echo htmlspecialchars($opt['option_text']); ?>
                                <?php if ($opt['is_correct']): ?>
                                    <span class="badge bg-success float-end"><i class="bi bi-check-circle"></i> คำตอบที่ถูก</span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <hr class="my-4">

    <!-- Add New Question Button -->
    <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#questionModal">
        <i class="bi bi-plus-circle"></i> เพิ่มคำถามใหม่
    </button>

</div>

<!-- Add/Edit Question Modal -->
<div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="questionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="admin_questions.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="questionModalLabel">เพิ่ม/แก้ไข คำถาม</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="lesson_id" value="<?php echo $selected_lesson_id; ?>">
                    <input type="hidden" name="question_id" id="question_id">

                    <div class="mb-3">
                        <label for="question_text" class="form-label">คำถาม</label>
                        <input type="text" class="form-control" id="question_text" name="question_text" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="time_in_seconds" class="form-label">เวลาที่แสดง (วินาที)</label>
                            <input type="number" class="form-control" id="time_in_seconds" name="time_in_seconds" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="points" class="form-label">คะแนน</label>
                            <input type="number" class="form-control" id="points" name="points" value="1" required>
                        </div>
                    </div>

                    <hr>
                    <h6>ตัวเลือก & คำตอบ</h6>
                    <div id="options-container">
                        <!-- Options will be added here by JS -->
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary mt-2" id="add-option-btn">
                        <i class="bi bi-plus"></i> เพิ่มตัวเลือก
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" name="save_question" class="btn btn-primary">บันทึกคำถาม</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const questionModal = document.getElementById('questionModal');
    const modal = new bootstrap.Modal(questionModal);
    const addOptionBtn = document.getElementById('add-option-btn');
    const optionsContainer = document.getElementById('options-container');
    let optionCount = 0;

    function addOption(optionText = '', isCorrect = false) {
        const optionIndex = optionCount;
        const div = document.createElement('div');
        div.className = 'input-group option-group';
        div.innerHTML = `
            <div class="input-group-text">
                <input class="form-check-input mt-0" type="radio" name="correct_option" value="${optionIndex}" ${isCorrect ? 'checked' : ''} required>
            </div>
            <input type="text" class="form-control" name="options[]" placeholder="เนื้อหาตัวเลือก" value="${optionText}" required>
            <button class="btn btn-outline-danger remove-option-btn" type="button"><i class="bi bi-trash"></i></button>
        `;
        optionsContainer.appendChild(div);
        optionCount++;

        div.querySelector('.remove-option-btn').addEventListener('click', function() {
            div.remove();
        });
    }

    addOptionBtn.addEventListener('click', () => addOption());

    questionModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const questionId = button.getAttribute('data-question-id');

        // Reset form
        const form = questionModal.querySelector('form');
        form.reset();
        document.getElementById('question_id').value = '';
        optionsContainer.innerHTML = '';
        optionCount = 0;
        
        const modalTitle = questionModal.querySelector('.modal-title');

        if (questionId) {
            // --- Edit Mode ---
            modalTitle.textContent = 'แก้ไขคำถาม';
            
            const questionText = button.getAttribute('data-question-text');
            const time = button.getAttribute('data-time');
            const points = button.getAttribute('data-points');
            const options = JSON.parse(button.getAttribute('data-options'));

            document.getElementById('question_id').value = questionId;
            document.getElementById('question_text').value = questionText;
            document.getElementById('time_in_seconds').value = time;
            document.getElementById('points').value = points;

            options.forEach(opt => {
                addOption(opt.option_text, !!opt.is_correct);
            });

        } else {
            // --- Add Mode ---
            modalTitle.textContent = 'เพิ่มคำถามใหม่';
            // Add 2 default empty options for a new question
            addOption('', true); // First option is correct by default
            addOption();
        }
    });
});
</script>
</body>
</html>
