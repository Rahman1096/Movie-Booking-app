<?php
include('../connect.php');

if (!isset($_SESSION['uid'])) {
    echo "<script> window.location.href='../login.php'; </script>";
}

// Show only "now showing" movies in the dropdown
$movies_query = mysqli_query($con, "SELECT * FROM movies WHERE status = 'now_showing'");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Screens</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600|Poppins:400,600|Roboto:400,600" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom Styles -->
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
        h2 {
            text-align: center;
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

        .form-group {
            text-align: center;
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

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
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

        table th {
            background-color: #007bff;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>

<?php include('header.php'); ?>

<div class="container">

    <!-- Add/Update Screen Form -->
    <div class="form-container">
        <h2 id="form-title">Add New Screen</h2>
        <form action="screens.php" method="post">
        <input type="hidden" name="editid" value="<?php echo isset($_GET['editid']) ? $_GET['editid'] : ''; ?>">

<div class="form-group">
    <label for="theaterid">Select Theater</label>
    <select name="theaterid" class="form-control" required>
        <?php
        $theaters = mysqli_query($con, "SELECT * FROM theater");
        while ($row = mysqli_fetch_assoc($theaters)) {
            echo "<option value='{$row['theaterid']}'>{$row['theater_name']}</option>";
        }
        ?>
    </select>
</div>

<div class="form-group">
    <input type="text" class="form-control" name="screen_name" placeholder="Enter Screen Name" required>
</div>

<div class="form-group">
    <input type="number" class="form-control" name="total_seats" placeholder="Total Seats" required>
</div>

<div class="form-group">
    <?php if (isset($_GET['editid'])): ?>
        <input type="submit" class="btn btn-primary" value="Update" name="update" id="submit-btn">
    <?php else: ?>
        <input type="submit" class="btn btn-primary" value="Add" name="add" id="submit-btn">
    <?php endif; ?>
</div>
</form>
</div>

<h2>Screen List</h2>
<table class="table table-bordered">
<thead>
<tr>
    <th>#</th>
    <th>Screen Name</th>
    <th>Theater Name</th>
    <th>Total Seats</th>
    <th>Current Movie</th>
    <th>Action</th>
</tr>
</thead>
<tbody>
<?php
$sql = "SELECT screens.*, theater.theater_name, movies.title as current_movie
        FROM screens 
        LEFT JOIN theater ON screens.theaterid = theater.theaterid
        LEFT JOIN movies ON screens.current_movie_id = movies.movieid";
$res = mysqli_query($con, $sql);
if (mysqli_num_rows($res) > 0) {
    while ($data = mysqli_fetch_array($res)) {
        echo "<tr>
            <td>{$data['screenid']}</td>
            <td>{$data['screen_name']}</td>
            <td>{$data['theater_name']}</td>
            <td>{$data['total_seats']}</td>
            <td>" . (empty($data['current_movie']) ? 'No movie assigned' : $data['current_movie']) . "</td>
            <td>
                <a href='screens.php?editid={$data['screenid']}' class='btn btn-primary'>Edit</a>
                <a href='screens.php?deleteid={$data['screenid']}' class='btn btn-danger'>Delete</a>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='6'>No screens found</td></tr>";
}
?>
</tbody>
</table>

<!-- Movie Assignment Section -->
<div class="form-container">
<h2>Assign Movie to Screen</h2>
<form action="screens.php" method="post">
<div class="form-group">
    <label for="screenid">Select Screen</label>
    <select name="screenid" class="form-control" required>
        <?php
        // Fetching all screens to assign movies to, along with theater name
        $screens_query = mysqli_query($con, "SELECT screens.screenid, screens.screen_name, theater.theater_name 
        FROM screens 
        JOIN theater ON screens.theaterid = theater.theaterid");

        while ($row = mysqli_fetch_assoc($screens_query)) {
            echo "<option value='{$row['screenid']}'>Screen: {$row['screen_name']} (Theater: {$row['theater_name']})</option>";
        }
        ?>
    </select>
</div>

<div class="form-group">
    <label for="movieid">Select Movie</label>
    <select name="movieid" class="form-control" required>
        <?php
        $movies_query = mysqli_query($con, "SELECT * FROM movies WHERE status = 'now_showing'");
        while ($movie = mysqli_fetch_assoc($movies_query)) {
            echo "<option value='{$movie['movieid']}'>{$movie['title']}</option>";
        }
        ?>
    </select>
</div>

<div class="form-group">
<label for="start_time">Start Time</label>
                <input type="datetime-local" name="start_time" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="end_time">End Time</label>
                <input type="datetime-local" name="end_time" class="form-control" required>
            </div>

            <div class="form-group">
                <button type="submit" name="assign_movie" class="btn btn-success">Assign Movie</button>
            </div>
        </form>
    </div>

</div>

<?php include('footer.php'); ?>

</body>
</html>

<?php

// Handling delete action
if (isset($_GET['deleteid'])) {
    $deleteid = $_GET['deleteid'];
    $sql = "DELETE FROM screens WHERE screenid = '$deleteid'";
    if (mysqli_query($con, $sql)) {
        echo "<script> alert('Screen deleted successfully');</script>";
        echo "<script> window.location.href='screens.php'; </script>";
    } else {
        echo "<script> alert('Error deleting screen');</script>";
    }
}

// Handling screen add
if (isset($_POST['add'])) {
    $screen_name = $_POST['screen_name'];
    $theaterid = $_POST['theaterid'];
    $total_seats = $_POST['total_seats'];

    // Check for duplicate screen names
    $checkSql = "SELECT * FROM screens WHERE screen_name='$screen_name' AND theaterid='$theaterid'";
    $checkRes = mysqli_query($con, $checkSql);
    if (mysqli_num_rows($checkRes) > 0) {
        echo "<script> alert('Screen name already exists in this theater.');</script>";
    } else {
        // Insert query to add the screen
        $sql = "INSERT INTO screens (screen_name, theaterid, total_seats) VALUES ('$screen_name', '$theaterid', '$total_seats')";

        if (mysqli_query($con, $sql)) {
            echo "<script> alert('Screen added successfully');</script>";
            echo "<script> window.location.href='screens.php'; </script>";
        } else {
            echo "<script> alert('Error adding screen: " . mysqli_error($con) . "');</script>";
        }
    }
}

// Handling edit action
if (isset($_GET['editid'])) {
    $editid = $_GET['editid'];
    $sql = "SELECT * FROM screens WHERE screenid = '$editid'";
    $result = mysqli_query($con, $sql);
    $data = mysqli_fetch_array($result);
    echo "<script>
        document.getElementsByName('screen_name')[0].value = '{$data['screen_name']}';
        document.getElementsByName('total_seats')[0].value = '{$data['total_seats']}';
        document.getElementsByName('theaterid')[0].value = '{$data['theaterid']}';
        document.getElementById('form-title').innerText = 'Edit Screen';
        document.getElementById('submit-btn').value = 'Update';
    </script>";
}

// Handling screen update
if (isset($_POST['update'])) {
    $screen_name = $_POST['screen_name'];
    $theaterid = $_POST['theaterid'];
    $total_seats = $_POST['total_seats'];
    $editid = $_POST['editid'];

    // Check for duplicate screen names
    $checkSql = "SELECT * FROM screens WHERE screen_name='$screen_name' AND theaterid='$theaterid' AND screenid != '$editid'";
    $checkRes = mysqli_query($con, $checkSql);
    if (mysqli_num_rows($checkRes) > 0) {
        echo "<script> alert('Screen name already exists in this theater.');</script>";
    } else {
        $sql = "UPDATE screens SET screen_name = '$screen_name', theaterid = '$theaterid', total_seats = '$total_seats' WHERE screenid = '$editid'";
        if (mysqli_query($con, $sql)) {
            echo "<script> alert('Screen updated successfully');</script>";
            echo "<script> window.location.href='screens.php'; </script>";
        } else {
            echo "<script> alert('Error updating screen');</script>";
        }
    }
}

// Handling movie assignment to screen
if (isset($_POST['assign_movie'])) {
    $screenid = $_POST['screenid'];
    $movieid = $_POST['movieid'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

        // Validate start and end times
        if ($start_time >= $end_time) {
            echo "<script>alert('Start time must be before end time.');</script>";
        } else {
            // Check if the movie is already scheduled at that time on the selected screen
            $check_schedule = mysqli_query($con, "SELECT * FROM schedule WHERE screenid = '$screenid' AND (start_time < '$end_time' AND end_time > '$start_time')");
            if (mysqli_num_rows($check_schedule) > 0) {
                echo "<script>alert('This screen is already booked for this time slot. Please choose a different time.');</script>";
            } else {
                // Update the screen with the selected movie
                $sql = "UPDATE screens SET current_movie_id='$movieid' WHERE screenid='$screenid'";
    
                if (mysqli_query($con, $sql)) {
                    // Insert the movie schedule
                    $schedule_sql = "INSERT INTO schedule (screenid, movieid, start_time, end_time) VALUES ('$screenid', '$movieid', '$start_time', '$end_time')";
                    if (mysqli_query($con, $schedule_sql)) {
                        echo "<script>alert('Movie assigned and scheduled successfully');</script>";
                    } else {
                        echo "<script>alert('Error scheduling movie: " . mysqli_error($con) . "');</script>";
                    }
                    echo "<script>window.location.href='screens.php';</script>";
                } else {
                    echo "<script>alert('Error assigning movie: " . mysqli_error($con) . "');</script>";
                }
            }
        }
    }
    ?>