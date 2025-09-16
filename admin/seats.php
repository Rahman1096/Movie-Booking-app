<?php
include('../connect.php');

if (!isset($_SESSION['uid'])) {
    echo "<script> window.location.href='../login.php'; </script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Seats</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600|Poppins:400,600|Roboto:400,600" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Minimal Custom Styles -->
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
            text-align: center; /* Centered Heading */
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

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        table {
            width: 100%;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        table th,
        table td {
            text-align: center;
            padding: 10px;
            font-size: 14px;
        }

        h1, h3 {
            text-align: center; /* Centered Heading */
        }

        h2 {
    text-align: center;
}

        table th {
            background-color: #007bff;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .seat-layout {
            margin-top: 20px;
        }

        .seat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
            gap: 10px;
            justify-items: center;
        }

        .seat {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 12px;
            color: #000;
        }

        #add-seat-form button {
            display: inline-block;
            text-align: center;
        }

        .btn-container {
            text-align: center;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>

    <div class="container">
        <h1 class="text-center">Manage Seats</h1>

        <!-- Screen Selection -->
        <div class="form-group">
            <label for="screen-select">Select Screen:</label>
            <select id="screen-select" class="form-control">
                <option value="">-- Select a Screen --</option>
                <?php
                $result = mysqli_query($con, "SELECT * FROM screens");
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='{$row['screenid']}'>{$row['screen_name']}</option>";
                }
                ?>
            </select>
        </div>

       <!-- Add Seat Form -->
<div id="add-seat-form" style="display: none; margin-top: 20px;">
    <h2 id="screen-name" class="text-center"></h2>
    <form id="seat-form">
        <div class="form-group">
            <label for="seat-type">Seat Type:</label>
            <select id="seat-type" class="form-control" required>
                <option value="VIP">VIP</option>
                <option value="Premium">Premium</option>
                <option value="Regular">Regular</option>
            </select>
        </div>

        <div class="form-group">
            <label for="seat-price">Seat Price:</label>
            <input type="number" id="seat-price" class="form-control" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="seat-status">Seat Status:</label>
            <select id="seat-status" class="form-control" required>
                <option value="Available">Available</option>
                <option value="Unavailable">Unavailable</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Add Seat</button>
    </form>
</div>

<!-- Seat Layout -->
<div id="seat-layout" class="seat-layout mt-4">
    <h3 class="text-center">Seat Layout</h3>
    <div id="seat-grid" class="seat-grid"></div>
</div>

<!-- Seat List -->
<div id="seat-list" class="mt-4">
    <h3 class="text-center">Seat List</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Seat Number</th>
                <th>Seat Type</th>
                <th>Seat Price</th>
                <th>Seat Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="seat-table-body">
            <!-- Seat rows will be dynamically added here -->
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function () {
    let selectedScreenId = null;

    // Handle screen selection
    $('#screen-select').on('change', function () {
        selectedScreenId = $(this).val();
        if (selectedScreenId) {
            loadScreenData(selectedScreenId);
        } else {
            $('#screen-name').text('');
            $('#seat-grid').empty();
            $('#add-seat-form').hide();
        }
    });

    // Load screen data (name and seats)
    function loadScreenData(screenId) {
        $.ajax({
            url: 'load_seats.php',
            type: 'GET',
            data: { screenid: screenId },
            success: function (response) {
                const data = JSON.parse(response);
                $('#screen-name').text(data.screen_name);
                renderSeatLayout(data.seats);
                loadSeatList(data.seats);
                $('#add-seat-form').show();
            }
        });
    }

 // Render seat layout with status-based color
function renderSeatLayout(seats) {
    const seatGrid = $('#seat-grid');
    seatGrid.empty();

    seats.forEach(seat => {
        let seatColor = seat.availability === 0 ? 'red' :  // Set to red if unavailable
                        seat.seat_type === 'VIP' ? 'yellow' :
                        seat.seat_type === 'Premium' ? 'orange' : 'green';

        const seatDiv = `<div class="seat" style="background-color: ${seatColor}; margin: 10px;" data-seat-id="${seat.seatid}">
            Seat ${seat.seat_number}<br>${seat.seat_type}<br>$${seat.price}<br>Status: ${seat.availability}  <!-- Display 0 or 1 -->
        </div>`;

        seatGrid.append(seatDiv);
    });

    // Attach click event for marking seat as unavailable
    $('.seat').on('click', function () {
        const seatId = $(this).data('seat-id');
        markSeatUnavailable(seatId);  // Function to mark seat as unavailable
    });
}




// Mark seat as unavailable (red color)
function markSeatUnavailable(seatId) {
    $.ajax({
        url: 'mark_seat_unavailable.php',
        type: 'POST',
        data: { seatid: seatId },
        success: function () {
            loadScreenData(selectedScreenId);  // Reload the screen data to reflect changes
        }
    });
}



   // Load seat list into table
function loadSeatList(seats) {
    const seatTableBody = $('#seat-table-body');
    seatTableBody.empty();

    seats.forEach(seat => {
        const seatStatus = seat.availability;  // Just show 1 or 0 without conversion

        const seatRow = `<tr>
            <td>${seat.seatid}</td>
            <td>${seat.seat_number}</td>  <!-- Display seat number -->
            <td>${seat.seat_type}</td>
            <td>$${seat.price}</td>
            <td>${seatStatus}</td>  <!-- Display 0 or 1 -->
            <td>
                <button class="btn btn-warning edit-seat" data-id="${seat.seatid}">Edit</button>
                <button class="btn btn-danger delete-seat" data-id="${seat.seatid}">Delete</button>
            </td>
        </tr>`;

        seatTableBody.append(seatRow);
    });

    // Attach event listeners for edit and delete
    $('.edit-seat').on('click', function () {
        const seatId = $(this).data('id');
        loadSeatForEditing(seatId);
    });

    $('.delete-seat').on('click', function () {
        const seatId = $(this).data('id');
        deleteSeat(seatId);
    });
}



    // Mark seat as unavailable (red color)
    function markSeatUnavailable(seatId) {
        $.ajax({
            url: 'mark_seat_unavailable.php',
            type: 'POST',
            data: { seatid: seatId },
            success: function () {
                loadScreenData(selectedScreenId);
            }
        });
    }

  // Load seat details into form for editing
function loadSeatForEditing(seatId) {
    $.ajax({
        url: 'load_seats.php',
        type: 'GET',
        data: { seatid: seatId },
        success: function (response) {
            const seat = JSON.parse(response);

            // Populate the form with seat details
            $('#seat-type').val(seat.seat_type);
            $('#seat-price').val(seat.price);
            $('#seat-status').val(seat.availability === 1 ? 'Available' : 'Unavailable');
            $('#seat-form').data('edit-id', seat.seatid);  // Store the seat ID for editing
            $('#seat-form button').text('Update Seat');  // Change button text to 'Update Seat'
        }
    });
}



// Handle Add or Update Seat Form Submission
$('#seat-form').on('submit', function (e) {
    e.preventDefault();

    const seatType = $('#seat-type').val();
    const seatPrice = $('#seat-price').val();
    const seatStatus = $('#seat-status').val() === 'Available' ? 1 : 0; // Convert to 1 (Available) or 0 (Unavailable)
    const editId = $(this).data('edit-id'); // Get the seat ID for editing

    const action = editId ? 'update_seat.php' : 'add_seat.php'; // Determine action based on editId

    const data = {
        screenid: selectedScreenId,
        seat_type: seatType,
        price: seatPrice,
        availability: seatStatus,
    };

    if (editId) {
        data.seatid = editId; // Ensure seatid is appended if editing
    }

    // Handle the request to either add or update a seat
    $.ajax({
        url: action,
        type: 'POST',
        data: data,
        success: function (response) {
            const res = JSON.parse(response);
            if (res.success) {
                alert(res.success);
                loadScreenData(selectedScreenId); // Reload the screen data to reflect changes
                $('#seat-form')[0].reset(); // Reset the form
                $('#seat-form').data('edit-id', null); // Clear the edit ID
                $('#seat-form button').text('Add Seat'); // Reset button text to 'Add Seat'
            } else {
                alert(res.error); // Display error if any
            }
        },
    });
});




    // Delete seat
    function deleteSeat(seatId) {
        $.ajax({
            url: 'delete_seat.php',
            type: 'GET',
            data: { seatid: seatId },
            success: function (response) {
                alert(response);
                loadScreenData(selectedScreenId);
            }
        });
    }
});
</script>
