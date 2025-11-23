tb_content 
- ID คือลำดับข้อมูลอะไรมาก่อนมาหลัง 
- Name ชื่อของแต่ละอย่างตามลำดับ
- lesson-index  
- title ชื่อของงานชิ้นนั้นที่ตั้งไว้ 
- lesson order ลำดับบทเรียน
- video_part_720,480  คลิปของแต่ละบท 
- poster_part รูปปกคลิป??
- slide_url pdfslideของแต่ละบท
- quiz_url  linkคำถาม??
- score คะแนนของบทนั้นๆ
- created_at  แต่ละอย่างเอาเข้าระบบมาวันไหนตอนไหน 


 

(ยังไม่ใช้)tb_Transcripts ทั้งหน้าคืออะไร? จากที่runมาไม่ได้ใช้สักอย่าง 

-id
-lesion_id_text
-cue_time
-text

exercise1_questions

-id  ลำดับ
-question คำถาม
-option1 ตัวเลือก1
-option2 ตัวเลือก2	
-option3 ตัวเลือก 3
-option4 ตัวเลือก4
-answer คำตอบที่ถูก
-timecode เวลาที่จะเด่งมาถาม

exercise1_video

-id ลำดับ
-filename ชื่อไฟล์
-id_c ???
-uploaded_at upขึ้นเมื่อไหร่



Tb_member

-id ลำดับ
-id-std accountที่ใช้(รหัส นศ)
-fullname  ชื่อคน
-class นศ ห้องไหน
-password รหัสผ่านเข้าระบบ?

tb_notice

-id ลำดับ
 -message  ข้อความที่adminสร้าง
-time เวลาที่upขึ้นระบบบ

tb_score

-id ลำดับ
-studen_id ชื่อaccount 
-lession_id บทที่เท่าไหร่
-score  คะแนน
-submitted_at upขึ้นระบบเมื่อไหร่



