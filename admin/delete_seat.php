<?php
include('../connect.php');

// Function to validate and delete a seat by ID
function deleteSeat($seatId) {
    global $con;
    // Prepare the DELETE query
    $query = "DELETE FROM seats WHERE seatid = ?";

    // Prepare the statement
    if ($stmt = mysqli_prepare($con, $query)) {
        // Bind the parameter
        mysqli_stmt_bind_param($stmt, "i", $seatId);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                return ['success' => 'Seat deleted successfully.'];
            } else {
                return ['error' => 'Seat ID not found.'];
            }
        } else {
            return ['error' => 'Error deleting seat: ' . mysqli_stmt_error($stmt)];
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        return ['error' => 'Error preparing the query: ' . mysqli_error($con)];
    }
}

// Check if the seatid is passed via GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['seatid'])) {
    $seatId = $_GET['seatid'];

    // Validate the seat ID
    if (!is_numeric($seatId)) {
        echo json_encode(['error' => 'Invalid seat ID provided.']);
        exit;
    }

    // Attempt to delete the seat
    $result = deleteSeat($seatId);
    echo json_encode($result);
} else {
    echo json_encode(['error' => 'Invalid request. Seat ID must be provided.']);
}
?>
