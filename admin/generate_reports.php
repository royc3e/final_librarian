<?php
include('../includes/db.php');

// Check if user is an Admin
session_start();
if ($_SESSION['user_type'] !== 'Admin') {
    header('Location: ../login.php');
    exit();
}

// Example: Generate list of overdue books
$sql = "SELECT Transactions.TransactionID, Users.Name, LibraryResources.Title, Transactions.DueDate
        FROM Transactions
        JOIN Users ON Transactions.UserID = Users.UserID
        JOIN LibraryResources ON Transactions.ResourceID = LibraryResources.ResourceID
        WHERE Transactions.ReturnDate IS NULL AND Transactions.DueDate < CURDATE()";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Reports</title>
</head>
<body>
    <h1>Overdue Books Report</h1>
    <table>
        <tr>
            <th>Transaction ID</th>
            <th>User Name</th>
            <th>Book Title</th>
            <th>Due Date</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['TransactionID'] ?></td>
            <td><?= $row['Name'] ?></td>
            <td><?= $row['Title'] ?></td>
            <td><?= $row['DueDate'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
