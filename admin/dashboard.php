<?php 
include('../connect.php');

if(!isset($_SESSION['uid'])){
    echo "<script> window.location.href='../login.php';  </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
            height: 100%; /* Ensures all cards are the same height */
        }

        .card-header {
            font-weight: bold;
            font-size: 18px;
        }

        .card-body h6 {
            font-size: 24px;
            color: #333;
            text-align: center;
        }

        /* Custom styles for the header */
        .custom-header {
            background-color: #fff; /* Set your desired background color */
            color: #333; /* Set your desired text color */
            padding: 20px; /* Adjust padding as needed */
            /* Add any other styles you want to customize */
        }

        .custom-header h1 {
            font-size: 24px; /* Adjust font size */
            font-weight: bold; /* Adjust font weight */
        }

        /* Override Bootstrap styles for header elements */
        .custom-header h1, 
        .custom-header p, 
        .custom-header a {
            margin: 0; /* Reset margin */
            padding: 0; /* Reset padding */
            color: inherit; /* Inherit color from custom-header */
            text-decoration: none; /* Remove underline from links */
        }
    </style>
</head>
<body>

<?php include('header.php'); ?>

<div class="container text-center">
    <h4>Welcome to Admin Dashboard!</h4>
    <div class="row">

        <!-- Categories Count -->
        <div class="col-lg-3 mb-2">
            <div class="card bg-info text-white">
                <div class="card-header">CATEGORIES</div>
                <div class="card-body">
                    <?php
                    $sql = "SELECT COUNT(catid) AS 'category' FROM `categories`";
                    $res = mysqli_query($con, $sql);
                    $catdata = mysqli_fetch_array($res);
                    ?>
                    <h6><?=$catdata['category']?></h6>
                </div>
            </div>
        </div>

        <!-- Movies Count -->
        <div class="col-lg-3 mb-2">
            <div class="card bg-success text-white">
                <div class="card-header">MOVIES</div>
                <div class="card-body">
                    <?php
                    $sql = "SELECT COUNT(movieid) AS 'total_movies' FROM `movies`";
                    $res = mysqli_query($con, $sql);
                    $moviedata = mysqli_fetch_array($res);
                    ?>
                    <h6><?=$moviedata['total_movies']?></h6>
                </div>
            </div>
        </div>

        <!-- Theater Count -->
        <div class="col-lg-3 mb-2">
            <div class="card bg-warning text-white">
                <div class="card-header">THEATER</div>
                <div class="card-body">
                    <?php
                    $sql = "SELECT COUNT(theaterid) AS 'total_theater' FROM `theater`";
                    $res = mysqli_query($con, $sql);
                    $theaterdata = mysqli_fetch_array($res);
                    ?>
                    <h6><?=$theaterdata['total_theater']?></h6>
                    </div>
            </div>
        </div>

        <!-- Total Bookings -->
        <div class="col-lg-3 mb-2">
            <div class="card bg-danger text-white">
                <div class="card-header">TOTAL BOOKINGS</div>
                <div class="card-body">
                    <?php
                    $sql = "SELECT COUNT(bookingid) AS 'total_booking' FROM `bookings` WHERE status = 'confirmed'";
                    $res = mysqli_query($con, $sql);
                    $bookingdata = mysqli_fetch_array($res);
                    ?>
                    <h6><?=$bookingdata['total_booking']?></h6>
                </div>
            </div>
        </div>

        <!-- Users Count -->
        <div class="col-lg-3 mb-2">
            <div class="card bg-primary text-white">
                <div class="card-header">USERS</div>
                <div class="card-body">
                    <?php
                    $sql = "SELECT COUNT(userid) AS 'total_users' FROM `users` WHERE role = 'customer'";
                    $res = mysqli_query($con, $sql);
                    $userdata = mysqli_fetch_array($res);
                    ?>
                    <h6><?=$userdata['total_users']?></h6>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div class="col-lg-3 mb-2">
            <div class="card bg-secondary text-white">
                <div class="card-header">REVENUE</div>
                <div class="card-body">
                    <?php
                    $sql = "SELECT SUM(total_amount) AS 'total_revenue' FROM `bookings` WHERE status = 'confirmed'";
                    $res = mysqli_query($con, $sql);
                    $revenueData = mysqli_fetch_array($res);
                    ?>
                    <h6><?=$revenueData['total_revenue']?> PKR</h6>
                </div>
            </div>
        </div>

        <!-- Screen Count -->
        <div class="col-lg-3 mb-2">
            <div class="card bg-light text-dark">
                <div class="card-header">SCREENS</div>
                <div class="card-body">
                    <?php
                    $sql = "SELECT COUNT(screenid) AS 'total_screens' FROM `screens`";
                    $res = mysqli_query($con, $sql);
                    $screenData = mysqli_fetch_array($res);
                    ?>
                    <h6><?=$screenData['total_screens']?></h6>
                </div>
            </div>
        </div>

        <!-- Total Seats -->
        <div class="col-lg-3 mb-2">
            <div class="card bg-info text-white">
                <div class="card-header">TOTAL SEATS</div>
                <div class="card-body">
                    <?php
                    $sql = "SELECT SUM(total_seats) AS 'total_seats' FROM `screens`";
                    $res = mysqli_query($con, $sql);
                    $seatsData = mysqli_fetch_array($res);
                    ?>
                    <h6><?=$seatsData['total_seats']?></h6>
                </div>
            </div>
        </div>

        <!-- Booking Status: Cancelled -->
        <div class="col-lg-3 mb-2">
            <div class="card bg-danger text-white">
                <div class="card-header">CANCELLED BOOKINGS</div>
                <div class="card-body">
                    <?php
                    $sql = "SELECT COUNT(bookingid) AS 'cancelled' FROM `bookings` WHERE status = 'cancelled'";
                    $res = mysqli_query($con, $sql);
                    $cancelledData = mysqli_fetch_array($res);
                    ?>
                    <h6><?=$cancelledData['cancelled']?></h6>
                </div>
            </div>
        </div>

        <!-- Booking Status: Pending -->
        <div class="col-lg-3 mb-2">
            <div class="card bg-warning text-dark">
                <div class="card-header">PENDING REFUNDS</div>
                <div class="card-body">
                    <?php
                    $sql = "SELECT COUNT(refundid) AS 'pending' FROM `refunds` WHERE status = 'pending'";
                    $res = mysqli_query($con, $sql);
                    $pendingData = mysqli_fetch_array($res);
                    ?>
                    <h6><?=$pendingData['pending']?></h6>
                </div>
            </div>
        </div>

        <!-- Popular Movies -->
        <div class="col-lg-3 mb-2">
            <div class="card bg-success text-white">
            <div class="card-header">TOP 3 MOVIES</div>
                <div class="card-body">
                    <?php
                    $sql = "SELECT title FROM `movies` ORDER BY rating DESC LIMIT 3";
                    $res = mysqli_query($con, $sql);
                    while ($row = mysqli_fetch_array($res)) {
                        echo "<h6>" . $row['title'] . "</h6>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Most Booked Movies -->
        <div class="col-lg-3 mb-2">
            <div class="card bg-primary text-white">
                <div class="card-header">MOST BOOKED MOVIES</div>
                <div class="card-body">
                    <?php
                    $sql = "SELECT title FROM `movies` m JOIN `bookings` b ON m.movieid = b.movieid GROUP BY m.movieid ORDER BY COUNT(b.bookingid) DESC LIMIT 3";
                    $res = mysqli_query($con, $sql);
                    while ($row = mysqli_fetch_array($res)) {
                        echo "<h6>" . $row['title'] . "</h6>";
                    }
                    ?>
                </div>
            </div>
        </div>

    </div> <!-- End of row -->
</div> <!-- End of container -->

<?php include('footer.php'); ?>

</body>
</html>