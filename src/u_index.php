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
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RECIPE</title>
</head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>RECIPE</title>
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

                  </div></button></a>
            </nav>
        </div>
    </header>
    <main id="indmain">
        <div id="div">
            <img id="recipe" src="/RECIPE/IMG/intro.jpg">
            <div class ="contents" id = "specialcontent">
            <div id="info">
            <h1 class="intro" >Banana Crunchies</h1>
            <p class="intro">Experience a fruity twist on snacking with Banana Crunchies, where each chip is a tropical delight waiting to be savored. Made from the finest bananas and infused with exotic flavors</p>
            <a href="\RECIPE\src\banana.html"><button id="intro" >Read More</button></a>
        </div>
       <p id="about"><?php include 'Include/about.php';?></p>
       <p id="contact"><?php include 'Include/contact.php';?></p>
    </main>
</body>
</html>