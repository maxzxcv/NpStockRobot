<?php
// กำหนดอายุคุกกี้และ session ให้อยู่ได้นาน 1 วัน (86400 วินาที)
$lifetime = 86400; // 1 วัน

// ตั้งค่า session cookie parameters
session_set_cookie_params([
    'lifetime' => $lifetime, 
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => isset($_SERVER['HTTPS']), // ใช้ secure ถ้าเป็น HTTPS
    'httponly' => true, // ป้องกัน XSS
    'samesite' => 'Lax', // ป้องกันปัญหา session หายจากลิงก์ภายนอก
]);

// เริ่มต้น session
session_start();

// ตั้งค่า session.gc_maxlifetime เพื่อให้ session ไม่หมดอายุโดย PHP
ini_set('session.gc_maxlifetime', $lifetime);

// กำหนดค่า session save path (ถ้าจำเป็น)
session_save_path(__DIR__ . '/sessions');

// สร้าง ID เซสชันใหม่เพื่อป้องกันการ hijacking
session_regenerate_id(true);
?>
