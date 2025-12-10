# Database Table Usage Report (Active Only)

จากการตรวจสอบ Codebase เฉพาะไฟล์ที่มีการใช้งานจริงในระบบ (ไม่นับส่วนทดสอบหรือไฟล์เก่า) สรุปการใช้งาน Table ได้ดังนี้ครับ:

## Table ที่มีการใช้งานจริง (Active Tables)

1.  **tb_member**
    *   **ใช้ทำอะไร:** เก็บข้อมูลสมาชิก ตรวจสอบการ Login และแสดงข้อมูลผู้เรียน
    *   **ไฟล์ที่ใช้:** `index.php`, `User.php`, `page/user_list.php`, `module/member/chksession.php`

2.  **tb_content**
    *   **ใช้ทำอะไร:** เก็บข้อมูลบทเรียน ลำดับบทเรียน และไฟล์วิดีโอ
    *   **ไฟล์ที่ใช้:** `index.php`, `page/content.php`, `page/video_and_unit.php`, `page/add_unit.php`

3.  **tb_work**
    *   **ใช้ทำอะไร:** เก็บข้อมูลการบ้านและงานที่มอบหมาย
    *   **ไฟล์ที่ใช้:** `page/work.php`, `page/user_list.php`

4.  **tb_notice**
    *   **ใช้ทำอะไร:** เก็บข่าวสารประกาศหน้าเว็บ
    *   **ไฟล์ที่ใช้:** `page/home.php`, `page/notice.php`, `page/notice_save.php`

5.  **tb_test**
    *   **ใช้ทำอะไร:** เก็บชุดข้อสอบสำหรับแต่ละบทเรียน
    *   **ไฟล์ที่ใช้:** `page/content.php`, `page/save_answer.php`

6.  **tb_scores**
    *   **ใช้ทำอะไร:** เก็บคะแนนการทำแบบฝึกหัดรายบทของผู้เรียน
    *   **ไฟล์ที่ใช้:** `page/save_score.php`, `page/user_list.php`

7.  **tb_exam_scores**
    *   **ใช้ทำอะไร:** เก็บคะแนนสอบวัดผล (Pre-test / Post-test)
    *   **ไฟล์ที่ใช้:** `page/user_list.php`

8.  **tb_transcripts**
    *   **ใช้ทำอะไร:** เก็บคำบรรยาย (Subtitle) ของวิดีโอ
    *   **ไฟล์ที่ใช้:** `page/content.php`

9.  **tb_work_submissions**
    *   **ใช้ทำอะไร:** เก็บข้อมูลการส่งงานของนักเรียน (ไฟล์งาน, เวลานำส่ง)
    *   **ไฟล์ที่ใช้:** `page/user_list.php`

---

## Table ที่ไม่ได้ใช้งานในระบบหลัก (Unused / Legacy)

*   `questions` และ `choices`: พบใน `page/add_quiz_demo.php` ซึ่งเป็นหน้าทดสอบที่ **ไม่มีลิงก์เชื่อมโยงมาจากหน้าไหนเลย**
*   `exercises1_questions` และ `exercises1_video`: ใช้ในโฟลเดอร์ `pjpt/` ซึ่งเป็นระบบแยกต่างหาก ไม่ได้ถูกเรียกใช้ในเว็บหลัก
