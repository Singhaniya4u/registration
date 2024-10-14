<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check for single or multiple IDs
    if (isset($_POST['ids'])) {
        // Decode the JSON array of IDs
        $ids = json_decode($_POST['ids'], true); // Use true to get an associative array

        if (is_array($ids) && !empty($ids)) {
            // Prepare the SQL statement to delete records
            $idList = implode(',', array_map('intval', $ids)); // Ensure IDs are integers
            $stmt = $link->prepare("UPDATE student SET deleted = 1 WHERE id IN ($idList)");

            if ($stmt) {
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo json_encode(['status' => 'success', 'message' => 'Records deleted successfully.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'No records found or update failed.']);
                }
                $stmt->close();
            } else {
                echo json_encode(['status' => 'error', 'message' => "Error preparing statement: " . $link->error]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No valid IDs provided.']);
        }
    } elseif (isset($_POST['id'])) {
        // Handle single ID deletion
        $id = $_POST['id'];
        $stmt = $link->prepare("UPDATE student SET deleted = 1 WHERE id = ?");

        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo json_encode(['status' => 'success', 'message' => 'Record deleted successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No records found or update failed.']);
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => "Error preparing statement: " . $link->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No ID provided.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
