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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="\RECIPE\src\style.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recipes</title>
</head>
<body>
    <header>
        <div>
            <img id="logo" src="\RECIPE\IMG/logo.png" alt="Recipe Logo">
            <h1 id="title">RECIPE</h1>
            <nav id="btn">
                <a href="\RECIPE\src\u_index.php"><button class="buttons">Home</button></a>
                <a href="\RECIPE\src\u_recipe.php"><button class="buttons">Recipe</button></a>
                <a href="\RECIPE\src\"><button class="buttons">About</button></a>
                <a href="\RECIPE\src\add_recipe.php"><button class="buttons">Add Recipe</button></a>
                <a href="\RECIPE\src\index.php"><button class="buttons">Logout <b>&#x2398;</b></button></a>
                <a href="\RECIPE\src\profile.php"><button class="profile"><img id="prof" src="\RECIPE\IMG/char.png" alt="Profile"><p id = "usernamesession"><?php echo $_SESSION['username']; ?></p></button></a>
            </nav>
        </div>
    </header>
    <main class="recipehead">
        <div class="contents">
            <h1>Recipes</h1>

            <?php

            // Display success message if set
            if (isset($_SESSION['success_message'])) {
                echo "<p style='color: green;'>{$_SESSION['success_message']}</p>";
                unset($_SESSION['success_message']); // Clear the message after displaying
            }

            // Fetch and display recipes
            $sql = "SELECT recipes.id, recipes.recipe_name, users.username AS recipe_owner, recipes.recipe_image, recipes.ingredients, recipes.instructions, categories.name AS category_name 
                    FROM recipes 
                    JOIN categories ON recipes.category_id = categories.id
                    JOIN users ON recipes.recipe_owner = users.id";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<ul>";
                while ($row = $result->fetch_assoc()) {
                    // Check if logged-in user is the recipe owner
                    $edit_link = "";
                    if ($_SESSION['username'] == $row['recipe_owner']) {
                        $edit_link = "<a href='/RECIPE/src/edit_recipe.php?id={$row['id']}'>Edit</a>";
                    }

                    // Output each recipe item with edit link if applicable
                    echo "<li id='recipe{$row['id']}'>
                        <h2>{$row['recipe_name']}</h2>
                        <p>by {$row['recipe_owner']}</p>
                        <img class='recipe-image' src='\RECIPE\uploads/{$row['recipe_image']}' alt='{$row['recipe_name']}'>
                        <p>Category: {$row['category_name']}</p>
                        <p>Ingredients: " . (!empty($row['ingredients']) ? $row['ingredients'] : "No ingredients provided") . "</p>
                        <p>Instructions: " . (!empty($row['instructions']) ? $row['instructions'] : "No instructions provided") . "</p>
                        $edit_link
                    </li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No recipes found.</p>";
            }

            // Close database connection
            $conn->close();
            ?>
        </div>
    </main>
    <footer></footer>
</body>
</html>
