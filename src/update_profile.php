<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: \RECIPE\src\signin.php");
    exit();
}

$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "recipe_app";

// Create connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$new_username = $_POST['username'];
$new_email = $_POST['email'];

// Update user details in the database
$current_username = $_SESSION['username'];

if ($new_password) {
    $sql = "UPDATE users SET username = ?, email = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $new_username, $new_email, $current_username);
} else {
    $sql = "UPDATE users SET username = ?, email = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $new_username, $new_email, $current_username);
}

if ($stmt->execute()) {
    // Update session username if it has changed
    if ($current_username !== $new_username) {
        $_SESSION['username'] = $new_username;
    }
    header("Location: \RECIPE\src\profile.php?update=success");
} else {
    echo "Error updating profile: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
