--Table pour les comptes en general
create table compte(
  adresseMail varchar2(50),
  nom varchar2(20) not null,
  prenom varchar2(20) not null,
  adresse varchar2(100) not null,
  tel varchar2(10) not null,
  dateDeNaissance date not null,
  mdp varchar2(50) not null,
  type varchar2(20) not null,
  primary key(adresseMail),
  check (type in ('Client', 'Gestionnaire_compte', 'Gestionnaire_oeuvre', 'Bibliothecaire' ))
);


--Table pour liaison entre compte et gestionnaire de compte
create table compteGestionnaire(
  adresseMailCompte varchar2(50),
  adresseMail varchar2(50),
  primary key (adresseMailCompte, adresseMail),
  foreign key(adresseMailCompte) references compte(adresseMail),
  foreign key(adresseMail) references compte(adresseMail)
);


--Table pour client
create table client(
  etat varchar2(25) not null,
  pseudo varchar2(25),
  adresseMail varchar2(50) not null,
  nbOeuvresEmpruntees int default 0,
  primary key (pseudo),
  foreign key(adresseMail) references compte(adresseMail),
  check (etat in ('En_cours_de_validation', 'Actif', 'Suspendu')),
  check (nbOeuvresEmpruntees<=5 and nbOeuvresEmpruntees>=0)
);




--Table pour les oeuvres
create table oeuvre(
  ido int,
  reference int not null,
  titre varchar2(100) not null,
  description clob not null,
  type varchar2(10) not null,
  nbExemplaires int not null,
  prixAchat int not null,
  prixLocation int not null,
  dateParution date not null,
  primary key(ido),
  unique (reference),
  check (type in ('Livre', 'CD', 'DVD'))
);

--Table liaison entre oeuvre et gestionnaire des oeuvres
create table oeuvreGestionnaire(
  ido int,
  adresseMail varchar2(50),
  primary key (ido, adresseMail),
  foreign key(ido) references oeuvre(ido),
  foreign key(adresseMail) references compte(adresseMail)
);

create sequence seq_oeuvre start with 1;


--tables pour les editions et les createurs et les liasons avec oeuvres
create table edition(
  nom varchar2(50),
  datededition date,
  primary key(nom)
);
create table editionOeuvre(
  nom varchar2(50),
  ido int,
  primary key(nom, ido),
  foreign key(nom) references edition(nom),
  foreign key(ido) references oeuvre(ido)
);
create table createur(
  nom varchar2(50),
  profession varchar2(50),
  primary key (nom, profession),
  check (profession in ('Compositeur', 'Auteur', 'Producteur'))
);
create table createurOeuvre(
  nom varchar2(50),
  profession varchar2(50),
  ido int,
  primary key(nom, ido),
  foreign key(nom, profession) references createur(nom, profession),
  foreign key(ido) references oeuvre(ido)
);



--Table emprunt
create table emprunt(
  ide int,
  valide int default 0 check (valide = 0 or valide = 1),
  dateReservation date,
  dateDebutEmprunt date not null,
  idcClient varchar2(25) not null,
  reglee int default 0 check (reglee = 0 or reglee = 1),
  montant int default 0,
  datePaiement date,
  primary key (ide),
  foreign key(idcClient) references client(pseudo)
);
create sequence seq_emprunt start with 1000;

--Liaison entre emprunt et bibliothecaire
create table empruntBibliothecaire (
  ide int,
  adresseMail varchar2(50),
  primary key (ide, adresseMail),
  foreign key(ide) references emprunt(ide),
  foreign key(adresseMail) references compte(adresseMail)
);

--Liaison entre oeuvre et emprunt
create table panier(
  ido int,
  ide int,
  rendue int default 0,  check (rendue = 0 or rendue = 1),
  primary key (ido, ide),
  foreign key(ido) references oeuvre(ido),
  foreign key(ide) references emprunt(ide)
);


--Table pour penalite d'un client
create table penalite(
  idp int,
  ide int not null,
  ido int not null,
  montant int not null,
  primary key (idp),
  foreign key(ido) references oeuvre(ido),
  foreign key(ide) references emprunt(ide)
);


--Liaison penalite bibliothecaire
create table penaliteBibliothecaire(
  idp int,
  idcBibliothecaire varchar2(50),
  primary key(idp,idcBibliothecaire),
  foreign key(idcBibliothecaire) references compte(adresseMail),
  foreign key(idp) references penalite(idp)
);
create sequence seq_penalite start with 100000;
