<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: /RECIPE/src/signin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if recipe ID is provided and valid
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        $error_message = "Invalid recipe ID.";
        $_SESSION['error_message'] = $error_message;
        echo json_encode(['success' => false, 'error_message' => $error_message]);
        exit();
    }

    $recipe_id = $_GET['id'];

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

    // Prepare DELETE statement
    $sql = "DELETE FROM recipes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $recipe_id);

    if ($stmt->execute()) {
        // Deletion successful
        $success_message = "Recipe deleted successfully.";
        $_SESSION['success_message'] = $success_message;
        echo json_encode(['success' => true]);
        exit();
    } else {
        // Deletion failed
        $error_message = "Error deleting recipe.";
        $_SESSION['error_message'] = $error_message;
        echo json_encode(['success' => false, 'error_message' => $error_message]);
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    // If POST method is not used, return error
    $error_message = "Invalid request method.";
    $_SESSION['error_message'] = $error_message;
    echo json_encode(['success' => false, 'error_message' => $error_message]);
    exit();
}
?>
