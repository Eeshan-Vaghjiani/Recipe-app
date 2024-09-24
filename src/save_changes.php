<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    header("HTTP/1.1 403 Forbidden");
    exit("Access denied. You are not authorized to perform this action.");
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipe_app";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Decode JSON data from POST request
$data = json_decode(file_get_contents('php://input'), true);

$users = $data['users'];
$deleteIds = $data['deleteIds'];

$response = [];
$error_occurred = false;

// Start transaction
$conn->begin_transaction();

// Process deletions first
foreach ($deleteIds as $userId) {
    if ($userId !== 'New') {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            $error_occurred = true;
            $response[] = ['success' => false, 'message' => 'Error deleting user with ID ' . $userId . ': ' . $stmt->error];
        }
        $stmt->close();
    }
}

// Process updates and inserts
foreach ($users as $user) {
    $userId = isset($user['id']) ? (int) $user['id'] : null;
    $username = isset($user['username']) ? htmlspecialchars($user['username']) : '';
    $password = isset($user['password']) ? $user['password'] : '';
    $email = isset($user['email']) ? filter_var($user['email'], FILTER_SANITIZE_EMAIL) : '';
    $role = isset($user['role']) ? htmlspecialchars($user['role']) : '';
    $status = isset($user['status']) ? htmlspecialchars($user['status']) : '';

    if ($userId == 'New' || $userId == null) {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, role, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $password, $email, $role, $status);
    } else {
        // Update existing user
        if (!empty($password)) {
            $stmt = $conn->prepare("UPDATE users SET username=?, password=?, email=?, role=?, status=? WHERE id=?");
            $stmt->bind_param("sssssi", $username, $password, $email, $role, $status, $userId);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role=?, status=? WHERE id=?");
            $stmt->bind_param("ssssi", $username, $email, $role, $status, $userId);
        }
    }

    if ($stmt->execute()) {
        if ($userId === 'New' || $userId === null) {
            // Get the ID of the newly inserted user
            $newUserId = $stmt->insert_id;
            $response[] = ['success' => true, 'message' => 'User added successfully.', 'newId' => $newUserId];
        } else {
            $response[] = ['success' => true, 'message' => 'User data updated successfully.'];
        }
    } else {
        $error_occurred = true;
        if ($userId === 'New' || $userId === null) {
            $response[] = ['success' => false, 'message' => 'Error adding new user: ' . $stmt->error];
        } else {
            $response[] = ['success' => false, 'message' => 'Error updating user data: ' . $stmt->error];
        }
    }

    $stmt->close();
}

// Commit or rollback transaction based on success or failure
if ($error_occurred) {
    $conn->rollback();
} else {
    $conn->commit();
}

$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
