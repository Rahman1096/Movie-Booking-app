<?php 

include('../connect.php');

if (!isset($_SESSION['uid'])) {
    echo "<script> window.location.href='../login.php'; </script>";
}

// Handle screen selection
$selectedScreenId = null;
$screenName = null;
if (isset($_POST['screen_id'])) {
    $selectedScreenId = $_POST['screen_id'];

    // Fetch the screen name for the selected screen ID
    $screenNameSql = "SELECT screen_name FROM screens WHERE screenid = '$selectedScreenId'";
    $screenNameRes = mysqli_query($con, $screenNameSql);
    $screenNameRow = mysqli_fetch_assoc($screenNameRes);
    $screenName = $screenNameRow['screen_name'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Booking</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600|Poppins:400,600|Roboto:400,600" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Theme Styling -->
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            margin-top: 30px;
        }

        .form-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .form-container h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            outline: none;
        }

        .btn {
            padding: 10px 15px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .seat {
            width: 50px;
            height: 50px;
            margin: 10px;
            display: inline-block;
            text-align: center;
            line-height: 50px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }

        .vip { background-color: yellow; }
        .premium { background-color: orange; }
        .regular { background-color: green; }
        .unavailable { background-color: red; }

        .seat-summary {
            margin-top: 20px;
            background-color: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .seat-summary h4 {
            margin-bottom: 10px;
            text-align: center;
        }

        h2, h3, h4 {
            text-align: center;
            font-weight: bold;
        }

        .btn-container {
            text-align: center;
        }
    </style>
</head>

<body>

<?php include('header.php'); ?>

<div class="container">
    <div class="form-container">
        <h2>Select a Screen</h2>
        <form action="viewallbooking.php" method="post">
            <div class="form-group">
                <select name="screen_id" class="form-control" required>
                    <option value="">Select Screen</option>
                    <?php
                    // Fetch all screens with their theater names
                    $screenSql = "SELECT s.screenid, s.screen_name, t.theater_name 
                                   FROM screens s 
                                   INNER JOIN theater t ON s.theaterid = t.theaterid";
                    $screenRes = mysqli_query($con, $screenSql);
                    while ($screen = mysqli_fetch_assoc($screenRes)) {
                        echo "<option value='{$screen['screenid']}'>{$screen['theater_name']} - {$screen['screen_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="btn-container">
                <button type="submit" class="btn btn-primary">View Seats</button>
            </div>
        </form>
    </div>

    <?php if ($selectedScreenId): ?>
        <h3 class="text-center">Seats for Screen: <?= $screenName ?></h3>
        <div class="seat-grid text-center">
            <?php
            // Fetch seats for the selected screen
            $seatSql = "SELECT seatid, seat_type, availability, price FROM seats WHERE screenid = '$selectedScreenId'";
            $seatRes = mysqli_query($con, $seatSql);
            $totalSeats = 0;
            $availableSeats = 0;
            $bookedSeats = 0;
            $totalRevenue = 0;

            while ($seat = mysqli_fetch_assoc($seatRes)) {
                $totalSeats++;
                if ($seat['availability']) {
                    $availableSeats++;
                } else {
                    $bookedSeats++;
                }
                $totalRevenue += $seat['availability'] ? 0 : $seat['price'];

                // Determine the class for the seat based on its type and availability
                $seatClass = $seat['availability'] ? strtolower($seat['seat_type']) : 'unavailable';
                echo "<div class='seat $seatClass'>{$seat['seatid']}<br>{$seat['seat_type']}</div>";
            }
            ?>
        </div>

        <div class="seat-summary">
            <h4>Seat Summary</h4>
            <p>Total Seats: <strong><?= $totalSeats ?></strong></p>
            <p>Available Seats: <strong><?= $availableSeats ?></strong></p>
            <p>Booked Seats: <strong><?= $bookedSeats ?></strong></p>
            <p>Total Revenue: <strong>Rs. <?= number_format($totalRevenue, 2) ?></strong></p>
        </div>
    <?php endif; ?>
</div>

<?php include('footer.php'); ?>

</body>
</html>
