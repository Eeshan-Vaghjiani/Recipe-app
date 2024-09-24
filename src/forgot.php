<?php
session_start(); // Start the session

// Establish database connection
$conn = new mysqli("localhost", "root", "", "recipe_app");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$username_or_email = "";
$new_password = "";
$confirm_password = "";
$error_message = "";
$success_message = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize inputs
    $username_or_email = trim($_POST['username_or_email']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Check if new password and confirm password match
    if ($new_password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Verify username or email
        $sql = "SELECT id, username, email FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username_or_email, $username_or_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // User found, update password
            $user = $result->fetch_assoc();
            $user_id = $user['id'];

            // Update password
            $sql_update = "UPDATE users SET password = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("si", $new_password, $user_id);

            if ($stmt_update->execute()) {
                $success_message = "Password updated successfully!";
            } else {
                $error_message = "Error updating password: " . $stmt_update->error;
            }

            $stmt_update->close();
        } else {
            $error_message = "Invalid username or email.";
        }

        $stmt->close();
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #faebd7;
            color: black;
            padding: 1em;
            text-align: center;
        }
        header img {
            vertical-align: middle;
            width: 50px;
        }
        header h1 {
            display: inline;
            font-size: 2em;
            margin: 0;
        }
        header nav {
            margin-top: 10px;
        }
        header nav .buttons, header nav .profile {
            background-color: white;
            border: none;
            color: black;
            padding: 10px;
            margin: 5px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }
        main {
            padding: 2em;
            text-align: center;
            background-image: url(/RECIPE/IMG/bg.jpg);
            height: 715px;
        }
        .contents {
            background-color: white;
            padding: 2em;
            margin: 2em auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(100,100,100,0.6);
            max-width: 500px;
            margin-top: 110px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label, input, button {
            margin: 10px 0;
            font-size: 1em;
        }
        input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            background-color: #1161ee;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
        }
        button:hover {
            background-color: #1454c4;
        }
        
    </style>
</head>
<body>
    <header>
        <div>
            <img id="logo" src="\RECIPE\IMG/logo.png" alt="Recipe Logo">
            <h1 id="title">RECIPE</h1>
            <nav id="btn">
                <a href="\RECIPE\src\index.php"><button class="buttons">Home</button></a>
                <a href="\RECIPE\src\recipe.php"><button class="buttons">Recipe</button></a>
                <a href="\RECIPE\src\u_index.php\#about"><button class="buttons">About</button></a>
                <a href="\RECIPE\src\u_index.php\#contact"><button class="buttons">Contact</button></a>
                <a href="\RECIPE\src\signin.php"><button class="buttons">Sign In</button></a>
            </nav>
        </div>
    </header>
    <main>
        <div class="contents">
            <h1>Forgot Password</h1>
            <?php
            if (!empty($error_message)) {
                echo "<p style='color: red;'>$error_message</p>";
            }
            if (!empty($success_message)) {
                echo "<p style='color: green;'>$success_message</p>";
            }
            ?>
            <form action="\RECIPE\src\forgot.php" method="post">
                <label for="username_or_email">Username or Email:</label>
                <input type="text" id="username_or_email" name="username_or_email" value="<?php echo htmlspecialchars($username_or_email); ?>" required>
                
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>
                
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                
                <button type="submit">Reset Password</button>
            </form>
        </div>
    </main>
</body>
</html>
