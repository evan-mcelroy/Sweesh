<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $replyId = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM replies WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $replyId, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "You are not authorized to edit this reply.";
        exit;
    }

    $reply = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply_text'])) {
    $replyText = $_POST['reply_text'];
    $stmt = $conn->prepare("UPDATE replies SET reply_text = ? WHERE id = ?");
    $stmt->bind_param("si", $replyText, $replyId);
    $stmt->execute();
    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Reply</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ececf9;
            color: #2c2c2c;
            margin: 0;
            padding: 0;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: center;
            align-items: center; /* Center vertically */
            height: 100vh;
        }

        /* Container for the whole page */
        .container {
            width: 100%;
            max-width: 600px;
            padding: 20px;
        }

        /* Form Container */
        .form-container {
            background: linear-gradient(to bottom, #fafafa, #444df717);
            border: 1px solid #aaa;
            border-radius: 4px;
            padding: 30px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2), inset 0 1px 0 #fff;
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Form Labels */
        label {
            display: block;
            margin-bottom: 10px;
            font-size: 14px;
            color: #444cf7;
        }

        /* Form Inputs */
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
            resize: none; /* disabless resizing of the textarea */
        }

        /* Submit Button */
        button {
            background: linear-gradient(to bottom, #444cf7, #444cf7);
            color: #ffffff;
            padding: 10px 0;
            font-size: 16px;
            border: 1px solid #888;
            border-radius: 4px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2), inset 0 1px 0 #fff;
            width: 100%;
            text-shadow: none;
        }

        /* Button Hover */
        button:hover {
            background: linear-gradient(to bottom, #9ea2f7, #9ea2f7);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
        }

        /* Error Message */
        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }

        /* Back Link */
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

    <h2>Edit Reply</h2>
        <!-- Display error message if there's any -->
        <?php if (isset($errorMessage)): ?>
            <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>

        <form method="POST">
            <textarea style = resize: none; name="reply_text" rows="6" required><?= htmlspecialchars($reply['reply_text']) ?></textarea>
            <button type="submit">Update Reply</button>
            <a href="index.php">Back to Posts</a>
        </form>
    </div>
</div>

</body>
</html>
