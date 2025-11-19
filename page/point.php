<?php
// point.php
?>

<div class="row g-3">
  <section class="col-12 d-flex">
    <div class="card h-100 flex-fill w-100">
      <div class="card-header bg-white">
        <i class="bi bi-clipboard-data me-1"></i> คะแนน
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">ชื่อนักศึกษา</th>
                <th scope="col">คะแนนก่อนเรียน</th>
                <th scope="col">คะแนนหลังเรียน</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // ในส่วนนี้ คุณจะต้องเขียนโค้ดเพื่อดึงข้อมูลจากฐานข้อมูล
              // ตัวอย่างข้อมูล
              $students = [
                ['id' => 1, 'name' => 'นาย ก', 'pre_test_score' => 8, 'post_test_score' => 15],
                ['id' => 2, 'name' => 'นางสาว ข', 'pre_test_score' => 7, 'post_test_score' => 18],
                ['id' => 3, 'name' => 'นาย ค', 'pre_test_score' => 9, 'post_test_score' => 20],
              ];

              foreach ($students as $index => $student) {
                echo "<tr>";
                echo "<th scope='row'>" . ($index + 1) . "</th>";
                echo "<td>" . htmlspecialchars($student['name']) . "</td>";
                echo "<td>" . htmlspecialchars($student['pre_test_score']) . "</td>";
                echo "<td>" . htmlspecialchars($student['post_test_score']) . "</td>";
                echo "</tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
        <p class="mt-3">
          <strong>หมายเหตุ:</strong> ข้อมูลคะแนนก่อนเรียนและหลังเรียนจะมาจาก Google Form ที่คุณให้มา
          <a href="https://docs.google.com/forms/d/e/1FAIpQLSfQVZzYN2JN3vpKa8_a9yzZOAxlujNkgM22S1thXdSUHXaBEQ/viewform" target="_blank">ลิงก์ Google Form</a>
        </p>
      </div>
    </div>
  </section>
</div>
