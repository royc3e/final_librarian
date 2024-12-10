<?php
include('../includes/db.php');



// Delete User
if (isset($_GET['delete_user'])) {
    $userID = $_GET['delete_user'];
    $sql = "DELETE FROM Users WHERE UserID = '$userID'";
    $conn->query($sql);
    echo "User deleted successfully!";
}

// Fetch all users
$sql = "SELECT * FROM Users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
</head>
<body>
    <h1>Manage Users</h1>
    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>User Type</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['Name'] ?></td>
            <td><?= $row['ContactInfo'] ?></td>
            <td><?= $row['UserType'] ?></td>
            <td>
                <a href="edit_user.php?user_id=<?= $row['UserID'] ?>">Edit</a> | 
                <a href="?delete_user=<?= $row['UserID'] ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
