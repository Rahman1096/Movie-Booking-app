<?php include('../connect.php') ?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>


    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600|Poppins:400,600|Roboto:400,600" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">


    <style>
        body {
            background: #ffffff;
            font-family: 'Poppins', sans-serif;
        }

        .top-bar {
            background-color: #343a40;
            color: white;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }

        .contact-info {
            font-size: 14px;
        }

        .social-links a {
            color: rgba(255, 255, 255, 0.9);
            margin-left: 20px;
            font-size: 18px;
            transition: color 0.3s;
        }

        .social-links a:hover {
            color: #ffc107;
        }

        #header {
            background: #ffffff;
            height: 90px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.5s;
        }

        .logo a {
            color: #007bff;
            font-size: 28px;
            font-weight: 700;
            text-decoration: none;
            transition: color 0.3s;
        }

        .logo a:hover {
            color: #ffc107;
        }

        .nav-link {
            color: #007bff;
            background-color: #ffffff;
            padding: 10px 15px;
            transition: color 0.3s, background-color 0.3s;
            border-radius: 5px;
            font-weight: 500;
            text-decoration: none;
        }

        .nav-link:hover {
            color: #fff;
            background-color: #007bff;
        }

        .nav-link.active {
            background-color: #007bff;
            color: #fff;
        }

        #navbar {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        #navbar ul {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        #navbar ul li {
            margin-right: 20px;
        }

        .user-name {
            color: #007bff;
            font-weight: bold;
            font-size: 16px;
            text-align: right;
            margin-left: 20px;
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .top-bar {
                flex-direction: column;
                height: auto;
                padding: 10px;
            }

            .contact-info {
                margin-bottom: 5px;
            }

            #navbar ul {
                flex-direction: column;
                align-items: center;
            }

            #navbar ul li {
                margin: 5px 0;
            }

            .user-name {
                text-align: center;
                padding: 5px 0;
            }
        }
    </style>
</head>

<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="contact-info d-flex align-items-center">
            <i class="bi bi-phone" style="margin-right: 5px;"></i>
            <a href="tel:+92335-4516743" style="color: white; text-decoration: none;">+92335-4516743</a>
            <i class="bi bi-envelope" style="margin: 0 10px;"></i>
            <a href="cinemabuddy@gmail.com" style="color: white; text-decoration: none;">cinemabuddy@gmail.com</a>
        </div>
        <div class="social-links">
            <a href="https://facebook.com" target="_blank" class="bi bi-facebook" style="color: white; margin: 0 10px;" title="Facebook"></a>
            <a href="https://twitter.com" target="_blank" class="bi bi-twitter" style="color: white; margin: 0 10px;" title="Twitter"></a>
            <a href="https://youtube.com" target="_blank" class="bi bi-youtube" style="color: white; margin: 0 10px;" title="YouTube"></a>
        </div>
    </div>

    <!-- Header -->
    <header id="header" class="d-flex align-items-center">
        <div class="container d-flex align-items-center justify-content-between">
            <h1 class="logo"><a href="dashboard.php">CinemaBuddy<span style="color: #ffc107;">.</span></a></h1>
            <nav id="navbar" class="navbar">
                <ul class="d-flex flex-row list-unstyled mb-0">
                    <li><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li><a class="nav-link" href="categories.php">Categories</a></li>
                    <li><a class="nav-link" href="movies.php">Movies</a></li>
                    <li><a class="nav-link" href="theater.php">Theater</a></li>
                    <li><a class="nav-link" href="viewallusers.php">Users</a></li>
                    <li><a class="nav-link" href="viewallbooking.php">Booking</a></li>
                    <li><a class="nav-link" href="screens.php">Screens</a></li>
                    <li><a class="nav-link" href="seats.php">Seats</a></li>
                    <li><a class="nav-link" href="refund.php">Payments</a></li>
                    <li><a class="nav-link" href="reviews.php">Review</a></li>
                    <li><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav>
            <?php
            // Check if user is logged in and display their name
            if (isset($_SESSION['uid'])) {
                $uid = $_SESSION['uid'];
                $query = "SELECT name FROM users WHERE userid = '$uid'";
                $result = mysqli_query($con, $query);
                if ($result && mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    echo '<div class="user-name">Welcome, ' . htmlspecialchars($row['name']) . '!</div>';
                }
            }
            ?>
        </div>
    </header>

    <!-- Main Content Section -->
    <section class="container py-5">
       <!-- <h2>Welcome to CinemaBuddy Dashboard</h2>-->
        <!-- Add your content as needed -->
    </section>

    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
