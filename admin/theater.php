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
    <title>Theaters</title>

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

        table th {
            background-color: #007bff;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
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
        <h2 id="form-title">Add New Theater</h2>
        <form action="theater.php" method="post">
            <input type="hidden" name="editid" value="<?php echo isset($_GET['editid']) ? $_GET['editid'] : ''; ?>">

            <div class="form-group">
                <input type="text" class="form-control" name="theater_name" placeholder="Enter Theater Name" required>
            </div>

            <div class="form-group">
                <input type="text" class="form-control" name="location" placeholder="Enter Location" required>
            </div>

            <div class="form-group btn-container">
                <?php if (isset($_GET['editid'])): ?>
                    <input type="submit" class="btn btn-primary" value="Update" name="update" id="submit-btn">
                <?php else: ?>
                    <input type="submit" class="btn btn-primary" value="Add" name="add" id="submit-btn">
                <?php endif; ?>
            </div>
        </form>
    </div>

    <h2>Theaters List</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Theater Name</th>
                <th>Location</th>
                <th>Screens</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM theater";
            $res = mysqli_query($con, $sql);
            if (mysqli_num_rows($res) > 0) {
                while ($data = mysqli_fetch_array($res)) {
                    $theater_id = $data['theaterid'];
                    $screen_count_query = "SELECT COUNT(screenid) AS screen_count FROM screens WHERE theaterid = '$theater_id'";
                    $screen_count_result = mysqli_query($con, $screen_count_query);
                    $screen_count = mysqli_fetch_assoc($screen_count_result)['screen_count'];

                    echo "<tr>
                        <td>{$data['theaterid']}</td>
                        <td>{$data['theater_name']}</td>
                        <td>{$data['location']}</td>
                        <td>{$screen_count}</td>
                        <td>
                            <a href='theater.php?editid={$data['theaterid']}' class='btn btn-primary'>Edit</a>
                            <a href='theater.php?deleteid={$data['theaterid']}' class='btn btn-danger'>Delete</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No theaters found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include('footer.php'); ?>

</body>
</html>

<?php
// Handling delete action
if (isset($_GET['deleteid'])) {
    $deleteid = $_GET['deleteid'];
    $sql = "DELETE FROM theater WHERE theaterid = '$deleteid'";
    if (mysqli_query($con, $sql)) {
        echo "<script> alert('Theater deleted successfully');</script>";
        echo "<script> window.location.href='theater.php'; </script>";
    } else {
        echo "<script> alert('Error deleting theater');</script>";
    }
}

// Handling edit action
if (isset($_GET['editid'])) {
    $editid = $_GET['editid'];
    $sql = "SELECT * FROM theater WHERE theaterid = '$editid'";
    $result = mysqli_query($con, $sql);
    $data = mysqli_fetch_array($result);
    echo "<script>
        document.getElementsByName('theater_name')[0].value = '{$data['theater_name']}';
        document.getElementsByName('location')[0].value = '{$data['location']}';
        document.getElementById('form-title').innerText = 'Edit Theater';
        document.getElementById('submit-btn').value = 'Update';
    </script>";
}

// Handling theater update
if (isset($_POST['update'])) {
    $theater_name = $_POST['theater_name'];
    $location = $_POST['location'];
    $editid = $_POST['editid'];

    // Check if the theater name already exists (excluding the current theater)
    $checkSql = "SELECT * FROM theater WHERE theater_name='$theater_name' AND theaterid != '$editid'";
    $checkRes = mysqli_query($con, $checkSql);
    if (mysqli_num_rows($checkRes) > 0) {
        echo "<script> alert('Theater with the same name already exists.');</script>";
    } else {
        $sql = "UPDATE theater SET theater_name='$theater_name', location='$location' WHERE theaterid='$editid'";
        if (mysqli_query($con, $sql)) {
            echo "<script> alert('Theater updated successfully');</script>";
            echo "<script> window.location.href='theater.php'; </script>";
        } else {
            echo "<script> alert('Error updating theater: " . mysqli_error($con) . "');</script>";
        }
    } 
}
// Handling theater insert
if (isset($_POST['add']) && $_POST['add'] == 'Add') {
    $theater_name = $_POST['theater_name'];
    $location = $_POST['location'];

    // Check if the theater name already exists
    $checkSql = "SELECT * FROM theater WHERE theater_name='$theater_name'";
    $checkRes = mysqli_query($con, $checkSql);
    if (mysqli_num_rows($checkRes) > 0) {
        echo "<script> alert('Theater with the same name already exists.');</script>";
    } else {
        $sql = "INSERT INTO theater (theater_name, location) VALUES ('$theater_name', '$location')";
        if (mysqli_query($con, $sql)) {
            echo "<script> alert('Theater added successfully');</script>";
            echo "<script> window.location.href='theater.php'; </script>";
        } else {
            echo "<script> alert('Error adding theater');</script>";
        }
    }
}
?>
