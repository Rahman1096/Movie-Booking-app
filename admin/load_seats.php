<?php
include('../connect.php');

// Function to validate and retrieve the screen name
function getScreenName($screenid) {
    global $con;
    $screenQuery = mysqli_query($con, "SELECT screen_name FROM screens WHERE screenid = '$screenid'");
    if ($screenQuery && mysqli_num_rows($screenQuery) > 0) {
        return mysqli_fetch_assoc($screenQuery)['screen_name'];
    }
    return null;
}

// Function to fetch seats for a given screen
function getSeats($screenid) {
    global $con;
    $seatsQuery = mysqli_query($con, "SELECT * FROM seats WHERE screenid = '$screenid'");
    if (!$seatsQuery) {
        return null;
    }

    $seats = [];
    while ($row = mysqli_fetch_assoc($seatsQuery)) {
        $seats[] = [
            'seatid' => $row['seatid'],
            'seat_number' => $row['seat_number'],
            'seat_type' => $row['seat_type'],
            'price' => $row['price'],
            'availability' => $row['availability'] == 1 ? 'Available' : 'Unavailable',
        ];
    }
    return $seats;
}

// Check if 'screenid' is set in the GET request
if (isset($_GET['screenid'])) {
    $screenid = $_GET['screenid'];

    // Validate screen ID
    if (!is_numeric($screenid)) {
        echo json_encode(['error' => 'Invalid Screen ID format']);
        exit;
    }

    // Fetch the screen name
    $screenName = getScreenName($screenid);
    if (!$screenName) {
        echo json_encode(['error' => 'Screen not found']);
        exit;
    }

    // Fetch all seats for the given screen
    $seats = getSeats($screenid);
    if ($seats === null) {
        echo json_encode(['error' => 'Error fetching seats']);
        exit;
    }

    // Return the screen name and seats data in JSON format
    echo json_encode([
        'screen_name' => $screenName,
        'seats' => $seats
    ]);
} else {
    // Return an error message if 'screenid' is not provided
    echo json_encode(['error' => 'Screen ID is required']);
}
?>
