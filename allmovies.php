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

    <title>All Movies</title>

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
    justify-content: center; /* Centers the content */
    margin-bottom: 20px;
}

.form-control {
    padding: 8px; /* Reduced padding */
    border-radius: 5px;
    border: 1px solid #ddd;
    background-color: #f9f9f9;
    font-size: 14px; /* Reduced font size */
    margin-right: 10px;
    width: 200px; /* Set a specific width */
}


.btn-primary {
    background-color: #007bff;
    color: white;
    font-size: 14px; /* Reduced font size */
    padding: 8px 15px; /* Reduced padding */
    border-radius: 5px;
    border: none;
}

        .btn-primary:hover {

            background-color: #0056b3;

        }

        .btn-back {

            background-color: #6c757d;

            color: white;

            font-size: 16px;

            padding: 10px 20px;

            border-radius: 5px;

            border: none;

            margin-top: 10px;

        }

        .btn-back:hover {

            background-color: #5a6268;

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

      

        <form action="allmovies.php" method="POST">

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

            $whereClause = "WHERE movies.title LIKE '%$movie_search%' AND movies.category_id = '$catid'";

        } elseif ($movie_search) {

            $whereClause = "WHERE movies.title LIKE '%$movie_search%'";

        } elseif ($catid) {

            $whereClause = "WHERE movies.category_id = '$catid'";

        }

        echo "<div class='search-container'>
                <button class='btn-back' onclick='window.location.href=\"allmovies.php\"'>Back</button>
              </div>";

    }

    $categoriesQuery = mysqli_query($con, "SELECT * FROM categories");

    $foundMovies = false;

    while ($category = mysqli_fetch_assoc($categoriesQuery)) {

        $sql = "SELECT movies.*, categories.catname 

                FROM movies 

                INNER JOIN categories ON categories.catid = movies.category_id 

                $whereClause AND movies.category_id = '{$category['catid']}' 

                ORDER BY movies.movieid DESC";



        $result = mysqli_query($con, $sql);



        if (mysqli_num_rows($result) > 0) {

            echo "<div class='category-title'>{$category['catname']}</div>";

            echo "<div class='movie-container'>";

            while ($movie = mysqli_fetch_assoc($result)) {

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

            $foundMovies = true;

        }

    }



    if (!$foundMovies && $whereClause) {

        echo "<div class='no-movies'>No movies match your search criteria</div>";

    }

    ?>

</div>

</body>

</html>
