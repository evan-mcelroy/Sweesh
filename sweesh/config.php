<?php
$conn = new mysqli("db-address", "db-user", "db-password", "db-name");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();
?>