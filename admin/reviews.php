<?php
include('../connect.php');

if (!isset($_SESSION['uid'])) {
    echo "<script> window.location.href='../login.php'; </script>";
    exit;
}

if (isset($_GET['deleteid'])) {
    $deleteid = $_GET['deleteid'];
    $deleteQuery = "DELETE FROM reviews WHERE reviewid = '$deleteid'";
    
    if (mysqli_query($con, $deleteQuery)) {
        echo "<script>alert('Review deleted successfully');</script>";
        echo "<script>window.location.href='reviews.php';</script>";
    } else {
        echo "<script>alert('Error deleting review');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews</title>

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

        h2 {
    text-align: center;
}

        table th {
            background-color: #007bff;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .form-group.btn-container {
    text-align: center;
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
        <h2>Filter Reviews</h2>
        <form method="GET" action="">
            <div class="form-group">
                <label for="rating">Rating:</label>
                <select class="form-control" name="rating" id="rating">
                    <option value="">All</option>
                    <option value="5">5</option>
                    <option value="4">4</option>
                    <option value="3">3</option>
                    <option value="2">2</option>
                    <option value="1">1</option>
                </select>
            </div>

            <div class="form-group">
                <label for="movie">Movie:</label>
                <select class="form-control" name="movie" id="movie">
                    <option value="">All</option>
                    <?php
                    $movies = mysqli_query($con, "SELECT movieid, title FROM movies");
                    while ($movie = mysqli_fetch_assoc($movies)) {
                        echo "<option value='{$movie['movieid']}'>{$movie['title']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="user">User:</label>
                <select class="form-control" name="user" id="user">
                    <option value="">All</option>
                    <?php
                    $users = mysqli_query($con, "SELECT userid, name FROM users WHERE role = 'customer'");
                    while ($user = mysqli_fetch_assoc($users)) {
                        echo "<option value='{$user['userid']}'>{$user['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="spoilers">Spoilers:</label>
                <select class="form-control" name="spoilers" id="spoilers">
                    <option value="">All</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>

            <div class="form-group btn-container">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>

    <h2>Reviews List</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Movie</th>
                <th>User</th>
                <th>Rating</th>
                <th>Review</th>
                <th>Spoilers</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $filter = "WHERE 1=1";

            if (!empty($_GET['rating'])) {
                $rating = $_GET['rating'];
                $filter .= " AND r.rating = '$rating'";
            }

            if (!empty($_GET['movie'])) {
                $movie = $_GET['movie'];
                $filter .= " AND r.movieid = '$movie'";
            }

            if (!empty($_GET['user'])) {
                $user = $_GET['user'];
                $filter .= " AND r.userid = '$user'";
            }

            if (isset($_GET['spoilers']) && $_GET['spoilers'] !== '') {
                $spoilers = $_GET['spoilers'];
                $filter .= " AND r.is_spoiler = '$spoilers'";
            }

            $query = "SELECT r.reviewid, m.title AS movie_title, u.name AS user_name, r.rating, r.review_text, r.is_spoiler, r.review_date
                      FROM reviews r
                      JOIN movies m ON r.movieid = m.movieid
                      JOIN users u ON r.userid = u.userid
                      $filter";

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
                        <td>{$row['reviewid']}</td>
                        <td>{$row['movie_title']}</td>
                        <td>{$row['user_name']}</td>
                        <td>" . displayStars($row['rating']) . "</td>
                        <td>{$row['review_text']}</td>
                        <td>" . ($row['is_spoiler'] ? 'Yes' : 'No') . "</td>
                        <td>{$row['review_date']}</td>
                        <td>
    <button class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#editModal' 
            onclick='editReview({$row['reviewid']}, \"{$row['review_text']}\", {$row['rating']}, {$row['is_spoiler']})'>Edit</button>
    <a href='reviews.php?deleteid={$row['reviewid']}' class='btn btn-danger'>Delete</a>
</td>

                    </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No reviews found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST" action="">
                    <input type="hidden" name="reviewid" id="editReviewId">
                    <div class="form-group">
                        <label for="editReviewText">Review:</label>
                        <textarea class="form-control" name="review_text" id="editReviewText" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editRating">Rating:</label>
                        <input type="number" class="form-control" name="rating" id="editRating" min="1" max="5" step="0.5" required>
                    </div>
                    <div class="form-group">
                        <label for="editSpoiler">Spoilers:</label>
                        <select class="form-control" name="is_spoiler" id="editSpoiler">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                        <div class="form-group btn-container">
    <button type="submit" class="btn btn-primary">Update</button>
</div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reviewid'])) {
    $reviewid = $_POST['reviewid'];
    $review_text = mysqli_real_escape_string($con, $_POST['review_text']);
    $rating = $_POST['rating'];
    $is_spoiler = $_POST['is_spoiler'];

    $updateQuery = "UPDATE reviews SET review_text = '$review_text', rating = '$rating', is_spoiler = '$is_spoiler' WHERE reviewid = '$reviewid'";
    if (mysqli_query($con, $updateQuery)) {
        echo "<script>alert('Review updated successfully');</script>";
        echo "<script>window.location.href='reviews.php';</script>";
    } else {
        echo "<script>alert('Error updating review');</script>";
    }
}
?>

<script>
function editReview(id, text, rating, spoiler) {
    document.getElementById('editReviewId').value = id;
    document.getElementById('editReviewText').value = text;
    document.getElementById('editRating').value = rating;
    document.getElementById('editSpoiler').value = spoiler;
}
</script>

<?php include('footer.php'); ?>

</body>
</html>
