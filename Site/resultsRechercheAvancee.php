<html>
  <head> 
        <link rel="stylesheet" href="styleGLA.css" type="text/css" />
        
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
        <title> Page résultats de recherche avancée </title>
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
            else 
              echo '<li><a href="connexion.php">Connexion</a></li>';
          ?>
        </ul>
        <form class="barMenu" method="post" action="resultatsDeRecherche.php">
          <input type="text" name="recherche" placeholder="Search.." pattern="[A-Za-z0-9]{1,10}" 
                   title =" keyword must contain only letters and numbers ! no more than 10 characters"> </input>
          <button class="boutonBarre"><i class="fas fa-search"></i></button>
        </form>
    <! -- Fin Barre principale -->
        

        <?php
            
          echo '<div class="main"></br></br></br><h1> Result(s) : </h1></br>';
          
     	  
            
          $ref =$_REQUEST['workref'];
          $workName = $_REQUEST['workName'];
          $authorName = $_REQUEST['authorName'];
          $editionName = $_REQUEST['editionName'];
          $type = $_REQUEST['type'];

     	  
        
          if (empty($ref) and empty($workName)and empty($authorName) and empty($editionName) and ($type =="All")){
                
             echo '<div   class="request" style="margin-top:10vh; margin-left:25%; font-size:32px">Please, enter one or more keywords !</div>';
              
           }
            
          else{
                
              $connexion = oci_connect('c##lizri_a', 'lizri_a', 'dbinfo');


              //construction de la requete 

              $requete =             
              " select  distinct o.reference, o.titre, o.type, o.prixLocation, eo.nom,co.nom "
              . "from oeuvre o, createurOeuvre co,editionOeuvre eo "
              . "where o.ido= co.ido and o.ido= eo.ido";

              if(!empty($ref)) {
                  $requete = $requete . " and o.reference = :ref ";
              }

              if(!empty($workName)) {
                  $requete = $requete . " and upper(titre) like '%' || upper(:txtTitre) || '%' ";
              }


              if(!empty($authorName)){
                  $requete=$requete." and upper(co.nom) like '%' || upper(:txtAuteur) || '%' ";
              }

              if(!empty($editionName)){
                  $requete=$requete." and upper(eo.nom) like '%' || upper(:txtEdition) || '%' ";
              }

              if(!empty($type)and $type !="All"){
                  $requete=$requete." and o.type like (:txtType)";
              }

             

              $ressource = oci_parse($connexion, $requete);

              
              if(!empty($ref)) {
                 oci_bind_by_name($ressource, ":ref", $ref);
              }

              if(!empty($workName)) {
                 oci_bind_by_name($ressource, ":txtTitre", $workName);
              }

              if(!empty($authorName)){
                 oci_bind_by_name($ressource, ":txtAuteur", $authorName);
              }
              
              if(!empty($editionName)){
                 oci_bind_by_name($ressource, ":txtEdition", $editionName);
              }

              if(!empty($type)and $type !="All"){
                 oci_bind_by_name($ressource, ":txtType", $type);
              }

              

      
              oci_execute($ressource);
              $test = ($row = oci_fetch_array($ressource, OCI_BOTH) );
			  if ($test ==false){
				  echo '<div   class="request" style="margin-top:9vh; margin-left:20%; font-size:26px; color:#A5749D"> 
			  	      			Sorry, There is no product matching your request.</div>';

			  }else{
				  echo '<table  id="compteClient" >';
				  echo "<tr><th> Reference </th><th> Title </th><th> Type</th><th> Publisher </th> <th> Creator </th> <th>Cost (€)</th></tr>";
					
		          while ($test !=false) {
		         
                     
                     echo '<tr> <td>'.$row[0].'</td><td>'. $row[1].'</td><td>'. $row[2].'</td>'
                             .'<td>'.$row[4].'</td>'.'<td>'.$row[5].'</td>'.'<td>'.$row[3].'</td>'
                             .'<td><button class="btn" style="width:100%; height:100%;" onclick="descrptionOeuvre('.$row[0].')">Show more...<i class="fas fa-plus-circle"></i></button>';  
                      $test = ($row =oci_fetch_array($ressource, OCI_BOTH) );       
                                        
                  }
                                  
                  echo '</table>';
                    
            }
          
          
            oci_free_statement($ressource);

            oci_close($connexion);
           
        }
            
            
        ?>
        
<script> 
      function descrptionOeuvre(ref){
      window.location.href = "descriptionOeuvre.php?reference="+ref;
  }
</script> 

  </body>
</html>
