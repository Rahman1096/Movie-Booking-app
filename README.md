# CinemaBuddy 🎬🍿
**Full-Stack Movie Ticket Booking & Management System**

CinemaBuddy is a comprehensive, full-stack web application designed to streamline the movie ticket booking process for users while providing a powerful Content Management System (CMS) for administrators. 

## 🚀 Features

### 👤 User (Customer) Features
* **Browse & Search:** Explore 'Now Showing', 'Trending', and 'Upcoming' movies. Filter by categories or search by name.
* **Interactive Seat Selection:** Dynamically select VIP, Premium, or Regular seats using an interactive, color-coded grid updated in real-time via AJAX.
* **Secure Booking & Payments:** Book tickets and calculate total costs dynamically based on seat types.
* **Refund Management:** Cancel active bookings and initiate automated refund requests.
* **User Reviews:** Leave ratings and reviews for movies, complete with spoiler tags.

### 🛡️ Admin CMS Features
* **Real-Time Analytics Dashboard:** Track active bookings, total revenue, pending refunds, and most popular movies.
* **Theater & Screen Management:** Add theaters, configure screens, and automatically generate seat layouts.
* **Movie Scheduling:** Assign movies to specific screens with designated start and end times to avoid scheduling conflicts.
* **Refund Processing:** Approve or reject user refund requests. Approved refunds automatically free up reserved seats in the database.
* **Secure Data Handling:** Employs prepared statements to prevent SQL injection and transaction rollbacks (ACID compliance) for booking integrity.

## 🛠️ Tech Stack
* **Frontend:** HTML5, CSS3, JavaScript (jQuery, AJAX), Bootstrap 5, Swiper.js
* **Backend:** PHP (Session Management, Transaction Handling)
* **Database:** MySQL (Relational Schema, Complex Joins)

## ⚙️ Installation & Setup
Setup Local Server:
Install XAMPP or [WAMP] and start the Apache and MySQL modules.

Database Configuration:

Open phpMyAdmin (http://localhost/phpmyadmin).

Create a new database named dbmovies.

Import the database schema (e.g., dbmovies.sql) into the new database.

Project Directory:
Move the cloned CinemaBuddy folder into the htdocs (for XAMPP) or www (for WAMP) directory.

Run the Application:
Open your browser and navigate to: http://localhost/CinemaBuddy/index.php

👨‍💻 Contributors
Azeem Raza
Muhammad Rahman
Rana Muhammad Usman
