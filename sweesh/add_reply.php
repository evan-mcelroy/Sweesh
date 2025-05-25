<?php
session_start();
require 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get the data from the form
$commentId = $_POST['comment_id'];
$parentReplyId = isset($_POST['parent_reply_id']) ? $_POST['parent_reply_id'] : null;
$replyText = $_POST['reply_text'];
$postId = $_POST['post_id']; // The post ID is being passed through the form

// Insert the reply into the database
$stmt = $conn->prepare("INSERT INTO replies (comment_id, parent_reply_id, user_id, reply_text, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("iiis", $commentId, $parentReplyId, $_SESSION['user_id'], $replyText);

if ($stmt->execute()) {
    // Redirect back to the index page (or the post page if you prefer)
    header("Location: index.php");
} else {
    echo "Error: " . $stmt->error;
}
?>
