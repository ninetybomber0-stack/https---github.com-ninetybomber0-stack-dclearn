<?php
// edit_user.php (วางในโฟลเดอร์ page/)
// ไฟล์นี้ใช้ตัวแปร $mysqli จากไฟล์ index.php

$user_id = $_GET['id'] ?? null; // รับค่า ID ของนักศึกษาที่ต้องการแก้ไขจาก URL

$message = ''; // สำหรับแสดงข้อความแจ้งเตือน

// --- 1. ตรรกะการประมวลผลการแก้ไข (UPDATE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    
    // รับค่าที่ส่งมาจากฟอร์ม
    $id = $_POST['id'];
    $fullname = $_POST['fullname'];
    $class = $_POST['class'];
    $email = $_POST['email']; 
    
    // คำสั่ง SQL UPDATE
    // *หมายเหตุ: ตรวจสอบให้แน่ใจว่าชื่อคอลัมน์ในตาราง tb_member ถูกต้องตามนี้*
    $sql_update = "UPDATE tb_member SET fullname=?, class=?, email=? WHERE id=?";
    
    $stmt = $mysqli->prepare($sql_update);
    $stmt->bind_param("sssi", $fullname, $class, $email, $id);

    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">✅ บันทึกการแก้ไขข้อมูลสำเร็จแล้ว!</div>';
        // อัปเดต ID เพื่อให้ฟอร์มแสดงข้อมูลล่าสุดทันที
        $user_id = $id; 
    } else {
        $message = '<div class="alert alert-danger">❌ เกิดข้อผิดพลาดในการบันทึก: ' . $stmt->error . '</div>';
    }
    $stmt->close();
}

// --- 2. ตรรกะการดึงข้อมูลเดิมมาแสดงในฟอร์ม (SELECT) ---
if ($user_id) {
    $sql_select = "SELECT id, id_std, fullname, class, email FROM tb_member WHERE id=?";
    $stmt_select = $mysqli->prepare($sql_select);
    $stmt_select->bind_param("i", $user_id);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();

    if ($result_select->num_rows === 1) {
        $user_data = $result_select->fetch_assoc();
    } else {
        $message = '<div class="alert alert-warning">ไม่พบข้อมูลนักศึกษาที่ต้องการแก้ไข</div>';
        $user_data = null;
    }
    $stmt_select->close();
} else {
    $user_data = null;
    $message = '<div class="alert alert-warning">กรุณาระบุ ID นักศึกษา</div>';
}
?>

<div class="card p-4 mt-4">
    <h3 class="mb-3">✏️ แก้ไขข้อมูลนักศึกษา</h3>
    
    <?php echo $message; // แสดงข้อความแจ้งเตือน ?>
    
    <?php if ($user_data): ?>
        <form action="index.php?page=edit_user" method="POST">
            
            <input type="hidden" name="id" value="<?= htmlspecialchars($user_data['id']) ?>">
            <input type="hidden" name="update_user" value="1">

            <div class="mb-3">
                <label class="form-label fw-semibold">รหัสนักศึกษา</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($user_data['id_std']) ?>" disabled>
            </div>
            
            <div class="mb-3">
                <label for="fullname" class="form-label fw-semibold">ชื่อ-นามสกุล</label>
                <input type="text" class="form-control" id="fullname" name="fullname" 
                       value="<?= htmlspecialchars($user_data['fullname']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="class" class="form-label fw-semibold">ชั้น/กลุ่ม</label>
                <input type="text" class="form-control" id="class" name="class" 
                       value="<?= htmlspecialchars($user_data['class']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?= htmlspecialchars($user_data['email']) ?>" required>
            </div>
            
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> บันทึกการแก้ไข</button>
            <a href="index.php?page=User" class="btn btn-secondary">ยกเลิก</a>
        </form>
    <?php endif; ?>
</div>