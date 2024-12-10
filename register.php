<?php
include('db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>User Registration</h1>
    </header>

    <div class="container">
        <form action="process.php" method="POST">
            <input type="text" name="name" placeholder="Full Name" required><br>
            <input type="email" name="contact" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            
            <!-- Updated dropdown to include Admin as a user type -->
            <select name="user_type" required>
                <option value="Student">Student</option>
                <option value="Faculty">Faculty</option>
                <option value="Staff">Staff</option>
                <option value="Admin">Admin</option> <!-- New Admin option -->
            </select><br>
            
            <button type="submit" name="register">Register</button>
        </form>
    </div>
</body>
</html>
