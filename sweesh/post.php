<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    die("Login required.");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $_SESSION['user_id'], $_POST['title'], $_POST['content']);
    $stmt->execute();
    header("Location: index.php");
}
?>
<form method="post">
    Title: <input name="title" required><br>
    Content: <textarea name="content" required></textarea><br>
    <button type="submit">Post</button>
</form>