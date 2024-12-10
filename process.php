<?php
 include('db.php');

// Register User
if (isset($_POST['register'])) {
    // Sanitize and validate input
    $name = $_POST['name'];
    $email = $_POST['contact'];
    $userType = $_POST['user_type'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert the user into the Users table (including the password)
    $sql = "INSERT INTO Users (Name, UserType, ContactInfo, Password) VALUES ('$name', '$userType', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Login User
if (isset($_POST['login'])) {
    // Check if email and password fields are set before accessing
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Sanitize input to prevent SQL Injection
        $email = mysqli_real_escape_string($conn, $email);
        $password = mysqli_real_escape_string($conn, $password);

        // Query to get user data based on email
        $sql = "SELECT * FROM Users WHERE ContactInfo = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $row['Password'])) {
                echo "Login successful!";
                // Start session and redirect to user dashboard
                session_start();
                $_SESSION['user_id'] = $row['UserID'];
                $_SESSION['user_type'] = $row['UserType'];
                header("Location: dashboard.php"); // Redirect to the dashboard page
                exit();
            } else {
                echo "Invalid password!";
            }
        } else {
            echo "User not found!";
        }
    } else {
        echo "Please enter both email and password!";
    }
}
?>
