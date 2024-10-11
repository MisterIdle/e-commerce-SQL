<?php
use Faker\Factory;

function sendDatabase($pdo) {
    $faker = Factory::create(); // Initialize Faker to generate fake data

    // Fill the 'user' table
    for ($i = 0; $i < 10; $i++) {
        $username = $faker->userName;
        $password = password_hash($faker->password, PASSWORD_DEFAULT); // Hash the password for security
        $email = $faker->unique()->safeEmail; // Ensure unique emails

        // Insert fake data into the 'user' table
        $stmt = $pdo->prepare("INSERT INTO user (username, first_name, last_name, password, email, phone) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $faker->firstName, $faker->lastName, $password, $email, $faker->phoneNumber]);
    }

    // Fill the 'address' table
    for ($i = 0; $i < 10; $i++) {
        $userId = $i + 1; // Assign addresses to users sequentially
        // Insert fake data into the 'address' table
        $stmt = $pdo->prepare("INSERT INTO address (user_id, street, city, state, zip_code, country) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $faker->streetAddress, $faker->city, $faker->state, $faker->postcode, $faker->country]);
    }

    // Fill the 'product' table
    for ($i = 0; $i < 20; $i++) {
        // Insert fake data into the 'product' table
        $stmt = $pdo->prepare("INSERT INTO product (name, description, price, stock) VALUES (?, ?, ?, ?)");
        $stmt->execute([$faker->word, $faker->text, $faker->randomFloat(2, 1, 100), $faker->numberBetween(1, 50)]);
    }

    // Fill the 'cart' table
    for ($i = 1; $i <= 10; $i++) {
        // Insert a cart for each user
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$faker->numberBetween(1, 10), $faker->numberBetween(1, 20), $faker->numberBetween(1, 5)]);
    }

    // Fill the 'command' table
    for ($i = 1; $i <= 10; $i++) {
        $status = $faker->randomElement(['pending', 'completed', 'cancelled']); // Random status
        // Insert fake data into the 'command' table
        $stmt = $pdo->prepare("INSERT INTO command (user_id, status) VALUES (?, ?)");
        $stmt->execute([$i, $status]);
    }

    // Fill the 'invoices' table
    for ($i = 1; $i <= 10; $i++) {
        $total = $faker->randomFloat(2, 10, 200); // Random total price
        // Insert fake data into the 'invoices' table
        $stmt = $pdo->prepare("INSERT INTO invoices (command_id, total) VALUES (?, ?)");
        $stmt->execute([$i, $total]);
    }

    // Fill the 'photo' table
    for ($i = 1; $i <= 20; $i++) {
        $url = $faker->imageUrl(640, 480, 'business', true); // Generate a random image URL
        // Insert fake data into the 'photo' table
        $stmt = $pdo->prepare("INSERT INTO photo (product_id, url) VALUES (?, ?)");
        $stmt->execute([$i, $url]);
    }

    // Get the maximum number of users
    $stmt = $pdo->query("SELECT COUNT(*) FROM user");
    $maxUserId = $stmt->fetchColumn(); // Fetch the user count

    // Fill the 'rate' table
    for ($i = 1; $i <= 20; $i++) {
        // Assign random user to each product rating
        $userId = rand(1, $maxUserId); // Select a random user ID
        // Insert fake data into the 'rate' table
        $stmt = $pdo->prepare("INSERT INTO rate (product_id, user_id, rating) VALUES (?, ?, ?)");
        $stmt->execute([$i, $userId, rand(1, 5)]); // Random rating between 1 and 5
    }

    // Fill the 'payment' table
    for ($i = 1; $i <= 10; $i++) {
        $method = $faker->randomElement(['Credit Card', 'Debit Card', 'PayPal', 'Bank Transfer']); // Random payment method
        // Insert fake data into the 'payment' table
        $stmt = $pdo->prepare("INSERT INTO payment (user_id, method) VALUES (?, ?)");
        $stmt->execute([$i, $method]);
    }
}
?>
