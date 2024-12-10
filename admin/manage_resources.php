<?php
include('../includes/db.php');
session_start();



// Add Resource Functionality
if (isset($_POST['add_resource'])) {
    $resourceType = $_POST['resource_type'];
    $title = $_POST['title'];
    $category = $_POST['category'];
    
    // Generate Accession Number
    $year = date('Y');
    $seqNumber = 1;  // Default sequence number
    $resourceID = "";

    // Generate unique sequence number for the Accession Number
    $sqlSeq = "SELECT MAX(CAST(SUBSTRING(AccessionNumber, 5) AS UNSIGNED)) AS MaxSequence
               FROM LibraryResources WHERE AccessionNumber LIKE '$resourceType-$year-%'";
    $resultSeq = $conn->query($sqlSeq);
    if ($resultSeq->num_rows > 0) {
        $row = $resultSeq->fetch_assoc();
        $seqNumber = $row['MaxSequence'] + 1;
    }

    $accessionNumber = "$resourceType-$year-" . str_pad($seqNumber, 3, '0', STR_PAD_LEFT);
    
    // Insert common data into LibraryResources table
    $sqlResource = "INSERT INTO LibraryResources (Title, Category, AccessionNumber) 
                    VALUES ('$title', '$category', '$accessionNumber')";
    $conn->query($sqlResource);

    // Fetch the ResourceID of the newly inserted resource
    $resourceID = $conn->insert_id;

    // Insert specific data based on resource type
    if ($resourceType == 'B') {
        // For Books
        $author = $_POST['author'];
        $isbn = $_POST['isbn'];
        $publisher = $_POST['publisher'];
        $edition = $_POST['edition'];
        $publicationDate = $_POST['publication_date'];

        $sqlBook = "INSERT INTO Books (ResourceID, Author, ISBN, Publisher, Edition, PublicationDate)
                    VALUES ('$resourceID', '$author', '$isbn', '$publisher', '$edition', '$publicationDate')";
        $conn->query($sqlBook);
    } elseif ($resourceType == 'P') {
        // For Periodicals
        $issn = $_POST['issn'];
        $volume = $_POST['volume'];
        $issue = $_POST['issue'];
        $publicationDate = $_POST['publication_date'];

        $sqlPeriodical = "INSERT INTO Periodicals (ResourceID, ISSN, Volume, Issue, PublicationDate)
                          VALUES ('$resourceID', '$issn', '$volume', '$issue', '$publicationDate')";
        $conn->query($sqlPeriodical);
    } elseif ($resourceType == 'M') {
        // For Media Resources (e.g., DVDs, CDs)
        $format = $_POST['format'];
        $runtime = $_POST['runtime'];
        $mediaType = $_POST['media_type'];

        $sqlMedia = "INSERT INTO MediaResources (ResourceID, Format, Runtime, MediaType)
                     VALUES ('$resourceID', '$format', '$runtime', '$mediaType')";
        $conn->query($sqlMedia);
    }

    echo "Resource added successfully!";
}

// Fetch all resources to display in the table
$sql = "SELECT * FROM LibraryResources";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Resources</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Manage Library Resources</h1>
    </header>

    <div class="container">
        <h2>Add New Resource</h2>
        <form action="manage_resources.php" method="POST">
            <select name="resource_type" id="resource_type" required>
                <option value="B">Book</option>
                <option value="P">Periodical</option>
                <option value="M">Media</option>
            </select><br>
            <input type="text" name="title" placeholder="Title" required><br>
            <input type="text" name="category" placeholder="Category" required><br>

            <!-- Book-specific fields -->
            <div id="book_fields" style="display: none;">
                <input type="text" name="author" placeholder="Author"><br>
                <input type="text" name="isbn" placeholder="ISBN"><br>
                <input type="text" name="publisher" placeholder="Publisher"><br>
                <input type="text" name="edition" placeholder="Edition"><br>
                <input type="date" name="publication_date"><br>
            </div>

            <!-- Periodical-specific fields -->
            <div id="periodical_fields" style="display: none;">
                <input type="text" name="issn" placeholder="ISSN"><br>
                <input type="text" name="volume" placeholder="Volume"><br>
                <input type="text" name="issue" placeholder="Issue"><br>
                <input type="date" name="publication_date"><br>
            </div>

            <!-- Media-specific fields -->
            <div id="media_fields" style="display: none;">
                <input type="text" name="format" placeholder="Format"><br>
                <input type="text" name="runtime" placeholder="Runtime"><br>
                <input type="text" name="media_type" placeholder="Media Type"><br>
            </div>

            <button type="submit" name="add_resource">Add Resource</button>
        </form>

        <h2>Existing Resources</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Accession Number</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['AccessionNumber']; ?></td>
                        <td><?php echo $row['Title']; ?></td>
                        <td><?php echo $row['Category']; ?></td>
                        <td>
                            <a href="edit_resource.php?id=<?php echo $row['ResourceID']; ?>">Edit</a> |
                            <a href="manage_resources.php?delete=<?php echo $row['ResourceID']; ?>" onclick="return confirm('Are you sure you want to delete this resource?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
        // Show/Hide fields based on resource type selection
        document.getElementById('resource_type').addEventListener('change', function () {
            var resourceType = this.value;
            document.getElementById('book_fields').style.display = (resourceType === 'B') ? 'block' : 'none';
            document.getElementById('periodical_fields').style.display = (resourceType === 'P') ? 'block' : 'none';
            document.getElementById('media_fields').style.display = (resourceType === 'M') ? 'block' : 'none';
        });

        // Trigger the change event to load the correct fields initially
        document.getElementById('resource_type').dispatchEvent(new Event('change'));
    </script>
</body>
</html>

<?php
$conn->close();
?>
