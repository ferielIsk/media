<?php
	function ajout_compte(){
		echo '<div id="formCompte" >

            <form method="post" action="monCompte.php?inf=creer">


                <label>Last name</label>
                <input type="text" name="nom"/> </br></br>

                <label>First name</label>
                <input type="text" name="prenom"/> </br></br>

                <label for="type">Type</label>
                <select id="type" name="type">
                <option value="Client">Client</option>
                <option value="Account_manager">Account_manager</option>
                <option value="Multimedia_manager">Multimedia_manager</option>
                <option value="Librarian">Librarian</option>
                </select></br></br>

                <label>Username</label>
                <input type="text" name="pseudo"/> </br></br>

                <label>Adresse</label>
                <input type="text" name="adresse"/> </br></br>

                <label>Date of birth</label>
                <input type="date" name="dateDeNaissance"/></br></br>

                <label>Phone number</label>
                <input type="text" name="numero"/> </br></br>

                <label>Email</label>
                <input type="email" name="mail"/></br></br>

                <label>Password</label>
                <input type="password" name="motDepasse"/></br></br>

                </br></br>


                <input type="submit" name="inscriptionForm" value="Validate"/></br></br>
            </form>
        </div>';

        //Si le formulaire a été validé
		if(isset($_POST['inscriptionForm']) ){

			//On vérifie que tous les champs ont bien été remplis
			if (empty($_POST['nom']) or empty($_POST['prenom'])
			or empty($_POST['pseudo']) or empty($_POST['adresse'])
			or empty($_POST['dateDeNaissance']) or empty($_POST['mail'])
			or empty($_POST['numero']) or empty($_POST['motDepasse']) 
			or empty($_POST['document']) or empty($_POST['type']) ){

				echo "<div style='position:absolute; top: 25vh; color:crimson;left: 40%;font-size:32px;  '>All fields must be filled!</div>";

			}else{

				$nom = $_POST['nom'];
				$prenom = $_POST['prenom'];
				$pseudo = $_POST['pseudo'];
				$adresse = $_POST['adresse'];
				$dateDeNaissance = $_POST['dateDeNaissance'];
				$mail = $_POST['mail'];
				$numero = $_POST['numero'];
				$motDepasse = $_POST['motDepasse'];
				$type = $_POST['type'];

				//on hache le mot de passe
				$hashedMdP=password_hash($motDepasse, PASSWORD_DEFAULT);

				//connexion à la BD
				$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');


				//Appel à la procédure ajouteClient

				$texte = "begin ajouteCompte('".$mail."', '".$nom."', '"
																			 .$prenom."', '".$adresse."', '".$numero."', "
																			 ."TO_DATE('".$dateDeNaissance."','yyyy-mm-dd'),'".$hashedMdP."','". $type."') ; end;";

				$ordre = ociparse($connexion, $texte);
				ociexecute($ordre);
				ocilogoff($connexion);
			}

		}
	}

	function supprimer_compte() {
		
    	if(isset($_POST['supprimeCompte']) ){
    		$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');
    		$texte = " delete client "
      				." where adresseMail = :mail ";
      		$ordre2 = oci_parse($connexion, $texte); 
      		oci_bind_by_name($ordre2, ":mail", $_REQUEST['adresse_supp']);
      		oci_execute($ordre2);
	    	oci_free_statement($ordre2);
	    	oci_close($connexion);
    	}
		$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');
		$txt = "select client.adresseMail, pseudo, nom, prenom "
			." from client, compte "
			." where etat = 'Asked_tobe_deleted'
				and client.adresseMail = compte.adresseMail";
		$ordre = oci_parse($connexion, $txt);  
        oci_execute($ordre);
        $test = ($row = oci_fetch_array($ordre, OCI_BOTH));
        if($test==false)
			echo "<div style='position:absolute; top: 50vh;left: 40%;font-size:32px; color: #588ebb; '>There's no client request.</div>";
		else {
			echo '<table id="compteClient"> <tr> <th>Email</th> <th>Username</th> <th>Last name</th><th>First name</th></tr>';
			while ($test !=false){
				echo '<tr> <td>'. $row[0] .'</td>'.'<td>'. $row[1] .'</td>'.'<td>'. $row[2] .'</td><td>'. $row[3] .'</td>'
					. '<td>'. '<form method="post" action="monCompte.php?inf=supprimer">'
					. '<input type="hidden" name="adresse_supp" id="adresse_supp" value="'.$row[0].'">'
					. '<input type="submit" name="supprimeCompte" style="width:100%; height:50px;" value="Delete"/>'
					. '</form>'.'</td></tr>';
				$test = ($row = oci_fetch_array($ordre, OCI_BOTH));
			}
			echo '</table>';
		}
    	oci_free_statement($ordre);
    	oci_close($connexion);

	}


	function modifier_compte() {

    	if(isset($_POST['modifieEtatCompte']) ){
    		if (!empty($_POST['pseudo']) and !empty($_POST['nouvelEtat'])) {
    			
    			$connexion2 = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');
    			$texte = "begin modifieEtatCompteClient(:pseudo, :nouvelEtat, :msg) ; end;";
    			$ordre2 = oci_parse($connexion2, $texte); 
    			oci_bind_by_name($ordre2, ":pseudo", $_POST['pseudo']);
            	oci_bind_by_name($ordre2, ":nouvelEtat", $_POST['nouvelEtat']); 
            	oci_bind_by_name($ordre2, ":msg", $msg, 40);
                oci_execute($ordre2);
				oci_free_statement($ordre2);
    	        oci_close($connexion2);
    		}
    	}
    	
    	$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');
		$txt = "select pseudo, etat, client.adresseMail, prenom, nom "
			." from client, compte "
			." where etat != 'Actif'  and client.adresseMail = compte.adresseMail";
		$ordre = oci_parse($connexion, $txt);  
        oci_execute($ordre);
        $test = ($row = oci_fetch_array($ordre, OCI_BOTH));
        
        if($test==false)
			echo "<div style='position:absolute; top: 50vh;left: 40%;font-size:32px; color: #588ebb; '>There is no account to edit.</div>";
		else {
			echo '<table id="compteClient"> <tr> <th>Email</th> <th>Last name</th><th>First name</th><th>Actual state</th> <th>New state</th> <th>Action</th><th>File ID</th></tr>';
			while ($test !=false){
				echo '<tr> <td>'. $row[2] .'</td>'.'<td>'. $row[4] .'</td><td>'. $row[3] .'</td>'
					. '<td>'. $row[1] .'</td>'
					. '<td>'. '<form method="post" action="monCompte.php?inf=modifier">'
					. '<input type="hidden" name="pseudo" id="pseudo" value="'.$row[0].'">'

					. '<select id="nouvelEtat" name="nouvelEtat" style="width:100%; height:50px">' 
					. '<option value="Activated">Activated</option>'
					. '<option value="Suspended">Suspended</option>'
					. '</select></br></br>'

					. '</td><td><input type="submit" name="modifieEtatCompte" style="width:100%; height:50px" value="Edit"/>'
					. '</form>'.'</td><td>'
					.'<a href="monCompte.php?inf=file&id='.$row[0].'"  > <i class="fas fa-file"></i></a></td></tr>';
				$test = ($row = oci_fetch_array($ordre, OCI_BOTH)); 
			}
			echo '</table>';
		}
		
    	oci_free_statement($ordre);
    	oci_close($connexion);

	}
	
	
	function visuFile($id){
		$file = "files/".$id.".pdf";
		echo "<iframe src=\"".$file."\" width=\"80%\" style=\"height:100%;margin-top:100px\"></iframe>";
	}
?>
