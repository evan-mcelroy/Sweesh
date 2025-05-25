<?php
require 'config.php';

$errorMessage = '';  // Initialize error message variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Username already taken, show error message
        $errorMessage = "Username is already taken. Please choose another one.";
    } else {
        // Hash the password and insert the user into the database
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $passwordHash);
        $stmt->execute();
        
        // Redirect to login page after successful registration
        header("Location: login.php");
        exit;
    }

    $stmt->close();
}
?>

<style>
/* General Styles */
body {
    background-color: #ececf9;
    opacity: 1;
    background-size: 20px 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #2c2c2c;
    margin: 0;
    padding: 0;
    box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.2);
    height: 100vh; /* Full viewport height */
    display: flex;
    justify-content: center; /* Horizontally center */
    align-items: center; /* Vertically center */
}

/* Register Container */
.register-container {
    background: linear-gradient(to bottom, #fafafa, #444df717);
    border: 1px solid #aaa;
    border-radius: 4px;
    padding: 30px;
    width: 300px;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2), inset 0 1px 0 #fff;
    text-align: center;
}

/* Register Title */
h1 {
    color: #444cf7;
    margin-bottom: 20px;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
    font-size: 50px;
    font-weight: bold;
    padding: 10px 20px;
    display: inline-block;
    font-family: 'Papyrus', cursive;
}

/* Form Labels */
label {
    font-size: 14px;
    color: #444cf7;
    margin-bottom: 8px;
    display: block;
    font-weight: bold;
}

/* Input Fields */
input[type="text"], input[type="password"] {
    width: 100%; /* Make input fields span the full container */
    padding: 12px;
    margin-top: 10px;
    margin-bottom: 15px;
    border: 1px solid #aaa;
    border-radius: 4px;
    background: #fefefe;
    font-family: inherit;
    font-size: 12px;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
}

/* Submit Button */
button, input[type="submit"] {
    background: linear-gradient(to bottom, #444cf7, #444cf7);
    color: #ffffff;
    padding: 10px 0;
    font-size: 16px;
    border: 1px solid #888;
    border-radius: 4px;
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2), inset 0 1px 0 #fff;
    margin-top: 10px;
    width: 100%; /* Make button span the full container */
    text-shadow: none;
}

/* Submit Button Hover */
button:hover, input[type="submit"]:hover {
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

<form class='register-container' method="post">
<a href='index.php'><img src='includes/img/sweesh.png' alt='Sweesh' style='height: 75px;'></a>
<h2>Register</h2>

    <!-- Display error message if any -->
    <?php if ($errorMessage): ?>
        <p class="error-message"><?php echo $errorMessage; ?></p>
    <?php endif; ?>

    <label for="username">Username:</label>
    <input name="username" type="text" required><br>
    
    <label for="password">Password:</label>
    <input name="password" type="password" required><br>
    
    <button type="submit">Register</button>

    <!-- Link to Login page if user already has an account -->
    <div class="auth-links">
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</form>
