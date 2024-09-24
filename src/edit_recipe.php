<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: /RECIPE/src/signin.php");
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

// Initialize variables
$recipe_id = $_GET['id']; // Get recipe ID from URL parameter

// Fetch recipe details
$sql = "SELECT recipes.id, recipes.recipe_name, recipes.recipe_image, recipes.ingredients, recipes.instructions, categories.name AS category_name 
        FROM recipes 
        JOIN categories ON recipes.category_id = categories.id
        WHERE recipes.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $recipe = $result->fetch_assoc();
} else {
    $_SESSION['error_message'] = "Recipe not found.";
    header("Location: /RECIPE/src/u_recipe.php");
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/RECIPE/src/style.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Recipe</title>
</head>
<body>
    <header>
        <div>
            <img id="logo" src="/RECIPE/IMG/logo.png" alt="Recipe Logo">
            <h1 id="title">RECIPE</h1>
            <nav id="btn">
                <a href="/RECIPE/src/u_index.php"><button class="buttons">Home</button></a>
                <a href="/RECIPE/src/u_recipe.php"><button class="buttons">Recipe</button></a>
                <a href="/RECIPE/src"><button class="buttons">About</button></a>
                <a href="/RECIPE/src/add_recipe.php"><button class="buttons">Add Recipe</button></a>
                <a href="/RECIPE/src/index.php"><button class="buttons">Logout <b>&#x2398;</b></button></a>
                <a href="/RECIPE/src/profile.php"><button class="profile"><img id="prof" src="/RECIPE/IMG/char.png" alt="Profile"><p id = "usernamesession"><?php echo $_SESSION['username']; ?></p></button></a>
            </nav>
        </div>
    </header>
    <main class="recipehead">
        <div class="contents">
            <h1>Edit Recipe</h1>
            <?php if (isset($_SESSION['error_message'])): ?>
                <p style="color: red;"><?php echo $_SESSION['error_message']; ?></p>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
            <div class="recipe-details">
                <!-- Left-hand side showing current recipe details -->
                <h2 id="recipe_name"><?php echo htmlspecialchars($recipe['recipe_name']); ?></h2>
                <img id="recipe_image" class="recipe-image" src="/RECIPE/uploads/<?php echo htmlspecialchars($recipe['recipe_image']); ?>" alt="<?php echo htmlspecialchars($recipe['recipe_name']); ?>">
                <p>Category: <span id="category_name"><?php echo htmlspecialchars($recipe['category_name']); ?></span></p>
                <p>Ingredients: <span id="ingredients"><?php echo htmlspecialchars($recipe['ingredients']); ?></span></p>
                <p>Instructions: <span id="instructions"><?php echo htmlspecialchars($recipe['instructions']); ?></span></p>
                <form id="deleteForm" action="/RECIPE/src/delete_recipe.php?id=<?php echo $recipe['id']; ?>" method="POST">
                    <button type="submit" onclick="return confirm('Are you sure you want to delete this recipe?')">Delete Recipe</button>
                </form>
            </div>
            <div class="edit-form">
                <!-- Right-hand side edit form -->
                <form id="editForm" enctype="multipart/form-data">
                    <input type="hidden" name="recipe_id" value="<?php echo $recipe['id']; ?>">
                    
                    <label for="edit_recipe_name">Recipe Name:</label>
                    <input type="text" id="edit_recipe_name" name="recipe_name" value="<?php echo htmlspecialchars($recipe['recipe_name']); ?>" required>
                    
                    <label for="edit_recipe_image">Recipe Image:</label>
                    <input type="file" id="edit_recipe_image" name="recipe_image" accept="image/*">
                    
                    <label for="edit_ingredients">Ingredients:</label>
                    <textarea id="edit_ingredients" name="ingredients" rows="4" required><?php echo htmlspecialchars($recipe['ingredients']); ?></textarea>
                    
                    <label for="edit_instructions">Instructions:</label>
                    <textarea id="edit_instructions" name="instructions" rows="6" required><?php echo htmlspecialchars($recipe['instructions']); ?></textarea>
                    
                    <button type="submit">Update Recipe</button>
                </form>
            </div>
        </div>
    </main>
    <footer></footer>

    <script>
        // Submit form via AJAX for real-time update
        document.getElementById('editForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Prepare FormData
            let formData = new FormData(this);
            
            // Send AJAX request
            fetch('/RECIPE/src/update_recipe.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update recipe details on the left-hand side dynamically
                    document.getElementById('recipe_name').textContent = data.recipe_name;
                    document.getElementById('category_name').textContent = data.category_name;
                    document.getElementById('ingredients').textContent = data.ingredients;
                    document.getElementById('instructions').textContent = data.instructions;
                    
                    // Optional: Update image if changed
                    if (data.recipe_image) {
                        document.getElementById('recipe_image').src = '/RECIPE/uploads/' + data.recipe_image;
                    }
                    
                    alert('Recipe updated successfully!');
                } else {
                    alert('Error updating recipe: ' + data.error_message);
                }
            })
            .catch(error => console.error('Error:', error));
        });

        // Handle form submission for delete
        document.getElementById('deleteForm').addEventListener('submit', function(event) {
            event.preventDefault();

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'submit=true'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Recipe deleted successfully.');
                    window.location.href = '/RECIPE/src/u_recipe.php';
                } else {
                    alert('Error deleting recipe: ' + data.error_message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>
