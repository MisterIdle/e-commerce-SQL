<?php
require 'vendor/autoload.php'; // Autoload necessary dependencies
require 'php/db.php'; // Include database credentials
require 'php/send.php'; // Include seed data logic

// Create a new PDO instance to connect to the MySQL server (without specifying the database initially)
$pdo = new PDO("mysql:host=$host;charset=utf8", $user, $pass);

// Set error reporting mode to exceptions
// PDO::ATTR_ERRMODE is an attribute that defines how PDO will report errors.
// By setting it to PDO::ERRMODE_EXCEPTION, it means any database error will throw a PHP Exception.
// This allows you to handle database errors more effectively and avoid silent failures.
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$dbExists = false;

// Check if the database already exists
$stmt = $pdo->query("SHOW DATABASES LIKE '$db'");
if ($stmt->rowCount() > 0) {
    $dbExists = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle request to create a new database
    if (isset($_POST['create'])) {

        if ($dbExists) {
            $message = "Database already exists!";
        } else {
            // Create the database if it doesn't exist
            createDatabase($pdo);
            $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
            createTables($pdo); // Create necessary tables
            sendDatabase($pdo); // Insert seed data into the database

            // Check again if the database exists and update the $dbExists flag
            $stmt = $pdo->query("SHOW DATABASES LIKE '$db'");
            if ($stmt->rowCount() > 0) {
                $dbExists = true;
            }

            $message = "Database successfully created!";
        }
    }

    // Handle request to drop (delete) the database
    if (isset($_POST['clear'])) {
        if ($dbExists) {
            // Drop the database if it exists
            $pdo->exec("DROP DATABASE $db");
            $dbExists = false;
            $message = "Database successfully deleted!";
        } else {
            $message = "Database does not exist. Nothing to delete.";
        }
    }

    // Handle request to add more data into the existing database
    if (isset($_POST["add"])) {
        // Connect to the database
        if (!$dbExists) {
            $message = "Database does not exist. Please create the database first.";
        } else {
            $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
            sendDatabase($pdo); // Insert seed data into the database
            $message = "Additional data successfully added!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Management</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <h1>Database Administration</h1>
    <h2>Website by HOUBLOUP Alexy (MisterIdle)</h2>
    <div class="link">
        <a href="../phpmyadmin">Go to phpMyAdmin</a>
    </div>

    <!-- Display any messages such as success or error messages -->
    <?php if (isset($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Indicate whether the database exists or not -->
    <!-- : equal true or false -->
    <p><?php echo $dbExists ? "Database '$db' exists." : "Database '$db' does not exist."; ?></p>
    
    <!-- Form to trigger database creation, deletion, or adding more data -->
    <form method="POST">
        <button type="submit" name="create">Create Database</button>
        <button type="submit" name="clear">Delete Database</button>
        <!-- Disable the "Add Data" button if the database doesn't exist -->
        <button type="submit" name="add" <?php echo $dbExists ? '' : 'disabled'; ?>>Add Data</button>
    </form>
</body>
</html>
