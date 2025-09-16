<?php
include('connect.php');

if (!isset($_SESSION['uid'])) {
    echo "<script> window.location.href='../login.php'; </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Reviews</title>

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

        .form-container button {
        display: block;
        margin: 0 auto;
    }

        .form-container h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
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

        .table {
            width: 100%;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .container h2 {
    text-align: center;
    margin-top: 30px; /* Adjust space between the top of the container and heading */
    margin-bottom: 20px; /* Space between heading and the reviews table */
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

        .search-container {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Adjust the search bar for larger input field */
        .form-group input[type="text"] {
            width: 50%; /* Adjust width of the search bar */
            display: inline-block;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <?php include('header.php'); ?>

    <div class="container">

        <!-- Search Form -->
        <div class="search-container">
            <h2>Search Reviews</h2>
            <form method="GET" action="">
                <div class="form-group">
                    <input type="text" class="form-control" name="search" placeholder="Search by Movie Name or User Name" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>


        <?php if (!isset($_GET['search']) || empty($_GET['search'])) : ?>
            <!-- Add Review Form -->
            <div class="form-container">
                <h2>Add Your Review</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="movie">Movie:</label>
                        <select class="form-control" name="movie" id="movie" required>
                            <option value="">Select Movie</option>
                            <?php
                            $movies = mysqli_query($con, "SELECT movieid, title FROM movies WHERE status = 'now_showing' AND movieid IN (SELECT movieid FROM schedule)");
                            while ($movie = mysqli_fetch_assoc($movies)) {
                                echo "<option value='{$movie['movieid']}'>{$movie['title']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="rating">Rating:</label>
                        <input type="number" class="form-control" name="rating" min="1" max="5" step="0.5" required>
                    </div>

                    <div class="form-group">
                        <label for="review_text">Review:</label>
                        <textarea class="form-control" name="review_text" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="is_spoiler">Spoiler:</label>
                        <select class="form-control" name="is_spoiler" required>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            </div>
        <?php endif; ?>
        <h2>User Reviews</h2>
        <!-- Display Reviews -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Movie</th>
                    <th>User</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Spoilers</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $search_query = "";
                if (isset($_GET['search']) && !empty($_GET['search'])) {
                    $search_query = "AND (m.title LIKE '%" . mysqli_real_escape_string($con, $_GET['search']) . "%' 
                                    OR u.name LIKE '%" . mysqli_real_escape_string($con, $_GET['search']) . "%')";
                }

                $query = "SELECT r.reviewid, m.title AS movie_title, u.name AS user_name, r.rating, r.review_text, r.is_spoiler, r.review_date
                          FROM reviews r
                          JOIN movies m ON r.movieid = m.movieid
                          JOIN users u ON r.userid = u.userid
                          WHERE r.userid = " . $_SESSION['uid'] . " 
                          $search_query
                          ORDER BY r.review_date DESC";

                $result = mysqli_query($con, $query);

                function displayStars($rating) {
                    $stars = '';
                    for ($i = 0; $i < floor($rating); $i++) {
                        $stars .= '<i class="bi bi-star-fill text-warning"></i>';
                    }
                    if ($rating - floor($rating) >= 0.5) {
                        $stars .= '<i class="bi bi-star-half text-warning"></i>';
                    }
                    return $stars;
                }

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td>{$row['movie_title']}</td>
                            <td>{$row['user_name']}</td>
                            <td>" . displayStars($row['rating']) . "</td>
                            <td>{$row['review_text']}</td>
                            <td>" . ($row['is_spoiler'] ? 'Yes' : 'No') . "</td>
                            <td>{$row['review_date']}</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No reviews found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $movie = $_POST['movie'];
        $rating = $_POST['rating'];
        $review_text = mysqli_real_escape_string($con, $_POST['review_text']);
        $is_spoiler = $_POST['is_spoiler'];

        $insertQuery = "INSERT INTO reviews (userid, movieid, rating, review_text, is_spoiler) 
                        VALUES ('" . $_SESSION['uid'] . "', '$movie', '$rating', '$review_text', '$is_spoiler')";

        if (mysqli_query($con, $insertQuery)) {
            echo "<script>alert('Review added successfully');</script>";
            echo "<script>window.location.href='userreviews.php';</script>";
        } else {
            echo "<script>alert('Error adding review');</script>";
        }
    }
    ?>

    <?php include('footer.php'); ?>

</body>
</html>
