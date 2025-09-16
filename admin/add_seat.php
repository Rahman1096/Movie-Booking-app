<?php
include('../connect.php');

// Function to generate the next seat number
function generateNextSeatNumber($lastSeatNumber) {
    // Assume seat numbers are in the format "A1", "A2", etc.
    // Extract the letter and the number part from the last seat number
    preg_match('/([A-Z]+)(\d+)/', $lastSeatNumber, $matches);
    $letter = $matches[1];
    $number = (int)$matches[2];
    
    // Increment the number part
    $nextSeatNumber = $letter . ($number + 1);

    return $nextSeatNumber;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['screenid'], $_POST['seat_type'], $_POST['price'], $_POST['availability'])) {

        $screenid = $_POST['screenid'];
        $seat_type = $_POST['seat_type'];
        $price = $_POST['price'];
        $availability = $_POST['availability'];

        // Get the total number of seats for this screen
        $totalSeatsQuery = "SELECT total_seats FROM screens WHERE screenid = ?";
        if ($stmt = mysqli_prepare($con, $totalSeatsQuery)) {
            mysqli_stmt_bind_param($stmt, 'i', $screenid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $total_seats);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(['error' => 'Error fetching total seats.']);
            exit;
        }

        // Get the current number of seats for this screen
        $currentSeatsQuery = "SELECT COUNT(*) as current_seat_count FROM seats WHERE screenid = ?";
        if ($stmt = mysqli_prepare($con, $currentSeatsQuery)) {
            mysqli_stmt_bind_param($stmt, 'i', $screenid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $current_seat_count);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(['error' => 'Error fetching current seat count.']);
            exit;
        }

        // Check if adding a new seat would exceed the total seats
        if ($current_seat_count >= $total_seats) {
            echo json_encode(['error' => 'Cannot add more seats. Total seat limit reached.']);
            exit;
        }

        // Get the last inserted seat number for this screen
        $query = "SELECT seat_number FROM seats WHERE screenid = ? ORDER BY seat_number DESC LIMIT 1";
        if ($stmt = mysqli_prepare($con, $query)) {
            mysqli_stmt_bind_param($stmt, 'i', $screenid);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // Check if we found any seats for the screen
            if ($row = mysqli_fetch_assoc($result)) {
                // If seats exist, generate the next seat number
                $lastSeatNumber = $row['seat_number'];
                $seat_number = generateNextSeatNumber($lastSeatNumber);
            } else {
                // If no seats exist, start from "A1"
                $seat_number = 'A1';
            }

            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(['error' => 'Error fetching the last seat number.']);
            exit;
        }

        // Insert the new seat with the generated seat number
        $query = "INSERT INTO seats (screenid, seat_number, seat_type, price, availability) VALUES (?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($con, $query)) {
            // Correctly binding the parameters in the correct order
            mysqli_stmt_bind_param($stmt, 'sssii', $screenid, $seat_number, $seat_type, $price, $availability);

            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => 'Seat added successfully.']);
            } else {
                echo json_encode(['error' => 'Error adding seat.']);
            }

            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(['error' => 'Error preparing the query.']);
        }

    } else {
        echo json_encode(['error' => 'Required fields are missing.']);
    }

}
?>