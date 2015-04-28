# TheptarinCobasInterface
ขั้นตอนการเชื่อมข้อมูลผู้ป่วยระหว่าง AS400 กับ Cobas1000
ระบบ Cobas จะส่งไฟล์ผลน้ำตาลเป็น text file 
ระบบ Link HN. รออ่านไฟล์ผลทุกนาที
เปิดไฟล์ผลหาบรรทัด PID|1||XXXXX| เพื่อคัด HN. ตรงส่วนที่เป็น XXXXX
ระบบ Link HN. นำ HN. ค้นหาข้อมูลผู้ป่วยจาก MySQL
ระบบ LInk HN. สร้างไฟล์ ADT กลับให้ Cobas
