<?php
	/*
	Affiche sous forme de formulaire les informations du compte
	*/
	function displayInformations(){
		if (!empty($_REQUEST['modified']) and $_REQUEST['modified']==true)
			echo "	<div style='position:absolute; top: 50vh;left: 40%;text-align: center; font-size:32px; color: #588ebb; '>Modification réussie ! </div>";
		else{
			echo 	 "	<div id='formCompte'><form method='post' action='monCompte.php?inf=info'>"
					."  <label for='mail'> Adresse Mail :</label>"
					."  <input type='email' name='mail' value='".$_SESSION['adresseMail']."'disabled/><br><br>";
			if( $_SESSION['type']=='Client')
				echo	"	<label for='pseudo'> Pseudo :</label>"
						."  <input type='text' name='pseudo' value='". $_SESSION['pseudo']."'/><br><br>";
			echo	 "  <label for='prenom'> Prénom :</label>"
					."  <input type='text' name='prenom' value='".$_SESSION['prenom']."'disabled/><br><br>"
					."	<label for='nom'> Nom :</label>"
					."  <input type='text' name='nom' value='". $_SESSION['nom']."'disabled/><br><br>"
					
					."  <label for='dateDeNaissance'> Date de naissance :</label>"
					."  <input type='date' name='dateDeNaissance' value='".date('Y-m-d',strtotime($_SESSION['dateDeNaissance']))."'disabled/><br><br>"
					
					."  <label for='adresse'> Adresse :</label>"
					."  <input type='text' name='adresse' value='".$_SESSION['adresse']."'/><br><br>"
					."	<label for='tel'> Tél :</label>"
					."  <input type='text' name='tel' value='".$_SESSION['tel']."'/><br><br>"
					
					
					."	<label for='mdp'> Mot de passe :</label>"
					."  <input type='password' name='mdp' placeholder='**********'/><br><br>"				
					."  <input class='modifier' type='submit' name='modifier' value='Modifier'/> </form>"
					
					
					."  <form method='post' action='monCompte.php?inf=info&suppression=true'> "
					."  <input type='submit' name='supprimer' value='Supprimer mon compte' class='suppression' />"
					."	</form></div>";
				}
		
	}
	
	
	/*
	Fonction qui permet de modifier dans la BD les attributs modifiés par l'utilisateur dans le formulaire
	*/
	function modifInformation(){
		$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');
		$reussi = false;
		//Si on a modifié le pseudo
		if ($_REQUEST['pseudo']!=$_SESSION['pseudo'] and !empty($_REQUEST['pseudo'])){
			$_SESSION['pseudo'] = $_REQUEST['pseudo'];
			$txt = "update client "
					." set pseudo = :pseudo "
					." where adresseMail = :adresseMail";
			$ordre = oci_parse($connexion, $txt);  
			oci_bind_by_name($ordre, ":pseudo", $_REQUEST['pseudo']);
            oci_bind_by_name($ordre, ":adresseMail", $_SESSION['adresseMail']);
            oci_execute($ordre);
	    	oci_free_statement($ordre);
	    	$reussi = true;
		}
		//Si on a modifié l'adresse
		if ($_REQUEST['adresse']!=$_SESSION['adresse'] and !empty($_REQUEST['adresse'])){
			$_SESSION['adresse'] = $_REQUEST['adresse'];
			$txt = "update client "
					." set adresse = :adresse "
					." where adresseMail = :adresseMail";
			$ordre = oci_parse($connexion, $txt);  
			oci_bind_by_name($ordre, ":adresse", $_REQUEST['adresse']);
            oci_bind_by_name($ordre, ":adresseMail", $_SESSION['adresseMail']);
            oci_execute($ordre);
	    	oci_free_statement($ordre);
	    	$reussi = true;
		}
		
		//Si on a modifié le tel
		if ($_REQUEST['tel']!=$_SESSION['tel'] and !empty($_REQUEST['tel'])){
			$_SESSION['tel'] = $_REQUEST['tel'];
			$txt = "update client "
					." set tel = :tel "
					." where adresseMail = :adresseMail";
			$ordre = oci_parse($connexion, $txt);  
			oci_bind_by_name($ordre, ":tel", $_REQUEST['tel']);
            oci_bind_by_name($ordre, ":adresseMail", $_SESSION['adresseMail']);
            oci_execute($ordre);
	    	oci_free_statement($ordre);
	    	$reussi = true;
		}
		
		
		//Si on a modifié le mdp
		if (!empty($_REQUEST['mdp']) and !empty($_REQUEST['mdp'])){
			$txt = "update compte "
					." set mdp = :mdp "
					." where adresseMail = :adresseMail";
			$ordre = oci_parse($connexion, $txt);  
			$mdp = password_hash($_REQUEST['mdp'], PASSWORD_DEFAULT);
			oci_bind_by_name($ordre, ":mdp", $mdp);
            oci_bind_by_name($ordre, ":adresseMail", $_SESSION['adresseMail']);
            oci_execute($ordre);
	    	oci_free_statement($ordre);
	    	$reussi = true;
		}
		if ($reussi)
			header("Location: monCompte.php?inf=info&modified=true");
        oci_close($connexion);
	}
	
	function demandeDeSuppression(){
		$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');
		$reussi = false;

		$txt = "update client "
				." set etat = 'Demande_de_suppression' "
				." where adresseMail = :adresseMail";
		$ordre = oci_parse($connexion, $txt);  
        oci_bind_by_name($ordre, ":adresseMail", $_SESSION['adresseMail']);
        
	    
	    
	    if(oci_execute($ordre))
	    	$reussi = true;
		
		if ($reussi)
			header("Location: monCompte.php?inf=info&demandeSuppression=true");
		oci_free_statement($ordre);
        oci_close($connexion);
    }

	/*Fonction qui permet d'afficher les emprunts*/
	function mesEmprunts(){
		$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');
		$txt = "select emprunt.ide, titre, reglee, rendue, dateReservation, dateDebutEmprunt, montant, prixLocation "
				." from emprunt, panier, oeuvre "
				." where valide =  1"
				."  and  idcClient = :pseudo "
				." 	and  emprunt.ide = panier.ide "
				."  and  panier.ido = oeuvre.ido ";
		$ordre = oci_parse($connexion, $txt);  
        oci_bind_by_name($ordre, ":pseudo", $_SESSION['pseudo']);
        oci_execute($ordre);
    	$res = array();
    	while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false){
    		$res[$row[0]] = array();
    		if ( $row[2] == 1)
    			$res[$row[0]]['paye'] ='Payé';
    		else 
    			$res[$row[0]]['paye'] ='Non payé';
    		$res[$row[0]]['oeuvres'] = array();
    		$res[$row[0]]['oeuvres'][$row[1]] = $row[3];
    		$res[$row[0]]['prixOeuvres'] = array();
    		$res[$row[0]]['prixOeuvres'][$row[1]] = $row[7];
    		
    		//Si les dates sont nulles
    		if (empty($row[4]))
    			$res[$row[0]]['dateReservation'] ="-";
    		else
    			$res[$row[0]]['dateReservation'] = date('d-m-Y',strtotime($row[4]));
    			
    		if (empty($row[5]))
    			$res[$row[0]]['dateEmprunt'] ="-";
    		else
    			$res[$row[0]]['dateEmprunt'] = date('d-m-Y',strtotime($row[5]));
    		$res[$row[0]]['montant']=$row[6];
    	}
    	oci_free_statement($ordre);
        oci_close($connexion);
		return $res;
	}
	
	/*Fonction qui permet d'afficher les oeuvres d'un emprunt*/
	
	function afficheEmprunts($arr){
		//Entete
		if (empty($arr))
			echo "<div style='position:absolute; top: 50vh;left: 40%;text-align: center; font-size:32px; 	color: #588ebb;'> Vous n'avez aucun emprunt pour le moment. </div>";
		else{
			echo '<table id="compteClient"> <tr> <th>Identifiant</th> <th>Date reservation</th> <th>Date début emprunt</th> <th>Payé</th> <th colspan=2>Facture</th> </tr>';
			foreach ($arr as $key => $value){
				//ligne d'un emprunt
				echo	 '<tr class="emp"> <td> Emprunt#'.$key.'</td>'
						.	'<td>'. $arr[$key]['dateReservation'] .'</td> '
						.	'<td>'. $arr[$key]['dateEmprunt'] .'</td> '
						.	'<td>'. $arr[$key]['paye'] .'</td> '
						.	'<td style="padding:0"><form method="post" action="maFacture.php">'
							//Informations à transmettre pour la facture
							."  <input type='text' name='prenom' value='".$_SESSION['prenom']."'hidden/>"
							."  <input type='text' name='nom' value='". $_SESSION['nom']."'hidden/>"
							."  <input type='text' name='montant' value='".$arr[$key]['montant']."'hidden/>"
							."  <input type='text' name='ide' value='". $key."'hidden/>"
							."  <input type='text' name='dateG' value='". $arr[$key]['dateReservation']."'hidden/>"
							//Bouton pour la facture
						.	' <input id="BoutonFacture" type="submit" name="facture" value="Ma facture" class="facture"style="width:100%; height:100%;" /></td>';	
				
				foreach ($arr[$key]['oeuvres'] as $key2 => $value2){
					//Prix et titre oeuvre pour facture
					echo "  <input type='text' name='oeuvre' value=' - ". $arr[$key]['prixOeuvres'][$key2]." euros.  ". $key2."'hidden/></form>";
					
					//Bouton plus				
					echo '	<td style="padding:0"> <button class="btn" style="width:100%; height:100%;" onclick="afficheOeuvres('.$key.')"><i class="fas fa-plus-circle"></i></button>'
						.	'</td> </tr>';
					//Affiche les oeuvres si on appuie sur le plus
					if(!empty($_REQUEST['ide']) and $key==$_REQUEST['ide']){
						$i =1;
						//ligne d'une oeuvre
						echo "<tr><td></td><td colspan=5> Oeuvre n°".$i." : " .$key2."</td></tr>";
						$i=$i+1;
					}
				}
			}		
			echo'</table><br>';	
		}
	}
	
	
	/*Affiche les pénalités du client */
	function affichePenalites(){
		$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');
		$txt = "select titre, type, dateDebutEmprunt, penalite.montant "
				." from emprunt, penalite, oeuvre "
				." where idcClient = :pseudo "
				." 	and  emprunt.ide = penalite.ide "
				."  and  penalite.ido = oeuvre.ido ";
		$ordre = oci_parse($connexion, $txt);  
        oci_bind_by_name($ordre, ":pseudo", $_SESSION['pseudo']);
        oci_execute($ordre);
        $test = ($row = oci_fetch_array($ordre, OCI_BOTH));
		if($test==false)
			echo "<div style='position:absolute; top: 50vh;left: 40%;text-align: center; font-size:32px; 	color: #588ebb; '> Vous n'avez pas de pénalités :) </div> ";
		else{
			echo '<table id="compteClient"> <tr> <th>Oeuvre</th> <th>Type</th>   <th>Date de rendu supposée</th> <th>Montant</th> </tr>';
			while ($test !=false){
				echo	 '<tr> <td>'. $row[0] .'</td>'
						.	  '<td>'. $row[1] .'</td> '
						.	  '<td>'.date_format( date_add(date_create_from_format('d-M-y',$row[2]), date_interval_create_from_date_string("15 days")),'Y-m-d').'</td>'
						.	  '<td style="color:red">'. $row[3].'</td> </tr>';
				
				$test = ($row = oci_fetch_array($ordre, OCI_BOTH));

			}
    	
			echo'</table>';	
		}
    	oci_free_statement($ordre);
        oci_close($connexion);	
	}
	
	/*Affiche le panier */
	function affichePanier(){
	
		$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');
		$txt = "select distinct titre, type, createurOeuvre.nom, editionOeuvre.nom, prixLocation, emprunt.ide,  oeuvre.ido "
				." from panier, oeuvre, createurOeuvre, editionOeuvre, emprunt  "
				." where valide = 0 "
				."	and  idcClient = :pseudo "
				." 	and  emprunt.ide = panier.ide "
				."  and  panier.ido = oeuvre.ido "
				."  and  oeuvre.ido = createurOeuvre.ido "
				."  and  oeuvre.ido = editionOeuvre.ido ";
		$ordre = oci_parse($connexion, $txt);  
        oci_bind_by_name($ordre, ":pseudo", $_SESSION['pseudo']);
        oci_execute($ordre);
        
        $montant = 0;
        
        $ide=null;
		//Affichage des oeuvres dans un tableaux 
		$test = ($row = oci_fetch_array($ordre, OCI_BOTH));
		if($test==false)
			echo "<div style='position:absolute; top: 50vh;left: 40%;font-size:32px; color: #588ebb; '>Votre panier est vide !</div> ";
		else{
			echo '<table id="compteClient"> <tr> <th>Oeuvre</th> <th>Type</th>   <th> De </th> <th>Edition</th> <th >Prix</th> </tr>';
			while ($test!=false){
				$ide=$row[5];
				echo	 '<tr> <td>'. $row[0] .'</td>'
						.	  '<td>'. $row[1] .'</td> '
						.	  '<td>'. $row[2] .'</td>'
						.	  '<td>'. $row[3] .'</td>'
						.	  '<td>'. $row[4] .'€</td>'
						.	  '<td style="padding:0"><button class="btnMinus" style="width:100%" onclick="retireOeuvre('.$row[5].','.$row[6].')"><i class="fas fa-minus-circle"></i></td> </tr>';
				$montant +=$row[4];
				$test = ($row = oci_fetch_array($ordre, OCI_BOTH));
			}
			
			echo'</table><br>';	
			
			echo '<div class="prix"> Prix total à payer : '.$montant.'€</div>';
			
			//Affichage des boutons 
			echo " <form class='boutonsPanier' method='post' action='monCompte.php?inf=panier&ide=".$ide."'> "
					." <input type='submit' name='valider' value='Valider le panier'/> <br>"
					." <input type='submit' name='annuler' value='Annuler le panier'/>	</form>";
		}
					
				
    	oci_free_statement($ordre);
        oci_close($connexion);	
	}
	
	
	
	
	/*Retire l'oeuvre passé en argument au lient du panier*/
	
	function retireOeuvreDuPanier(){
		$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');
		$txt = "begin supprimeOeuvreDePanier(:ide, :ido) ; end;";
		$ordre = oci_parse($connexion, $txt);  
        oci_bind_by_name($ordre, ":ide", $_REQUEST['ide']);
        oci_bind_by_name($ordre, ":ido", $_REQUEST['ido']);
        oci_execute($ordre);
	}
	
	function annulerPanier(){
		$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');
		$txt = "begin annulePanier(:ide) ; end;";
		$ordre = oci_parse($connexion, $txt);  
        oci_bind_by_name($ordre, ":ide", $_REQUEST['ide']);
        oci_execute($ordre);
	}
	function validerPanier(){
		if ($_SESSION['etat']=='Suspendu' or $_SESSION['etat']=='En_cours_de_validation'){
			echo "	<div style='position:absolute; top: 50vh;left: 40%;text-align: center; font-size:32px; color: #588ebb; '> Votre compte est toujours : <br> ". $_SESSION['etat'] ."</div>";
			return -1;
		}else{
			$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');
			$txt = "begin valideEmprunt(:pseudo, :ide, :msg) ; end;";
			$ordre = oci_parse($connexion, $txt);  
		    oci_bind_by_name($ordre, ":ide", $_REQUEST['ide']);
		    oci_bind_by_name($ordre, ":pseudo", $_SESSION['pseudo']);
		    oci_bind_by_name($ordre, ":msg", $_msg);
		    echo $_msg;
		    oci_execute($ordre);
		}
		return 1;
	}
	
	
	function facture (){

	}
	
	
	
	
	
?>
