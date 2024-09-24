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

// Get user details from the database
$current_username = $_SESSION['username'];
$sql = "SELECT username, email, created_at, role FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $current_username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <style>
        /* Style for main#profile_main */
#profile_main {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background-color: #f9f9f9;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    margin-top: 200px;
}

/* Style for h1 inside main#profile_main */
#profile_main h1 {
    font-size: 2rem;
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

/* Style for form#profile_form */
#profile_form {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 0 5px rgba(0,0,0,0.1);
}

/* Style for label inside form#profile_form */
#profile_form label {
    display: block;
    margin-bottom: 10px;
    font-weight: bold;
    color: #333;
}

/* Style for input[type="text"], input[type="email"], input[type="password"] inside form#profile_form */
#profile_form input[type="text"],
#profile_form input[type="email"],
#profile_form input[type="password"] {
    width: calc(100% - 22px); /* Adjust width to accommodate padding and borders */
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 1rem;
    box-sizing: border-box;
}

/* Style for p inside form#profile_form */
#profile_form p {
    margin-bottom: 10px;
    color: #666;
    font-size: 0.9rem;
}

/* Style for button inside form#profile_form */
#profile_form button {
    padding: 10px 20px;
    background-color: #333;
    color: #fff;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
}

/* Hover style for button inside form#profile_form */
#profile_form button:hover {
    background-color: #555;
}

    </style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="\RECIPE\src\style.css">
</head>
<body>
<header>
    <div>
        <img id="logo" src="\RECIPE\IMG/logo.png">
        <h1 id="title">RECIPE</h1>
        <nav id="btn">
            <a href="\RECIPE\src\u_index.php"><button class="buttons">Home</button></a>
            <a href="\RECIPE\src\u_recipe.php"><button class="buttons">Recipe</button></a>
            <a href="\RECIPE\src\u_index.php\#about"><button class="buttons">About</button></a>
            <a href="\RECIPE\src\u_index.php\#contact"><button class="buttons">Contact</button></a>
            <a href="\RECIPE\src\index.php"><button class="buttons">Logout <b>&#x2398;</b></button></a>
            <a href="\RECIPE\src\profile.php"><button class="profile"><img id="prof" src="\RECIPE\IMG/char.png"><p id = "usernamesession"><?php echo $_SESSION['username']; ?></p></button></a>
        </nav>
    </div>
</header>
<main id="profile_main">
    <h1>User Profile</h1>
    <form id="profile_form" action="\RECIPE\src\update_profile.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>

        <p>Account Created: <?php echo htmlspecialchars($user['created_at']); ?></p>
        <p>Role: <?php echo htmlspecialchars($user['role']); ?></p>

        <button type="submit">Update Profile</button>
    </form>
</main>
</body>
</html>
