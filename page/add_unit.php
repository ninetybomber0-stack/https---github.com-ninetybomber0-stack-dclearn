<?php
// page/add_unit.php

// Only allow admin access
if (!isset($_SESSION['sess_username']) || $_SESSION['sess_username'] !== 'kamol') {
    // Using the same access denied message as in user_list.php
?>
<div class="row g-3">
  <section class="col-12">
    <div class="alert alert-danger text-center" role="alert">
      <h4 class="alert-heading">ปฏิเสธการเข้าถึง</h4>
      <p>คุณไม่มีสิทธิ์ในการดูหน้านี้ หน้านี้สำหรับผู้ดูแลระบบเท่านั้น</p>
      <hr>
      <p class="mb-0">กรุณาติดต่อผู้ดูแลระบบหากคุณเชื่อว่านี่เป็นข้อผิดพลาด</p>
    </div>
  </section>
</div>
<?php
    return; // Stop further execution
}

// --- Logic to save form data would go here ---
$is_saved = false;
if (isset($_POST['unit_name']) && !empty(trim($_POST['unit_name']))) {
    $unit_name = trim($_POST['unit_name']);
    
    // --- เพิ่มโค้ดบันทึกลงฐานข้อมูล ---
    $stmt = $mysqli->prepare("INSERT INTO tb_content (name) VALUES (?)");
    if ($stmt) {
        $stmt->bind_param('s', $unit_name);
        if ($stmt->execute()) {
            $is_saved = true;
        }
        $stmt->close();
    }
}

?>

<section id="add-unit">
    <h2 class="h5 section-title mb-3">เพิ่มหน่วยการเรียนใหม่</h2>

    <?php if ($is_saved): ?>
        <div class="alert alert-success">
            <strong>บันทึกสำเร็จ!</strong> เพิ่มหน่วยการเรียน "<?= htmlspecialchars($unit_name, ENT_QUOTES, 'UTF-8') ?>" เรียบร้อยแล้ว
            <div class="mt-2">
                <a href="index.php?page=plane" class="btn btn-sm btn-outline-primary">กลับไปหน้าแผนการเรียน</a>
                <a href="index.php?page=add_unit" class="btn btn-sm btn-primary">เพิ่มหน่วยการเรียนอีก</a>
            </div>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="index.php?page=add_unit">
                <div class="mb-3">
                    <label for="unit_name" class="form-label">ชื่อหน่วยการเรียน</label>
                    <input type="text" class="form-control" id="unit_name" name="unit_name" required placeholder="เช่น ความรู้เบื้องต้นเกี่ยวกับเครือข่าย">
                </div>
                
                <!-- Add other fields for tb_content here if needed -->
                <!-- e.g., <textarea class="form-control" name="description"></textarea> -->
                
                <div class="d-flex justify-content-end gap-2">
                    <a href="index.php?page=plane" class="btn btn-outline-secondary">ยกเลิก</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
