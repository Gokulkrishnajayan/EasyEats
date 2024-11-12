<?php
session_start();
require_once '../db.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Input validation
    if (empty($username) || empty($password)) {
        header("Location: login.php?error=All fields are required");
        exit;
    }

    // Check if the username is an email or just a username
    if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
        // The username is an email
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE email = :email");
        $stmt->bindParam(':email', $username);
    } else {
        // The username is not an email, it's a regular username
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
    }

    // Execute the query
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists and verify password
    if ($user && password_verify($password, $user['password'])) {
        // Password is correct, create session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Redirect to the home page/dashboard
        header("Location: home.php"); // Redirect after successful login
        exit;
    } else {
        // Invalid username/password
        header("Location: login.php?error=Incorrect username or password");
        exit;
    }
} else {
    // If the form is not submitted, redirect back to login page
    header("Location: login.php");
    exit;
}
