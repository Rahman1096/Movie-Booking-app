<?php
include('../connect.php');


if (isset($_POST['approve_refund'])) {

    $refundid = mysqli_real_escape_string($con, $_POST['refundid']);
    $bookingid = mysqli_real_escape_string($con, $_POST['bookingid']);

    // Approve refund
    $approveSql = "UPDATE refunds SET status = 'approved' WHERE refundid = '$refundid'";

    if (mysqli_query($con, $approveSql)) {

        // Delete payment entry
        $deletePaymentSql = "DELETE FROM payments WHERE bookingid = '$bookingid'";
        mysqli_query($con, $deletePaymentSql);

        // Update booking status to 'cancelled'
        $updateBookingSql = "UPDATE bookings SET status = 'cancelled' WHERE bookingid = '$bookingid'";
        mysqli_query($con, $updateBookingSql);

       // Update seat availability to 1 (available) for all seats in this booking
    // Assuming there is a relationship between booking and seats (like a booking_seat table or bookingid in seats table)
    $updateSeatsSql = "UPDATE seats SET availability = 1 
    WHERE screenid IN (SELECT screenid FROM bookings WHERE bookingid = '$bookingid')";
mysqli_query($con, $updateSeatsSql);

        echo "<script>alert('Refund approved, payment removed successfully, and seats marked as available.'); window.location.href='refund.php';</script>";

    } else {
        echo "<script>alert('Error approving refund.'); window.location.href='refund.php';</script>";
    }
}


if (isset($_POST['reject_refund'])) {
    $refundid = mysqli_real_escape_string($con, $_POST['refundid']);
    $bookingid = mysqli_real_escape_string($con, $_POST['bookingid']);

    // Reject refund
    $rejectSql = "UPDATE refunds SET status = 'rejected' WHERE refundid = '$refundid'";
    if (mysqli_query($con, $rejectSql)) {
        // Update booking status to 'confirmed'
        $updateBookingSql = "UPDATE bookings SET status = 'confirmed' WHERE bookingid = '$bookingid'";
        mysqli_query($con, $updateBookingSql);

        echo "<script>alert('Refund rejected and booking status updated to confirmed.'); window.location.href='refund.php';</script>";
    } else {
        echo "<script>alert('Error rejecting refund.'); window.location.href='refund.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Refund Management</title>
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

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
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
        <h2>Refund Management</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Booking ID</th>
                    <th>Movie Title</th>
                    <th>Theater</th>
                    <th>Refund Amount</th>
                    <th>Refund Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
            // Get all refund requests
            $sql = "SELECT r.refundid, r.bookingid, b.total_amount, r.status, m.title, t.theater_name 
                    FROM refunds r
                    INNER JOIN bookings b ON r.bookingid = b.bookingid
                    INNER JOIN movies m ON b.movieid = m.movieid
                    INNER JOIN screens s ON b.screenid = s.screenid
                    INNER JOIN theater t ON s.theaterid = t.theaterid
                    WHERE r.status = 'pending'";

            $res = mysqli_query($con, $sql);
            if (mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_array($res)) {
                    ?>
                    <tr>
                        <td><?= $row['refundid'] ?></td>
                        <td><?= $row['bookingid'] ?></td>
                        <td><?= $row['title'] ?></td>
                        <td><?= $row['theater_name'] ?></td>
                        <td><?= $row['total_amount'] ?> PKR</td>
                        <td><?= $row['status'] ?></td>
                        <td>
                            <!-- Approve and Reject Buttons -->
                            <form method="POST" action="refund.php">
                                <input type="hidden" name="refundid" value="<?= $row['refundid'] ?>">
                                <input type="hidden" name="bookingid" value="<?= $row['bookingid'] ?>">
                                <button type="submit" name="approve_refund" class="btn btn-success">Approve</button>
                                <button type="submit" name="reject_refund" class="btn btn-danger">Reject</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo '<tr><td colspan="7">No pending refund requests.</td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('footer.php'); ?>
</body>
</html>
