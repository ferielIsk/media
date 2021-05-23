<?php
	function ajouter_oeuvre() {
		echo '<div id="formCompte" >

            <form method="post" action="monCompte.php?inf=ajouteOeuvre">


                <label>Reference</label>
                <input type="number" name="reference"/> </br></br>

                <label>Title</label>
                <input type="text" name="titre"/> </br></br>

                <label>Description</label>
                <input type="text" name="description"/> </br></br>

                <label for="type">Type of work</label>
                <select id="type" name="type">
                <option value="Book">Book</option>
                <option value="CD">CD</option>
                <option value="DVD">DVD</option>
                </select></br></br>

                <label>Number of items</label>
                <input type="number" name="nbExemplaires"/> </br></br>

                <label>Purchase price</label>
                <input type="number" name="prixAchat"/></br></br>

                <label>Borrowing price</label>
                <input type="number" name="prixLocation"/> </br></br>

                <label>Date of publication</label>
                <input type="date" name="dateParution"/></br></br>

                <label>Edition name</label>
                <input type="text" name="nomEdition"/></br></br>

                <label>Creator name </label>
                <input type="text" name="nomCreateur"/></br></br>

                <label>Edition date</label>
                <input type="date" name="dateEdition"/></br></br>

                <label for="profession">Profession of creator</label>
                <select id="profession" name="profession">
                <option value="Composer">Composer</option>
                <option value="Author">Author</option>
                <option value="Producer">Producer</option>
                </select>
                </br></br>

                <input type="submit" name="oeuvreForm" value="Validate"/></br></br>
            </form>
        </div>';


        //Si le formulaire a été validé
		if(isset($_POST['oeuvreForm']) ){

			//On vérifie que tous les champs ont bien été remplis
			if (empty($_POST['reference']) or empty($_POST['titre'])
			or empty($_POST['description']) or empty($_POST['type'])
			or empty($_POST['nbExemplaires']) or empty($_POST['prixAchat'])
			or empty($_POST['prixLocation']) or empty($_POST['dateParution'])
			or empty($_POST['nomEdition']) or empty($_POST['nomCreateur'])
			or empty($_POST['dateEdition']) or empty($_POST['profession']) ){

				echo "<div style='position:absolute; top: 25vh; color:crimson;left: 40%;font-size:32px;  '>All fields must be filled!</div>";
			}else{

				$reference = intval($_POST['reference']);
				$titre = $_POST['titre'];
				$description = $_POST['description'];
				$type = $_POST['type'];
				$nbExemplaires = intval($_POST['nbExemplaires']);
				$prixAchat = intval($_POST['prixAchat']);
				$prixLocation = intval($_POST['prixLocation']);
				$dateParution = $_POST['dateParution'];
				$nomEdition = $_POST['nomEdition'];
				$nomCreateur = $_POST['nomCreateur'];
				$dateEdition = $_POST['dateEdition'];
				$profession = $_POST['profession'];

				//connexion à la BD
				$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');


				//Appel à la procédure ajouteOeuvre
				$texte = "begin ajouteOeuvre(".$reference.", '".$titre."', '".$description."', '".$type."', ".$nbExemplaires.", ".$prixAchat.", ".$prixLocation.", "."TO_DATE('".$dateParution."','yyyy-mm-dd'),'".$nomEdition."', '".$nomCreateur."', "."TO_DATE('".$dateEdition."','yyyy-mm-dd'),'". $profession."') ; end;";
				$ordre = oci_parse($connexion, $texte);
				echo "<div style='position:absolute; top: 50vh;left: 35%;font-size:32px; color: #588ebb; '>";
		  		if (oci_execute($ordre))
		  			echo " Successful !  </div>";
				else 
					echo " Error occured ! </div>";
				oci_free_statement($ordre);
		    	oci_close($connexion);
			}

		}
	}

	function supprimer_oeuvre() {
		echo '<div id="formCompte">

            <form method="post" action="monCompte.php?inf=supprimerOeuvre">
            	Reference of product to delete: <br>
                <input type="number" name="reference"/>

                <input type="submit" name="oeuvreSuppForm" value="Validate"/></br></br>
            </form>
        </div>';

        //Si le formulaire a été validé
		if(isset($_POST['oeuvreSuppForm']) ){
			//On vérifie que tous les champs ont bien été remplis
			if ( empty($_REQUEST['reference']) ){
				echo "<div style='position:absolute; top: 50vh;left: 40%;font-size:32px; color: #588ebb; '> Enter an identifier! <div>";
			}else{
				$reference = $_REQUEST['reference'];
				//connexion à la BD
				$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');
				$texte = "update oeuvre "
					." set est_disponible = 0 "
					." where reference = :reference";
	      		$ordre2 = oci_parse($connexion, $texte);
	      		oci_bind_by_name($ordre2, ":reference", $_REQUEST['reference']);
	      		if(oci_execute($ordre2))
	      				echo "<div style='position:absolute; top: 50vh;left: 40%;font-size:32px; color: #588ebb; '> Product deleted! <div>";;
		    	oci_free_statement($ordre2);
		    	oci_close($connexion);

			}
		}
	}


	function modifier_oeuvre() {
		if( empty($_REQUEST['ref']) ) {
			echo '<div id="formCompte" >

        	ID of the product to edit: 
        	<form method="post" action="monCompte.php?inf=modifierOeuvre">
        	<input type="number" name ="ref" id="ref"> </input>

        	<input type="submit" name="affiche" value="Validate"/>
        	</form></div>';
		} else {
			$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');

    		$texte = " select ido, reference, titre, description, type, nbExemplaires, prixAchat,prixLocation, dateParution "
    				. " from oeuvre "
      				." where ref = :ref ";
      		$ordre = oci_parse($connexion, $texte);
      		oci_bind_by_name($ordre, ":ref", $_REQUEST['ref']);
      		oci_execute($ordre);
      		$test = ($row = oci_fetch_array($ordre, OCI_BOTH));
        	if($test==false)
				echo "Product not found.";
			else {
				while ($test !=false) {
					if (!empty($_REQUEST['modifiedOeuvre']) and $_REQUEST['modifiedOeuvre']==true)
						echo "	Modification done!";

					echo '<div id="formCompte" ><form method="post" action="monCompte.php?inf=modifierOeuvre">
						Identifier of product : '.$row[0].' <br><br>

		                <label>Reference</label>
		                <input type="hidden" name="referenceAncienne" value="'.$row[1].'"/>
		                <input type="number" name="reference" value="'.$row[1].'"/> </br></br>

		                <label>Title</label>
		                <input type="hidden" name="titreAncienne" value="'.$row[2].'"/>
		                <input type="text" name="titre" value="'.$row[2].'"/> </br></br>

		                <label>Description</label>
		                <input type="hidden" name="descriptionAncienne" value="'.$row[3]->load().'"/>
		                <input type="text" name="description" value="'.$row[3]->load().'"/> </br></br>

		                <label>Product type</label>
		                <input type="hidden" name="typeAncienne" value="'.$row[4].'"/>
		                <select id="type" name="type">
		                <option value="'.$row[4].'">'.$row[4].'</option>
                        <option value="Book">Book</option>
                        <option value="CD">CD</option>
                        <option value="DVD">DVD</option>
                        </select></br></br>

		                <label>Number of items</label>
		                <input type="hidden" name="nbExemplairesAncienne" value="'.$row[5].'"/>
		                <input type="number" name="nbExemplaires" value="'.$row[5].'"/> </br></br>

		                <label>Purchase cost</label>
		                <input type="hidden" name="prixAchatAncienne" value="'.$row[6].'"/>
		                <input type="number" name="prixAchat" value="'.$row[6].'"/></br></br>

		                <label>Borrowing cost</label>
		                <input type="hidden" name="prixLocationAncienne" value="'.$row[7].'"/>
		                <input type="number" name="prixLocation" value="'.$row[7].'"/> </br></br>

		                <label>Date of publication</label>
		                <input type="hidden" name="dateParutionAncienne" value="'.date('d-m-y',strtotime($row[8])).'"/>
		                
		                <input type="date" name="dateParution" value="'.date('Y-m-d',strtotime($row[8])).'"/></br></br>

		                <input type="submit" name="modifierOe" value="Edit"/></br></br>
		            </form></div>';
		            $test=false;

				}
			}
	    	oci_free_statement($ordre);
	    	oci_close($connexion);
		}
	}


	function modifInformationOeuvre() {
		$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');
		$reussi = false;
		//Si on a modifié le reference
		if ($_REQUEST['reference']!=$_REQUEST['referenceAncienne'] and !empty($_REQUEST['reference'])){
			$txt = "update oeuvre "
					." set reference = :reference "
					." where reference = :referenceAncienne";
			$ordre = oci_parse($connexion, $txt);
			oci_bind_by_name($ordre, ":reference", $_REQUEST['reference']);
            oci_bind_by_name($ordre, ":referenceAncienne", $_REQUEST['referenceAncienne']);
            oci_execute($ordre);
	    	oci_free_statement($ordre);
	    	$reussi = true;
		}
		//Si on a modifié le Titre
		if ($_REQUEST['titre']!=$_REQUEST['titreAncienne'] and !empty($_REQUEST['titre'])){
			$txt = "update oeuvre "
					." set titre = :titre "
					." where titre = :titreAncienne";
			$ordre = oci_parse($connexion, $txt);
			oci_bind_by_name($ordre, ":titre", $_REQUEST['titre']); //new
            oci_bind_by_name($ordre, ":titreAncienne", $_REQUEST['titreAncienne']); //ancienne
            oci_execute($ordre);
	    	oci_free_statement($ordre);
	    	$reussi = true;
		}

		//Si on a modifié le description
		if ($_REQUEST['description']!=$_REQUEST['descriptionAncienne'] and !empty($_REQUEST['description'])){
			$txt = "update oeuvre "
					." set description = :description "
					." where description = :descriptionAncienne";
			$ordre = oci_parse($connexion, $txt);
			oci_bind_by_name($ordre, ":description", $_REQUEST['description']);
            oci_bind_by_name($ordre, ":descriptionAncienne", $_REQUEST['descriptionAncienne']);
            oci_execute($ordre);
	    	oci_free_statement($ordre);
	    	$reussi = true;
		}

		//Si on a modifié le type
		if ($_REQUEST['type']!=$_REQUEST['typeAncienne'] and !empty($_REQUEST['type'])){
			$txt = "update oeuvre "
					." set type = :type "
					." where type = :typeAncienne";
			$ordre = oci_parse($connexion, $txt);
			oci_bind_by_name($ordre, ":type", $_REQUEST['type']);
            oci_bind_by_name($ordre, ":typeAncienne", $_REQUEST['typeAncienne']);
            oci_execute($ordre);
	    	oci_free_statement($ordre);
	    	$reussi = true;
		}

		//Si on a modifié le nbExemplaires
		if ($_REQUEST['nbExemplaires']!=$_REQUEST['nbExemplairesAncienne'] and !empty($_REQUEST['nbExemplaires'])){
			$txt = "update oeuvre "
					." set nbExemplaires = :nbExemplaires "
					." where nbExemplaires = :nbExemplairesAncienne";
			$ordre = oci_parse($connexion, $txt);
			echo
			oci_bind_by_name($ordre, ":nbExemplaires", $_REQUEST['nbExemplaires']);
            oci_bind_by_name($ordre, ":nbExemplairesAncienne", $_REQUEST['nbExemplairesAncienne']);
            oci_execute($ordre);
	    	oci_free_statement($ordre);
	    	$reussi = true;
		}

		//Si on a modifié le prixAchat
		if ($_REQUEST['prixAchat']!=$_REQUEST['prixAchatAncienne'] and !empty($_REQUEST['prixAchat'])){
			$txt = "update oeuvre "
					." set prixAchat = :prixAchat "
					." where prixAchat = :prixAchatAncienne";
			$ordre = oci_parse($connexion, $txt);
			oci_bind_by_name($ordre, ":prixAchat", $_REQUEST['prixAchat']);
            oci_bind_by_name($ordre, ":prixAchatAncienne", $_REQUEST['prixAchatAncienne']);
            oci_execute($ordre);
	    	oci_free_statement($ordre);
	    	$reussi = true;
		}

		//Si on a modifié le prixLocation
		if ($_REQUEST['prixLocation']!=$_REQUEST['prixLocationAncienne'] and !empty($_REQUEST['prixLocation'])){
			$txt = "update oeuvre "
					." set prixLocation = :prixLocation "
					." where prixLocation = :prixLocationAncienne";
			$ordre = oci_parse($connexion, $txt);
			oci_bind_by_name($ordre, ":prixLocation", $_REQUEST['prixLocation']);
            oci_bind_by_name($ordre, ":prixLocationAncienne", $_REQUEST['prixLocationAncienne']);
            oci_execute($ordre);
	    	oci_free_statement($ordre);
	    	$reussi = true;
		}

		//Si on a modifié la dateParution
		if ($_REQUEST['dateParution']!=$_REQUEST['dateParutionAncienne'] and !empty($_REQUEST['dateParution'])){
			$txt = "update oeuvre "
					." set dateParution = :dateParution "
					." where dateParution = :dateParutionAncienne";
			$ordre = oci_parse($connexion, $txt);
			oci_bind_by_name($ordre, ":dateParution", $_REQUEST['dateParution']);
            oci_bind_by_name($ordre, ":dateParutionAncienne", $_REQUEST['dateParutionAncienne']);
            oci_execute($ordre);
	    	oci_free_statement($ordre);
	    	$reussi = true;
		}


		if ($reussi) {
			header("Location: monCompte.php?inf=modifierOeuvre&modifiedOeuvre=true");
		}
        oci_close($connexion);
	}


?>

