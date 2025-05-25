<?php
require 'config.php';

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $title = htmlspecialchars(trim($_POST['title']));
    $content = htmlspecialchars(trim($_POST['content']));

    // Ensure the title and content are not empty
    if (!empty($title) && !empty($content)) {
        $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $_SESSION['user_id'], $title, $content);
        $stmt->execute();
        header("Location: index.php");
        exit;
    } else {
        echo "<p class='error-message'>Please fill out both the title and content fields.</p>";
    }
}
?>

<form method="post" class="form-container">
    <a href='index.php'><img src='includes/img/sweesh.png' alt='Sweesh' style='height: 100px;'></a>
    <h2>Create a New Post</h2>

    <!-- Post Title -->
    <label for="title">Title:</label>
    <input name="title" type="text" required><br>

    <!-- Post Content -->
    <label for="content">Content:</label>
    <textarea name="content" required></textarea><br>

    <button type="submit">Post</button>

    <a class=''href="index.php">Back to Posts</a>

</form>



<style>
   /* General Styles */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #2c2c2c;
    margin: 0;
    padding: 20px;
    background-color: #ececf9;
    box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.2);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Form Container (Post Creation Form) */
.form-container {
    background: linear-gradient(to bottom, #fafafa, #444df717);
    border: 1px solid #aaa;
    border-radius: 4px;
    padding: 30px;
    width: 400px;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2), inset 0 1px 0 #fff;
    text-align: center;
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

/* title */
h2 {
    font-size: 24px;
    color:rgb(0, 0, 0);
    margin-bottom: 20px;
}

/* Form Labels */
label {
    font-size: 14px;
    color:rgb(0, 0, 0);
    margin-bottom: 8px;
    display: block;
    font-weight: bold;
}

/* Input Fields */
input[type="text"], textarea {
    width: 100%;
    padding: 12px;
    margin-top: 10px;
    margin-bottom: 15px;
    border: 1px solid #aaa;
    border-radius: 4px;
    background: #fefefe;
    font-family: inherit;
    font-size: 12px;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
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
    margin-top: 10px;
    width: 100%;
    text-shadow: none;
}

/* Submit Button Hover */
button:hover {
    background: linear-gradient(to bottom, #9ea2f7, #9ea2f7);
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
}

/* Error Message */
.error-message {
    color: red;
    font-size: 14px;
    margin-bottom: 10px;
}

/* Authentication Links */
.auth-links {
    text-align: center;
    margin-top: 15px;
}

.auth-links a {
    color: #444cf7;
    text-decoration: none;
    font-size: 14px;
    display: inline-block;
    margin-top: 10px;
}

.auth-links a:hover {
    text-decoration: underline;
}

</style>
