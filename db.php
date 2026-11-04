<?php
// ข้อมูลการเชื่อมต่อ Supabase PostgreSQL
$host = "db.nsnaewdbpzdhwpasrqgf.supabase.co"; // โฮสต์จาก Supabase ของคุณ
$port = "5432";                                 // พอร์ตมาตรฐานของ PostgreSQL
$dbname = "postgres";                           // ชื่อฐานข้อมูลหลักของ Supabase
$user = "postgres";                             // ชื่อผู้ใช้เริ่มต้น
$password = "ใส่รหัสผ่านฐานข้อมูลของคุณที่นี่";       // รหัสผ่านที่คุณตั้งไว้ตอนสร้างโปรเจกต์ Supabase

try {
    // เชื่อมต่อด้วย PDO สำหรับ PostgreSQL พร้อมบังคับใช้ SSL
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    $conn = new PDO($dsn, $user, $password);
    
    // ตั้งค่า Error Mode เป็น Exception เพื่อให้จับข้อผิดพลาดได้ง่าย
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // หากเชื่อมต่อไม่สำเร็จ ให้แสดงข้อความแจ้งเตือน
    die("Connection failed: " . $e->getMessage());
}
?>
