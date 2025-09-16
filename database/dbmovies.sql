-- Complete SQL Schema with Necessary Tables and Relationships -- Enable Strict SQL Mode 
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO"; 
START TRANSACTION; 
SET time_zone = "+00:00"; 

-- USERS TABLE 
CREATE TABLE `users` ( 
`userid` INT(11) NOT NULL AUTO_INCREMENT, 
`name` VARCHAR(50) NOT NULL, 
`email` VARCHAR(50) NOT NULL UNIQUE, 
`password` VARCHAR(60) NOT NULL, 
`phone` VARCHAR(15) NOT NULL, 
`role` ENUM('admin', 'customer') NOT NULL, 
PRIMARY KEY (`userid`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 

-- CATEGORIES TABLE 
CREATE TABLE `categories` ( 
`catid` INT(11) NOT NULL AUTO_INCREMENT, 
`catname` VARCHAR(50) NOT NULL, 
PRIMARY KEY (`catid`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- MOVIES TABLE 
CREATE TABLE `movies` ( 
`movieid` INT(11) NOT NULL AUTO_INCREMENT, 
`title` VARCHAR(100) NOT NULL, 
`description` TEXT NOT NULL, 
`release_date` DATE NOT NULL, 
`language` VARCHAR(50) NOT NULL, 
`rating` FLOAT CHECK (rating BETWEEN 1 AND 5), 
`image` VARCHAR(255) NOT NULL, 
`trailer` VARCHAR(255), 
`category_id` INT(11) NOT NULL, 
PRIMARY KEY (`movieid`), 
FOREIGN KEY (`category_id`) REFERENCES `categories` (`catid`) 
ON DELETE CASCADE 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 

-- THEATER TABLE 
CREATE TABLE `theater` ( 
`theaterid` INT(11) NOT NULL AUTO_INCREMENT, 
`theater_name` VARCHAR(100) NOT NULL, 
`location` VARCHAR(100) NOT NULL, 
PRIMARY KEY (`theaterid`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 

-- SCREENS TABLE 
CREATE TABLE `screens` ( 
`screenid` INT(11) NOT NULL AUTO_INCREMENT, 
`theaterid` INT(11) NOT NULL, 
`screen_name` VARCHAR(50) NOT NULL, 
`total_seats` INT(11) NOT NULL, 
PRIMARY KEY (`screenid`), 
FOREIGN KEY (`theaterid`) REFERENCES `theater` (`theaterid`) 
ON DELETE CASCADE 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEATS TABLE 
CREATE TABLE `seats` ( 
`seatid` INT(11) NOT NULL AUTO_INCREMENT, 
`screenid` INT(11) NOT NULL, 
`seat_type` ENUM('VIP', 'Premium', 'Regular') NOT NULL, 
`availability` BOOLEAN DEFAULT TRUE, 
`price` DECIMAL(10, 2) NOT NULL, 
PRIMARY KEY (`seatid`), 
FOREIGN KEY (`screenid`) REFERENCES `screens` (`screenid`) 
ON DELETE CASCADE 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 

-- BOOKINGS TABLE 
CREATE TABLE `bookings` ( 
`bookingid` INT(11) NOT NULL AUTO_INCREMENT, 
`userid` INT(11) NOT NULL, 
`screenid` INT(11) NOT NULL, 
`movieid` INT(11) NOT NULL, 
`booking_date` DATE NOT NULL, 
`status` ENUM('confirmed', 'cancelled') DEFAULT 'confirmed', 
`total_amount` DECIMAL(10, 2) NOT NULL, 
PRIMARY KEY (`bookingid`), 
FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) 
ON DELETE CASCADE, 
FOREIGN KEY (`screenid`) REFERENCES `screens` (`screenid`) 
ON DELETE CASCADE, 
FOREIGN KEY (`movieid`) REFERENCES `movies` (`movieid`) 
ON DELETE CASCADE 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 

-- PAYMENTS TABLE 
CREATE TABLE `payments` ( 
`paymentid` INT(11) NOT NULL AUTO_INCREMENT, 
`bookingid` INT(11) NOT NULL, 
`payment_date` DATE NOT NULL, 
`amount` DECIMAL(10, 2) NOT NULL, 
`method` ENUM('Credit Card', 'Debit Card', 'Cash', 'Online') NOT NULL, 
`status` ENUM('successful', 'failed') NOT NULL, 
PRIMARY KEY (`paymentid`), 
FOREIGN KEY (`bookingid`) REFERENCES `bookings` (`bookingid`) 
ON DELETE CASCADE 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

 -- REVIEWS TABLE 
CREATE TABLE `reviews` ( 
`reviewid` INT(11) NOT NULL AUTO_INCREMENT, 
`userid` INT(11) NOT NULL, 
`movieid` INT(11) NOT NULL, 
`rating` FLOAT CHECK (rating BETWEEN 1 AND 5), 
`review_text` TEXT, 
`is_spoiler` BOOLEAN DEFAULT FALSE, 
PRIMARY KEY (`reviewid`), 
FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) 
ON DELETE CASCADE, 
FOREIGN KEY (`movieid`) REFERENCES `movies` (`movieid`) 
ON DELETE CASCADE 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 

-- REFUNDS TABLE 
CREATE TABLE `refunds` ( 
`refundid` INT(11) NOT NULL AUTO_INCREMENT, 
`bookingid` INT(11) NOT NULL, 
`request_date` DATE NOT NULL, 
`refund_amount` DECIMAL(10, 2) NOT NULL, 
`status` ENUM('approved', 'rejected', 'pending') DEFAULT 'pending', 
PRIMARY KEY (`refundid`), 
FOREIGN KEY (`bookingid`) REFERENCES `bookings` (`bookingid`) 
ON DELETE CASCADE 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 

CREATE TABLE `schedule` (
    `scheduleid` INT(11) NOT NULL AUTO_INCREMENT,
    `screenid` INT(11) NOT NULL,
    `movieid` INT(11) NOT NULL,
    `start_time` DATETIME NOT NULL,
    `end_time` DATETIME NOT NULL,
    PRIMARY KEY (`scheduleid`),
    FOREIGN KEY (`screenid`) REFERENCES `screens`(`screenid`) ON DELETE CASCADE,
    FOREIGN KEY (`movieid`) REFERENCES `movies`(`movieid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `movies` 
ADD COLUMN `status` ENUM('now_showing', 'upcoming', 'archived') DEFAULT 'upcoming',
ADD COLUMN `duration` INT(11) NOT NULL COMMENT 'Duration in minutes';

ALTER TABLE `screens` 
ADD COLUMN `current_movie_id` INT(11) DEFAULT NULL, 
ADD FOREIGN KEY (`current_movie_id`) REFERENCES `movies`(`movieid`);

ALTER TABLE `seats` 
ADD COLUMN `seat_number` VARCHAR(10) NOT NULL;
 
ALTER TABLE `movies` 
ADD COLUMN `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00;

ALTER TABLE bookings ADD COLUMN booked_seat_ids VARCHAR(255); 


COMMIT; 