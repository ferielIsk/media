<html>
    <head>
        <link rel="stylesheet" href="styleGLA.css" />
        <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
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
			//Si deja connecté redirige vers le compte
			if (!empty($_SESSION['started']) and $_SESSION['started'] == true)
				header("Location: monCompte.php");
		?>



		<div class="welcome"> Welcome to Mediatech</div><br><br>

        <div class="formulaireCentre">
        	<?php
        		//Si y'a eu une erreur recharger la page avec message
				if (!empty ($_REQUEST['error']))
					if ($_REQUEST['error']==1)
						echo "Veuillez saisir vos informations ! ";
					else
						if($_REQUEST['error']==2)
							echo "Utilisateur introuvable ! ";
						else
							if($_REQUEST['error']==3)
								echo "Mot de passe incorrect ! ";
			?>




        	<form method="post" action="connexion.php">

        		<div style="position:absolute; left:26%">Login<br></div>
            	<br><br><input type="text" name="login" placeholder="xxxxxxxx@xxx.xxx">  </input><br><br>
            	<div style="position:absolute; left:26%">Password<br></div>
            	<br><br><input type="password" name="mdp" placeholder="**********">  </input><br><br>

            	<?php
            		//Si page de connexion simple on affiche un bouton pour pouvoir acceder à la version du personnel
            		if (empty ($_REQUEST['personnel']))
            			echo "<input type='submit' name='particulier' value='Particular'> </input>";
            		else
            			if ($_REQUEST['personnel']==true)
							echo "<label style='position:absolute; left:26%' for='fonction'> Function :</label>"
								."<select id='fonction' name='fonction'>"
									."<option value='Account_manager'>Account manager</option>"
									."<option value='Multimedia_manager'>Multimedia manager</option>"
									."<option value='Librarian'>Librarian</option>"
								."</select></br></br></br>";
            	?>


            	<input type="submit" name="submit" value="Sign in"> </input>
        	</form>
        	<?php
        		if (empty ($_REQUEST['personnel']))
        			echo "<a class='linkR' href='inscription.php'>Don't have an account yet? Sign Up</a><br>";
        	?>
        </div>
    </body>
</html>
<?php

	//Si le formulaire client n'a pas été validé
	if(!isset($_REQUEST['submit']) ){
		if(isset($_REQUEST['particulier']) ){
			header("Location: connexion.php?personnel=true");
		}
	}else{
		$login = $_REQUEST['login'];
        $mdp = $_REQUEST['mdp'];
        $type = $_REQUEST['fonction'];

        //Si l'un des champs est vide
        if (empty($login) or empty($mdp)) {
        	header("Location: connexion.php?error=1");
        }else{
        	//Requete BD
        	$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');

            $txt="";
            if (empty($type)){
            	$type = 'Client';
            	$txt =
				    " select co.mdp, co.adresseMail,  co.nom, co.prenom, co.tel, co.adresse, co.dateDeNaissance, cl.pseudo, cl.etat "
				    . "from client cl, compte co "
				    . "where co.type = :type and cl.etat != 'In_validation_process' and  (co.adresseMail = :login "
				    . "   or (cl.pseudo = :login and cl.adresseMail = co.adresseMail)) ";
            }else{
                $txt =
				    " select co.mdp, co.adresseMail, co.nom, co.prenom, co.tel, co.adresse, co.dateDeNaissance "
				    . "from compte co "
				    . "where co.type = :type and co.adresseMail = :login ";
            }


            echo $type;


			$ordre = oci_parse($connexion, $txt);
           	oci_bind_by_name($ordre, ":type", $type);
            oci_bind_by_name($ordre, ":login", $login);
            oci_execute($ordre);

            //Teste si l'utilisateur est trouvable et c'est le bon mot de passe
            if (($row = oci_fetch_array($ordre, OCI_BOTH)) ==false){
				header("Location: connexion.php?error=2");
            }else{
            	if (!password_verify($mdp,$row[0])){
					header("Location: connexion.php?error=3");
				}else{
					//initialise session et redirige vers compte
					$_SESSION['started'] = true;
					$_SESSION['adresseMail'] = $row[1];
					$_SESSION['nom'] = $row[2];
					$_SESSION['prenom'] = $row[3];
					$_SESSION['tel'] = $row[4];
					$_SESSION['adresse'] = $row[5];
					$_SESSION['dateDeNaissance'] = $row[6];
					if ($type == 'Client') {
						$_SESSION['pseudo'] = $row[7];
						$_SESSION['etat'] = $row[8];
					}
					$_SESSION['time']  = time();
					$_SESSION['type'] = $type;
					header("Location: monCompte.php");
				}
            }
            oci_free_statement($ordre);
            oci_close($connexion);
        }
	}
?>
