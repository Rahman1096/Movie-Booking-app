<?php 

include('connect.php');

if(!isset($_SESSION['uid'])){
    echo "<script> window.location.href='../login.php';  </script>";
}

// Update user details
if (isset($_POST['update_details'])) {
    $new_name = mysqli_real_escape_string($con, $_POST['name']);
    $new_email = mysqli_real_escape_string($con, $_POST['email']);
    $new_password = mysqli_real_escape_string($con, $_POST['password']);
    
    // Check if password meets the criteria
    if (!empty($new_password) && (!preg_match('/[A-Za-z]/', $new_password) || !preg_match('/[0-9]/', $new_password) || strlen($new_password) < 8 || !preg_match('/[^A-Za-z0-9]/', $new_password))) {
        echo "<script>alert('Password must be at least 8 characters long, contain at least one number, one special character, and one letter.');</script>";
    } else {
        // Do not hash the password, store it as is
        $new_password_to_store = !empty($new_password) ? $new_password : null;
        
        // Check if email is already in use
        $checkSql = "SELECT * FROM `users` WHERE `email` = '$new_email' AND `userid` != '{$_SESSION['uid']}'";
        $checkRes = mysqli_query($con, $checkSql);

        if (mysqli_num_rows($checkRes) > 0) {
            echo "<script>alert('Email is already in use by another user. Please use a different email.');</script>";
        } else {
            // Prepare the update query, only update the fields that have changed
            $updateFields = [];
            if (!empty($new_name)) $updateFields[] = "`name` = '$new_name'";
            if (!empty($new_email)) $updateFields[] = "`email` = '$new_email'";
            if ($new_password_to_store !== null) $updateFields[] = "`password` = '$new_password_to_store'";

            if (count($updateFields) > 0) {
                $updateSql = "UPDATE `users` SET " . implode(", ", $updateFields) . " WHERE `userid` = '{$_SESSION['uid']}'";

                if (mysqli_query($con, $updateSql)) {
                    echo "<script>alert('Profile updated successfully'); window.location.href='viewprofile.php';</script>";
                } else {
                    echo "<script>alert('Error updating profile');</script>";
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600" rel="stylesheet">
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

        .form-container, .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .form-container h2, .card h3 {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            text-align: center;
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

        .btn {
            padding: 10px 15px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 0 auto;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-radius: 8px;
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

        .password-eye {
            position: relative;
        }

        .password-eye i {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
</head>
<body>
<?php include('header.php'); ?>

<div class="container">
    <div class="form-container">
        <h2>User Profile</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Password</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $uid = $_SESSION['uid'];
                $sql = "SELECT * FROM `users` WHERE userid = '$uid'";
                $res = mysqli_query($con, $sql);
                if (mysqli_num_rows($res) > 0) {
                    while ($data = mysqli_fetch_array($res)) {
                        echo "<tr>
                                <td>{$data['userid']}</td>
                                <td>{$data['name']}</td>
                                <td>
                                    <div class='password-eye'>
                                        <span class='email'>{$data['email']}</span>
                                        <i class='bi bi-eye-slash' onclick='toggleEmailVisibility(this)'></i>
                                    </div>
                                </td>
                                <td>
                                    <div class='password-eye'>
                                        <span data-password='{$data['password']}' class='password'>********</span>
                                        <i class='bi bi-eye-slash' onclick='togglePasswordVisibility(this)'></i>
                                    </div>
                                </td>
                              </tr>";
                    }
                } else {
                    echo '<tr><td colspan="4">No user found</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="form-container">
        <h2>Update Email, Password, and Name</h2>
        <form method="POST" action="viewprofile.php">
            <input type="text" name="name" class="form-control" placeholder="New Name">
            <input type="email" name="email" class="form-control" placeholder="New Email" required>
            <div class="password-eye">
                <input type="password" name="password" class="form-control" placeholder="New Password" required>
                <i class="bi bi-eye-slash" onclick="togglePassword()"></i>
            </div>
            <button type="submit" name="update_details" class="btn btn-primary">Update</button>
        </form>
    </div>

    <div class="card">
        <h3>Your Bookings</h3>
        <?php
        $bookingSql = "SELECT b.bookingid, m.title, s.screen_name, b.booking_date, b.status, b.total_amount
                       FROM bookings b
                       JOIN movies m ON b.movieid = m.movieid
                       JOIN screens s ON b.screenid = s.screenid
                       WHERE b.userid = '$uid'";
        $bookingRes = mysqli_query($con, $bookingSql);

        if (mysqli_num_rows($bookingRes) > 0) {
            echo '<table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Movie</th>
                            <th>Screen</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>';
                    while ($booking = mysqli_fetch_array($bookingRes)) {
                      echo "<tr>
                              <td>{$booking['bookingid']}</td>
                              <td>{$booking['title']}</td>
                              <td>{$booking['screen_name']}</td>
                              <td>{$booking['booking_date']}</td>
                              <td>{$booking['status']}</td>
                              <td>{$booking['total_amount']} PKR</td>
                            </tr>";
                  }
                  echo '</tbody></table>';
              } else {
                  echo '<p>No bookings found</p>';
              }
              ?>
          </div>
      </div>
      
      <?php include('footer.php'); ?>
      
      <script>
      function togglePassword() {
          const passwordInput = document.querySelector('input[name="password"]');
          const icon = document.querySelector('.password-eye i');
          if (passwordInput.type === 'password') {
              passwordInput.type = 'text';
              icon.classList.replace('bi-eye-slash', 'bi-eye');
          } else {
              passwordInput.type = 'password';
              icon.classList.replace('bi-eye', 'bi-eye-slash');
          }
      }
      
      function toggleEmailVisibility(icon) {
          const emailSpan = icon.previousElementSibling;
          if (emailSpan.style.display === 'none') {
              emailSpan.style.display = 'inline';
              icon.classList.replace('bi-eye', 'bi-eye-slash');
          } else {
              emailSpan.style.display = 'none';
              icon.classList.replace('bi-eye-slash', 'bi-eye');
          }
      }
      
      function togglePasswordVisibility(icon) {
          const passwordSpan = icon.previousElementSibling;
          if (passwordSpan.textContent === '********') {
              passwordSpan.textContent = passwordSpan.dataset.password; // Show the actual password
              icon.classList.replace('bi-eye-slash', 'bi-eye');
          } else {
              passwordSpan.textContent = '********';
              icon.classList.replace('bi-eye', 'bi-eye-slash');
          }
      }
      </script>
      
      </body>
      </html>