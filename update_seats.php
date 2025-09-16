<?php
include('connect.php');
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve POST data
    $seats = isset($_POST['seats']) ? $_POST['seats'] : [];
    $movie_id = isset($_POST['movie_id']) ? $_POST['movie_id'] : null;
    $screen_id = isset($_POST['screen_id']) ? $_POST['screen_id'] : null;
    $total_amount = isset($_POST['total_amount']) ? $_POST['total_amount'] : 0;
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;
    $user_id = isset($_SESSION['uid']) ? $_SESSION['uid'] : 1; // Replace with actual user session ID

    // Validate required data
    if (empty($seats) || !$movie_id || !$screen_id || !$payment_method) {
        echo 'error'; // Return error if required data is missing
        error_log('Missing POST data: ' . print_r($_POST, true)); // Log missing data
        exit;
    }

    // Begin transaction
    mysqli_begin_transaction($con);
    $success = true;

    // Update seat availability
    foreach ($seats as $seat_id) {
        $updateQuery = "UPDATE seats 
                        SET availability = 0 
                        WHERE seatid = '$seat_id' AND screenid = '$screen_id'";
        if (!mysqli_query($con, $updateQuery)) {
            $success = false;
            error_log("SQL Error (Seats Update): " . mysqli_error($con)); // Log SQL error
            mysqli_rollback($con); // Rollback changes
            break;
        }
    }
    if ($success) {
        // Insert booking details
        $seats_json = json_encode($seats); // Store seat IDs as a JSON string
        $bookingQuery = "INSERT INTO bookings 
                         (userid, screenid, movieid, booking_date, status, total_amount) 
                         VALUES ('$user_id', '$screen_id', '$movie_id', NOW(), 'confirmed', '$total_amount')";
        if (!mysqli_query($con, $bookingQuery)) {
            $success = false;
            error_log("SQL Error (Booking Insert): " . mysqli_error($con)); // Log SQL error
            mysqli_rollback($con); // Rollback changes
        }
    }

    if ($success) {
        // Insert payment details
        $booking_id = mysqli_insert_id($con); // Get the last inserted booking ID
        $paymentQuery = "INSERT INTO payments 
                         (bookingid, payment_date, amount, method, status) 
                         VALUES ('$booking_id', NOW(), '$total_amount', '$payment_method', 'successful')";
        if (!mysqli_query($con, $paymentQuery)) {
            $success = false;
            error_log("SQL Error (Payment Insert): " . mysqli_error($con)); // Log SQL error
            mysqli_rollback($con); // Rollback changes
        }
    }

    if ($success) {
        mysqli_commit($con); // Commit if all operations succeed
        echo 'success';
    } else {
        mysqli_rollback($con); // Rollback on any failure
        echo 'error';
    }
} else {
    echo 'invalid_request'; // Invalid request type
}
?>
