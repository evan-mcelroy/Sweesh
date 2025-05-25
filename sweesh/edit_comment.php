<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $commentId = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM comments WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $commentId, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "You are not authorized to edit this comment.";
        exit;
    }

    $comment = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment_text'])) {
    $commentText = $_POST['comment_text'];
    $stmt = $conn->prepare("UPDATE comments SET comment_text = ? WHERE id = ?");
    $stmt->bind_param("si", $commentText, $commentId);
    $stmt->execute();
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Comment</title>
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ececf9;
            color: #2c2c2c;
            margin: 0;
            padding: 20px;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100vh;
        }

        .container {
            width: 200%;
            max-width: 600px;
            margin: 0 auto;
        }

        .form-container {
            background: linear-gradient(to bottom, #fafafa, #444df717);
            border: 1px solid #aaa;
            border-radius: 4px;
            padding: 30px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2), inset 0 1px 0 #fff;
            margin-top: 20px;
        }

        h1 {
            color:rgb(0, 0, 0);
            font-size: 32px;
            font: 'Papyrus', cursive;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-size: 14px;
            color: #444cf7;
        }

        input[type="text"], textarea {
            width: 96%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #aaa;
            border-radius: 4px;
            background: #fefefe;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
            font-family: inherit;
            font-size: 14px;
            resize: none; /* Prevent resizing of textarea */
        }

        button {
            background: linear-gradient(to bottom, #444cf7, #444cf7);
            color: #ffffff;
            padding: 10px 0;
            font-size: 16px;
            border: 1px solid #888;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2), inset 0 1px 0 #fff;
        }

        button:hover {
            background: linear-gradient(to bottom, #9ea2f7, #9ea2f7);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }

        a {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #333;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
    <a href='index.php'><img src='includes/img/sweesh.png' alt='Sweesh' style='height: 100px;'></a>
    <h1>Edit Comment</h1>
        <form method="POST">
            <textarea styles=resize:none; name="comment_text" rows="6" required><?= htmlspecialchars($comment['comment_text']) ?></textarea><br>
            <button type="submit">Update Comment</button>
            <a href="index.php">Back to Posts</a>

        </form>
    </div>

</div>

</body>
</html>
