<?php
// --- ตั้งค่าการเชื่อมต่อฐานข้อมูล ---
$servername = "localhost";   // หรือ IP ของเซิร์ฟเวอร์ฐานข้อมูล
$username = "root";          // ชื่อผู้ใช้ของฐานข้อมูล (ค่าเริ่มต้นคือ root)
$password = "12345678";              // รหัสผ่าน (ค่าเริ่มต้นมักจะว่าง)
$dbname = "teaching_website"; // ชื่อฐานข้อมูลที่คุณสร้างไว้

// --- สร้างการเชื่อมต่อ ---
$conn = new mysqli($servername, $username, $password, $dbname);

// --- ตรวจสอบการเชื่อมต่อ ---
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- ตั้งค่า Character Set เป็น UTF-8 เพื่อรองรับภา<?php
/*
 * File: connect.php
 * This file is the central point for database connection.
 * All other files that need to access the database should include this file.
 */

// --- Database Configuration ---
// Replace these values with your actual database credentials.

// The server where your database is hosted (usually 'localhost' for local development).
$servername = "localhost";

// The username for your database. The default for XAMPP/MAMP is 'root'.
$username = "root";

// The password for your database user. The default for XAMPP/MAMP is an empty string ''.
$password = "12345678";

// The name of the database you want to connect to.
$dbname = "teaching_website";


// --- Create and Check Connection ---

// Create a new MySQLi object to establish the connection.
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection attempt resulted in an error.
if ($conn->connect_error) {
    // If there is a connection error, stop the script and display the error message.
    // This is crucial for debugging connection issues.
    die("Connection failed: " . $conn->connect_error);
}

// Set the character set to 'utf8' to ensure proper handling of Thai characters.
// This prevents issues with displaying or saving Thai language data.
if (!$conn->set_charset("utf8")) {
    // Optional: You can add an error message if setting the charset fails.
    // printf("Error loading character set utf8: %s\n", $conn->error);
}

// The connection is now established and ready to be used.
// The variable $conn holds the active database connection object.
?>

