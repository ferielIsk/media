<html>
    <head>
        <link rel="stylesheet" href="styleGLA.css" type="text/css" />
   		<link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
        <meta charset="utf-8"/>
        <title> Compte </title>
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
				  ?>
				</ul>
				<form class="barMenu" method="post" action="resultatsDeRecherche.php">
				  <input type="text" name="recherche" placeholder="Search.." pattern="[A-Za-z0-9]{1,10}" 
 			             title =" keyword must contain only letters and numbers ! no more than 10 characters"> </input>
				  <button class="boutonBarre"><i class="fas fa-search"></i></button>
				</form>
			<! -- Fin Barre principale -->




    	<?php

			//Contient des fonctions generales
			include 'fonctions/fonctionsClient.php';
			include 'fonctions/fonctionsGestCompte.php';
			include 'fonctions/fonctionsGestOeuvre.php';
			include 'fonctions/fonctionsBibliothecaire.php';



			//Si non connecté on redirige vers la page de connexion
			if (empty($_SESSION['started']))
				header("Location: connexion.php");

			//Si souhaite se deconnecter
			if (!empty($_REQUEST['deconnexion'])){
				session_unset();
				session_destroy();
				header("Location: connexion.php");
			}

			echo '<div class="welcome" > Welcome ' . ucfirst ($_SESSION['prenom']) .'   '. ucfirst ($_SESSION['nom']).'</div>';
			echo '<div id="boutonSeDeco">'
			.' <a href="monCompte.php?deconnexion=true" > Sign out <i class="fas fa-sign-out-alt"></i></a>'
			.' </div>';



	          //On affiche ce qu'il faut

									/****************************************************************/
									/*						Client									*/
									/****************************************************************/
			if ($_SESSION['type']=='Client'){
				echo '<button class="bouton1"  onclick=rechargePage("info")>Personal information</button>';
				echo '<button class="bouton2"  onclick=rechargePage("emprunt")>My borrowings</button>';
				echo '<button class="bouton3"  onclick=rechargePage("penalite")>My penalties</button>';


				echo	"<div id='boutonPanier'> <a href='monCompte.php?inf=panier'> 
						My cart <i class='fas fa-shopping-cart'></i> </a></div>";
				if (!empty($_REQUEST['inf'])){
					echo "<div class='inf'>";
					switch ($_REQUEST['inf']) {
						case 'info':
							if (isset($_REQUEST['modifier']))
								modifInformation();
							if (isset($_REQUEST['supprimer']))
								demandeDeSuppression();
							if (!empty($_REQUEST['demandeSuppression']))
								echo "	<div style='position:absolute; top: 50vh;left: 40%;text-align: center; font-size:32px; color: #588ebb; '> Your request will be processed ! </div>";
							else
								displayInformations();

							break;
						case 'emprunt':
							afficheEmprunts(mesEmprunts());
							break;
						case 'penalite':
							affichePenalites();
							break;
						case 'panier':
							$indication = 0;
							if(!empty($_REQUEST['ide']) and !empty($_REQUEST['ido']))
								retireOeuvreDuPanier();
							if (isset ($_REQUEST['valider']))
								$indication = validerPanier();
							else
								if (isset ($_REQUEST['annuler']))
									 annulerPanier();
							if($indication!=-1)
								affichePanier();
							break;
					}
					echo "</div>";
				}
			}else{
							//Bouton commun informations perso
		      	echo '<button class="bouton1"  onclick=rechargePage("info")>Personal information</button>';

		      	echo "<div class='inf'>";
		      	if (!empty($_REQUEST['inf'])){
					if ($_REQUEST['inf']=='info') {
						if (isset($_REQUEST['modifier']))
							modifInformation();
						displayInformations();
					}
				}
		        echo '</div>';


							/****************************************************************/
							/*					Gest comptes								*/
							/****************************************************************/
				if ($_SESSION['type']=='Account_manager'){
					echo '<button class="bouton2"  onclick=rechargePage("creer")>Create account</button>';
					echo '<button class="bouton3"  onclick=rechargePage("modifier")>Update account</button>';
					echo '<button class="bouton4"  onclick=rechargePage("supprimer")>Delete account</button>';
					if (!empty($_REQUEST['inf'])){
						echo "<div class='inf'>";
						switch ($_REQUEST['inf']) {
							case 'modifier':
								modifier_compte();
								break;
							case 'creer':
								ajout_compte();
								break;
							case 'supprimer':
								supprimer_compte();
								break;
							case 'file':
								visuFile($_REQUEST['id']);
								break;
						}
						echo "</div>";
					}
				}else{
							/****************************************************************/
							/*					Gest oeuvres								*/
							/****************************************************************/
					if ($_SESSION['type']=='Multimedia_manager'){
						echo '<button class="bouton2"  onclick=rechargePage("ajouteOeuvre")>Add product </button>';
						echo '<button class="bouton3"  onclick=rechargePage("supprimerOeuvre")>Delete product</button>';
						echo '<button class="bouton4"  onclick=rechargePage("modifierOeuvre")>Update product</button>';
						if (!empty($_REQUEST['inf'])){
							echo "<div class='inf'>";
							switch ($_REQUEST['inf']) {
								case 'ajouteOeuvre':
									ajouter_oeuvre();
									break;
								case 'supprimerOeuvre':
									supprimer_oeuvre();
									break;
								case 'modifierOeuvre':
									if (isset($_REQUEST['modifierOe']))
										modifInformationOeuvre();
									modifier_oeuvre();
									break;
							}
							echo "</div>";
						}
					}else{
						if ($_SESSION['type']=='Librarian') {
							
							echo '<button class="bouton2"  onclick=rechargePage("enregistrerEmprunt")>Register borrowing </button>';
							echo '<button class="bouton3"  onclick=rechargePage("enregistrerRetour")>Register return product</button>';
							echo '<button class="bouton4"  onclick=rechargePage("annulerPenal")>Cancel penalty</button>';
							echo '<button class="bouton5"  onclick=rechargePage("remetEnRayon")>Return products</button>';
							echo '<button class="bouton6"  onclick=rechargePage("maj")>Update penalties</button>';
							if (!empty($_REQUEST['inf'])) {
								echo "<div class='inf'>";
								switch ($_REQUEST['inf']) {
									case 'enregistrerEmprunt':
										if (isset($_REQUEST['emprunt']))
											confirme_emprunt();
										enregistrer_emprunt();
										break;
									case 'enregistrerRetour':
										enregistrer_Retour();
										break;
									case 'annulerPenal':
										annuler_Penalite();
										break;
									case 'remetEnRayon':
										remet_enRayon();
										break;
									case 'maj':
										mAJPenalites();
										break;
								}
								echo "</div>";
							}
						}
					}
				}
			
		}
        ?>
        <script>
					/*Clients*/
        	function afficheOeuvres(l_ide){
				window.location.href = "monCompte.php?inf=emprunt&ide="+l_ide;
			}
			function retireOeuvre(l_ide, l_ido){
				window.location.href = "monCompte.php?inf=panier&ide="+l_ide+"&ido="+l_ido;
			}
					/*general*/
			function rechargePage(inf){
				window.location.href = "monCompte.php?inf="+inf;
			}

		</script>

    </body>
</html>
