<?php
// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$servername = "db"; // Docker MySQL service name
$username = "root";
$password = "root";
$dbname = "ip"; // เปลี่ยนเป็นชื่อฐานข้อมูลของคุณ

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>