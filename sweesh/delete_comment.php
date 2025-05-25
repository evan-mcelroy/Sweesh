<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $commentId = intval($_GET['id']);

    // Verify the comment belongs to the user
    $stmt = $conn->prepare("SELECT user_id FROM comments WHERE id = ?");
    $stmt->bind_param("i", $commentId);
    $stmt->execute();
    $stmt->bind_result($ownerId);
    $stmt->fetch();
    $stmt->close();

    if ($ownerId == $_SESSION['user_id']) {
        // Delete the comment
        $stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->bind_param("i", $commentId);
        $stmt->execute();
        $stmt->close();

        // Optionally: delete replies to this comment
        $stmt = $conn->prepare("DELETE FROM replies WHERE comment_id = ?");
        $stmt->bind_param("i", $commentId);
        $stmt->execute();
        $stmt->close();
    }
}

header('Location: index.php');
exit();
?>
