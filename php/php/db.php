<?php

$host = 'localhost';
$db   = 'e_commerce_db'; // Name of the database

// Root because it's a local server
$user = 'root'; // Database username

// Empty password because it's a local server
$pass = ''; // Database password

try {
    // Create a PDO connection to check and create the database
    $pdo = new PDO("mysql:host=$host;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable exceptions on error

    // Function to create the database if it doesn't exist
    function createDatabase($pdo) {
        global $db;
        // Creates the database if it does not exist, and sets the character set and collation
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $db CHARACTER SET utf8 COLLATE utf8_general_ci");
        // Use the newly created database
        $pdo->exec("USE $db");
    }

    // Function to create a table if it doesn't exist
    function createTableIfNotExists($pdo, $query) {
        // Executes the SQL query to create the table
        $pdo->exec($query);
    }

    // Create all necessary tables
    function createTables($pdo) {
        // Table for storing user data
        createTableIfNotExists($pdo, "
            CREATE TABLE IF NOT EXISTS user (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL,
                first_name VARCHAR(50),
                last_name VARCHAR(50),
                password VARCHAR(255) NOT NULL, -- Password will be stored as a hashed value
                email VARCHAR(100) NOT NULL UNIQUE, -- Email must be unique
                phone VARCHAR(20)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");
        
        // Table for storing user addresses
        createTableIfNotExists($pdo, "
            CREATE TABLE IF NOT EXISTS address (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                street VARCHAR(255),
                city VARCHAR(100),
                state VARCHAR(100),
                zip_code VARCHAR(20),
                country VARCHAR(100),
                FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE -- Deletes addresses when the user is deleted
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");
        
        // Table for storing product information
        createTableIfNotExists($pdo, "
            CREATE TABLE IF NOT EXISTS product (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL, -- Product name
                description TEXT, -- Detailed description of the product
                price DECIMAL(10, 2) NOT NULL, -- Price of the product, with two decimal places
                stock INT NOT NULL -- Number of units available in stock
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");
        
        // Table for storing user carts
        createTableIfNotExists($pdo, "
            CREATE TABLE IF NOT EXISTS cart (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                product_id INT,
                quantity INT NOT NULL DEFAULT 1,
                FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");
    

        // Table for storing commands (orders)
        createTableIfNotExists($pdo, "
            CREATE TABLE IF NOT EXISTS command (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                status ENUM('pending', 'completed', 'cancelled') NOT NULL, -- Order status
                FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE -- Deletes order when the user is deleted
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");
        
        // Table for storing invoice data
        createTableIfNotExists($pdo, "
            CREATE TABLE IF NOT EXISTS invoices (
                id INT AUTO_INCREMENT PRIMARY KEY,
                command_id INT,
                total DECIMAL(10, 2) NOT NULL, -- Total amount for the invoice
                FOREIGN KEY (command_id) REFERENCES command(id) ON DELETE CASCADE -- Deletes invoice when the command is deleted
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");
        
        // Table for storing product photos
        createTableIfNotExists($pdo, "
            CREATE TABLE IF NOT EXISTS photo (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT,
                url VARCHAR(255), -- URL of the product image
                FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE -- Deletes photo when the product is deleted
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");
        
        // Table for storing product ratings
        createTableIfNotExists($pdo, "
            CREATE TABLE IF NOT EXISTS rate (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT,
                user_id INT,
                rating INT CHECK (rating >= 1 AND rating <= 5), -- Rating should be between 1 and 5
                FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE, -- Deletes rating when the product is deleted
                FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE -- Deletes rating when the user is deleted
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");
        
        // Table for storing payment methods
        createTableIfNotExists($pdo, "
            CREATE TABLE IF NOT EXISTS payment (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                method ENUM('Credit Card', 'Debit Card', 'PayPal', 'Bank Transfer') NOT NULL, -- Supported payment methods
                FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE -- Deletes payment method when the user is deleted
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");
    }
} catch (PDOException $e) {
    // Catch and display any PDO-related errors
    echo "Error: " . $e->getMessage() . "\n";
}
?>
