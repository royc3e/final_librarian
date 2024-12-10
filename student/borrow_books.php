<?php
include('../includes/db.php');

// Check if user is a Student
session_start();
if ($_SESSION['user_type'] !== 'Student') {
    header('Location: ../login.php');
    exit();
}

// Fetch available books
$sql = "SELECT * FROM LibraryResources WHERE ResourceID NOT IN (SELECT ResourceID FROM Transactions WHERE UserID = {$_SESSION['user_id']} AND ReturnDate IS NULL)";
$result = $conn->query($sql);

// Borrow book functionality (limit 3 books)
if (isset($_POST['borrow'])) {
    $resourceID = $_POST['resource_id'];

    // Check how many books the user has borrowed
    $sqlCheck = "SELECT COUNT(*) AS borrowed_count FROM Transactions WHERE UserID = {$_SESSION['user_id']} AND ReturnDate IS NULL";
    $checkResult = $conn->query($sqlCheck);
    $checkData = $checkResult->fetch_assoc();

    if ($checkData['borrowed_count'] < 3) {
        // Borrow the book
        $dueDate = date('Y-m-d', strtotime('+7 days')); // 1 week due date
        $sql = "INSERT INTO Transactions (UserID, ResourceID, BorrowDate, DueDate) VALUES ({$_SESSION['user_id']}, '$resourceID', NOW(), '$dueDate')";
        $conn->query($sql);

        echo "Book borrowed successfully! Due date: $dueDate";
    } else {
        echo "You can borrow a maximum of 3 books only.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow Book</title>
</head>
<body>
    <h1>Borrow a Book</h1>
    <form action="borrow_book.php" method="POST">
        <select name="resource_id" required>
            <?php while ($row = $result->fetch_assoc()): ?>
            <option value="<?= $row['ResourceID'] ?>"><?= $row['Title'] ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit" name="borrow">Borrow Book</button>
    </form>
</body>
</html>
