<?php
session_start();

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'webblog';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

function isAuthor() {
    return isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'author']);
}
?>