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
    		
    		//Contient des fonctions generales
            include 'fonctions/fonctionsClient.php';
    		
            
            
    		//Si non connecté on redirige vers la page de connexion
        	if (empty($_SESSION['started']))
        		header("Location: connexion.php");
        		
        	//Si souhaite se deconnecter
        	if (!empty($_REQUEST['deconnexion'])){
        		session_unset();
        		session_destroy();
        		header("Location: connexion.php");
        	}
        
            echo '<div class="welcome" style="margin-top:40px;"> Welcome ' . ucfirst ($_SESSION['prenom']) .'   '. ucfirst ($_SESSION['nom']).'</div>';
            echo '<div id="boutonSeDeco">'
            	.' <a href="monCompte.php?deconnexion=true" > Sign out <i class="fas fa-sign-out-alt"></i></a>'
            	.' </div>';
            
            //On affiche ce qu'il faut 
            
            //Si c'est un client 
            if ($_SESSION['type']=='Client'){   
                echo '<button class="bouton1"  onclick=afficheInfoPersonnel()>Personal information</button>';
            	echo '<button class="bouton2"  onclick=emprunts()>My borrowings</button>';         	
            	echo '<button class="bouton3"  onclick=penalites()>My penalties</button>';  

						
				echo	"<div id='boutonPanier'> <a href='monCompte.php?inf=panier'> My cart <i class='fas fa-shopping-cart'></i> </a></div>";
				if (!empty($_REQUEST['inf'])){
					echo "<div class='inf'>";
					switch ($_REQUEST['inf']) {
						case 'info':
							if (isset($_REQUEST['modifier']))
								modifInformation();
							if (isset($_REQUEST['supprimer']))
								demandeDeSuppression();
							if (!empty($_REQUEST['demandeSuppression']))
								echo "	<div style='position:absolute; top: 50vh;left: 40%;text-align: center; font-size:32px; color: #588ebb; '> Your request well be processed ! </div>";
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
						case 'facture':
							facture ();
							break;
					}
					echo "</div>";
				}
            }else{
            	
            	echo '<button class="bouton1"  onclick=afficheInfoPersonnel()>Personal information</button>';
            	echo '<button class="bouton2" onclick=mesFonctions()>My functions </button>';
            	
            	echo "<div class='inf'>";
            	if (!empty($_REQUEST['inf'])){
					if ($_REQUEST['inf']=='info') {
						if (isset($_REQUEST['modifier']))
								modifInformation();
						displayInformations();
					}else{
						if($_REQUEST['inf']=='fonctions')
							header("Location: mesFonctions.php");
					}
				}
            	echo '</div>';
            }
        ?>
        <script> 
        	function afficheOeuvres(l_ide){
				window.location.href = "monCompte.php?inf=emprunt&ide="+l_ide;
			}
			function retireOeuvre(l_ide, l_ido){
				window.location.href = "monCompte.php?inf=panier&ide="+l_ide+"&ido="+l_ido;
			}
			function afficheInfoPersonnel(){
				window.location.href = "monCompte.php?inf=info";
			}
			function mesFonctions(){
				window.location.href = "monCompte.php?inf=fonctions";
			}
			function emprunts(){
				window.location.href = "monCompte.php?inf=emprunt";
			}
			function penalites(){
				window.location.href = "monCompte.php?inf=penalite";
			}
		</script>

    </body>
</html>
