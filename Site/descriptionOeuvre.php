<html>
	<head>
	  <meta charset="utf-8">
	  <link rel="stylesheet" href="styleGLA.css" />
      <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
      <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	  <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
	  <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
	  <title> Page description d'oeuvre </title>
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
        	//Connexion a la base de donnees
			$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');

        	//Si on ajoute l'oeuvre au panier
	        	if (!empty($_REQUEST['ido']) and !empty($_REQUEST['add']) and $_REQUEST['add']==true ){
							if (!empty($_SESSION['pseudo'])){	//Si connecté
								if ($_SESSION['etat'] == 'Activated') {
									$texte = "begin ajouteAPanierEmprunt(:pseudo, :ido, :msg) ; end;";
									$ordre = ociparse($connexion, $texte);
									oci_bind_by_name($ordre, ":pseudo", $_SESSION['pseudo']);
									oci_bind_by_name($ordre, ":ido", $_REQUEST['ido']);
									oci_bind_by_name($ordre, ":msg", $msg, 20);
									ociexecute($ordre);
									echo "<div style='text-align: center; font-size:24px; color: #588ebb; '>";
									if ($msg=='Ok')
										echo "This product was added to your cart successfully !</div>";
									else
										echo "Failure ! ".$msg."</div>";
								}else{
									echo "Failure : Your account is remaining in ".$_SESSION['etat'] ."</div>";
								}
							}else{
								header("Location: connexion.php");
							}
					}
					echo '<br><br><div class="col-xs-4 item-photo">
	                    <img style="max-width:100%;" src="https://ak1.ostkcdn.com/images/products/8818677/Samsung-Galaxy-S4-I337-16GB-AT-T-Unlocked-GSM-Android-Cell-Phone-85e3430e-6981-4252-a984-245862302c78_600.jpg" />
	                </div>';

          $ref = null;
          if (empty($_REQUEST['reference']))
          	header("Location: index.html");
          else
          	$ref = $_REQUEST['reference'];







//la disponibilite

          $texteDispo="begin :1 := fdisponibilite(".$ref.") ; end;";
		  
          $ordreDispo = oci_parse($connexion, $texteDispo);

          oci_bind_by_name($ordreDispo, ":1", $dispo,8);

          // Exécution de la requête
          oci_execute($ordreDispo);



          oci_free_statement($ordreDispo);

//l'oeuvre

          $texte = " select titre, description , type,dateParution , prixAchat , prixLocation, ido"
                    ." from oeuvre o"
                    ." where reference= :ref ";


          $ordre = oci_parse($connexion, $texte);

          oci_bind_by_name($ordre, ":ref", $ref);

          // Exécution de la requête
          oci_execute($ordre);




					$test = ($row = oci_fetch_array($ordre, OCI_BOTH));
					if ($test==false)
						echo "<div style='position:absolute; top: 50vh;left: 40%;text-align: center; font-size:32px; 	color: #588ebb;'> Cette oeuvre n'existe pas. </div>";
					else{
			            echo'<div class="col-xs-5" style="border:0px solid gray"> <h1>' . $row[0] . '</h1><br>';

			            echo'<b> Type: </b> ' . $row[2].'<br>';
			            echo'<b> Date de parution: </b> ' . $row[3].'<br>';
			            echo"<b> Prix d'achat : </b> ". $row[4].'€<br>';
			            echo'<b> Prix de location : </b> ' . $row[5].'€<br>';
			            echo "<b> L'oeuvre est :</b>  ".$dispo."<br>";
			            echo '<div class="section" style="padding-bottom:20px;">
			                    <button class="btnOeuvre"> <a href="descriptionOeuvre.php?reference='.$ref.'&add=true&ido='.$row[6].'"> Add to cart <i class="fas fa-shopping-cart"></i></a></button>
			                  </div></div>';

				    }
				  echo '<div class="col-xs-9">';
					echo '<div style="width:100%;border-top:1px solid silver"> <p style="padding:15px;"><b> Description: <br> </b>'.$row[1]->load().'<br>';
			    oci_free_statement($ordre);





			//le createur

          $texteCreateur = "select distinct nom, profession"
                    ." from oeuvre o , createurOeuvre co"
                    ." where o.reference= :ref and co.ido = o.ido ";


          $ordreCreateur = oci_parse($connexion, $texteCreateur);

          oci_bind_by_name($ordreCreateur, ":ref", $ref);

          // Exécution de la requête
          oci_execute($ordreCreateur);

					$test = ($row = oci_fetch_array($ordreCreateur, OCI_BOTH));
					if ($test!=false){
						echo'<b>'. $row[1].'(s)</b> : <br>';
				        while ($test!=false) {
				            echo $row[0].'. ';
				            $test = ($row = oci_fetch_array($ordreCreateur, OCI_BOTH));
				        }
				        echo '<br>';
				    }

			    oci_free_statement($ordreCreateur);


			//l edition

          $texteEdition= "select distinct e.nom, e.datededition"
                    ." from oeuvre o , editionOeuvre eo, edition e"
                    ." where o.reference= :ref "
                    ."and eo.ido = o.ido  and e.nom= eo.nom";



          $ordreEdition = oci_parse($connexion, $texteEdition);

          oci_bind_by_name($ordreEdition, ":ref", $ref);

          // Exécution de la requête
          oci_execute($ordreEdition);


				$test = ($row = oci_fetch_array($ordreEdition, OCI_BOTH));
				if ($test!=false){
					echo'<b> Edition(s) : </b> <br>';
			        while ($test!=false) {
			            echo $row[0]." (". $row[1]."). ";
			            $test = ($row = oci_fetch_array($ordreEdition, OCI_BOTH));
			        }
		          }
				echo '</div></div>';
		    oci_free_statement($ordreEdition);


          ?>
	</body>
</html>
