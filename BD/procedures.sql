
--         Gestionnaires de compte




--Ajouter un compte en general

create or replace procedure ajouteCompte(
    l_adresseMail compte.adresseMail%type,
    le_nom compte.nom%type,
    le_prenom compte.prenom%type,
    l_adresse compte.adresse%type,
    le_tel compte.tel%type,
    la_dateDeNaissance compte.nom%type,
    le_mdp compte.mdp%type,
    le_type compte.type%type
   )
is
  cursor c is
    select adresseMail
    from compte
    where type = 'Gestionnaire_compte';
  cursor cOeuvre is
  	select ido
  	from oeuvre;
  cursor cEmprunt is
    select ide
    from emprunt;
  cursor cPenalite is
    select idp
    from penalite;
  cursor cCompte is
    select adresseMail
    from compte
    where adresseMail!=l_adresseMail;
  l_adresseMailGest compte.adresseMail%type;
  l_idp penalite.idp%type;
  l_ido oeuvre.ido%type;
  l_ide emprunt.ide%type;
  l_adresseMailCompte compte.adresseMail%type;
begin
    insert into compte values (l_adresseMail, le_nom, le_prenom, l_adresse, le_tel, la_dateDeNaissance, le_mdp, le_type);

    if le_type = 'Gestionnaire_oeuvre' then
      open cOeuvre;
      fetch cOeuvre into l_ido;
      while cOeuvre%found loop
        insert into oeuvreGestionnaire values(l_ido, l_adresseMail);
        fetch cOeuvre into l_ido;
      end loop;
      close cOeuvre;
    else
      if le_type = 'Bibliothecaire' then
        open cEmprunt; open cPenalite;
        fetch cEmprunt into l_ide; fetch cPenalite into l_idp;
        while cEmprunt%found loop
          insert into empruntBibliothecaire values (l_ide, l_adresseMail);
          fetch cEmprunt into l_ide;
        end loop;
        while cPenalite%found loop
          insert into penaliteBibliothecaire values (l_idp, l_adresseMail);
          fetch cPenalite into l_idp;
        end loop;
        close cEmprunt; close cPenalite;
      else
        if le_type = 'Gestionnaire_compte' then
          open cCompte;
          fetch cCompte into l_adresseMailCompte;
          while cCompte%found loop
            dbms_output.put_line(' ______________ici________'||l_adresseMailCompte|| '  '|| l_adresseMail);
            insert into compteGestionnaire values (l_adresseMailCompte, l_adresseMail);
            fetch cCompte into l_adresseMailCompte;
          end loop;
          close cCompte;
        end if;
      end if;
    end if;
    open c;
    fetch c into l_adresseMailGest;
    while c%found loop
      insert into compteGestionnaire values (l_adresseMail, l_adresseMailGest);
      fetch c into l_adresseMailGest;
    end loop;
    close c;
end;
/





-- modifie etat d'un compte client (Le valide ou le bloque)

create or replace procedure modifieEtatCompteClient(
    le_pseudo client.pseudo%type,
    l_etat client.etat%type,
    le_message out varchar2)
is
  cursor c is
    select *
    from client
    where pseudo = le_pseudo;
  le_client c%rowtype;
begin
    open c;
    fetch c into le_client;
    if c%found then
      update client
        set etat = l_etat
        where pseudo = le_pseudo;
      le_message:='Modification réussie.';
    else
      le_message:='Modification état échouée.';
    end if;
    close c;
end;
/





--         Gestionnaires d'oeuvres


-- Ajoute Oeuvre à  la BD

create or replace procedure ajouteOeuvre(
    la_reference oeuvre.reference%type,
    le_titre oeuvre.titre%type,
    la_description oeuvre.description%type,
    le_type oeuvre.type%type,
    le_nbExemplaires oeuvre.nbExemplaires%type,
    le_prixAchat oeuvre.prixAchat%type,
    le_prixLocation  oeuvre.prixLocation%type,
    la_dateParution oeuvre.dateParution%type,
    le_nomEdition edition.nom%type,
    le_nomCreateur createur.nom%type,
    la_dateEdition edition.datededition%type,
    la_profession createur.profession%type
   )
is
  cursor c1 is
    select *
    from createur
    where nom = le_nomCreateur;
  cursor c2 is
    select *
    from edition
    where nom = le_nomEdition;
  cursor c3 is
    select adresseMail
    from compte
    where type = 'Gestionnaire_oeuvre';
  cursor cOeuvre is
    select ido
    from oeuvre
    where reference = la_reference;
  l_adresseMailGest compte.adresseMail%type;
  l_oeuvre oeuvre.ido%type;
  le_createur c1%rowtype;
  l_edition c2%rowtype;
begin
    open c1; open c2; open c3; open cOeuvre;
    fetch c1 into le_createur;
    fetch c2 into l_edition;
    fetch cOeuvre into l_oeuvre;

    if c1%notfound then
      insert into createur values (le_nomCreateur, la_profession);
    end if;
    if c2%notfound then
      insert into edition values (le_nomEdition, la_dateEdition);
    end if;
    if cOeuvre%notfound then
      l_oeuvre := seq_oeuvre.nextval;
      insert into oeuvre values (l_oeuvre, la_reference, le_titre, la_description, le_type, le_nbExemplaires, le_prixAchat, le_prixLocation, la_dateParution);
    end if;

    insert into editionOeuvre values (le_nomEdition, l_oeuvre);
    insert into createurOeuvre values (le_nomCreateur, la_profession, l_oeuvre);
    fetch c3 into l_adresseMailGest;
    while c3%found loop
        insert into oeuvreGestionnaire values (l_oeuvre, l_adresseMailGest);
        fetch c3 into l_adresseMailGest;
    end loop;
    close c1; close c2; close c3; close cOeuvre;
end;
/







--          bibliothecaire



--Le bibliothecaire confirme un emprunt (le client est bien venu recuperer les oeuvres de son panier)
create or replace procedure confirmeEmprunt(
    le_pseudo client.pseudo%type,
    l_ide emprunt.ide%type)
is
    cursor cOeuvre is
      select oeuvre.ido, nbExemplaires
      from oeuvre, panier
      where ide = l_ide
        and oeuvre.ido = panier.ido;
    l_ido oeuvre.ido%type;
    le_nbExemplaires oeuvre.prixLocation%type;
    le_nbOeuvres client.nbOeuvresEmpruntees%type;
    le_nbOeuvresEmpruntees client.nbOeuvresEmpruntees%type;
begin
  open cOeuvre;
  fetch cOeuvre into l_ido, le_nbExemplaires;
  le_nbOeuvres:=0;
  --Compte le nb d'oeuvre a emprunter
  while cOeuvre%found loop
    le_nbOeuvres:=le_nbOeuvres+1;
    fetch cOeuvre into l_ido, le_nbExemplaires;
  end loop;
  close cOeuvre;

  --Recupere le nb doeuvres deja empruntees actuellement pour le mettre a jour
  select nbOeuvresEmpruntees
    into le_nbOeuvresEmpruntees
    from client
    where pseudo=le_pseudo;
  update client
    set nbOeuvresEmpruntees=nbOeuvresEmpruntees + le_nbOeuvres
    where pseudo=le_pseudo;

  --Met a jour la date debut emprunt et confrime que les oeuvres ont ete recup et payées
  update emprunt
    set reglee=1, dateDebutEmprunt=sysdate
    where ide=l_ide;
end;
/






--Annule la penalite d'un client
create or replace procedure supprimePenalite(
    l_idp client.etat%type)
is
begin
    delete penaliteBibliothecaire
      where idp = l_idp;
    delete penalite
      where idp = l_idp;
end;
/





-- Client a rendue l'oeuvre d'un emprunt
create or replace procedure aRenduOeuvre(
    l_ido oeuvre.ido%type,
    l_ide emprunt.ide%type)
is
    le_nbExemplaires oeuvre.nbExemplaires%type;
    le_nbOeuvresEmpruntees client.nbOeuvresEmpruntees%type;
    le_pseudo client.pseudo%type;
begin
    update panier
      set rendue = 1
      where ido = l_ido
        and ide = l_ide;
    select nbExemplaires
      into le_nbExemplaires
      from oeuvre
      where ido = l_ido;
    update oeuvre
      set nbExemplaires=le_nbExemplaires+1;

    select nbOeuvresEmpruntees, pseudo
      into le_nbOeuvresEmpruntees, le_pseudo
      from client, emprunt
      where ide=l_ide
        and pseudo = idcClient;
    update client
      set nbOeuvresEmpruntees = le_nbOeuvresEmpruntees-1
      where pseudo = le_pseudo;
end;
/




-- Remet en rayon les oeuvres dont les reservations nont pas ete reglées (ie le client nest pas venu payer et prendre les oeuvres)
--  On annule la reservation : on remet au rayon, et on supprime le panier
create or replace procedure remetEnRayon
is
    cursor cEmprunt is
      select ide
        from emprunt
        where valide = 1
          and sysdate > dateReservation+3;
    l_ide emprunt.ide%type;
    l_ido oeuvre.ido%type;
    le_nbExemplaires oeuvre.nbExemplaires%type;
begin
    open cEmprunt;
    fetch cEmprunt into l_ide;
    while cEmprunt%found loop
      --Met a jour nb dexemplaires des eouvres
      for x in (
        select panier.ido, nbExemplaires
          from panier, oeuvre
          where ide = l_ide
            and panier.ido = oeuvre.ido)
        loop
          update oeuvre
            set nbExemplaires = x.nbExemplaires+1
            where ido=x.ido;
        end loop;
      -- Supprime le panier
      delete empruntBibliothecaire
        where ide = l_ide;
      delete panier
        where ide = l_ide;
      delete emprunt
        where ide = l_ide;

      fetch cEmprunt into l_ide;
    end loop;
    close cEmprunt;
end;
/




-- Permet de creer/mettre a jour toutes les penalites d'aujourdhui
create or replace procedure creationEtMAJPenalites
is
    cursor cEmprunt is
      select ido, emprunt.ide, idcClient
        from emprunt, panier
        where reglee = 1
          and emprunt.ide = panier.ide
          and sysdate>(dateDebutEmprunt + 15)
          and rendue = 0;
    cursor cBibliothecaire is
      select adresseMail
      from compte
      where type = 'Bibliothecaire';
    l_emprunt cEmprunt%rowtype;
    l_idp penalite.idp%type;
    l_adresseMailBib compte.adresseMail%type;
begin
    l_idp:=-1;
    open cEmprunt;
    fetch cEmprunt into l_emprunt;
    while cEmprunt%found loop
        -- si la penalite existe deja on lui rajoute 1euro
      for x in (
          select idp, montant
            from penalite
            where ide = l_emprunt.ide
              and ido = l_emprunt.ido)
          loop
            l_idp:=x.idp;
            update penalite
              set montant = x.montant+1
              where idp = l_idp;
          end loop;
      -- Si la penalite n'exite pas on la cree
      if l_idp=(-1) then
        l_idp:= seq_penalite.nextval;
        insert into penalite values (l_idp, l_emprunt.ide, l_emprunt.ido, 1);
      end if;

      -- On ajoute la penalite aux biblothecaires
      fetch cBibliothecaire into l_adresseMailBib;
      while cBibliothecaire%found loop
        insert into penaliteBibliothecaire values (l_idp, l_adresseMailBib);
        fetch cBibliothecaire into l_adresseMailBib;
      end loop;
      fetch cEmprunt into l_emprunt;
    end loop;
    close cEmprunt;
end;
/



--          Client



-- Ajouter un compte client

create or replace procedure ajouteClient(
    l_adresseMail compte.adresseMail%type,
    le_nom compte.nom%type,
    le_prenom compte.prenom%type,
    l_adresse compte.adresse%type,
    le_tel compte.tel%type,
    la_dateDeNaissance compte.nom%type,
    le_mdp compte.mdp%type,
    le_pseudo client.pseudo%type,
    l_etat client.etat%type)
is
begin
    ajouteCompte(l_adresse, le_nom, le_prenom, l_adresse, le_tel, la_dateDeNaissance, le_mdp, 'Client');
    insert into client values ('En_cours_de_validation', le_pseudo, l_adresseMail, 0);
end;
/



-- Renvoie l'identifiant de l'emprunt (panier) en cours (i.e qui n'a pas encore été confirmé et validé/payé)

create or replace function panierEnCours(
    le_pseudo emprunt.idcClient%type)
    return emprunt.ide%type
is
    cursor c is
      select ide
      from emprunt
      where valide = 0
      and idcClient = le_pseudo;
    l_ide emprunt.ide%type;
begin
    open c;
    fetch c into l_ide;
    if c%notfound then
      return -1;
    end if;
    close c;
    return l_ide;
end;
/


-- Rajoute une oeuvre si possible à emprunter à un emprunt/panier
create or replace procedure ajouteAPanierEmprunt(
    le_pseudo client.pseudo%type,
    l_ido oeuvre.ido%type,
    le_message out varchar2)
is
    cursor c1 is
      select nbOeuvresEmpruntees
      from client
      where pseudo = le_pseudo;
    cursor c2 is
      select nbExemplaires
      from oeuvre
      where ido = l_ido;
    le_nbOeuvresEmpruntees client.nbOeuvresEmpruntees%type;
    le_nbExemplaires oeuvre.nbExemplaires%type;
    l_ide emprunt.ide%type;
begin
    open c1; open c2;
    fetch c1 into le_nbOeuvresEmpruntees;
    fetch c2 into le_nbExemplaires;
    if c1%found and c2%found then
      if le_nbOeuvresEmpruntees<5 then
        if le_nbExemplaires>0 then
          l_ide := panierEnCours(le_pseudo);
          if l_ide=-1 then  --Si il n'existe pas de panier/emprunt qu'on n'a pas encore validé
            l_ide:=seq_emprunt.nextval;
            insert into emprunt(ide, idcClient) values (l_ide, le_pseudo);
          end if;
          insert into panier values(l_ido, l_ide, 0);
          le_message := 'Ajout réussi';
        else
          le_message:='Oeuvre non disponible';
        end if;
      else
        le_message:='Panier plein : Dépassement limite emprunts';
      end if;
    end if;
    close c1; close c2;
end;
/




-- Valide un panier/emprunt
create or replace procedure valideEmprunt(
    le_pseudo client.pseudo%type,
    l_ide emprunt.ide%type,
    le_message out varchar2)
is
    cursor cOeuvresPanier is
      select oeuvre.ido, nbExemplaires
      from panier, oeuvre
      where ide = l_ide
        and oeuvre.ido = panier.ido;

    cursor cBibliothecaire is
      select adresseMail
      from compte
      where type = 'Bibliothecaire';

    l_ido oeuvre.ido%type;
    le_prix oeuvre.prixLocation%type;
    le_montant oeuvre.prixLocation%type;
    le_nbOeuvresEmpruntees client.nbOeuvresEmpruntees%type;
    le_nbOeuvresAEmprunter client.nbOeuvresEmpruntees%type;
    l_adresseMailBib compte.adresseMail%type;
    le_nbExemplaires oeuvre.nbExemplaires%type;
begin
    -- On recupere le nb d'oeuvres deja empruntées actuellement
    select nbOeuvresEmpruntees
      into le_nbOeuvresEmpruntees
      from client
      where pseudo=le_pseudo;

    -- On recupere le nombre d'oeuvres qu'on veut emprunter et le prix total
    le_montant:= 0;
    le_nbOeuvresAEmprunter:= 0;
    open cOeuvresPanier;
    fetch cOeuvresPanier into l_ido, le_nbExemplaires;
    while cOeuvresPanier%found loop
      select prixLocation
        into le_prix
        from oeuvre
        where ido=l_ido;
      le_montant:= le_montant + le_prix;
      le_nbOeuvresAEmprunter:=le_nbOeuvresAEmprunter+1;
      fetch cOeuvresPanier into l_ido, le_nbExemplaires;
    end loop;
    close cOeuvresPanier;

    open cOeuvresPanier;
    fetch cOeuvresPanier into l_ido, le_nbExemplaires;

    --Si le nb d'oeuvres quon va emprunter + celles deja empruntees <=5 alors on peut valider le panier
    if (le_nbOeuvresAEmprunter+le_nbOeuvresEmpruntees)<=5 then
      update emprunt
        set valide = 1, montant = le_montant, dateReservation=sysdate
        where ide=l_ide;
      open cBibliothecaire;
      fetch cBibliothecaire into l_adresseMailBib;
      while cBibliothecaire%found loop
        insert into empruntBibliothecaire values (l_ide, l_adresseMailBib);
        fetch cBibliothecaire into l_adresseMailBib;
      end loop;
      close cBibliothecaire;

      --met a jour le nb d'exemplaires
      while cOeuvresPanier%found loop
        update oeuvre
          set nbExemplaires = (le_nbExemplaires-1)
          where ido = l_ido;
        fetch cOeuvresPanier into l_ido, le_nbExemplaires;
      end loop;
      close cOeuvresPanier;
    end if;
end;
/



--Annule Panier
create or replace procedure annulePanier(
    l_ide emprunt.ide%type)
is
begin
    delete panier
      where ide = l_ide;
    delete emprunt
      where ide = l_ide;
end;
/

--Supprime oeuvre de Panier
create or replace procedure supprimeOeuvreDePanier(
    l_ide emprunt.ide%type,
    l_ido oeuvre.ido%type)
is
begin
    delete panier
      where ide = l_ide
        and ido = l_ido;
end;
/
