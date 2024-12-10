<?php
include('../includes/db.php');

// Check if user is an Admin
session_start();
if ($_SESSION['user_type'] !== 'Admin') {
    header('Location: ../login.php');
    exit();
}

// Add Book Functionality
if (isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $publisher = $_POST['publisher'];
    $edition = $_POST['edition'];
    $publicationDate = $_POST['publication_date'];
    $category = $_POST['category'];

    // Insert book into the library
    $sql = "INSERT INTO LibraryResources (Title, Category, AccessionNumber) VALUES ('$title', '$category', CONCAT('B-', YEAR(CURRENT_DATE), '-', LPAD((SELECT COUNT(*) + 1 FROM LibraryResources WHERE YEAR(CURRENT_DATE) = YEAR(CURRENT_DATE)), 3, '0')))";
    $conn->query($sql);

    // Fetch ResourceID for the newly inserted book
    $resourceID = $conn->insert_id;

    // Insert book-specific details
    $sqlBook = "INSERT INTO Books (ResourceID, Author, ISBN, Publisher, Edition, PublicationDate) VALUES ('$resourceID', '$author', '$isbn', '$publisher', '$edition', '$publicationDate')";
    $conn->query($sqlBook);

    echo "Book added successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
</head>
<body>
    <h1>Add New Book</h1>
    <form action="add_book.php" method="POST">
        <input type="text" name="title" placeholder="Title" required><br>
        <input type="text" name="author" placeholder="Author" required><br>
        <input type="text" name="isbn" placeholder="ISBN" required><br>
        <input type="text" name="publisher" placeholder="Publisher" required><br>
        <input type="text" name="edition" placeholder="Edition" required><br>
        <input type="date" name="publication_date" required><br>
        <input type="text" name="category" placeholder="Category (e.g., Book)" required><br>
        <button type="submit" name="add_book">Add Book</button>
    </form>
</body>
</html>
