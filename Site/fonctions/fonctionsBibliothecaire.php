<?php
	function enregistrer_emprunt() {
    	if( empty($_REQUEST['pseudo']) ) {
			echo '<div id="formCompte"">

        	Username of the person:
        	<form method="post" action="monCompte.php?inf=enregistrerEmprunt">
        	<input type="text" name ="pseudo" id="pseudo"> </input>
        	
        	<input  type="submit" name="affiche" value="Valider"/>
        	</form></div>';
		} else {
			$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');
			$texte = " select ide, dateReservation, dateDebutEmprunt "
    				. " from emprunt "
      				." where idcClient = :pseudo and valide=1";
      		$ordre = oci_parse($connexion, $texte); 
      		oci_bind_by_name($ordre, ":pseudo", $_REQUEST['pseudo']);
      		oci_execute($ordre);
      		$test = ($row = oci_fetch_array($ordre, OCI_BOTH));
        	if($test==false)
				echo "Client introuvable ou aucune réservation";
			else {
				echo '<table id="compteClient">';
				echo ' <tr> <th>Identifiant Emprunt</th> <th>dateReservation</th> <th>dateDebutEmprunt</th> </tr>';
				while ($test !=false) {
					echo '<tr> <td>'.$row[0].'</td> <td>'.$row[1].'</td>';
					if( $row[2] != NULL) {
						echo '<td>'.$row[2].'</td></tr>';
					} else {
						echo '<td> <form method="post" action="monCompte.php?inf=enregistrerEmprunt">
        					<input type="hidden" name ="ide" id="ide"> </input>
        					<input style="width:100%" type="submit" name="emprunt" value="Validate"/>
        					</form>
						</td></tr>';
					}
					$test = ($row = oci_fetch_array($ordre, OCI_BOTH));
				}
				echo '</table>';
			}
			oci_free_statement($ordre);
        	oci_close($connexion);
		}
	}

	function confirme_emprunt() {
    	$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');
		$txt = "begin confirmeEmprunt(:pseudo, :ide) ; end;";
		$ordre = oci_parse($connexion, $txt);  
		oci_bind_by_name($ordre, ":pseudo", $_REQUEST['pseudo']);
        oci_bind_by_name($ordre, ":ide", $_REQUEST['ide']);
        oci_execute($ordre);
    	oci_free_statement($ordre);
    	oci_close($connexion);
	}
	
	function enregistrer_Retour() {
		if( empty($_REQUEST['retour']) ) {
			echo '<div id="formCompte">
			<form method="post" action="monCompte.php?inf=enregistrerRetour">
							 ID of the product returned: <br> 
							<input type="number" name ="ido_retour" id="ido_retour" > </input><br><br>
        					
        					 ID of the borrowing: <br>
        					<input type="number" name ="ide_retour" id="ide_retour" > </input><br><br>

        					<input  type="submit" name="retour" value="Register return"/>
        					</form></div>';
        } else {
        	$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');
			$texte = "begin aRenduOeuvre(:ido, :ide) ; end;";
      		$ordre = oci_parse($connexion, $texte); 
      		oci_bind_by_name($ordre, ":ido", $_REQUEST['ido_retour']);
      		oci_bind_by_name($ordre, ":ide", $_REQUEST['ide_retour']);
      		oci_execute($ordre);
      		oci_free_statement($ordre);
    		oci_close($connexion);
        }
	}

	function annuler_Penalite() {
		if( empty($_REQUEST['annuler']) ) {
			echo '<div id="formCompte">
			<form method="post" action="monCompte.php?inf=annulerPenal">
							ID of penalty: 
							<input type="number" name ="idp" id="idp" > </input>
        					
        					<input type="submit" name="annuler" value="Cancel penalty"/>
        					</form></div>';
        } else {
        	$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');
			$texte = "begin supprimePenalite(:idp) ; end;";
      		$ordre = oci_parse($connexion, $texte); 
      		oci_bind_by_name($ordre, ":idp", $_REQUEST['idp']);
      		oci_execute($ordre);
      		oci_free_statement($ordre);
    		oci_close($connexion);
        }
	}

	function remet_enRayon() {
		$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');
		$texte = "begin remetEnRayon ; end;";
  		$ordre = oci_parse($connexion, $texte);
  		echo "<div style='position:absolute; top: 50vh;left: 35%;font-size:32px; color: #588ebb; '>"; 
  		if (oci_execute($ordre))
  			echo " Products concerned have been restored !  </div>";
  		else 
  			echo " Error occured ! </div>";
  		
  		oci_free_statement($ordre);
		oci_close($connexion);
	}

	//creationEtMAJPenalites
	function mAJPenalites() {
		$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');
		$texte = "begin creationEtMAJPenalites ; end;";
  		$ordre = oci_parse($connexion, $texte); 
  		echo "<div style='position:absolute; top: 50vh;left: 40%;font-size:32px; color: #588ebb; '>";
  		if (oci_execute($ordre))
  			echo " Penalties updated successfully ! </div>";
  		else 
  			echo " Error occured ! </div>";
  		oci_free_statement($ordre);
		oci_close($connexion);
		
	}

?>
