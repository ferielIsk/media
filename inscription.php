
	<html>
    <head>
        <meta charset="utf-8">
        <title> Page d’inscription</title>
    </head>
    <body>
        <div class="ligne"style = "display: flex;margin: 10px;">

            <form method="post" action="inscription.php">


                <label>Nom</label>
                <input type="text" name="nom"/> </br></br>

                <label>Prénom</label>
                <input type="text" name="prenom"/> </br></br>

                <label>Nom d'utilisateur</label>
                <input type="text" name="pseudo"/> </br></br>

                <label>Adresse postale</label>
                <input type="text" name="adresse"/> </br></br>

                <label>Date de naissance</label>
                <input type="date" name="dateDeNaissance"/></br></br>

                <label>Numéro de téléphone</label>
                <input type="text" name="numero"/> </br></br>

                <label>Adresse mail</label>
                <input type="email" name="mail"/></br></br>

                <label>Mot de passe</label>
                <input type="password" name="motDepasse"/></br></br>

                <label>Ajouter un document </label>
                <input type="file" name="document"/></br></br>


                <input type="submit" name="inscriptionForm" value="valider"/></br></br>

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
		or empty($_POST['numero']) or empty($_POST['motDepasse']) or empty($_POST['document']) ){

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
		}

	}

	?>
