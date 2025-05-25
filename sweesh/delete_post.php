<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$postId = (int) $_GET['id'];

$conn->begin_transaction();

try {
    // Delete replies that belong to comments of this post
    $conn->query("
        DELETE replies FROM replies
        INNER JOIN comments ON replies.comment_id = comments.id
        WHERE comments.post_id = $postId
    ");

    // Delete comments that belong to this post
    $conn->query("DELETE FROM comments WHERE post_id = $postId");

    // Delete votes that belong to this post
    $conn->query("DELETE FROM votes WHERE post_id = $postId");

    // Delete the post itself
    $conn->query("DELETE FROM posts WHERE id = $postId");

    $conn->commit();

} catch (Exception $e) {
    $conn->rollback();
    echo "Failed to delete post: " . $e->getMessage();
    exit;
}

header('Location: index.php');
exit;
?>
