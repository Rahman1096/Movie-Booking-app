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
    <title>Categories</title>

    <!-- Google Fonts -->
     
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600|Poppins:400,600|Roboto:400,600" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Add minimal custom styles for form -->
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Custom Form Styles */
        .form-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 30px;
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
            align-items: center;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            font-size: 16px;
            margin-bottom: 15px; /* Margin for space between fields and button */
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            outline: none;
        }

        .btn {
            padding: 8px 10px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-info {
            background-color: #17a2b8;
            color: white;
        }

        .btn-info:hover {
            background-color: #138496;
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

        /* Ensure Footer remains intact */
        footer {
            background-color: #343a40;
            color: white;
            padding: 20px;
            text-align: center;
        }

        /* Aligning the table properly */
        table {
            width: 100%;
            margin-top: 20px;
        }

        /* Center align headings */
h2 {
    text-align: center;
}


        table th, table td {
            text-align: center;
            padding: 10px;
            font-size: 16px;
        }

        /* Align the buttons */
        .button-container {
            display: flex;
            justify-content: center; /* Aligns buttons in the center */
            width: 100%;
        }

        .button-container input {
            margin-top: 10px; /* Adds space above the button */
            width: 100%; /* Ensures the button is centered with maximum width */
            max-width: 200px; /* Maximum width of the button */
        }
    </style>
</head>

<body>

<?php include('header.php') ?>

<?php
  if(isset($_GET['editid'])){
    $editid  = $_GET['editid'];
    $sql = "SELECT * FROM `categories` WHERE catid= '$editid'";
    $res  = mysqli_query($con, $sql);
    $editdata = mysqli_fetch_array($res);
?>

<div class="container form-container">
    <h2>Edit Category</h2>
    <form action="categories.php" method="post">
        <div class="row">
            <div class="">
                <input type="hidden" class="form-control" value="<?=$editdata['catid']?>" name="catid">
            </div>
            <div class="col-md-12 mb-4">
                <input type="text" class="form-control" name="catname" value="<?=$editdata['catname']?>" placeholder="Enter category name" required>
            </div>
            <div class="col-md-12 mb-4 button-container">
                <input type="submit" class="btn btn-info" value="Update" name="update">
            </div>
        </div>
    </form>
</div>

<?php 
  } else {
?>

<div class="container form-container">
    <h2>Add New Category</h2>
    <form action="categories.php" method="post">
        <div class="row">
            <div class="col-md-4 mb-4">
                <input type="text" class="form-control" name="catname" placeholder="Enter category name" required>
            </div>
            <!-- Centering the Add button -->
            <div class="col-md-12 mb-4 button-container">
                <input type="submit" class="btn btn-primary" value="Add" name="add">
            </div>
        </div>
    </form>
</div>

<?php
  }
?>

<div class="container">
    <h2>Category List</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM `categories`";
            $res  = mysqli_query($con, $sql);
            if(mysqli_num_rows($res) > 0){
                while($data = mysqli_fetch_array($res)){
            ?>
            <tr>
                <td><?= $data['catid'] ?></td>
                <td><?= $data['catname'] ?></td>
                <td>
                    <a href="categories.php?editid=<?= $data['catid'] ?>" class="btn btn-primary"> Edit</a>
                    <a href="categories.php?deleteid=<?= $data['catid'] ?>" class="btn btn-danger"> Delete</a>
                </td>
            </tr>
            <?php
                }
            } else {
                echo '<tr><td colspan="3">No categories found</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<?php include('footer.php') ?>

</body>
</html>

<?php

if (isset($_POST['add'])) {
    $name = $_POST['catname'];
    
    // Check if category already exists
    $checkSql = "SELECT * FROM `categories` WHERE `catname` = '$name'";
    $checkRes = mysqli_query($con, $checkSql);
    
    if (mysqli_num_rows($checkRes) > 0) {
        echo "<script> alert('Category already exists!')</script>";
    } else {
        // Add category if it doesn't exist
        $sql = "INSERT INTO `categories`(`catname`) VALUES ('$name')";
        if (mysqli_query($con, $sql)) {
            echo "<script> alert('Category added successfully')</script>";
            echo "<script> window.location.href='categories.php' </script>";
        } else {
            echo "<script> alert('Failed to add category')</script>";
        }
    }
}

if (isset($_POST['update'])) {
    $catid = $_POST['catid'];
    $name = $_POST['catname'];
    
    // Check if updated category name already exists
    $checkSql = "SELECT * FROM `categories` WHERE `catname` = '$name' AND `catid` != '$catid'";
    $checkRes = mysqli_query($con, $checkSql);
    
    if (mysqli_num_rows($checkRes) > 0) {
        echo "<script> alert('Category already exists!')</script>";
    } else {
        // Update category if the name is unique
        $sql = "UPDATE `categories` SET `catname` = '$name' WHERE `catid` = '$catid'";
        if (mysqli_query($con, $sql)) {
            echo "<script> alert('Category updated successfully')</script>";
            echo "<script> window.location.href='categories.php' </script>";
        } else {
            echo "<script> alert('Failed to update category')</script>";
        }
    }
}

if (isset($_GET['deleteid'])) {
    $deleteid = $_GET['deleteid'];
    $sql = "DELETE FROM `categories` WHERE `catid` = '$deleteid'";
    if (mysqli_query($con, $sql)) {
        echo "<script> alert('Category deleted successfully')</script>";
        echo "<script> window.location.href='categories.php' </script>";
    } else {
        echo "<script> alert('Failed to delete category')</script>";
    }
}
?>
