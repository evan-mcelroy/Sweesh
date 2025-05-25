<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $postId = (int)$_POST['post_id'];
    $commentText = trim($_POST['comment_text']);

    if (!empty($commentText)) {
        $stmt = $conn->prepare("INSERT INTO comments (user_id, post_id, comment_text) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $userId, $postId, $commentText);
        $stmt->execute();
    }
}

header("Location: index.php");
exit;
