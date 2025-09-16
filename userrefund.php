<?php 

include('connect.php');

if(!isset($_SESSION['uid'])){
    echo "<script> window.location.href='login.php';  </script>";
}

$uid = $_SESSION['uid'];

// Get current date and time
$currentDateTime = date('Y-m-d H:i:s');

// Handle booking cancellation
if (isset($_POST['cancel_booking'])) {
    $bookingid = mysqli_real_escape_string($con, $_POST['bookingid']);

    // Check if the booking is already cancelled
    $checkStatusSql = "SELECT status FROM bookings WHERE bookingid = '$bookingid'";
    $checkStatusRes = mysqli_query($con, $checkStatusSql);
    $bookingData = mysqli_fetch_assoc($checkStatusRes);

    if ($bookingData['status'] == 'cancelled') {
        echo "<script>alert('This booking has already been cancelled.'); window.location.href='userrefund.php';</script>";
    } else {
        // Update booking status to cancelled
        $updateBookingSql = "UPDATE bookings SET status = 'cancelled' WHERE bookingid = '$bookingid'";

        if (mysqli_query($con, $updateBookingSql)) {
            // Insert into refunds table
            $refundAmountSql = "SELECT total_amount FROM bookings WHERE bookingid = '$bookingid'";
            $refundAmountRes = mysqli_query($con, $refundAmountSql);
            $refundAmount = mysqli_fetch_assoc($refundAmountRes)['total_amount'];

            // Insert refund request with status 'pending'
            $insertRefundSql = "INSERT INTO refunds (bookingid, request_date, refund_amount, status) 
                                VALUES ('$bookingid', NOW(), '$refundAmount', 'pending')";

            if (mysqli_query($con, $insertRefundSql)) {
                echo "<script>alert('Booking cancelled successfully. Refund is pending.'); window.location.href='userrefund.php';</script>";
            } else {
                echo "<script>alert('Error processing refund request.'); window.location.href='userrefund.php';</script>";
            }
        } else {
            echo "<script>alert('Error cancelling booking.'); window.location.href='userrefund.php';</script>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Refund</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            margin-top: 30px;
            flex: 1;
        }

        .form-container, .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .form-container h2, .card h3 {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            text-align: center;
        }

        .table {
            width: 100%;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .table th, .table td {
            text-align: center;
            padding: 10px;
            font-size: 14px;
        }

        .btn {
            padding: 10px 15px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 5px;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

<?php include('header.php'); ?>

<div class="container">
    <div class="form-container">
        <h2>Your Active Bookings</h2>
        <table class="table">
            <tr>
                <th>#</th>
                <th>Movie</th>
                <th>Theater</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php
            // Query for active bookings, join with the schedule table to get start_time
            $sql = "SELECT 
            b.bookingid, 
            m.title, 
            t.theater_name, 
            b.booking_date, 
            s.start_time, 
            b.status
        FROM 
            bookings b
        INNER JOIN 
            movies m ON b.movieid = m.movieid
        INNER JOIN 
            screens sc ON b.screenid = sc.screenid
        INNER JOIN 
            theater t ON sc.theaterid = t.theaterid
        INNER JOIN 
            schedule s ON sc.screenid = s.screenid AND m.movieid = s.movieid
        WHERE 
            b.userid = '$uid' 
            AND s.start_time > now()
            AND b.status = 'confirmed'";


            $res = mysqli_query($con, $sql);
            if(mysqli_num_rows($res) > 0){
                while($data = mysqli_fetch_array($res)){
                    ?>
                    <tr>
                        <td><?= $data['bookingid'] ?></td>
                        <td><?= $data['title'] ?></td>
                        <td><?= $data['theater_name'] ?></td>
                        <td><?= $data['booking_date'] ?></td>
                        <td><?= date('H:i', strtotime($data['start_time'])) ?></td>
                        <td><?= $data['status'] ?></td>
                        <td>
                            <form method="POST" action="userrefund.php">
                                <input type="hidden" name="bookingid" value="<?= $data['bookingid'] ?>">
                                <button type="submit" name="cancel_booking" class="btn btn-danger">Cancel</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo '<tr><td colspan="7">No active bookings found</td></tr>';
            }
            ?>
        </table>
    </div>

    <div class="form-container">
        <h2>Your Cancelled Bookings</h2>
        <table class="table">
            <tr>
                <th>#</th>
                <th>Movie</th>
                <th>Theater</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Refund Status</th>
            </tr>
            <?php
            // Query for cancelled bookings, ensuring they exist in the refunds table
            $cancelledSql = "SELECT 
            b.bookingid, 
            m.title, 
            t.theater_name, 
            b.booking_date, 
            s.start_time, 
            b.status AS booking_status,
            r.status AS refund_status
        FROM 
            bookings b
        INNER JOIN 
            movies m ON b.movieid = m.movieid
        INNER JOIN 
            screens sc ON b.screenid = sc.screenid
        INNER JOIN 
            theater t ON sc.theaterid = t.theaterid
        INNER JOIN 
            schedule s ON sc.screenid = s.screenid AND m.movieid = s.movieid
        INNER JOIN 
            refunds r ON b.bookingid = r.bookingid
        WHERE 
            b.userid = '$uid' AND b.status = 'cancelled'";
        


            $cancelledRes = mysqli_query($con, $cancelledSql);
            if(mysqli_num_rows($cancelledRes) > 0){
                while($cancelledData = mysqli_fetch_array($cancelledRes)){
                    ?>
                    <tr>
                        <td><?= $cancelledData['bookingid'] ?></td>
                        <td><?= $cancelledData['title'] ?></td>
                        <td><?= $cancelledData['theater_name'] ?></td>
                        <td><?= $cancelledData['booking_date'] ?></td>
                        <td><?= date('H:i', strtotime($cancelledData['start_time'])) ?></td>
                        <td><?= $cancelledData['booking_status'] ?></td>
                        <td><?= $cancelledData['refund_status'] ?></td>
                    </tr>
                    <?php
                }
            } else {
                echo '<tr><td colspan="6">No cancelled bookings found</td></tr>';
            }
            ?>
        </table>
    </div>
</div>

<?php include('footer.php'); ?>

</body>
</html>
