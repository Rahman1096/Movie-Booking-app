<?php 
include('../connect.php');

if (!isset($_SESSION['uid'])) {
    echo "<script> window.location.href='../login.php'; </script>";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movies</title>

   <!-- Google Fonts -->
   <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600|Poppins:400,600|Roboto:400,600" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- FontAwesome for star rating -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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
            padding: 8px 12px; /* Reduced padding for smaller button */
            font-size: 12px; /* Reduced font size for the button */
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

        table th {
            background-color: #007bff;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table img {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }

        /* Star rating color */
        .star-rating i {
            font-size: 18px;
        }

        .star-rating .filled {
            color: black;
        }

        .star-rating .empty {
            color: lightgray;
        }
        h2 {
    text-align: center;
}

        /* Footer Styling */
        footer {
            font-size: 12px;
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: white;
        }

                /* Button Alignment */
                .form-group {
            display: flex;
            justify-content: center;
        }

        .form-group input[type="submit"] {
            width: 50%;
            text-align: center;
        }
    </style>
</head>

<body>

<?php include('header.php'); ?>

<div class="container">
    <div class="form-container">
        <h2 id="form-title">Add New Movie</h2>
        <form action="movies.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="editid" value="<?php echo isset($_GET['editid']) ? $_GET['editid'] : ''; ?>">

            <div class="form-group">
                <select name="catid" class="form-control" required>
                    <option value="">Select Category</option>
                    <?php
                    $sql = "SELECT * FROM `categories`";
                    $res = mysqli_query($con, $sql);
                    if (mysqli_num_rows($res) > 0) {
                        while ($data = mysqli_fetch_array($res)) {
                            echo "<option value='{$data['catid']}'>{$data['catname']}</option>";
                        }
                    } else {
                        echo "<option value=''>No Category found</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="title" id="title" placeholder="Enter Movie Title" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="description" id="description" placeholder="Enter Movie Description" required>
            </div>
            <div class="form-group">
                <input type="date" class="form-control" name="release_date" id="release_date" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="language" id="language" placeholder="Enter Language" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="rating" id="rating" placeholder="Enter Rating (1-5)" required>
            </div>

            <div class="form-group">
         <input type="number" class="form-control" name="duration" id="duration" placeholder="Enter Duration (in minutes)" value="<?php echo isset($data['duration']) ? $data['duration'] : ''; ?>" required>
           </div>
            <div class="form-group">
              <select name="status" class="form-control" required>
              <option value="now_showing" <?php echo (isset($data['status']) && $data['status'] == 'now_showing') ? 'selected' : ''; ?>>Now Showing</option>
              <option value="upcoming" <?php echo (isset($data['status']) && $data['status'] == 'upcoming') ? 'selected' : ''; ?>>Upcoming</option>
               <option value="archived" <?php echo (isset($data['status']) && $data['status'] == 'archived') ? 'selected' : ''; ?>>Archived</option>
              </select>
            </div>
            <div class="form-group">
                <input type="number" class="form-control" name="price" id="price" placeholder="Enter Price" required>
            </div>
            <div class="form-group">
                <input type="file" class="form-control" name="image" id="image" accept="image/*">
                <input type="hidden" name="existing_image" id="existing_image">
            </div>
            <div class="form-group">
                <input type="url" class="form-control" name="trailer" id="trailer" placeholder="Enter YouTube URL" required>
            </div>
            <div class="form-group">
                <?php if (isset($_GET['editid'])): ?>
                    <input type="submit" class="btn btn-primary" value="Update" name="update" id="submit-btn">
                <?php else: ?>
                    <input type="submit" class="btn btn-primary" value="Add" name="add" id="submit-btn">
                <?php endif; ?>
            </div>
        </form>
    </div>

    <h2>Movies List</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Category</th>
                <th>Movie Price</th>
                <th>Language</th>
                <th>status</th>
                <th>Rating</th>
                <th>Poster</th>
                <th>Trailer</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT movies.*, categories.catname FROM movies INNER JOIN categories ON categories.catid = movies.category_id";
            $res = mysqli_query($con, $sql);
            if (mysqli_num_rows($res) > 0) {
                while ($data = mysqli_fetch_array($res)) {
                    $fullStars = floor($data['rating']);
                    $halfStar = ($data['rating'] - $fullStars >= 0.5) ? 1 : 0;
                    $emptyStars = 5 - $fullStars - $halfStar;

                    echo "<tr>
                        <td>{$data['movieid']}</td>
                        <td>{$data['title']}</td>
                        <td>{$data['catname']}</td>
                        <td>{$data['price']}</td>
                        <td>{$data['language']}</td>
                        <td>{$data['status']}</td>
                        <td>
                            <div class='star-rating'>";
                            for ($i = 0; $i < $fullStars; $i++) {
                              echo "<i class='fas fa-star filled'></i>";
                          }
                          if ($halfStar) {
                              echo "<i class='fas fa-star-half-alt filled'></i>";
                          }
                          for ($i = 0; $i < $emptyStars; $i++) {
                              echo "<i class='far fa-star empty'></i>";
                          }
                          echo "</div></td>
                              <td><img src='uploads/{$data['image']}' alt='Poster'></td>
                              <td><a href='{$data['trailer']}' target='_blank'>Watch Trailer</a></td>
                              <td>
                                  <a href='movies.php?editid={$data['movieid']}' class='btn btn-primary'>Edit</a>
                                  <a href='movies.php?deleteid={$data['movieid']}' class='btn btn-danger'>Delete</a>
                              </td>
                          </tr>";
                      }
                  } else {
                      echo "<tr><td colspan='10'>No movies found</td></tr>";
                  }
                  ?>
              </tbody>
          </table>
      </div>
      
      <?php include('footer.php'); ?>
      
      </body>
      </html>
      
      <?php
      
      // Handling delete action
      if (isset($_GET['deleteid'])) {
          $deleteid = $_GET['deleteid'];
          $sql = "DELETE FROM movies WHERE movieid = '$deleteid'";
          if (mysqli_query($con, $sql)) {
              echo "<script> alert('Movie deleted successfully');</script>";
              echo "<script> window.location.href='movies.php'; </script>";
          } else {
              echo "<script> alert('Error deleting movie');</script>";
          }
      }
      
      // Handling edit action
      if (isset($_GET['editid'])) {
          $editid = $_GET['editid'];
          $sql = "SELECT * FROM movies WHERE movieid = '$editid'";
          $result = mysqli_query($con, $sql);
          $data = mysqli_fetch_array($result);
          echo "<script>
              document.getElementsByName('title')[0].value = '{$data['title']}';
              document.getElementsByName('description')[0].value = '{$data['description']}';
              document.getElementsByName('release_date')[0].value = '{$data['release_date']}';
              document.getElementsByName('language')[0].value = '{$data['language']}';
              document.getElementsByName('rating')[0].value = '{$data['rating']}';
              document.getElementsByName('trailer')[0].value = '{$data['trailer']}';
              document.getElementsByName('catid')[0].value = '{$data['category_id']}';
              document.getElementsByName('status')[0].value = '{$data['status']}';
              document.getElementsByName('duration')[0].value = '{$data['duration']}';
              document.getElementsByName('price')[0].value = '{$data['price']}';
              document.getElementById('form-title').innerText = 'Edit Movie';
              document.getElementById('submit-btn').value = 'Update';
              document.getElementById('existing_image').value = '{$data['image']}';
          </script>";
      }
      
     // Handling movie update
if (isset($_POST['update'])) {
    $catid = $_POST['catid'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $releasedate = $_POST['release_date'];
    $language = $_POST['language'];
    $rating = $_POST['rating'];
    $trailer = $_POST['trailer'];
    $duration = $_POST['duration']; 
    $status = $_POST['status']; 
    $price = $_POST['price'];  
    $image = $_FILES['image']['name'];
    $editid = $_POST['editid']; // Get the movie ID from a hidden input field

    // Check if the movie already exists (excluding the current movie)
    $checkSql = "SELECT * FROM movies WHERE title='$title' AND image='$image' AND movieid != '$editid'";
    $checkRes = mysqli_query($con, $checkSql);
    if (mysqli_num_rows($checkRes) > 0) {
        echo "<script> alert('Movie with the same title and poster already exists.');</script>";
    } else {
        // Check if a new image is uploaded
        if ($image) {
            $tmp_image = $_FILES['image']['tmp_name'];
            move_uploaded_file($tmp_image, "uploads/$image");
        } else {
            // If no new image is uploaded, retain the existing image
            $image = $_POST['existing_image'];
        }

        // Prepare the update query
        $sql = "UPDATE movies 
                SET title='$title', 
                    description='$description', 
                    release_date='$releasedate', 
                    language='$language', 
                    rating='$rating', 
                    image='$image', 
                    trailer='$trailer', 
                    category_id='$catid', 
                    duration='$duration', 
                    status='$status',  
                    price='$price'
                WHERE movieid='$editid'";

        // Execute the query and check for errors
                // Execute the query and check for errors
                if (mysqli_query($con, $sql)) {
                    echo "<script> alert('Movie updated successfully');</script>";
                    echo "<script> window.location.href='movies.php'; </script>";
                } else {
                    // Output the error message if the query fails
                    echo "<script> alert('Error updating movie: " . mysqli_error($con) . "');</script>";
                }
            }
        }
// Handling movie insert
if (isset($_POST['add']) && $_POST['add'] == 'Add') {
    $catid = $_POST['catid'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $releasedate = $_POST['release_date'];
    $language = $_POST['language'];
    $rating = $_POST['rating'];
    $trailer = $_POST['trailer'];
    $image = $_FILES['image']['name'];
    $duration = $_POST['duration'];  // Get the duration value
    $status = $_POST['status'];  // Get the status value
    $price = $_POST['price'];

    // Check if the movie already exists
    $checkSql = "SELECT * FROM movies WHERE title='$title' AND image='$image'";
    $checkRes = mysqli_query($con, $checkSql);
    if (mysqli_num_rows($checkRes) > 0) {
        echo "<script> alert('Movie with the same title and poster already exists.');</script>";
    } else {
        $tmp_image = $_FILES['image']['tmp_name'];
        move_uploaded_file($tmp_image, "uploads/$image");

        $sql = "INSERT INTO movies (category_id, title, description, release_date, language, rating, image, trailer, duration, status, price)
                VALUES ('$catid', '$title', '$description', '$releasedate', '$language', '$rating', '$image', '$trailer', '$duration', '$status', '$price')";

        if (mysqli_query($con, $sql)) {
            echo "<script> alert('Movie added successfully');</script>";
            echo "<script> window.location.href='movies.php'; </script>";
        } else {
            echo "<script> alert('Error adding movie');</script>";
        }
    }
}

      ?>