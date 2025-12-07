<?php
// work.php
?>

<div class="row g-3">
  <section class="col-12">
    <div class="card">
      <div class="card-header bg-white">
        <i class="bi bi-briefcase me-1"></i> งานที่มอบหมาย
      </div>
      <div class="card-body">
        <?php if (isset($_SESSION['sess_username']) && $_SESSION['sess_username'] === 'kamol'): ?>
          
          <!-- Button to trigger modal -->
          <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addWorkModal">
            <i class="bi bi-plus-circle me-1"></i> เพิ่มงานใหม่
          </button>

          <!-- Modal for Adding Work -->
          <div class="modal fade" id="addWorkModal" tabindex="-1" aria-labelledby="addWorkModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="addWorkModalLabel">ฟอร์มเพิ่มงาน</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form action="page/work_save.php" method="post" enctype="multipart/form-data" id="addWorkForm">
                    <div class="mb-3">
                      <label for="workTitle" class="form-label">ชื่องาน</label>
                      <input type="text" class="form-control" id="workTitle" name="work_title" required>
                    </div>
                    <div class="mb-3">
                      <label for="workDescription" class="form-label">คำอธิบาย</label>
                      <textarea class="form-control" id="workDescription" name="work_description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                      <label for="workFile" class="form-label">แนบไฟล์ (PDF File) (ถ้ามี)</label>
                      <input class="form-control" type="file" id="workFile" name="work_file">
                    </div>
                    <div class="mb-3">
                      <label for="dueDate" class="form-label">กำหนดส่ง</label>
                      <input type="date" class="form-control" id="dueDate" name="due_date" required>
                    </div>
                    <button type="submit" class="btn btn-success">บันทึกงาน</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <hr>
          
        <?php endif; ?>

        <!-- Student and Admin View -->
        <h5 class="card-title">รายการงาน</h5>
        
        <!-- Example of an assignment item -->
        <div class="list-group">
          <div class="list-group-item">
            <div class="d-flex w-100 justify-content-between">
              <h6 class="mb-1">แบบฝึกหัดท้ายบทที่ 1</h6>
              <small class="text-muted">ส่งภายใน: 30 พ.ย. 2568</small>
            </div>
            <p class="mb-1">ให้นักศึกษาทำแบบฝึกหัดท้ายบทที่ 1 และส่งในรูปแบบไฟล์ PDF</p>
            
            <?php if (isset($_SESSION['sess_username']) && $_SESSION['sess_username'] === 'kamol'): ?>
            <div class="mt-2">
                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil-square"></i> แก้ไข</button>
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> ลบ</button>
            </div>
            <?php else: ?>
            <div class="mt-2">
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#submitWorkModal" data-work-title="แบบฝึกหัดท้ายบทที่ 1">
                    <i class="bi bi-upload"></i> ส่งงาน
                </button>
            </div>
            <?php endif; ?>
          </div>
          
           <div class="list-group-item">
            <div class="d-flex w-100 justify-content-between">
              <h6 class="mb-1">รายงานกลุ่ม: สถาปัตยกรรมเครือข่าย</h6>
              <small class="text-muted">ส่งภายใน: 15 ธ.ค. 2568</small>
            </div>
            <p class="mb-1">จัดกลุ่ม 3-4 คน จัดทำรายงานเกี่ยวกับสถาปัตยกรรมเครือข่ายคอมพิวเตอร์ 5 ประเภท พร้อมยกตัวอย่าง</p>
            
            <?php if (isset($_SESSION['sess_username']) && $_SESSION['sess_username'] === 'kamol'): ?>
            <div class="mt-2">
                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil-square"></i> แก้ไข</button>
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> ลบ</button>
            </div>
            <?php else: ?>
            <div class="mt-2">
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#submitWorkModal" data-work-title="รายงานกลุ่ม: สถาปัตยกรรมเครือข่าย">
                    <i class="bi bi-upload"></i> ส่งงาน
                </button>
            </div>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>
  </section>
</div>

<!-- Modal for Submitting Work -->
<div class="modal fade" id="submitWorkModal" tabindex="-1" aria-labelledby="submitWorkModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="submitWorkModalLabel">ส่งงาน: </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="page/work_save.php" method="post" enctype="multipart/form-data" id="submitWorkForm">
          <input type="hidden" id="submissionWorkTitle" name="work_title">
          <div class="mb-3">
            <label for="submissionText" class="form-label">ข้อความ</label>
            <textarea class="form-control" id="submissionText" name="submission_text" rows="5" placeholder="พิมพ์ข้อความ..."></textarea>
          </div>
          <div class="mb-3">
            <label for="submissionFile" class="form-label">แนบไฟล์</label>
            <input class="form-control" type="file" id="submissionFile" name="submission_file">
          </div>
          <button type="submit" class="btn btn-primary">ส่งงาน</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var submitWorkModal = document.getElementById('submitWorkModal');
  if (submitWorkModal) {
      submitWorkModal.addEventListener('show.bs.modal', function (event) {
        // Button that triggered the modal
        var button = event.relatedTarget;
        // Extract info from data-bs-* attributes
        var workTitle = button.getAttribute('data-work-title');
        
        // Update the modal's content.
        var modalTitle = submitWorkModal.querySelector('.modal-title');
        var workTitleInput = submitWorkModal.querySelector('#submissionWorkTitle');

        modalTitle.textContent = 'ส่งงาน: ' + workTitle;
        workTitleInput.value = workTitle;
      });
  }
});
</script>
