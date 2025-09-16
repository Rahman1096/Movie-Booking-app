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
    <title>Users</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600|Poppins:400,600|Roboto:400,600" rel="stylesheet">
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

        table th, table td {
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

        .container {
            margin-top: 30px;
        }

        /* Footer Styles */
        footer {
            background-color: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
            margin-top: 30px;
            position: relative;
            width: 100%;
            bottom: 0;
        }
    </style>
</head>

<body>

<?php include('header.php') ?>

<div class="container">
    <h2>Users List</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM `users`";
                $res  = mysqli_query($con, $sql);
                if(mysqli_num_rows($res) > 0){
                    while($data = mysqli_fetch_array($res)){
                ?>
                <tr>
                    <td><?= $data['userid'] ?></td>
                    <td><?= $data['name'] ?></td>
                    <td><?= $data['email'] ?> </td>
                    <td><?= $data['password'] ?> </td>  
                    <td><?= $data['phone'] ?> </td>  
                    <td><?= $data['role'] ?> </td>     
                    <td>
                        <a href="viewallusers.php?userid=<?= $data['userid'] ?>" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="7">No user found</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('footer.php') ?>

</body>
</html>

<?php
if(isset($_GET['userid'])){
    $userid = $_GET['userid'];
    $sql = "DELETE FROM users WHERE userid ='$userid'";
    if(mysqli_query($con, $sql)){
        echo "<script> alert('User deleted successfully'); window.location.href='viewallusers.php'; </script>";
    } else {
        echo "<script> alert('User not deleted'); </script>";
    }
}
?>
