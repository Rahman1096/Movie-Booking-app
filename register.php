<?php include('connect.php') ?>
<!DOCTYPE html>

<html lang="en">
<?php

if (isset($_POST['register'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Plain text password
    $confirmPassword = $_POST['confirm_password'];
    $phone = $_POST['phone'];

    // Check if email exists
    $emailCheckQuery = "SELECT * FROM `users` WHERE `email` = '$email'";
    $result = mysqli_query($con, $emailCheckQuery);

    if (mysqli_num_rows($result) > 0) {
        $error = "The email is already registered. Please use another email.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match!";
    } else {
        // Save password in plain text (not recommended)
        $sql = "INSERT INTO `users`(`name`, `email`, `password`, `phone`, `role`) VALUES ('$name','$email','$password','$phone','customer')";

        if (mysqli_query($con, $sql)) {
            echo "<script>alert('User registered successfully!');</script>";
            echo "<script>window.location.href='login.php';</script>";
        } else {
            $error = "User not registered. Please try again.";
        }
    }
}
?>

<head>

    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register</title>

    <!-- Bootstrap CSS -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <style>

        body {

            background: linear-gradient(135deg, #6c63ff, #a288ee);

            font-family: 'Poppins', sans-serif;

        }

        .container {

            background: white;

            padding: 40px;

            border-radius: 15px;

            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.2);

            margin-top: 50px;

            max-width: 600px;

        }

        .form-control {

            padding: 15px;

            font-size: 16px;

            border-radius: 10px;

        }

        .btn-primary {

            padding: 15px;

            font-size: 16px;

            border-radius: 10px;

            background-color: #6c63ff;

            border: none;

        }

        .btn-primary:hover {

            background-color: #534cc9;

        }

        .password-strength {

            height: 7px;

            width: 100%;

            margin-top: 5px;

        }

        .weak { background-color: red; }

        .medium { background-color: orange; }

        .strong { background-color: green; }

        .eye-icon {

            position: absolute;

            right: 10px;

            top: 50%;

            transform: translateY(-50%);

            cursor: pointer;

        }

        .form-group {

            position: relative;

        }

        .header-text {

            text-align: center;

            margin-bottom: 30px;

            color: #333;

        }

    </style>

</head>

<body>

    <div class="container">

        <h2 class="header-text">Sign Up Now!</h2>

        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <form action="register.php" method="post" onsubmit="return validateForm()">

            <div class="mb-3">

                <input type="text" class="form-control" name="name" id="name" placeholder="Your Name" required>

            </div>

            <div class="mb-3">

                <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required>

            </div>

            <div class="mb-3">

                <input type="text" class="form-control" name="phone" id="phone" placeholder="Your Phone (e.g., 1234-5678901)" maxlength="13" required oninput="formatPhone(this)">

            </div>

            <div class="mb-3 form-group">

                <input type="password" class="form-control" name="password" id="password" placeholder="Your Password" required oninput="checkPasswordStrength()">

                <span class="eye-icon" onclick="togglePasswordVisibility()"><i class="fas fa-eye"></i></span>

                <div class="password-strength" id="passwordStrength"></div>

            </div>

            <div class="mb-3">

                <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>

            </div>

            <div class="text-center">

                <button type="submit" class="btn btn-primary w-100" name="register">Register</button>

                <button type="button" class="btn btn-secondary w-100 mt-2" onclick="window.location.href='index.php'">Back</button>

            </div>

        </form>

    </div>

    <script>

        function formatPhone(input) {

            let value = input.value.replace(/\D/g, '');

            if (value.length > 11) value = value.slice(0, 11);

            if (value.length > 4) value = value.slice(0, 4) + '-' + value.slice(4);

            input.value = value;

        }

        function togglePasswordVisibility() {

            const password = document.getElementById('password');

            const type = password.type === 'password' ? 'text' : 'password';

            password.type = type;

            document.querySelector('.eye-icon i').classList.toggle('fa-eye-slash');

        }

        function checkPasswordStrength() {

            const password = document.getElementById('password').value;

            const strengthBar = document.getElementById('passwordStrength');

            strengthBar.className = 'password-strength';

            if (password.length >= 8 && /[\W_]/.test(password) && /\d/.test(password)) {

                strengthBar.classList.add('strong');

            } else if (password.length >= 6) {

                strengthBar.classList.add('medium');

            } else {

                strengthBar.classList.add('weak');

            }

        }

        function validateForm() {

            const password = document.getElementById('password').value;

            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {

                alert("Passwords do not match!");

                return false;

            }

            if (!/^\d{4}-\d{7}$/.test(document.getElementById('phone').value)) {

                alert("Invalid phone format. Use 0334-5678901.");

                return false;

            }

            if (!/(?=.*\d)(?=.*[\W_])(?=.*[a-zA-Z]).{8,}/.test(password)) {

                alert("Password must be at least 8 characters long, contain at least one special character, and one number.");

                return false;

            }

            return true;

        }

    </script>


