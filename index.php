<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Home Page</title>

    <!-- Swiper CSS -->
    <link
      rel="stylesheet"
      href="https://unpkg.com/swiper/swiper-bundle.min.css"
    />

    <style>
        /* Full Dark Theme */
        body {
           margin: 0;
           font-family: Arial, sans-serif;
           display: flex;
            flex-direction: column;
           min-height: 100vh;
           background-color: #121212; /* Solid dark color */
           color: white; /* White text for better readability */
          }
        header {
            z-index: 1000;
            background: #222; /* Dark header for contrast */
            padding: 10px;
        }
        .swiper-container {
            width: 100%;
            max-width: 1200px;
            height: 500px;
            margin: 20px auto;
            border-radius: 10px;
            overflow: hidden;
            background: #000; /* Solid black as fallback */
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.8); /* Subtle shadow for depth */
        }
        .swiper-slide {
            display: flex;
            justify-content: center;
            align-items: center;
            background: #000; /* Dark background for images */
        }
        .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Ensure the image covers the slide */
            background-color: #111; /* Fallback color if an image fails to load */
        }
        .swiper-button-prev, .swiper-button-next {
            color: white; /* Navigation buttons in white */
        }
        .swiper-pagination-bullet {
            background: white; /* Pagination bullets in white */
        }
        footer {
            background: #111; /* Dark footer */
            color: #fff;
            text-align: center;
            padding: 10px;
            position: relative;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <?php include('connect.php'); ?>
    <?php include('header.php'); ?>

    <!-- Image Slider -->
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <!-- Each slide -->
            <div class="swiper-slide">
                <img src="image/wp1945909.jpg" alt="Film Reel" onerror="this.src='images/placeholder.jpg'">
            </div>
            <div class="swiper-slide">
                <img src="image/Movie-1200-630.jpg" alt="Movie Poster" onerror="this.src='images/placeholder.jpg'">
            </div>
            <div class="swiper-slide">
                <img src="image/Site-community-image.webp" alt="movie pedia" onerror="this.src='images/placeholder.jpg'">
            </div>
            <div class="swiper-slide">
                <img src="image/17c747e9aca225997c7860ff05229d21.jpg" alt="design" onerror="this.src='images/placeholder.jpg'">
            </div>
            <div class="swiper-slide">
                <img src="image/photo-1626814026160-2237a95fc5a0.jpg" alt="Film Roll" onerror="this.src='images/placeholder.jpg'">
            </div>
            <div class="swiper-slide">
                <img src="image/Piturro-Vincent_SciFiFilm_07062022_AM-21.jpg" alt="Sci-Fi Film" onerror="this.src='images/placeholder.jpg'">
            </div>
        </div>

        <!-- Pagination -->
        <div class="swiper-pagination"></div>
        <!-- Navigation buttons -->
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>

    <?php include('footer.php'); ?>

    <!-- Swiper JS -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    
    <script>
        // Initialize Swiper
        const swiper = new Swiper('.swiper-container', {
            loop: true,
            autoplay: {
                delay: 3000,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            effect: 'fade', // Smooth fade transition
        });
    </script>
</body>
</html>

          
