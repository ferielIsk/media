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
 			     </input>
				  <button class="boutonBarre"><i class="fas fa-search"></i></button>
				</form>
		<! -- Fin Barre principale -->
		<?php
			//Si deja connectée redirige vers le compte
			if (!empty($_SESSION['started']) and $_SESSION['started'] == true)
				header("Location: monCompte.php");
			if (!empty($_REQUEST['inscr'])){
				if ($_REQUEST['inscr']=="reussie")
					echo '<div class="request" style="position:absolute; top:24vh; margin-left:36%;  font-size:32px; color: steelblue;"> Your request will be processed !  </div>';
				else
					echo '<div class="request" style="position:absolute; top:24vh; margin-left:40%; font-size:32px; color: crimson;"> User exists already !  </div>';
			}
		?>
		<div class="welcome"> Welcome to Mediatech</div>
		<div class="request" style="margin-top:10vh; margin-left:25%"> Please enter your information</div>
        <div  class="formulaireCentre">

            <form method="post" action="inscription.php" enctype="multipart/form-data">


                <label style="position:absolute; left:10%">Last name</label>
                <input type="text" name="nom" pattern="[A-Za-z _]{1,20}" title =" Last name must contain only letters and the underscore and no more than 20 characters" required="" />
				 </br></br>

                <label style="position:absolute; left:10%">First name</label>
                <input type="text" name="prenom" pattern="[A-Za-z _]{1,20}" title ="First name must contain only letters and the underscore; no more than 20 characters" required /> </br></br>

                <label style="position:absolute; left:10%">Username</label>
                <input type="text" name="pseudo" pattern="[A-Za-z0-9_]{1,20}" title ="Username must contain only letters,numbers and the underscore; no more than 10 characters" required /> </br></br>

                <label style="position:absolute; left:10%">Adresse</label>
                <input type="text" name="adresse" pattern="[A-Za-z-0-9_,/\s/]{1,80}" title ="adress must contain only letters,numbers,commas and the underscore; no more than 60 characters" required/> </br></br>

                <label style="position:absolute; left:10%">Date of birth</label>
                <input type="date" name="dateDeNaissance" required /></br></br>

                <label style="position:absolute; left:10%">Phone number</label>
                <input type="text" name="numero"pattern="[0-9]{10}" title="Phone number must stard with '0' and contain 10 numbers" required/> 
                  </br></br>

                <label style="position:absolute; left:10%">Email</label>
                <input type="email" name="mail" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="Please, enter a valid email address" required /></br></br>

                <label style="position:absolute; left:10%">Password</label>
                <input type="password" name="motDepasse" pattern=".{8,}" title=" Password must contain at least eight or more characters" required /></br></br>

                <label style="position:absolute; left:10%">Add a document (Proof of ID)</label>
                <input type="file" name="document" required /></br></br>


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

			echo '<div class="request" style="position:absolute; top:24vh; margin-left:40%; font-size:32px; color: crimson;">All fields must be filled! </div>';

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

			$txt = "select * from compte, client where compte.adresseMail='".$mail."' or pseudo='".$pseudo."'";
			$ordreC = oci_parse($connexion, $txt);
			oci_execute($ordreC);
			
			if (($row = oci_fetch_array($ordreC, OCI_BOTH)) !=false){
				header("Location: inscription.php?inscr=echec");
			}else{
			
				if (!empty($_FILES['document']['name'])){
						//Enregistre le fichier 
						
						$uploaddir = 'files/';
						$filename = $_FILES['document']['name'];
						$ext = pathinfo($filename, PATHINFO_EXTENSION); 
						$uploadfile = $uploaddir .   $pseudo."." .$ext;
						
						
						
						move_uploaded_file($_FILES['document']['tmp_name'], $uploadfile);
				}
				//Appel à la procédure ajouteClient

				$texte = "begin ajouteClient('".$mail."', '".$nom."', '"
						.$prenom."', '".$adresse."', '".$numero."', "
						."TO_DATE('".$dateDeNaissance."','yyyy-mm-dd'),'".$hashedMdP."','". $pseudo."') ; end;";


				$ordre = oci_parse($connexion, $texte);
				oci_execute($ordre);
				oci_free_statement($ordre);
				oci_close($connexion);
				
				

				header("Location: inscription.php?inscr=reussie");
			}

			
		}

	}

	?>
