<?php
session_start();
include('../includes/db.php');




// Handle resource addition
if (isset($_POST['add_resource'])) {
    $resourceType = $_POST['resource_type'];
    $title = $_POST['title'];
    $category = $_POST['category'];
    
    // Generate Accession Number based on resource type
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Resource</title>
</head>
<body>
    <h1>Add New Resource</h1>
    <form action="add_resources.php" method="POST">
        <label for="resource_type">Resource Type:</label>
        <select name="resource_type" id="resource_type" required>
            <option value="B">Book</option>
            <option value="P">Periodical</option>
            <option value="M">Media</option>
        </select><br>

        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required><br>

        <label for="category">Category (e.g., Fiction, Academic, Reference):</label>
        <input type="text" name="category" id="category" required><br>

        <!-- Book-specific fields -->
        <div id="book_fields" style="display: none;">
            <label for="author">Author:</label>
            <input type="text" name="author" id="author"><br>

            <label for="isbn">ISBN:</label>
            <input type="text" name="isbn" id="isbn"><br>

            <label for="publisher">Publisher:</label>
            <input type="text" name="publisher" id="publisher"><br>

            <label for="edition">Edition:</label>
            <input type="text" name="edition" id="edition"><br>

            <label for="publication_date">Publication Date:</label>
            <input type="date" name="publication_date" id="publication_date"><br>
        </div>

        <!-- Periodical-specific fields -->
        <div id="periodical_fields" style="display: none;">
            <label for="issn">ISSN:</label>
            <input type="text" name="issn" id="issn"><br>

            <label for="volume">Volume:</label>
            <input type="text" name="volume" id="volume"><br>

            <label for="issue">Issue:</label>
            <input type="text" name="issue" id="issue"><br>

            <label for="publication_date">Publication Date:</label>
            <input type="date" name="publication_date" id="publication_date"><br>
        </div>

        <!-- Media-specific fields -->
        <div id="media_fields" style="display: none;">
            <label for="format">Format:</label>
            <input type="text" name="format" id="format"><br>

            <label for="runtime">Runtime:</label>
            <input type="text" name="runtime" id="runtime"><br>

            <label for="media_type">Media Type:</label>
            <input type="text" name="media_type" id="media_type"><br>
        </div>

        <button type="submit" name="add_resource">Add Resource</button>
    </form>

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
