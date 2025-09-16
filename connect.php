<?php
// Start a session only if it is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Establish database connection
$con = mysqli_connect('localhost', 'root', '', 'dbmovies');

if (!$con) {
    die('Cannot establish a connection to the database');
}
?>
