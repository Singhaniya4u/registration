<?php
$directoryPath = 'include/uploads/images/'; // Relative path
$images = glob($directoryPath . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

// Handle search query
$searchQuery = isset($_POST['search']) ? strtolower(trim($_POST['search'])) : '';

$filteredImages = array_filter($images, function($image) use ($searchQuery) {
    $filename = strtolower(basename($image));
    return strpos($filename, $searchQuery) !== false;
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Gallery</title>
    <link rel="stylesheet" href="style.css">
    <style>
        input{
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

<h2>Image Gallery</h2>

<!-- Search Form -->
<form method="post" style="text-align: center;" action="">
    <input type="text" name="search" placeholder="Search for an Image..." value="<?php echo htmlspecialchars($searchQuery); ?>">
    <button type="submit">Search</button>
</form>

<div style="display: flex; flex-wrap: wrap; margin-top: 20px;">
    <?php
    
    // Check if there are filtered images to display
    if ($filteredImages) {
        foreach ($filteredImages as $image) {
            // Convert to a web-accessible URL
            $webPath = htmlspecialchars($image);
            $filename = htmlspecialchars(basename($image));

            // Display the image and its name
            echo '<div style="margin: 10px; display: inline-block; text-align: center;">';
            echo '<img src="' . $webPath . '" alt="image" style="width:200px; height:auto; margin-bottom: 5px;">';
            echo '<div>' . $filename . '</div>'; // Display filename
            echo '</div>';
        }
    } else {
        // echo "Search for the Image";
    }
    ?>
</div>
<div>
</div>
<footer>
    <?php include 'footer.php'?>
</footer>
</body>
</html>
