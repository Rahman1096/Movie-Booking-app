<?php
include('connect.php');

if(!isset($_SESSION['uid'])){
  echo "<script> window.location.href='login.php';  </script>";
}
include('header.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Movies</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600|Poppins:400,600|Roboto:400,600" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .container {
            margin-top: 30px;
        }

        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .form-control {
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            font-size: 16px;
            margin-right: 10px;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .category-title {
            text-align: center;
            margin: 30px 0;
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .movie-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .movie-card {
            width: 23%;
            margin-bottom: 20px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.2s;
            padding: 15px; /* Added padding for better spacing */
        }

        .movie-card:hover {
            transform: scale(1.02);
        }

        .movie-poster {
            width: 100%;
            height: 300px; /* Adjusted height for better appearance */
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        }

        .movie-poster img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .movie-details {
            text-align: center; /* Center align text */
        }

        .movie-details h5 {
            margin: 10px 0;
            font-weight: bold;
        }

        .movie-details p {
            margin: 5px 0;
            font-size: 14px;
        }

        .movie-rating {
            font-size: 18px;
            color: #FFD700;
        }

        .price {
            font-weight: bold;
            color: #28a745;
        }

        @media (max-width: 768px) {
            .movie-card {
                width: 48%;
            }
        }

        @media (max-width: 480px) {
            .movie-card {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="container">
    <!-- Search Bar -->
    <div class="search-container">
        <form action="booking.php" method="POST">
            <input type="text" name="movie_search" class="form-control" placeholder="Search Movie Name">
            <select name="catid" class="form-control">
                <option value="">Select Category</option>
                <?php
                $categories = mysqli_query($con, "SELECT * FROM categories");
                while ($category = mysqli_fetch_assoc($categories)) {
                    echo "<option value='{$category['catid']}'>{$category['catname']}</option>";
                }
                ?>
            </select>
            <button type="submit" class="btn btn-primary" name="btnSearch">Search</button>
        </form>
    </div>

    <?php
    $whereClause = '';

    if (isset($_POST['btnSearch'])) {
      $movie_search = mysqli_real_escape_string($con, $_POST['movie_search']);
      $catid = mysqli_real_escape_string($con, $_POST['catid']);
      if ($movie_search && $catid) {
          $whereClause = "AND movies.title LIKE '%$movie_search%' AND movies.category_id = '$catid'";
      } elseif ($movie_search) {
          $whereClause = "AND movies.title LIKE '%$movie_search%'";
      } elseif ($catid) {
          $whereClause = "AND movies.category_id = '$catid'";
      }
  }

  // Now Showing Movies Query
  echo "<div class='category-title'>Now Showing</div>";

  $nowShowingQuery = "SELECT 
                        movies.*, 
                        screens.*, 
                        theater.theater_name, 
                        schedule.start_time, 
                        schedule.end_time 
                    FROM 
                        movies 
                    INNER JOIN 
                        screens ON screens.current_movie_id = movies.movieid 
                    INNER JOIN 
                        schedule ON schedule.movieid = movies.movieid 
                    INNER JOIN 
                        theater ON theater.theaterid = screens.theaterid 
                    WHERE 
                        movies.status = 'now_showing' 
                    AND 
                        screens.current_movie_id IS NOT NULL 
                    AND 
                        schedule.start_time >= NOW() 
                    $whereClause 
                    ORDER BY 
                        schedule.start_time ASC"; // Order by start time ascending


  $nowShowingResult = mysqli_query($con, $nowShowingQuery);

  if (mysqli_num_rows($nowShowingResult) > 0) {
      echo "<div class='movie-container'>";
      while ($movie = mysqli_fetch_assoc($nowShowingResult)) {
          echo "<div class='movie-card'>
                  <div class='movie-poster'>
                      <img src='admin/uploads/{$movie['image']}' alt='{$movie['title']}'>
                  </div>
                  <div class='movie-details'>
                      <h5>{$movie['title']}</h5>
                      <p><strong>Theater:</strong><br> {$movie['theater_name']}</p>
                      <p><strong>Screen:</strong><br> {$movie['screen_name']}</p>
                      <p><strong>Start Time:</strong><br> " . date('g:i A', strtotime($movie['start_time'])) . "</p>
                      <p><strong>End Time:</strong><br> " . date('g:i A', strtotime($movie['end_time'])) . "</p>
                      <h5>Description</h5>
                      <p>{$movie['description']}</p>
                      <h5>Rating:</h5>
                      <div class='movie-rating'>";

          // Rating stars
          for ($i = 1; $i <= 5; $i++) {
              echo ($i <= $movie['rating']) ? "★" : "☆";
          }

          echo "</div>
                  <h5>Language:</h5>
                  <p>{$movie['language']}</p>
                  <h5>Duration:</h5>
                  <p>{$movie['duration']} minutes</p>
                  <h5>Price:</h5>
                  <p class='price'>₨" . number_format($movie['price'], 2) . "</p>
                  <div class='book-btn' style='margin-top: 15px;'> <!-- Added margin-top for spacing -->
                      <a href='select_seat.php?movieid={$movie['movieid']}&screenid={$movie['screenid']}' class='btn btn-primary'>Book Now</a>
                  </div>
              </div>
          </div>";
      }
      echo "</div>"; // Close movie-container
  } else {
      echo "<div class='no-movies'>No movies currently showing.</div>";
  }
  ?>

</div>

</body>
</html>