<html>
    <head>
        <link rel="stylesheet" href="styleGLA.css" type="text/css" />
        
        <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
        <title> Page de recherche avancée </title>
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
         <br></br>
        
        <h1>Advanced Research</h1>
        <div class="formulaireResearch">
            <form method="post" action="resultsRechercheAvancee.php">
                Work's reference : </br>
                <input type="text" name="workref" pattern="[0-9]{1,20}" 
                title ="must contain only numbers; no more than 20 characters"> </input> <br><br>
                Work's name : </br>
                <input type="text" name="workName"pattern="[A-Za-z-0-9]{1,10}" 
                title ="must contain only letters and numbers; no more than 10 characters"> </input> <br><br>
                Author's name : </br>
                <input type="text" name="authorName"pattern="[A-Za-z-0-9]{1,10}" 
                title ="must contain only letters and numbers; no more than 10 characters"> </input> <br><br>
                Publisher's name  : </br>
                <input type="text" name="editionName" pattern="[A-Za-z-0-9]{1,10}" 
                title ="must contain only letters and numbers; no more than 10 characters"> </input> <br><br>
                Type :</br>
                
                <select name = "type" class="form-control" style=" width: 50%">
                      <option value='All'>All</option>
                      <option value='Book'>Book</option>
                      <option value='CD'>CD</option>
                      <option value='DVD'>DVD</option>
                </select> <br> <br>
                
                <input type="submit" name="submit" value="Search"> </input>
                
                
                
            </form>
        </div>
    </body>
</html>

    
