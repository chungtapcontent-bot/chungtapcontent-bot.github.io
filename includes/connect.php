<?php
$servername = "localhost";
$username = "root";     
$password = "";        
$dbname = "web_ban_do_pet";   

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối đến Cơ sở dữ liệu thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

