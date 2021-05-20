<html>
	<head>
		<link rel="stylesheet" href="styleGLA.css" type="text/css" />
   		<link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
		<meta charset="utf-8">
		<title> Page d’inscription</title>
	</head>
	<body>
    	    <! -- Début Barre principale -->

				<ul class="barMenu">
				  <li><a href="index.html">Home</a></li>
				  <li><a href="advancedResearch.html">Advanced research</a></li>
				  <li><a href="about.html">About</a></li>
				  <?php
				  	session_start(['cookie_lifetime' => 600]);
				  	if(!empty($_SESSION['started']))
				  		echo '<li><a href="monCompte.php">My account</a></li>';
				  ?>
				</ul>
				<form class="barMenu" method="post" action="resultatsDeRecherche.php">
				  <input type="text" name="recherche" placeholder="Search.."> </input>
				  <button class="boutonBarre"><i class="fas fa-search"></i></button>
				</form>
		<! -- Fin Barre principale -->
		<?php
			//Si deja connectée redirige vers le compte 
			if (!empty($_SESSION['started']) and $_SESSION['started'] == true)
				header("Location: monCompte.php");
		?>
		<div class="welcome"> Welcome to Mediatech</div>
		<div class="request" style="margin-top:10vh; margin-left:25%"> Please enter your information</div>
        <div  class="formulaireCentre">

            <form method="post" action="inscription.php">


                <label style="position:absolute; left:10%">Last name</label>
                <input type="text" name="nom"/> </br></br>

                <label style="position:absolute; left:10%">First name</label>
                <input type="text" name="prenom"/> </br></br>

                <label style="position:absolute; left:10%">Username</label>
                <input type="text" name="pseudo"/> </br></br>

                <label style="position:absolute; left:10%">Adresse</label>
                <input type="text" name="adresse"/> </br></br>

                <label style="position:absolute; left:10%">Date of birth</label>
                <input type="date" name="dateDeNaissance"/></br></br>

                <label style="position:absolute; left:10%">Phone number</label>
                <input type="text" name="numero"/> </br></br>

                <label style="position:absolute; left:10%">Email</label>
                <input type="email" name="mail"/></br></br>

                <label style="position:absolute; left:10%">Password</label>
                <input type="password" name="motDepasse"/></br></br>

                <label style="position:absolute; left:10%">Add a document (Proof of ID)</label>
                <input type="file" name="document"/></br></br>


                <input type="submit" name="inscriptionForm" value="Sign up"/></br></br>

            </form>
        </div>

    </body>
</html>

<?php

	//Si le formulaire a été validé
	if(isset($_POST['inscriptionForm']) ){

		//On vérifie que tous les champs ont bien été remplis
		if (empty($_POST['nom']) or empty($_POST['prenom'])
		or empty($_POST['pseudo']) or empty($_POST['adresse'])
		or empty($_POST['dateDeNaissance']) or empty($_POST['mail'])
		or empty($_POST['numero']) or empty($_POST['motDepasse']) ){

			echo "tous les champs doivent etre remplis!";

		}else{

			$nom = $_POST['nom'];
			$prenom = $_POST['prenom'];
			$pseudo = $_POST['pseudo'];
			$adresse = $_POST['adresse'];
			$dateDeNaissance = $_POST['dateDeNaissance'];
			$mail = $_POST['mail'];
			$numero = $_POST['numero'];
			$motDepasse = $_POST['motDepasse'];

			//on hache le mot de passe
			$hashedMdP=password_hash($motDepasse, PASSWORD_DEFAULT);

			//connexion à la BD
			$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');


			//Appel à la procédure ajouteClient

			$texte = "begin ajouteClient('".$mail."', '".$nom."', '"
					.$prenom."', '".$adresse."', '".$numero."', "
					."TO_DATE('".$dateDeNaissance."','yyyy-mm-dd'),'".$hashedMdP."','". $pseudo."') ; end;";


			$ordre = ociparse($connexion, $texte);
			ociexecute($ordre);
			ocilogoff($connexion);
			header("Location: monCompte.php");
		}

	}

	?>

