# Walkthrough - Student Score Table (ตารางคะแนนนักศึกษา)

I have implemented the feature to display and manage Pre-test and Post-test scores for students in the admin dashboard.

## Changes
### Database
- Created a new table `tb_exam_scores` to store:
  - `student_id`
  - `exam_type` ('pre_test', 'post_test')
  - `score`
  - `full_score`

### User Interface (`user_list.php`)
- Added a new section **"คะแนนสอบ (Pre-test / Post-test)"** in the student details accordion.
- Displays current scores for Pre-test and Post-test.
- Added an **Edit** button (pencil icon) that reveals a form to input/update scores manually.
- **All UI labels are in Thai** as requested.

## How to Verify
1.  **Login as Admin** (Username: `kamol`).
2.  Navigate to the **Student List** page (รายชื่อนักศึกษา).
3.  Click on any student's name to expand their details.
4.  Scroll down to the "คะแนนสอบ (Pre-test / Post-test)" section.
5.  Click the **Edit** button (pencil icon) next to "สอบก่อนเรียน" or "สอบหลังเรียน".
6.  Enter the score and full score.
7.  Click **บันทึก (Save)**.
8.  The page will reload and show the updated score.

## Screenshots
*(No screenshots available as I am running in a headless environment, but the code has been verified)*

## Update: Course Description (2025-11-27)
- Updated `task.md` with detailed course information.
- Updated `page/home.php` to display the full course description.
- **UI Improvement**: Implemented a "Show More / Show Less" toggle button to keep the interface clean.
  - Initially shows only the Course Name and Code.
  - Clicking the arrow expands to show Subject Code, Credits, Description, and CLOs.
