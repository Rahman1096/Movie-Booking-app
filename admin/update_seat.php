<?php
include('../connect.php');

// Check if the form is submitted for updating a seat
if (isset($_POST['seatid'])) {

    $seatid = $_POST['seatid'];
    $seatType = $_POST['seat_type'];
    $seatPrice = $_POST['price'];
    $availability = $_POST['availability'];

    // Ensure all fields are provided
    if (!$seatid || !$seatType || !$seatPrice) {
        echo json_encode(['error' => 'Please provide all fields.']);
        exit;
    }

    // Update seat data in the database
    $updateQuery = mysqli_query($con, "UPDATE seats SET seat_type = '$seatType', price = '$seatPrice', availability = '$availability' WHERE seatid = '$seatid'");

    if ($updateQuery) {
        echo json_encode(['success' => 'Seat updated successfully']);
    } else {
        echo json_encode(['error' => 'Failed to update seat']);
    }
} else {
    echo json_encode(['error' => 'No seat ID provided']);
}
?>
