<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: /RECIPE/src/signin.php");
    exit();
}

// Establish database connection
$conn = new mysqli("localhost", "root", "", "recipe_app");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$recipe_name = "";
$recipe_owner = "";
$category_id = "";
$new_category = "";
$ingredients = "";
$instructions = "";
$error_message = "";

// Set logged-in user as recipe owner
$current_username = $_SESSION['username'];
$sql_user = "SELECT id FROM users WHERE username = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $current_username);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();
$recipe_owner = $user['id'];
$stmt_user->close();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize inputs
    $recipe_name = trim($_POST['recipe_name']);
    $category_id = intval($_POST['category_id']);
    $new_category = trim($_POST['new_category']);
    $ingredients = trim($_POST['ingredients']);
    $instructions = trim($_POST['instructions']);

    // Check if a new category was provided
    if (!empty($new_category)) {
        // Insert new category into the database
        $stmt_cat = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt_cat->bind_param("s", $new_category);
        if ($stmt_cat->execute()) {
            $category_id = $stmt_cat->insert_id; // Get the ID of the newly inserted category
        } else {
            $error_message = "Error adding new category: " . $stmt_cat->error;
        }
        $stmt_cat->close();
    }

    // If no errors, proceed to handle file upload and insert recipe
    if (empty($error_message)) {
        // Define the target directory
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/RECIPE/uploads/";

        // Check if the directory exists, if not, create it
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0755, true)) {
                $error_message = "Failed to create directory: " . $target_dir;
            }
        }

        // Proceed if the directory exists or was successfully created
        if (empty($error_message)) {
            $target_file = $target_dir . basename($_FILES["recipe_image"]["name"]);

            if ($_FILES["recipe_image"]["error"] === UPLOAD_ERR_OK) {
                // Check file upload success
                if (move_uploaded_file($_FILES["recipe_image"]["tmp_name"], $target_file)) {
                    $recipe_image = basename($_FILES["recipe_image"]["name"]);

                    // Use prepared statements to prevent SQL injection
                    $sql = "INSERT INTO recipes (recipe_name, recipe_owner, recipe_image, category_id, ingredients, instructions) 
                            VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssss", $recipe_name, $recipe_owner, $recipe_image, $category_id, $ingredients, $instructions);

                    // Execute the statement
                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = "Recipe added successfully!";
                        header('Location: /RECIPE/src/U_recipe.php');
                        exit();
                    } else {
                        $error_message = "Error: " . $stmt->error;
                    }

                    $stmt->close();
                } else {
                    $error_message = "Error moving uploaded file. Please check the target directory permissions and path.";
                }
            } else {
                $error_message = "File upload error: " . $_FILES["recipe_image"]["error"];
            }
        }
    }
}

// Retrieve categories for select dropdown
$sql_categories = "SELECT id, name FROM categories";
$result_categories = $conn->query($sql_categories);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Recipe</title>
    <link rel="stylesheet" href="/RECIPE/src/style.css">
</head>
<body>
    <header>
        <div>
            <img id="logo" src="/RECIPE/IMG/logo.png" alt="Recipe Logo">
            <h1 id="title">RECIPE</h1>
            <nav id="btn">
                <a href="/RECIPE/src/u_index.php"><button class="buttons">Home</button></a>
                <a href="/RECIPE/src/u_recipe.php"><button class="buttons">Recipe</button></a>
                <a href="/RECIPE/src/u_index.php#about"><button class="buttons">About</button></a>
                <a href="/RECIPE/src/u_index.php#contact"><button class="buttons">Contact</button></a>
                <a href="/RECIPE/src/add_recipe.php"><button class="buttons">Add Recipe</button></a>
                <a href="/RECIPE/src/index.php"><button class="buttons">Logout <b>&#x2398;</b></button></a>
                <a href="/RECIPE/src/profile.php"><button class="profile"><img id="prof" src="/RECIPE/IMG/char.png" alt="Profile"><p id="usernamesession"><?php echo $_SESSION['username']; ?></p></button></a>
            </nav>
        </div>
    </header>
    <main>
        <div class="contents">
            <h1>Add Recipe</h1>
            <?php
            if (!empty($error_message)) {
                echo "<p style='color: red;'>$error_message</p>";
            }
            ?>
            <form action="/RECIPE/src/add_recipe.php" method="post" enctype="multipart/form-data">
                <label for="recipe_name">Recipe Name:</label>
                <input type="text" id="recipe_name" name="recipe_name" value="<?php echo htmlspecialchars($recipe_name); ?>" required>
                
                <input type="hidden" id="recipe_owner" name="recipe_owner" value="<?php echo htmlspecialchars($recipe_owner); ?>">

                <label for="recipe_image">Recipe Image:</label>
                <input type="file" id="recipe_image" name="recipe_image" accept="image/*" required>
                
                <label for="category_id">Category:</label>
                <select id="category_id" name="category_id" required>
                    <?php while($row = $result_categories->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($row['id']); ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                    <?php endwhile; ?>
                    <option value="new">Add New Category</option>
                </select>

                <div id="new_category_input" style="display: none;">
                    <label for="new_category">New Category:</label>
                    <input type="text" id="new_category" name="new_category" placeholder="Enter new category">
                </div>
                
                <label for="ingredients">Ingredients:</label>
                <textarea id="ingredients" name="ingredients" rows="4" required><?php echo htmlspecialchars($ingredients); ?></textarea>
                
                <label for="instructions">Instructions:</label>
                <textarea id="instructions" name="instructions" rows="6" required><?php echo htmlspecialchars($instructions); ?></textarea>
                
                <button type="submit">Submit Recipe</button>
            </form>
        </div>
    </main>
    <footer></footer>

    <script>
        document.getElementById('category_id').addEventListener('change', function () {
            var newCategoryInput = document.getElementById('new_category_input');
            if (this.value === 'new') {
                newCategoryInput.style.display = 'block';
            } else {
                newCategoryInput.style.display = 'none';
            }
        });
    </script>
</body>
</html>
