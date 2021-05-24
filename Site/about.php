<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="styleGLA.css" type="text/css"/>
   	<link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
	<title> Informations pratiques</title>
</head>

<body>
    	<! -- Début Barre principale -->

				<ul class="barMenu">
				  <li><a href="index.php">Home</a></li>
				  <li><a href="advancedResearch.php">Advanced research</a></li>
				  <li><a href="about.php">About</a></li>
				  <?php
				  	session_start(['cookie_lifetime' => 1800]);
				  	if(!empty($_SESSION['started']))
				  		echo '<li><a href="monCompte.php">My account</a></li>';
				  	else 
				  		echo '<li><a href="connexion.php">Connexion</a></li>';
				  ?>
				</ul>
				<form class="barMenu" method="post" action="resultatsDeRecherche.php">
				   <input type="text" name="recherche" placeholder="Search.."pattern="[A-Za-z0-9\s/]{1,20}" 
			         title =" keyword must contain only letters and numbers ! no more than 20 characters"> </input>
				  <button class="boutonBarre"><i class="fas fa-search"></i></button>
				</form>
		<! -- Fin Barre principale -->
	<header>
	<h1> Some informations </h1>
	</header>

	<div style="margin-left:20px; font-size:20px">
		<img src="images/imgAbout/montre.jpg" width="140" height="140" ><br><br>
		The multimedia library is open from 9am to 5pm.<br><br>

		<img src="images/imgAbout/maison.png" width="29" height="29">
		Address: 3 chemin des fleurs, 91300 Massy <br>
		<img src="images/imgAbout/handicap.jpg" width="30" height="30">
		All buildings are accessible to people with reduced mobility. <br>
		<img src="images/imgAbout/cadenas.png" width="25" height="25">
		The library is open to everyone. <br>
		<img src="images/imgAbout/livre.jpg" width="25" height="25">
		To be able to borrow a product, you have to create an account.<br>
		<img src="images/imgAbout/info.png" width="25" height="25">
		To learn more about additional information :
		<a class ='lien' style="margin-right: 60px" href="modalites.php">Click right here !</a>
	</div>


</body>
</html>
