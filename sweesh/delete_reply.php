<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

if (!isset($_GET['id'])) {
    die("No reply ID provided.");
}

$replyId = intval($_GET['id']);

// First, check if the user owns the reply
$stmt = $conn->prepare("SELECT * FROM replies WHERE id = ?");
$stmt->bind_param("i", $replyId);
$stmt->execute();
$result = $stmt->get_result();
$reply = $result->fetch_assoc();

if (!$reply) {
    die("Reply not found.");
}

if ($reply['user_id'] != $_SESSION['user_id']) {
    die("You are not authorized to delete this reply.");
}

// Delete any child replies (replies to this reply)
$deleteChildReplies = $conn->prepare("DELETE FROM replies WHERE parent_reply_id = ?");
$deleteChildReplies->bind_param("i", $replyId);
$deleteChildReplies->execute();

// Delete the main reply
$deleteReply = $conn->prepare("DELETE FROM replies WHERE id = ?");
$deleteReply->bind_param("i", $replyId);
if ($deleteReply->execute()) {
    header("Location: index.php");
    exit();
} else {
    die("Failed to delete reply: " . $conn->error);
}



?>
