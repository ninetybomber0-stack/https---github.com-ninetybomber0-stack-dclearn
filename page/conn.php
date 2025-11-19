    <!-- MODULES -->
    <section id="modules" class="mb-4">
      <div class="d-flex justify-content-between align-items-end mb-2">
        <h2 class="h5 section-title mb-0">หัวข้อบทเรียน</h2>
        <a class="link-muted" href="#"><i class="bi bi-journal-text me-1"></i>ดาวน์โหลด Course Outline (PDF)</a>
      </div>
      <div id="moduleGrid" class="row g-3">
        <!-- Cards -->
      </div>
    </section>

    <script>
            // ข้อมูลโมดูล (สามารถผูก API ภายหลัง)
    const modules = [
      {id:1, title:'สัปดาห์ที่ 1: บทนำและพื้นฐานการสื่อสารข้อมูล', tag:'ความหมาย ความสำคัญ องค์ประกอบระบบ ประเภทเครือข่าย LAN/WAN/MAN', minutes:60, href:'#'},
      {id:2, title:'สัปดาห์ที่ 2: สถาปัตยกรรมเครือข่ายและโมเดล OSI', tag:'OSI 7 Layers เปรียบเทียบ OSI vs TCP/IP', minutes:90, href:'#', badge:'สำคัญ'},
      {id:3, title:'สัปดาห์ที่ 3: การเชื่อมต่อและอุปกรณ์เครือข่าย', tag:'Router Switch Hub Modem Access Point Firewall สายสัญญาณ', minutes:80, href:'#'},
      {id:4, title:'สัปดาห์ที่ 4: การส่งข้อมูลและสื่อกลางการสื่อสาร', tag:'Analog vs Digital UTP Fiber วิทยุ', minutes:85, href:'#'},
      {id:5, title:'สัปดาห์ที่ 5: โปรโตคอลและมาตรฐานเครือข่าย', tag:'HTTP FTP SMTP POP3 IMAP IEEE Ethernet', minutes:90, href:'#'},
      {id:6, title:'สัปดาห์ที่ 6: การจัดการที่อยู่ IP และ DNS', tag:'IPv4 IPv6 Subnetting CIDR DNS', minutes:120, href:'#', badge:'ลงมือทำ'},
      {id:7, title:'สัปดาห์ที่ 7: การรักษาความปลอดภัยเครือข่าย', tag:'ภัยคุกคาม Malware Phishing DoS/DDoS การป้องกัน Encryption Auth VPN', minutes:90, href:'#'},
      {id:8, title:'สัปดาห์ที่ 8: เครือข่ายไร้สาย', tag:'Wi‑Fi Bluetooth Cellular ความปลอดภัยและการตั้งค่าสำหรับธุรกิจ', minutes:80, href:'#'},
      {id:9, title:'สัปดาห์ที่ 9: Cloud Computing และการสื่อสารข้อมูลในธุรกิจ', tag:'IaaS PaaS SaaS การใช้งานและการจัดการข้อมูลบน Cloud', minutes:85, href:'#'},
      {id:10, title:'สัปดาห์ที่ 10: Internet of Things (IoT) และการประยุกต์ใช้', tag:'องค์ประกอบ IoT การสื่อสาร ตัวอย่างการใช้งานในธุรกิจ', minutes:85, href:'#'},
      {id:11, title:'สัปดาห์ที่ 11: Big Data และเครือข่าย', tag:'ความสัมพันธ์เครือข่ายกับการประมวลผลข้อมูล กรณีศึกษา', minutes:90, href:'#'},
      {id:12, title:'สัปดาห์ที่ 12: แนวโน้มเทคโนโลยีเครือข่ายในอนาคต', tag:'5G and Beyond AI for Networking ธุรกิจยุคดิจิทัล', minutes:75, href:'#'},
      {id:13, title:'สัปดาห์ที่ 13: ทบทวนและประเมินผล', tag:'ทบทวนทั้งรายวิชา กิจกรรมวิเคราะห์กรณีศึกษา สอบปลายภาค', minutes:60, href:'#', badge:'สอบ'}
    ];
</script>