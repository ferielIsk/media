<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="styleGLA.css" type="text/css"/>

	 	<link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
		<title> Modalités</title>
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

	<header>
	<h1> Modalités </h1>
	</header>
	

	<h2>Utilisateur</h2>
	<div>
		<ul>
		<li>Non connecté<br>
		Celui-ci a accès à une partie des fonctionnalités qu’offre le site, il a la possibilité de :
consulter les informations pratiques de la médiathèque (adresse et horaires d’ouverture ),
faire des recherches dans le catalogue et de s'inscrire pour pouvoir accès aux autres
fonctionnalités qui s’offrent à lui en étant connecté.</li>
	
		<li>Connecté<br>
		En plus des fonctionnalités qu’il peut déjà faire en n’étant pas connecté, le visiteur inscrit peut
réserver des œuvres (en les ajoutant à son panier et en validant celui-ci), accéder aux emprunts
déjà effectués, consulter ses informations personnelles et modifier certaines d’entre elles (son
pseudo, son adresse postale, son numéro de téléphone, son mot de passe), il peut aussi prendre
connaissance des éventuelles factures et amendes qui lui ont été infligées. Il pourra aussi se
désinscrire à tout moment</li>

		<li>Un utilisateur est reconnu de manière unique soit par son adresse électronique soit par son
pseudo. Ce qui implique que 2 utilisateurs différents ne peuvent partager une même adresse
électronique ou un même pseudo.
L’utilisateur doit avoir un seul mot de passe qui lui est propre.
Un gestionnaire de comptes ne peut accéder aux mots de passe des comptes (Il ne peut les
consulter, ni les modifier)</li>
		</ul></div>

	<div>
	<h2>Inscription</h2>

	Une inscription n’est effective qu’après validation par le gestionnaire client. <br>
	Un client désirant s’inscrire doit soumettre une pièce d’identité valide
	
	<h2>Modalité d'utilisation</h2>

	Seul un adhérent (et donc un client connecté dont le compte a été validé par le gestionnaire
		des comptes et dont l’état est « actif ») peut réserver des œuvres (remplir son panier et le
		valider). <br>
	Un adhérent ne peut emprunter que 5 œuvres en même temps. 
	Chaque œuvre peut être réservée seule ou avec d’autres œuvres. 
	Une œuvre ne peut être réservée que si elle est disponible. 
	Un adhérent réserve une œuvre en l’ajoutant à son panier et en le validant. <br>

	Lorsqu’un adhérent valide son panier, une facture associée à ce dernier sera générée, La
		facture contiendra un récapitulatif du panier, c’est-à-dire l’ensemble des œuvres ajoutées
		par l’adhérent et leur prix de location. <br>
	Le total du panier correspondant à la somme des prix de location des œuvres sélectionnées
		par l’adhérent. <br>
	Une œuvre a une durée de pré-réservation de maximum trois jours. Si au bout de 3 jours
		l'adhérent n’est pas venu procéder au payement de sa facture et récupérer ses œuvres
		réservées, la réservation sera annulée (ce qui implique la remise en rayon des œuvres
		concernées.) <br>
	Un client peut réserver une œuvre pour une durée de maximum 2 semaines à compter du
		jour où l’œuvre a été récupérée. Au-delà de ce délai, le retour de l’œuvre sera considéré en
		retard. <br>
	Des pénalités s’appliquent en cas de retard : un euro par jour et par œuvre. <br>
	En cas détérioration de l'œuvre, le client est facturé du montant correspondant au prix
		d’achat de l'œuvre majoré de 20 euros. <br>
	En cas de retard d’un mois ou plus, ou dans le cas d’une amende non payée datant de plus
		de deux mois le compte de l’adhérent concerné est suspendu. <br>
	Si le compte d’un utilisateur est suspendu il ne peut pas emprunter de nouvelles œuvres, il
		peut cependant retourner les œuvres déjà empruntées et payer ses amendes. <br>
	</div>


</body>
</html>