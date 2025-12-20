<?php
// Set timezone cho Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Tự động detect môi trường (Docker hoặc XAMPP)
if (getenv('DOCKER_ENV') || file_exists('/.dockerenv')) {
    // Chạy trong Docker - kết nối đến MySQL trên host (XAMPP)
    $servername = "host.docker.internal";
    $username = "root";
    $password = "";
    $dbname = "tthuong_store";
} else {
    // Chạy trong XAMPP
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "tthuong_store";
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4"); 