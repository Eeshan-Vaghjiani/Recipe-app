<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="\RECIPE\src\style.css">
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
            <img id="logo" src="/RECIPE/IMG/logo.png">
            <h1 id="title">RECIPE</h1>
            <nav id="btn">
                <a href="\RECIPE\src\Index.php"><button class="buttons">Home</button></a>
                <a href="\RECIPE\src\recipe.php"><button class="buttons">Recipe</button></a>
                <a href="\RECIPE\src\Index.php\#About"><button class="buttons">About</button></a>
                <a href="\RECIPE\src\Index.php\#Contact"><button class="buttons">Contact</button></a>
                <a href="\RECIPE\src\signin.php"><button class="buttons">Sign In</button></a>
                <a href="\RECIPE\src\signup.php"><button class="buttons">Sign Up</button></a>
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
       <p id="About"><?php include 'Include/about.php';?></p>
       <p id="Contact"><?php include 'Include/contact.php';?></p>
    </main>

</body>
</html>