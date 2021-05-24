<html>
    <head>
        <link rel="stylesheet" href="styleGLA.css" />
        <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
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
		<! -- Fin Barre principale -->
		<div class="welcome"> Welcome to Mediatech</div><br><br>
		<div class="request" style="margin-top:5vh; margin-left:23%"> Please enter your a keyword </div>
		<div  style="	text-align:center;	margin-top: 0vh;    " >

			<form  method="post" action="resultatsDeRecherche.php"><br><br>
			   <input type="text" name="recherche" placeholder="Search.."pattern="[A-Za-z0-9\s/]{1,20}" 
			    title =" keyword must contain only letters and numbers ! no more than 10 characters"> </input>
			  <button  style="	background-color: #f2f2f2; border: none; color: darkgrey;padding: 12px 30px; 
			  					font-size: 16px; cursor: pointer; "><i class="fas fa-search"></i></button><br><br>
			  <a class='linkR' href='inscription.php'>Don't have an account yet? Sign Up</a><br>
			</form>
		</div>
		
    </body>
</html>
