<?php
session_start();
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
} else {
	echo $check;
}
?>