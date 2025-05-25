<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $postId = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $postId, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "You are not authorized to edit this post.";
        exit;
    }

    $post = $result->fetch_assoc();
} else {
    echo "No post specified.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title']) && isset($_POST['content'])) {
    // Sanitize title and content before storing in the database
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    
    // Make sure neither title nor content are empty
    if (empty($title) || empty($content)) {
        $errorMessage = "Title and content cannot be empty.";
    } else {
        // Update the post with both title and content
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
        $stmt->bind_param("ssi", $title, $content, $postId);
        $stmt->execute();
        header('Location: index.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ececf9;
            color: #2c2c2c;
            margin: 0;
            padding: 20px;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Adjust to align the form properly */
            height: 100vh;
        }

        /* Container for the whole page */
        .container {
            width: 100%;
            max-width: 600px; /* Ensures the form does not stretch too wide */
            margin: 0 auto;
        }

        /* Form Container (Post Creation Form) */
        .form-container {
            background: linear-gradient(to bottom, #fafafa, #444df717);
            border: 1px solid #aaa;
            border-radius: 4px;
            padding: 30px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2), inset 0 1px 0 #fff;
            margin-top: 20px; /* Adds spacing between text and form */
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
            resize: none; /* Prevent resizing of textarea */
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
    <?php if (isset($post)): ?>
        <div class="form-container">

        <a href='index.php'><img src='includes/img/sweesh.png' alt='Sweesh' style='height: 100px;'></a>


        <h2>Edit Post</h2>

            <!-- Display error message if there's any -->
            <?php if (isset($errorMessage)): ?>
                <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
            <?php endif; ?>

            <form method="POST">
                <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required><br><br>

                <textarea style = resize: none; name="content" rows="4" required><?= htmlspecialchars($post['content']) ?></textarea><br><br>

                <button type="submit">Update Post</button>
                <a href="index.php">Back to Posts</a>

            </form>
        </div>
    <?php else: ?>
        <p>No post found for editing.</p>
    <?php endif; ?>

</div>

</body>
</html>
