<!-- Hero -->
  <header class="hero py-5">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <h1 class="display-5 fw-bold mb-3">เรียนเขียนโปรแกรมออนไลน์แบบเป็นระบบ</h1>
          <p class="lead text-body-secondary mb-4">คอร์สคุณภาพ เส้นทางการเรียนชัดเจน วิดีโอแบบอินเทอร์แอคทีฟพร้อมแบบทดสอบ และระบบติดตามความก้าวหน้า</p>
          <div class="d-flex gap-2">
            <a href="#courses" class="btn btn-primary btn-lg"><i class="bi bi-rocket-takeoff me-2"></i>เริ่มต้นเรียน</a>
            <button class="btn btn-outline-secondary btn-lg" data-bs-toggle="modal" data-bs-target="#videoModal"><i class="bi bi-play-circle me-2"></i>ดูตัวอย่าง</button>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="card shadow-sm border-0">
            <div class="card-body p-4">
              <h5 class="card-title mb-3">ค้นหาคอร์สที่ใช่</h5>
              <div class="row g-2">
                <div class="col-md-6">
                  <label class="form-label">หมวดหมู่</label>
                  <select class="form-select">
                    <option>ทั้งหมด</option>
                    <option>Frontend</option>
                    <option>Backend</option>
                    <option>Data/AI</option>
                    <option>Mobile</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">ระดับ</label>
                  <select class="form-select">
                    <option>ทั้งหมด</option>
                    <option>เริ่มต้น</option>
                    <option>กลาง</option>
                    <option>เชี่ยวชาญ</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">คำค้นหา</label>
                  <input type="text" class="form-control" placeholder="เช่น React, PHP, SQL, Machine Learning">
                </div>
                <div class="col-12 d-grid">
                  <a href="#courses" class="btn btn-primary">ค้นหา</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Highlights / Features -->
  <section class="py-5">
    <div class="container">
      <div class="row g-4 text-center">
        <div class="col-md-3">
          <i class="bi bi-journal-code fs-1 text-primary"></i>
          <h6 class="mt-3 mb-1">คอร์สคุณภาพ</h6>
          <p class="text-body-secondary small mb-0">อัปเดตเนื้อหาให้ทันสมัย พร้อมตัวอย่างโค้ดใช้งานจริง</p>
        </div>
        <div class="col-md-3">
          <i class="bi bi-ui-checks-grid fs-1 text-primary"></i>
          <h6 class="mt-3 mb-1">เรียนเป็นเส้นทาง</h6>
          <p class="text-body-secondary small mb-0">แผนการเรียนครบ ตั้งแต่พื้นฐานจนสร้างโปรเจกต์</p>
        </div>
        <div class="col-md-3">
          <i class="bi bi-clipboard2-check fs-1 text-primary"></i>
          <h6 class="mt-3 mb-1">แบบทดสอบในวิดีโอ</h6>
          <p class="text-body-secondary small mb-0">เช็คความเข้าใจระหว่างเรียน พร้อมเฉลยเมื่อทำผิด</p>
        </div>
        <div class="col-md-3">
          <i class="bi bi-graph-up-arrow fs-1 text-primary"></i>
          <h6 class="mt-3 mb-1">ติดตามความก้าวหน้า</h6>
          <p class="text-body-secondary small mb-0">ระบบบันทึกความคืบหน้าและคะแนนของผู้เรียน</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Course List -->
  <section id="courses" class="py-5 bg-body-tertiary">
    <div class="container">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="h3 mb-0">คอร์สแนะนำ</h2>
        <a class="btn btn-outline-primary btn-sm" href="#">ดูทั้งหมด</a>
      </div>

      <div class="row g-4">
        <!-- Course Card -->
        <div class="col-md-6 col-lg-4">
          <div class="card course-card h-100 border-0 shadow-sm">
            <img src="https://images.unsplash.com/photo-1516116216624-53e697fedbe9?q=80&w=1200&auto=format&fit=crop" class="card-img-top" alt="Frontend Web"/>
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge text-bg-primary badge-level">Beginner</span>
                <span class="small text-body-secondary"><i class="bi bi-clock me-1"></i>12 ชม.</span>
              </div>
              <h5 class="card-title">Frontend Web ด้วย HTML5, CSS3, Bootstrap 5</h5>
              <p class="card-text text-body-secondary small">สร้างเว็บสวย มืออาชีพ รองรับมือถือ พร้อมโครงสร้างที่ถูกต้อง</p>
              <div class="d-flex align-items-center gap-2 small text-body-secondary mb-3">
                <i class="bi bi-bar-chart"></i><span>โปรเจกต์ 3 ชิ้น</span>
                <i class="bi bi-stars ms-2"></i><span>4.8/5</span>
              </div>
              <div class="d-grid gap-2">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#videoModal">ดูตัวอย่าง</button>
                <a href="#courseDetail" class="btn btn-outline-secondary" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="courseDetail">ดูรายละเอียด</a>
              </div>
            </div>
          </div>
        </div>
        <!-- Course Card -->
        <div class="col-md-6 col-lg-4">
          <div class="card course-card h-100 border-0 shadow-sm">
            <img src="https://images.unsplash.com/photo-1518779578993-ec3579fee39f?q=80&w=1200&auto=format&fit=crop" class="card-img-top" alt="Backend"/>
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge text-bg-success badge-level">Intermediate</span>
                <span class="small text-body-secondary"><i class="bi bi-clock me-1"></i>15 ชม.</span>
              </div>
              <h5 class="card-title">พัฒนา Backend ด้วย PHP 8 + MySQL</h5>
              <p class="card-text text-body-secondary small">สร้าง REST API, จัดการฐานข้อมูล และระบบหลังบ้านอย่างปลอดภัย</p>
              <div class="d-flex align-items-center gap-2 small text-body-secondary mb-3">
                <i class="bi bi-bar-chart"></i><span>โปรเจกต์ 2 ชิ้น</span>
                <i class="bi bi-stars ms-2"></i><span>4.7/5</span>
              </div>
              <div class="d-grid gap-2">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#videoModal">ดูตัวอย่าง</button>
                <a href="#courseDetail" class="btn btn-outline-secondary" data-bs-toggle="collapse">ดูรายละเอียด</a>
              </div>
            </div>
          </div>
        </div>
        <!-- Course Card -->
        <div class="col-md-6 col-lg-4">
          <div class="card course-card h-100 border-0 shadow-sm">
            <img src="https://images.unsplash.com/photo-1515879218367-8466d910aaa4?q=80&w=1200&auto=format&fit=crop" class="card-img-top" alt="React"/>
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge text-bg-warning badge-level">Advanced</span>
                <span class="small text-body-secondary"><i class="bi bi-clock me-1"></i>10 ชม.</span>
              </div>
              <h5 class="card-title">สร้างเว็บแอปด้วย React + Bootstrap</h5>
              <p class="card-text text-body-secondary small">เข้าใจ state, props, hook และการจัดการฟอร์ม/ตาราง/โมดัล</p>
              <div class="d-flex align-items-center gap-2 small text-body-secondary mb-3">
                <i class="bi bi-bar-chart"></i><span>โปรเจกต์ 1 ชิ้น</span>
                <i class="bi bi-stars ms-2"></i><span>4.9/5</span>
              </div>
              <div class="d-grid gap-2">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#videoModal">ดูตัวอย่าง</button>
                <a href="#courseDetail" class="btn btn-outline-secondary" data-bs-toggle="collapse">ดูรายละเอียด</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Collapsible Course Detail (sample) -->
      <div class="collapse mt-4" id="courseDetail">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="row g-4">
              <div class="col-lg-4">
                <div class="list-group list-group-flush lesson-sidebar">
                  <a href="#" class="list-group-item list-group-item-action active" aria-current="true">
                    <i class="bi bi-play-btn me-2"></i> บทที่ 1: แนะนำหลักสูตร
                  </a>
                  <a href="#" class="list-group-item list-group-item-action"><i class="bi bi-play-btn me-2"></i>บทที่ 2: โครงสร้างโปรเจกต์</a>
                  <a href="#" class="list-group-item list-group-item-action"><i class="bi bi-play-btn me-2"></i>บทที่ 3: ตั้งค่า Bootstrap</a>
                  <a href="#" class="list-group-item list-group-item-action"><i class="bi bi-question-circle me-2"></i>Quiz 1: เนื้อหาพื้นฐาน</a>
                  <a href="#" class="list-group-item list-group-item-action"><i class="bi bi-play-btn me-2"></i>บทที่ 4: Layout + Components</a>
                  <a href="#" class="list-group-item list-group-item-action"><i class="bi bi-code-square me-2"></i>Project: Landing Page</a>
                </div>
              </div>
              <div class="col-lg-8">
                <div class="ratio ratio-16x9 rounded overflow-hidden">
                  <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="ตัวอย่างวิดีโอ" allowfullscreen></iframe>
                </div>
                <div class="d-flex align-items-center gap-2 mt-3">
                  <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quizModal"><i class="bi bi-ui-checks me-2"></i>ทำแบบทดสอบ</button>
                  <button class="btn btn-outline-secondary" onclick="resetOnlyVideo()"><i class="bi bi-arrow-counterclockwise me-2"></i>รีเซ็ตวิดีโอ</button>
                  <div class="ms-auto small text-body-secondary">ความคืบหน้า: <span class="fw-semibold">60%</span></div>
                </div>
                <hr>
                <h6>รายละเอียดคอร์ส</h6>
                <p class="text-body-secondary">เรียนรู้การสร้างหน้าเว็บด้วย Bootstrap 5 ตั้งแต่พื้นฐาน grid system, utilities, ไปจนถึง component ยอดนิยม เช่น Navbar, Card, Modal พร้อมตัวอย่าง UX จริง</p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- Paths / Learning Tracks -->
  <section id="paths" class="py-5">
    <div class="container">
      <h2 class="h3 mb-4">เส้นทางการเรียน</h2>
      <div class="row g-4">
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <h5 class="card-title">Frontend Developer</h5>
              <ul class="small text-body-secondary mb-3">
                <li>HTML5 & CSS3 พื้นฐาน</li>
                <li>Bootstrap 5 / Responsive</li>
                <li>JavaScript ขั้นพื้นฐาน → ขั้นกลาง</li>
                <li>React เบื้องต้น</li>
              </ul>
              <a href="#" class="btn btn-outline-primary btn-sm">เริ่มเส้นทางนี้</a>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <h5 class="card-title">Backend Developer</h5>
              <ul class="small text-body-secondary mb-3">
                <li>พื้นฐาน PHP 8</li>
                <li>MySQL & SQL</li>
                <li>REST API & Authentication</li>
                <li>การ Deploy</li>
              </ul>
              <a href="#" class="btn btn-outline-primary btn-sm">เริ่มเส้นทางนี้</a>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <h5 class="card-title">Data / AI</h5>
              <ul class="small text-body-secondary mb-3">
                <li>Python พื้นฐาน</li>
                <li>Pandas / Visualization</li>
                <li>ML เบื้องต้น</li>
                <li>โปรเจกต์สรุป</li>
              </ul>
              <a href="#" class="btn btn-outline-primary btn-sm">เริ่มเส้นทางนี้</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Pricing -->
  <section id="pricing" class="py-5 bg-body-tertiary">
    <div class="container">
      <h2 class="h3 mb-4">แพ็กเกจราคา</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent text-center">
              <h6 class="my-2">Basic</h6>
            </div>
            <div class="card-body text-center">
              <h3 class="fw-bold">฿0</h3>
              <p class="text-body-secondary small">คอร์สฟรีบางส่วน · ไม่มีใบประกาศ</p>
              <ul class="list-unstyled small text-start mx-auto" style="max-width: 240px;">
                <li><i class="bi bi-check-circle me-2"></i>วิดีโอตัวอย่าง</li>
                <li><i class="bi bi-check-circle me-2"></i>แบบทดสอบขั้นพื้นฐาน</li>
                <li><i class="bi bi-x-circle me-2"></i>ไม่มีดาวน์โหลดไฟล์</li>
              </ul>
              <a href="#" class="btn btn-outline-primary w-100">เริ่มใช้</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100 border-primary-subtle">
            <div class="card-header bg-primary-subtle text-center">
              <h6 class="my-2">Pro (ยอดนิยม)</h6>
            </div>
            <div class="card-body text-center">
              <h3 class="fw-bold">฿490/เดือน</h3>
              <p class="text-body-secondary small">เข้าถึงคอร์สทั้งหมด · ใบประกาศ</p>
              <ul class="list-unstyled small text-start mx-auto" style="max-width: 260px;">
                <li><i class="bi bi-check-circle me-2"></i>คอร์สทั้งหมดไม่จำกัด</li>
                <li><i class="bi bi-check-circle me-2"></i>ดาวน์โหลดโค้ดตัวอย่าง</li>
                <li><i class="bi bi-check-circle me-2"></i>ใบประกาศเมื่อเรียนจบ</li>
              </ul>
              <a href="#" class="btn btn-primary w-100">สมัคร Pro</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent text-center">
              <h6 class="my-2">Team / School</h6>
            </div>
            <div class="card-body text-center">
              <h3 class="fw-bold">กำหนดเอง</h3>
              <p class="text-body-secondary small">สำหรับทีม องค์กร หรือโรงเรียน</p>
              <ul class="list-unstyled small text-start mx-auto" style="max-width: 260px;">
                <li><i class="bi bi-check-circle me-2"></i>ที่นั่งผู้ใช้หลายบัญชี</li>
                <li><i class="bi bi-check-circle me-2"></i>แดชบอร์ดผู้สอน/แอดมิน</li>
                <li><i class="bi bi-check-circle me-2"></i>ซิงก์คะแนนผ่าน API</li>
              </ul>
              <a href="#" class="btn btn-outline-primary w-100">ติดต่อฝ่ายขาย</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section id="faq" class="py-5">
    <div class="container">
      <h2 class="h3 mb-4">คำถามที่พบบ่อย</h2>
      <div class="accordion" id="faqAcc">
        <div class="accordion-item">
          <h2 class="accordion-header" id="q1h">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#q1" aria-expanded="true" aria-controls="q1">
              เรียนได้ตลอดเวลาหรือไม่?
            </button>
          </h2>
          <div id="q1" class="accordion-collapse collapse show" aria-labelledby="q1h" data-bs-parent="#faqAcc">
            <div class="accordion-body">ได้ เรียนได้ทุกอุปกรณ์ ตลอด 24 ชั่วโมง</div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="q2h">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q2" aria-expanded="false" aria-controls="q2">
              มีแบบทดสอบและเฉลยหรือไม่?
            </button>
          </h2>
          <div id="q2" class="accordion-collapse collapse" aria-labelledby="q2h" data-bs-parent="#faqAcc">
            <div class="accordion-body">มี แบบทดสอบแทรกในวิดีโอ พร้อมเฉลยทันทีเมื่อทำผิด</div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="q3h">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q3" aria-expanded="false" aria-controls="q3">
              สามารถขอใบประกาศได้หรือไม่?
            </button>
          </h2>
          <div id="q3" class="accordion-collapse collapse" aria-labelledby="q3h" data-bs-parent="#faqAcc">
            <div class="accordion-body">ได้ สำหรับแพ็กเกจ Pro และ Team</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Admin note -->
  <section id="adminNote" class="py-5 bg-body-tertiary">
    <div class="container">
      <div class="alert alert-info border-0 shadow-sm" role="alert">
        <i class="bi bi-gear-wide-connected me-2"></i>
        <strong>โครงสำหรับผู้ดูแลระบบ:</strong> ส่วนนี้สามารถเชื่อมต่อไปยังหน้า Admin เพื่อเพิ่ม/แก้ไข คอร์ส บทเรียน วิดีโอ และแบบทดสอบ รวมถึงส่งคะแนนไปยังฐานข้อมูลหรือ Google Sheets ตามที่ต้องการ
      </div>
    </div>
  </section>
