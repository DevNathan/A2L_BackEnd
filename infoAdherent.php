<?php 
try
{
	// On se connecte à MySQL en phase de test
	$bdd = new PDO('mysql:host=localhost;dbname=test;charset=utf8', 'root', 'root');
}
catch(Exception $e)
{
	// En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur database: '.$e->getMessage());
}

//Cette API n'est destinée qu'au membre du bureau. Ainsi sans le nom et le mot de passe elle ne revoie rien du tout
//Données de l'URL :
$nom = $_POST['Nom'];//On récupère le nom associé
$dateNaissance = $_POST['DateNaissance'];//Et on récupère le HASH su mot de passe associé.



$checkInfo = $bdd->query('SELECT * FROM ListeAdherents WHERE Nom="'.$nom.'"');

//On formatte les données récupérés
$infos = $checkInfo->fetch();


if($infos['Nom'] != "") { //Si on ne detecte aucun élève
	//La connexion est prête. Verification de la date de naissance : 
	if($infos['DateNaissance'] == $dateNaissance) {
		//On execute en MySQL pour atteindre la database voulue
		$reponse = $bdd->query('SELECT * FROM ListeAdherents WHERE Nom="'.$nom.'"');

		$donnesReponse = $reponse -> fetch();


		//Tableau total de la forme [id:1, Nom: "nom",Statut = "adherent",DateNaissance: 2002-11-14]
		$entireArray = array();
		$row['id'] = $donnesReponse['id'];
		$row['Nom'] = $donnesReponse['Nom'];
		$row['Statut'] = $donnesReponse['Statut'];
		$row['DateNaissance'] = $donnesReponse['DateNaissance'];
		$row['Classe'] = $donnesReponse['Classe'];
		$row['PointFidelite'] = $donnesReponse['PointFidelite'];
		if($donnesReponse['CodeTemporaire'] == ""){
			$row['HaveCodeTemporaire?'] = "false";
		} else {
			$row['HaveCodeTemporaire?'] = "true";
		}

		$entireArray[] = $row;

		//On encode en JSON, convention pour une API
		print json_encode($entireArray);

		$reponse -> closeCursor();
	} else { // Les infromations sont cohérentes mais le mot de passe est faux
		?>"Date de naissance incorrect"<?php
	}
	
} else { // Les infromations ne sont pas bonnes
	?>"Élève introuvable"<?php
}


?>