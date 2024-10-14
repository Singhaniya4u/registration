<?php
// Path for PDFs
$pdfDirectoryPath = 'include/uploads/resume/'; // Path for PDFs

// Get PDFs
$pdfs = glob($pdfDirectoryPath . '*.pdf');

// Handle search query for PDFs
$searchQuery = isset($_POST['search']) ? strtolower(trim($_POST['search'])) : '';
//echo "Search Query: " . htmlspecialchars($searchQuery) . "<br>"; // Debug output

$filteredPdfs = array_filter($pdfs, function ($pdf) use ($searchQuery) {
    $filename = strtolower(basename($pdf));
    return strpos($filename, $searchQuery) !== false;
});

// Debug output for filtered PDFs
// if (!empty($pdfs)) {
//     echo "Found PDFs:<br>";
//     foreach ($pdfs as $pdf) {
//         echo htmlspecialchars(basename($pdf)) . "<br>";
//     }
// } else {
//     echo "No PDFs found in the directory.<br>";
// }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Gallery</title>
    <link rel="stylesheet" href="style.css">
    <style>
        input {
            border-radius: 50px;
            width: 300px;
            padding-left: 10px;
        }

        button {
            background-color: #586470;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            margin: 10px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <h2>PDF Gallery</h2>

    <!-- Search Form -->
    <form method="post" style="text-align:center" action="">
        <input type="text" name="search" placeholder="Search for a Resume..."
            value="<?php echo htmlspecialchars($searchQuery); ?>">
        <button type="submit">Search</button>
    </form>

    <!-- PDFs Section -->
    <div style="display: flex; flex-wrap: wrap; margin-top: 20px;">
        <?php
        // Check if there are filtered PDFs to display
        if ($filteredPdfs) {
            foreach ($filteredPdfs as $pdf) {
                $webPath = htmlspecialchars($pdf);
                $filename = htmlspecialchars(basename($pdf));
                echo '<div style="margin: 10px; display: inline-block; text-align: center;">';
                echo '<a href="' . $webPath . '" target="_blank" style="text-decoration: none; color: black;">';
                echo '<img src="/Shivnath/Image/pdf.png" alt="pdf" style="width:100px; height:auto; margin-bottom: 5px;">';
                echo '<div>' . $filename . '</div>';
                echo '</a>';
                echo '</div>';
            }
        } else {
            // echo "Search for the Resume";
        }
        ?>
    </div>

    <footer>
        <?php include 'footer.php'; ?>
    </footer>
</body>

</html>