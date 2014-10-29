<?php
session_start();
include('./class/dijkstra.class.php');
function check() {
	if(!empty($_SESSION['a']) AND !empty($_SESSION['de'])) {
		if(isset($_POST['departDate']) AND isset($_POST['retourDate']) AND isset($_POST['adultes']) AND isset($_POST['enfants'])) {
			if(is_numeric($_POST['adultes']) AND is_numeric($_POST['enfants'])) {
				if($_POST['adultes']+$_POST['enfants'] > 0) {
					$return = array();
					$return['adultes'] = $_POST['adultes'];
					$return['enfants'] = $_POST['enfants'];
					$return['de'] = $_SESSION['de'];
					$return['a'] = $_SESSION['a'];
					if($departDate = strtotime($_POST['departDate'])) {
						$return['departDate'] = $departDate;
						if(!empty($_POST['retourDate'])) {
							if($retourDate = strtotime($_POST['retourDate'])) {
								$return['retourDate'] = $retourDate;
								return $return;
							} else {
								return 'Erreur, la date de retour est invalide';
							}
						} else {
							$return['retourDate'] = '';
							return $return;
						}
					} else {
						return 'Erreur, la date de départ est invalide';
					}
				} else {
					return 'Erreur, vous devez prendre au moins une place !';
				}
			} else {
				return 'Erreur, entrez un nombre pour choisir le nombre de places !';
			}
		} else {
			return 'Erreur système !';
		}
	} else {
		return 'Erreur système !';
	}
}
$check = check();
if(is_array($check)) {
	echo json_encode($check);
	/*$n = array();
	$arcs = array();
	$i = 0;
	foreach ($values as $value) {
		$n[$i] = new Noeud($i, $value['arret']);
		$i++;
	}
	$i = 0;
	foreach ($values as $value) {
		$arcs[$i] = new Arc($n[0], $n[1], 42);
		$i++;
	}
	$graphe = new Graphe($n, $arcs);
	$dij = new Dijkstra($graphe);
	$rc = $dij->setDepart($n[$depart]);
	$rc = $dij->setArrivee($n[$arrivee]);
	if($rc === true) {
		if ($dij->recherche()) {
			$chemin_str = $dij->get_string_chemin();
			echo 'chemin : '.$chemin_str;
			echo 'la distance la plus courte entre le noeud '.$dij->getDepart().' et le noeud '.$dij->getArrivee().' est '.$dij->getDistance_minimale();
		}
		else 'Il n\'y a pas de chemin entre '.$dij->getDepart().' et '.$dij->getArrivee();
	}
	else {
		echo 'Erreur système !';
	}*/
} else {
	echo $check;
}
?>