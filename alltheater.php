<?php

include('connect.php');

include('header.php');

?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theater Movies</title>
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
        .movie-poster {
            position: relative;
            width: 100%;
            height: 350px;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        }
        .movie-poster img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .movie-poster:hover img {
            transform: scale(1.1);
        }
        .movie-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            padding: 15px;
            text-align: center;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .movie-poster:hover .movie-info {
            opacity: 1;
        }
        .movie-info h5 {
            color: #fff;
            margin: 10px 0;
            font-size: 18px;
            font-weight: bold;
        }
        .movie-info a {
            display: inline-block;
            padding: 8px 15px;
            color: #fff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }
        .movie-info a:hover {
            background-color: #0056b3;
        }
        .category-title {
            text-align: center;
            margin: 30px 0;
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .no-movies {
            text-align: center;
            font-size: 20px;
            color: #dc3545;
        }
        .movie-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .movie-card {
            width: 23%;
            margin-bottom: 20px;
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
        <form action="alltheater.php" method="POST">
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

    // Now Showing
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
                        <div class='movie-info'>
                            <h5>{$movie['title']}</h5>
                            <p>Theater: {$movie['theater_name']}</p>
                            <p>Start Time: {$movie['start_time']}</p>
                            <p>End Time: {$movie['end_time']}</p>
                            <a href='{$movie['trailer']}' target='_blank'>Watch Trailer</a>
                            <a href='booking.php?id={$movie['theaterid']}' target='_blank' class='btn btn-primary'>Book Now</a>
                        </div>
                    </div>
                </div>";
        }
        echo "</div>";
    } else {
        echo "<div class='no-movies'>No movies currently showing.</div>";
    }

    // Trending Movies
    echo "<div class='category-title'>Trending Movies</div>";
    $trendingQuery = "SELECT movies.*, screens.*, theater.theater_name, COUNT(bookings.bookingid) AS booking_count
                      FROM movies
                      INNER JOIN screens ON screens.current_movie_id = movies.movieid
                      INNER JOIN theater ON theater.theaterid = screens.theaterid
                      LEFT JOIN bookings ON bookings.movieid = movies.movieid
                      WHERE 1 $whereClause
                      GROUP BY movies.movieid
                      ORDER BY booking_count DESC
                      Limit 5";
    $trendingResult = mysqli_query($con, $trendingQuery);
    if (mysqli_num_rows($trendingResult) > 0) {
        echo "<div class='movie-container'>";
        while ($movie = mysqli_fetch_assoc($trendingResult)) {
            echo "<div class='movie-card'>
                    <div class='movie-poster'>
                        <img src='admin/uploads/{$movie['image']}' alt='{$movie['title']}'>
                        <div class='movie-info'>
                            <h5>{$movie['title']}</h5>
                            <a href='{$movie['trailer']}' target='_blank'>Watch Trailer</a>
                        </div>
                    </div>
                  </div>";
        }
        echo "</div>";
    } else {
        echo "<div class='no-movies'>No trending movies.</div>";
    }

    // Upcoming Movies
    echo "<div class='category-title'>Upcoming Movies</div>";
    $upcomingQuery = "SELECT * FROM movies WHERE status = 'upcoming' $whereClause";
    $upcomingResult = mysqli_query($con, $upcomingQuery);
    if (mysqli_num_rows($upcomingResult) > 0) {
        echo "<div class='movie-container'>";
        while ($movie = mysqli_fetch_assoc($upcomingResult)) {
            echo "<div class='movie-card'>
                    <div class='movie-poster'>
                        <img src='admin/uploads/{$movie['image']}' alt='{$movie['title']}'>
                        <div class='movie-info'>
                            <h5>{$movie['title']}</h5>
                            <a href='{$movie['trailer']}' target='_blank'>Watch Trailer</a>
                        </div>
                    </div>
                  </div>";
        }
        echo "</div>";
    } else {
        echo "<div class='no-movies'>No upcoming movies.</div>";
    }
    ?>

</div>

</body>

</html>
