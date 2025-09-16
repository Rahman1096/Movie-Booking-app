<?php include('connect.php'); ?>

<?php
// Check if user is logged in and retrieve their name
if (isset($_SESSION['uid'])) {
    $uid = $_SESSION['uid'];

    // Fetch the user's name from the database
    $query = "SELECT name FROM users WHERE userid = '$uid'"; // Update 'id' to 'userid'
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['name'] = $row['name']; // Store the name in the session variable
    }
}
?>

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

       /* Top Bar */
#topbar {
    background: #343a40;
    color: #fff;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    flex-wrap: wrap;
    box-sizing: border-box;
    position: relative;
}

.contact-info {
    font-size: 14px;
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.contact-info a {
    color: white;
    text-decoration: none;
}

.contact-info i {
    font-size: 16px;
    margin-right: 5px;
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

@media (max-width: 768px) {
    /* For smaller devices, the top bar's layout will adjust */
    #topbar {
        flex-direction: column;
        align-items: center;
        height: auto;
        padding: 10px;
    }

    .contact-info {
        margin-bottom: 10px;
        text-align: center;
    }

    .social-links {
        margin-top: 10px;
        text-align: center;
    }
}


        /* Header */
        #header {
            background: #ffffff;
            height: 90px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.5s;
            display: flex;
            align-items: center;
            justify-content: space-between;
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

        /* Navbar Links */
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

        /* Centered Navbar */
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
            padding: 5px 20px;
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            #topbar {
                flex-direction: column;
                height: auto;
                padding: 10px;
                align-items: center;
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

        /* Ensure the logo and navigation are centered for non-logged in users */
        .centered-navbar {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        /* Home Tab Hover Effect */
        .nav-link.home-tab {
            background-color: #ffffff;
            color: #007bff;
        }

        .nav-link.home-tab:hover {
            background-color: #007bff;
            color: white;
        }

        .nav-link.home-tab.active {
            background-color: #007bff;
            color: white;
        }
    </style>

</head>

<body>

    <!-- Top Bar -->
    <div id="topbar">
        <div class="contact-info">
            <i class="bi bi-phone"></i><a href="tel:+92335 4516743" style="color: white; text-decoration: none;">+92335 4516743</a>
            <i class="bi bi-envelope"></i><a href="mailto:cinemabuddy@gmail.com" style="color: white; text-decoration: none;">cinemabuddy@gmail.com</a>
        </div>

        <div class="social-links">
            <a href="https://facebook.com" target="_blank" class="bi bi-facebook" title="Facebook"></a>
            <a href="https://twitter.com" target="_blank" class="bi bi-twitter" title="Twitter"></a>
            <a href="https://youtube.com" target="_blank" class="bi bi-youtube" title="YouTube"></a>
        </div>
    </div>

    <!-- Header -->
    <header id="header" class="d-flex align-items-center">
        <div class="container d-flex align-items-center justify-content-between">
            <h1 class="logo"><a href="dashboard.php">CinemaBuddy<span style="color: #ffc107;">.</span></a></h1>

            <nav id="navbar" class="navbar">
                <ul class="centered-navbar">
                    <li><a class="nav-link home-tab" href="index.php">Home</a></li>

                    <?php
                    if (!isset($_SESSION['uid'])) {
                        echo '
                        <li><a class="nav-link" href="allmovies.php">Movies</a></li>
                        <li><a class="nav-link" href="alltheater.php">Theater</a></li>
                        <li><a class="nav-link" href="login.php">Login</a></li>
                        <li><a class="nav-link" href="register.php">Register</a></li>
                        ';
                    } else {
                        $type = $_SESSION['type'];
                        if ($type == 'customer') {
                            echo '
                            <li><a class="nav-link" href="allmovies.php">Movies</a></li>
                            <li><a class="nav-link" href="alltheater.php">Theater</a></li>
                            <li><a class="nav-link" href="booking.php">My Booking</a></li>
                            <li><a class="nav-link" href="viewprofile.php">My Profile</a></li>
                            <li><a class="nav-link" href="userreviews.php">My Reviews</a></li>
                            <li><a class="nav-link" href="userrefund.php">Request Refund</a></li>
                            <li><a class="nav-link" href="logout.php">Logout</a></li>
                            ';
                        }
                    }
                    ?>
                </ul>
            </nav>

            <?php
            // Display the user's name if logged in
            if (isset($_SESSION['name'])) {
                echo '<div class="user-name">Welcome, ' . htmlspecialchars($_SESSION['name']) . '!</div>';
            }
            ?>

        </div>
    </header>

    <!-- Main Content Section -->
    <section class="container py-5">
        <h2>     </h2>
        <!-- Add your content as needed -->
    </section>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>
