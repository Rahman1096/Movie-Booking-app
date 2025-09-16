<?php

include('connect.php');
include('header.php');  // Assuming this is the file where your header is defined.

if (!isset($_GET['movieid']) || !isset($_GET['screenid'])) {
    die('Movie ID or Screen ID is missing!');
}

$movie_id = $_GET['movieid'];
$screen_id = $_GET['screenid'];

$movieQuery = "SELECT movies.*, screens.screenid, screens.screen_name, screens.total_seats, theater.theater_name
               FROM movies
               JOIN screens ON screens.screenid = '$screen_id'
               JOIN theater ON theater.theaterid = screens.theaterid
               WHERE movies.movieid = '$movie_id'";

$movieResult = mysqli_query($con, $movieQuery);
$movie = mysqli_fetch_assoc($movieResult);

if (!$movie) {
    die('Invalid movie or screen selection.');
}

$seatsQuery = "SELECT * FROM seats WHERE screenid = '$screen_id'";
$seatsResult = mysqli_query($con, $seatsQuery);

$seatData = [];
while ($seat = mysqli_fetch_assoc($seatsResult)) {
    $seatData[] = $seat;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Seat - <?= htmlspecialchars($movie['title']); ?></title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600|Poppins:400,600|Roboto:400,600" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            margin-top: 30px;
            flex: 1;
        }

        .seat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* Adjusted to 4 columns for larger seats */
            gap: 15px; /* Increased gap for better spacing */
            justify-items: center;
            margin-top: 30px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .seat {
            width: 100px; /* Increased width */
            height: 100px; /* Increased height */
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            position: relative;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            text-align: center;
            font-size: 14px; /* Increased font size for better readability */
        }

        .seat.vip {
            background-color: #FFD700; /* Gold */
            color: black;
        }

        .seat.premium {
            background-color: #FFA500; /* Orange */
            color: black;
        }

        .seat.regular {
            background-color: #28a745; /* Green */
            color: black;
        }

        .seat.unavailable {
            background-color: darkred; /* Dark Red for unavailable seats */
            color: white; /* Ensure text is visible on dark red */
        }

        .seat.selected {
            background-color: #007bff !important; /* Blue */
        }

        .seat-info {
            position: absolute;
            bottom: -20px;
            font-size: 12px;
            text-align: center;
        }

        .payment-section {
            margin-top: 30px;
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .payment-methods {
            margin-top: 10px;
        }

        select {
            width: 50%;
            padding: 10px;
            margin: 20px auto;
            display: block;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .btn-danger {
            margin-top: 20px;
            background-color: #dc3545;
            border-color: #dc3545;
        }

        #total-amount {
            font-size: 1.5em;
            font-weight: bold;
            color: #007bff;
        }

    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center my-4">Select Seats for <?= htmlspecialchars($movie['title']); ?></h1>

    <div class="seat-grid">
        <?php foreach ($seatData as $seat) {
            $seat_class = ($seat['availability'] == 0) ? 'unavailable' : strtolower($seat['seat_type']);
        ?>

            <div class="seat <?= $seat_class ?>" data-seat-id="<?= $seat['seatid'] ?>" data-seat-type="<?= $seat['seat_type'] ?>" data-seat-price="<?= $seat['price'] ?>" data-availability="<?= $seat['availability'] ?>">
                <?= $seat['seat_number'] ?>
                <div class="seat-info">
                    <?= strtoupper($seat['seat_type']) ?><br>
                    <?= number_format($seat['price'], 2) ?> PKR <!-- Ensure price is displayed -->
                </div>
            </div>

        <?php } ?>
    </div>

    <div class="payment-section">
        <h4>Total Amount: <span id="total-amount">0</span> PKR</h4>

        <select id="payment-method">
            <option value="" disabled selected>Select Payment Method</option>
            <option value="Credit Card">Credit Card</option>
            <option value="Debit Card">Debit Card</option>
            <option value="Online">Online</option>
        </select>

        <button class="btn btn-danger" id="pay-btn">Pay Now</button>
    </div>
</div>

<?php include('footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        const moviePrice = parseFloat(<?= $movie['price']; ?>); // Get the movie price from PHP
        let selectedSeats = [];
        let totalAmount = moviePrice; // Initialize total amount with movie price

        // Set the initial total amount display
        $('#total-amount').text(totalAmount.toFixed(2)); // Display the initial total amount

        $('.seat').click(function() {
            const seat = $(this);
            const seatId = seat.data('seat-id');
            const seatPrice = parseFloat(seat.data('seat-price'));
            const seatAvailability = seat.data('availability');

            if (seatAvailability == 0) {
                alert('This seat is unavailable');
                return;
            }

            if (seat.hasClass('selected')) {
                seat.removeClass('selected');
                selectedSeats = selectedSeats.filter(id => id !== seatId);
                totalAmount -= seatPrice; // Subtract seat price if deselected
            } else {
                seat.addClass('selected');
                selectedSeats.push(seatId);
                totalAmount += seatPrice; // Add seat price if selected
            }

            // Update the total amount display
            $('#total-amount').text(totalAmount.toFixed(2)); // Update total amount display
        });

        $('#pay-btn').click(function() {
            if (selectedSeats.length === 0) {
                alert('Please select at least one seat');
                return;
            }

            const paymentMethod = $('#payment-method').val();
            if (!paymentMethod) {
                alert('Please select a payment method');
                return;
            }

            $.ajax({
                url: 'update_seats.php',
                type: 'POST',
                data: {
                    seats: selectedSeats,
                    movie_id: <?= $movie_id; ?>,
                    screen_id: <?= $screen_id; ?>,
                    total_amount: totalAmount.toFixed(2), // Include movie price in total
                    payment_method: paymentMethod
                },
                success: function(response) {
                    if (response === 'success') {
                        alert('Booking confirmed! Payment successful.');
                        window.location.href = 'booking.php';
                    } else {
                        alert('An error occurred while processing your payment.');
                    }
                }
            });
        });
    });
</script>

</body>
</html>