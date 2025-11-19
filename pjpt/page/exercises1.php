<?php include "header.php"; ?>
<?php
include "config/connect.php";

// ดึงวิดีโอ (เอาไฟล์ล่าสุดที่อัปโหลด)
$sql="SELECT filename FROM exercises1_video ORDER BY id DESC LIMIT 1";
$video_result = $conn->query($sql);
$video = $video_result->fetch_assoc();
$video_path = "videos/" . ($video ? htmlspecialchars($video['filename']) : 'default.mp4');
//echo "Video Path: " . $video_path;
// ดึงคำถามจาก DB
$questions = [];
$result = $conn->query("SELECT * FROM exercises1_questions ORDER BY timecode ASC");
while($row = $result->fetch_assoc()){
    $questions[] = $row;
}
?>

<h1>แบบฝึกหัดบทที่ 1</h1>
<p>ชมวิดีโอและตอบคำถามระหว่างเล่น</p>

<!-- ครอบ video กับ popup ไว้ใน container -->
<div id="video-container" style="position:relative; display:inline-block;">
    <video id="video" width="640" controls>
        <source src="<?= $video_path ?>" type="video/mp4">
        เบราว์เซอร์ของคุณไม่รองรับวิดีโอ
    </video>

    <!-- ป๊อบอัปคำถาม (overlay) -->
    <div id="question-box" style="
        display:none;
        position:absolute;
        top:50%;
        left:50%;
        transform:translate(-50%, -50%);
        background:rgba(0,0,0,0.85);
        color:#fff;
        padding:20px;
        border-radius:12px;
        width:80%;
        max-width:400px;
        text-align:left;
        z-index:999;
        box-shadow:0 4px 15px rgba(0,0,0,0.5);
    ">
        <h3 id="question-text"></h3>
        <form id="answer-form">
            <input type="radio" name="answer" id="ans1" value="1"> <label for="ans1" id="opt1"></label><br>
            <input type="radio" name="answer" id="ans2" value="2"> <label for="ans2" id="opt2"></label><br>
            <input type="radio" name="answer" id="ans3" value="3"> <label for="ans3" id="opt3"></label><br>
            <input type="radio" name="answer" id="ans4" value="4"> <label for="ans4" id="opt4"></label><br><br>
            <button type="button" id="submit-answer">ส่งคำตอบ</button>
        </form>
    </div>
</div>

<!-- กล่องแสดงผลคะแนน -->
<div id="finished-box" style="display:none; margin-top:20px; border:1px solid #4CAF50; padding:15px; background-color:#f0fff0;">
    <h2>ทำแบบทดสอบเสร็จแล้ว!</h2>
    <p>คุณได้คะแนน: <span id="final-score">0</span> / <?= count($questions) ?></p>
    <form id="score-form" method="post" action="save_score.php">
        <input type="hidden" name="student_id" value="001"> 
        <input type="hidden" name="lesson_id" value="1">
        <input type="hidden" id="score_to_save" name="score" value="">
        <button type="submit">บันทึกคะแนน</button>
    </form>
    <p id="save-status"></p>
</div>

<script>
const questions = <?= json_encode($questions, JSON_UNESCAPED_UNICODE); ?>;
let currentQ = 0;
let score = 0;
const video = document.getElementById('video');
const questionBox = document.getElementById('question-box');
let answerTimeout = null; // ตัวแปรจับเวลา

video.addEventListener('timeupdate', function() {
    if(currentQ < questions.length && Math.floor(video.currentTime) >= parseInt(questions[currentQ].timecode)) {
        if (questionBox.style.display === 'none') {
            video.pause();
            showQuestion(questions[currentQ]);
        }
    }
});

function showQuestion(q) {
    // ล้างตัวเลือกเก่า
    document.querySelector('input[name="answer"]:checked')?.removeAttribute('checked');
    questionBox.style.display = 'block';
    document.getElementById('question-text').innerText = q.question;
    document.getElementById('opt1').innerText = q.option1;
    document.getElementById('opt2').innerText = q.option2;
    document.getElementById('opt3').innerText = q.option3;
    document.getElementById('opt4').innerText = q.option4;

    // ตั้งเวลา 10 วินาที ถ้าไม่ตอบให้รีเซ็ตวิดีโอ
    clearTimeout(answerTimeout);
    answerTimeout = setTimeout(() => {
        alert("หมดเวลา! วิดีโอจะเริ่มใหม่");
        questionBox.style.display = 'none';
        video.currentTime = 0;
        video.play();
    }, 10000);
}

document.getElementById('submit-answer').addEventListener('click', function() {
    let selected = document.querySelector('input[name="answer"]:checked');
    if(selected){
        clearTimeout(answerTimeout); // ยกเลิก timeout ถ้ามีการตอบ
        if(parseInt(selected.value) === parseInt(questions[currentQ].answer)) {
            score++;
        }
        currentQ++;
        questionBox.style.display = 'none';
        selected.checked = false; // เคลียร์ตัวเลือก
        video.play();
    } else {
        alert("กรุณาเลือกคำตอบก่อนส่ง");
    }
});

video.addEventListener('ended', function() {
    document.getElementById('finished-box').style.display = 'block';
    document.getElementById('final-score').innerText = score;
    document.getElementById('score_to_save').value = score;
});

// ส่งฟอร์มด้วย AJAX
document.getElementById('score-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const statusP = document.getElementById('save-status');
    statusP.innerText = 'กำลังบันทึก...';

    fetch('save_score.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        statusP.innerText = data;
        statusP.style.color = 'green';
    })
    .catch(error => {
        statusP.innerText = 'เกิดข้อผิดพลาดในการบันทึก';
        statusP.style.color = 'red';
    });
});
</script>

<?php include "footer.php"; ?>
