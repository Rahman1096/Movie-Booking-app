<?php include('connect.php') ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Bootstrap CSS (Using CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <section id="team" class="team section-bg">
        <div class="container mt-5">
            <div class="section-title text-center mb-4">
                <h2>Admin / User Login Portal</h2>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 d-flex align-items-center">
                    <form action="login.php" method="post" class="php-email-form w-100">
                        
                        <!-- Email Input -->
                        <div class="form-group mb-3">
                            <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required>
                        </div>

                        <!-- Password Input -->
                        <div class="form-group mb-3">
                            <input type="password" class="form-control" name="password" id="password" placeholder="Your Password" required>
                        </div>

                        <!-- Login and Register Buttons -->
                        <div class="text-center">
                            <button type="submit" name="login" class="btn btn-primary w-100 mb-2">Login</button>
                            <a href="register.php" class="btn btn-secondary w-100 mb-2">Register</a>
                            <a href="index.php" class="btn btn-light w-100">Back to Home</a> <!-- Back button -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</body>
</html>

<?php
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to check the entered email and password
    $sql = "SELECT * FROM `users` WHERE email = '$email' AND password = '$password'";

    // Execute query
    $rs = mysqli_query($con, $sql);

    // Check if any record is found
    if (mysqli_num_rows($rs) > 0) {
        $data = mysqli_fetch_array($rs);
        $role = $data['role'];  // Ensure you use the correct field name for role

        // Set session variables
        $_SESSION['uid'] = $data['userid'];
        $_SESSION['type'] = $role;

        // Redirect based on user role
        if ($role == 'admin') {
            echo "<script> alert('Admin login successfully!') </script>";
            echo "<script> window.location.href='admin/dashboard.php'; </script>";
        } else if ($role == 'customer') {
            echo "<script> alert('User login successfully!') </script>";
            echo "<script> window.location.href='index.php'; </script>";
        }
    } else {
        // Error handling for invalid email or password
        echo "<script> alert('Invalid email or password! Please check and try again.') </script>";
    }
}
?>
