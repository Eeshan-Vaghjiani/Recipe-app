<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: \RECIPE\src\signin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['recipe_id'])) {
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

    // Prepare update query
    $recipe_id = $_POST['recipe_id'];
    $recipe_name = $_POST['recipe_name'];
    $ingredients = $_POST['ingredients'];
    $instructions = $_POST['instructions'];

    // Handle recipe image update if provided
    $recipe_image = null;
    if (!empty($_FILES["recipe_image"]["name"])) {
        $target_dir = "\RECIPE\uploads/";
        $target_file = $target_dir . basename($_FILES["recipe_image"]["name"]);
        if (move_uploaded_file($_FILES["recipe_image"]["tmp_name"], $target_file)) {
            $recipe_image = basename($_FILES["recipe_image"]["name"]);
        } else {
            echo json_encode(array('success' => false, 'error_message' => 'Error uploading file.'));
            exit();
        }
    }

    // Update query
    if ($recipe_image) {
        $sql_update = "UPDATE recipes SET recipe_name=?, recipe_image=?, ingredients=?, instructions=? WHERE id=?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("ssssi", $recipe_name, $recipe_image, $ingredients, $instructions, $recipe_id);
    } else {
        $sql_update = "UPDATE recipes SET recipe_name=?, ingredients=?, instructions=? WHERE id=?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("sssi", $recipe_name, $ingredients, $instructions, $recipe_id);
    }

    if ($stmt->execute()) {
        // Fetch updated details to send back in response
        $sql_select = "SELECT recipes.recipe_name, recipes.recipe_image, recipes.ingredients, recipes.instructions, categories.name AS category_name 
                       FROM recipes 
                       JOIN categories ON recipes.category_id = categories.id
                       WHERE recipes.id=?";
        $stmt_select = $conn->prepare($sql_select);
        $stmt_select->bind_param("i", $recipe_id);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        $updated_recipe = $result->fetch_assoc();

        echo json_encode(array(
            'success' => true,
            'recipe_name' => $updated_recipe['recipe_name'],
            'category_name' => $updated_recipe['category_name'],
            'ingredients' => $updated_recipe['ingredients'],
            'instructions' => $updated_recipe['instructions'],
            'recipe_image' => $updated_recipe['recipe_image']
        ));
    } else {
        echo json_encode(array('success' => false, 'error_message' => 'Error updating recipe.'));
    }

    $stmt->close();
    $conn->close();
    exit();
} else {
    echo json_encode(array('success' => false, 'error_message' => 'Invalid request.'));
    exit();
}
?>
