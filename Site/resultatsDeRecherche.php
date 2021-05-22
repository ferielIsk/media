<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="styleGLA.css" />
        <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
		<title> Page résultats de recherche </title>
	</head>

	<body>
    	    <! -- Début Barre principale -->

				<ul class="barMenu">
				  <li><a href="index.php">Home</a></li>
				  <li><a href="advancedResearch.php">Advanced research</a></li>
				  <li><a href="about.php">About</a></li>
				  <?php
				  	session_start(['cookie_lifetime' => 600]);
				  	if(!empty($_SESSION['started']))
				  		echo '<li><a href="monCompte.php">My account</a></li>';
				  	else 
				  		echo '<li><a href="connexion.php">Connexion</a></li>';
				  ?>
				</ul>
				<form class="barMenu" method="post" action="resultatsDeRecherche.php">
				  <input type="text" name="recherche" placeholder="Search.."> </input>
				  <button class="boutonBarre"><i class="fas fa-search"></i></button>
				</form>
		<! -- Fin Barre principale -->
		
		<header>
			<h1> Research results </h1>
		</header>
	  


	</body>


	<?php
		if(empty($_REQUEST['recherche'])){
			echo '<div   class="request" style="margin-top:10vh; margin-left:25%; font-size:32px">Please, enter a keyword !</div>';
		}else{
	 
			$mot = $_REQUEST['recherche'];
			
			echo '<div   class="request" style="margin-top:10vh; margin-left:25%; font-size:32px"> Result(s) for : "'. $mot.'" </div>';
			
			$mot="%".$mot."%";
			//connexion à la BD
			$connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');

	   		$texte = "select distinct o.reference,o.titre , o.type, o.prixLocation,eo.nom,co.nom"
		                  ." from oeuvre o,editionOeuvre eo,createurOeuvre co"
		                  ." where (o.ido= co.ido and o.ido= eo.ido and upper(o.titre) like upper(:mot))"
		                  ." or ( o.ido= co.ido and o.ido= eo.ido and upper( o.description) like upper(:mot))"
		                  ." or (o.ido= co.ido and o.ido= eo.ido and upper(co.nom) like upper(:mot))"
		                  ." or ( o.ido= co.ido and o.ido= eo.ido and upper(eo.nom) like upper(:mot))";
		   
		   
		    
		    

		    $ordre = oci_parse($connexion, $texte);

		    oci_bind_by_name($ordre, ':mot', $mot);
		    
		    oci_execute($ordre);
		    
		   
		    echo '<table  id="compteClient" style="margin-left:20%">';
		    echo "<tr><th> Reference </th><th> Title </th><th> Type</th><th> Publisher </th> <th> Creator </th> <th>Cost (€)</th></tr>";

		    

		    while (($row = oci_fetch_array($ordre, OCI_BOTH)) !=false) {
		            echo '<tr> <td>'.$row[0].'</td><td>'. $row[1].'</td><td>'. $row[2].'</td>'
		           		 .'<td>'.$row[4].'</td>'.'<td>'.$row[5].'</td>'.'<td>'.$row[3].'</td>'
		           		 .'<td><button class="btn" style="width:100%; height:100%;" onclick="descrptionOeuvre('.$row[0].')">Show more...<i class="fas fa-plus-circle"></i></button>';

		    }
		    echo '</table>';
		           
		    oci_close($connexion);
		}

	?>
		<script> 
		    function descrptionOeuvre(ref){
				window.location.href = "descriptionOeuvre.php?reference="+ref;
			}
		</script> 
</html>
