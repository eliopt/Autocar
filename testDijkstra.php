<?php

/*
Description de l'algorithme de Dijkstra
On suppose ici que le sommet de départ (qui sera la racine de l'arborescence) est le sommet 1. 
Notons qu'on peut toujours renuméroter les sommets pour que ce soit le cas.

Initialisations 
	c(i,j) = 0 si i=j
	c(i,j) = infini si i != j et (i,j) n'est pas un arc
	c(i,j) = d(i,j) si i != j et (i,j) est un arc
	l(j) = c(1,j) et p(j) = NIL, pour 1 <= j <= n
	Pour 2 <= j <= n faire
	   Si c(1,j) < infini alors p(j) = 1.
	S = {1} ; T = {2, 3, ..., n}.

Itérations
	Tant que T n'est pas vide faire :
	Choisir i dans T tel que l(i) est minimum
	Retirer i de T et l'ajouter à S
	Pour chaque successeur j de i, avec j dans T, faire
		  Si l(j) > l(i) + d(i,j) alors
				l(j) = l(i) + d(i,j)
				p(j) = i

*/


function echoln($chaine) {
    echo $chaine . "\n</br>";
}

// -------------------------------------------------------------
class Noeud {
	const C_INFINI = '1000000000';
	
	private $id;
	private $nom = "";
	private $numero = 0;
	private $etat = "aucun";
	private $valeur = self::C_INFINI;
	private $noeud_precedent = null;
	
	function __construct ($id, $nom = "") {
		$this->id = $id;
		$this->nom = $nom;
	}
	
	function __toString() {
		return $this->nom;
	}
	
	function getId() {return $this->id;}
	function getNom() {return $this->nom;}
	function getNumero() {return $this->numero;}
	function getEtat() {return $this->etat;}
	function getValeur() {return $this->valeur;}
	function getNoeud_precedent() {return $this->noeud_precedent;}

	function setId($id) {$this->id = $id;}
	function setNom($nom) {$this->nom = $nom;}
	function setNumero($numero) {$this->numero = $numero;}
	function setEtat($etat) {$this->etat = $etat;}
	function setValeur($valeur) {$this->valeur = $valeur;}
	function setNoeud_precedent(noeud $np) {$this->noeud_precedent = $np;}
	
	function init() {
		$this->etat = "aucun";
		$this->valeur = self::C_INFINI;
		$this->noeud_precedent = null;
	}
}

class Arc {
	private $noeud_depart;
	private $noeud_arrivee;
	private $valeur;
	
	function __construct (Noeud $d, Noeud $a, $valeur) {
		$this->noeud_depart = $d;
		$this->noeud_arrivee = $a;
		$this->valeur = $valeur;
	}
	
	function __toString() {
		return $this->noeud_depart->getNom() . " -> " .  $this->noeud_arrivee->getNom() . " (" . $this->valeur . ")";
	}
	
	function getNoeud_depart() {return $this->noeud_depart;}
	function getNoeud_arrivee() {return $this->noeud_arrivee;}
	function getValeur() {return $this->valeur;}
	function setNoeud_depart(Noeud $n) {$this->noeud_depart = $n;}
	function setNoeud_arrivee(Noeud $n) {$this->noeud_arrivee = $n;}
	function setValeur($v) {$this->valeur = $v;}
}

class Graphe {
	private $tab_noeud = array();
	private $tab_arc = array();
	
	function __construct(Array $n, array $a) {
		$this->tab_noeud = $n;
		$this->tab_arc = $a;
	}

	function getTab_noeud() {return $this->tab_noeud;}
	function getTab_arc() {return $this->tab_arc;}
	function setTab_noeud(Array $t) {$this->tab_noeud = $t;}
	function setTab_arc(Array $t) {$this->tab_arc = $t;}
	
	function get_nb_noeuds() { return count($this->tab_noeud);}
	function get_nb_arcs() { return count($this->tab_arc);}
	// retourne l'arc éventuel contenant les deux noeuds précisés
	function get_arc(Noeud $d, Noeud $a) {
		foreach($this->tab_arc as $arc) {
			if ($arc->noeud_depart == $d and $arc->noeud_arrivee == $a) return $arc;
		}
		return null;
	}
	
	function print_arcs() {
		$arcs = $this->getTab_arc();
		foreach($arcs as $arc) echoln($arc);
	}

	// retourne un tableau de noeuds connectés au noeud spécifié par un arc du graphe, avec sa valeur
	// dans toutes ces méthodes il faudrait vérifier que le noeud en paramètre est bien un noeud du graphe...
	public function get_noeuds_suivants(Noeud $n) {
		$liste_noeuds = array();
		$liste_valeurs = array();
		foreach($this->tab_arc as $arc) {
			if ($arc->getNoeud_depart() == $n) {
				$liste_noeuds[]  = $arc->getNoeud_arrivee();
				$liste_valeurs[] = $arc->getValeur();
			}
		}
		
		return array($liste_noeuds, $liste_valeurs);
	}
	
	function get_noeuds_valeurs () {
		$resultat = array();
		foreach($this->tab_noeud as $noeud) {
			$resultat[$noeud->getId()] = $noeud->getValeur();
		}
		return $resultat;
	}

	function get_noeuds_valeurs_par_nom () {
		$resultat = array();
		foreach($this->tab_noeud as $noeud) {
			$resultat[$noeud->getNom()] = $noeud->getValeur();
		}
		return $resultat;
	}
	
	# retourne le noeud sélectionné. Il ne doit y en avoir qu'un, ce contrôle n'est pas fait.
	function get_noeud_selectionne() {
		foreach($this->tab_noeud as $noeud) {
			if ($noeud->getEtat() == "sélectionné") return $noeud;
		}
		return null;
	}
	
	#retourne les noeuds non traités
	function get_noeuds_non_traites() {
		$tab_noeuds_non_traites = array();
		$tab_valeur_noeuds_non_traites = array();
		
		foreach($this->tab_noeud as $noeud) {
			if ($noeud->getEtat() == "aucun") {
				$tab_noeuds_non_traites[] = $noeud;
			}
		}
		return $tab_noeuds_non_traites;
	}
	
	
	function set_noeud_selectionne(Noeud $n) {
		$ancien_noeud_selection = $this->get_noeud_selectionne();
		if ($ancien_noeud_selection != null) $ancien_noeud_selection->setEtat("traité");
		$n->setEtat("sélectionné");
	}

	
	# retourne les noeuds non marqués qui suivent le noeud sélectionné. 
	# Pour chaque noeud, retourne aussi la valeur de l'arc entre le noeud sélectionné et ce noeud
	function get_noeuds_suivants_non_marques_depuis_noeud_selectionne() {
		$tab_noeud_non_marque = array();
		$tab_valeur_arc = array();

		$selection = $this->get_noeud_selectionne();
		list($noeuds, $valeurs_arcs) = $this->get_noeuds_suivants($selection);
		if ($noeuds !== null) {
			foreach($noeuds as $cle => $n) {
				if ($n->getEtat() == "aucun") {
					$tab_noeud_non_marque[] = $n;
					$tab_valeur_arc[] = $valeurs_arcs[$cle];
				}
			}
			return array($tab_noeud_non_marque, $tab_valeur_arc);
		} 
		else return array(null, null);
		 
	}
}

class Dijkstra {
	private $graphe = null;
	private $depart = null;
	private $arrivee = null;
	private $chemin_minimal = array();
	private $distance_minimale = Noeud::C_INFINI;

	function __construct (Graphe $g) {
		$this->graphe = $g;
	}
	
	function getArrivee() {return $this->arrivee;}
	function getGraphe() {return $this->graphe;}
	function getDepart() {return $this->depart;}
	function getChemin_minimal() {return $this->chemin_minimal;}	
	function getDistance_minimale() {return $this->distance_minimale;}	

	function setGraphe(Graphe $g) {$this->graphe = $g;}

	function setArrivee(Noeud $d) {
		if (!in_array($d, $this->graphe->getTab_noeud() )) {
			return false;
		}
		$this->arrivee = $d;
		if ($d != null) {
			return true;
		}
		else return false;
		
	}
	
	function setDepart(Noeud $d) {
		if (!in_array($d, $this->graphe->getTab_noeud() )) {
			return false;
		}
		$this->depart = $d;
		foreach($this->graphe->getTab_noeud() as $noeud) {
			$noeud->init();
		}

		$chemin_minimal = array();
		$distance_minimale = Noeud::C_INFINI;
		if ($this->depart != null) {
			$this->depart->setValeur(0);
			
			$this->graphe->set_noeud_selectionne($this->depart);
			$this->actualise_valeur_noeuds();
			return true;
		}
		else return false;
	}
	
	function print_valeur_noeuds() {
		$str = "";
		$val = $this->graphe->get_noeuds_valeurs();
		print_r($val);
		echoln("");
	}
	
	function print_valeur_noeuds_par_nom() {
		$str = "";
		$val = $this->graphe->get_noeuds_valeurs_par_nom();
		print_r($val);
		echoln("");
	}
	
	# actualise la valeur des noeuds qui suivent celui qui est sélectionné
	function actualise_valeur_noeuds() {
		$rc = 0;
		$selection = $this->graphe->get_noeud_selectionne();
		if ($selection != null) {
			list($tab_noeuds, $tab_valeur_arc) = $this->graphe->get_noeuds_suivants_non_marques_depuis_noeud_selectionne();
			if ($tab_noeuds != null) {
				foreach($tab_noeuds as $cle => $noeud) {
					$nouvelle_valeur = $selection->getValeur() + $tab_valeur_arc[$cle];
					if ($nouvelle_valeur < $noeud->getValeur()) {
						$noeud->setValeur($nouvelle_valeur);
						$noeud->setNoeud_precedent($selection);
					}
				}
			}
			else $rc = -1;

		}
		else $rc = -2;
		
		return ($rc);
	}
	
	#fonction principale qui effectue la recherche
	function recherche() {
		if ($this->getDepart() === null) {echoln("le noeud de départ n'est pas précisé"); return false;}
		if ($this->getArrivee() === null) {echoln("le noeud d'arrivé n'est pas précisé"); return false;}
		$iteration = 0;
		$noeud = $this->etape_recherche();
		
		while ($noeud !== null) {
			$iteration++;
			#echoln("iteration $iteration");
			//$this->print_valeur_noeuds_par_nom();
			$noeud = $this->etape_recherche();
		}
		
		$distmin = $this->getArrivee()->getValeur();
		$this->distance_minimale = $distmin;
		if ($distmin == Noeud::C_INFINI) {
			return false;
		}
		else {
			$this->calcule_chemin();
			return true;
		}
	}
	
	# depuis le noeud sélectionné en paramètre on cherche l'arc de moindre valeur conduisant aux noeuds suivants
	# le noeud pointé par l'arc minimal devient sélectionné tandis que l'ancien passe à 'traité'
	# retourne la nouvelle sélection
	private function etape_recherche() {
		$rc = 0;
		
		# 1/ recherche du noeud à valeur minimal non marqué
			#1.1/ Recherche des noeuds non marqués
			$noeuds = $this->graphe->get_noeuds_non_traites();
			if ( ($noeuds == null) or (count($noeuds) == 0) ) {
				echoln("tous les noeuds sont traités");
				return null;
			}
		
			#1.2/ Parmi eux, recherche de celui qui a la valeur minimale
			$valeur_min = Noeud::C_INFINI;
			$cle_min = "";
			foreach($noeuds as $cle=>$n) {
				if ($n->getValeur() < $valeur_min) {
					$valeur_min = $n->getValeur();
					$cle_min = $cle;
				}
			}

			#1.3/ Sivaleur_min est infinie, c'est que les noeuds non marqués ont tous une valeur infinie : soient il n'y a pas de chemin qui vont 
			# d'eux à l'arrivée, soit il n'y a pas de chemin qui vont du noeud départ à eux => le processus est alors arrêté
			if ($valeur_min == Noeud::C_INFINI) {
				return null;
			}
		
		# 2/ sélectionner ce noeud
		$this->graphe->set_noeud_selectionne($noeuds[$cle_min]);
		
		# 3/ pour tout successeur non traité du noeud sélectionné, 
		# la valeur du successeur est au plus égale à 
		# la valeur du noeud sélectionné + la valeur de l'arc qui relie le noeud sélectionné au successeur
		$this->actualise_valeur_noeuds();
		
		# 4/ marquage du noeud sélectionné
		$selection = $this->graphe->get_noeud_selectionne();
		$selection->setEtat("traité");
		
		return $selection;
	}
	
	# calcule le chemin minimal du point de départ au point d'arrivée sous forme de tableau de noeuds.
	# retourne le nombre d'étapes pour y parvenir
	public function calcule_chemin() {
		$chemin = array();
		$noeud = $this->getArrivee();
		while ($noeud !== null) {
			$chemin[] = $noeud;
			$noeud = $noeud->getNoeud_precedent();
		}
		$chemin = array_reverse($chemin);
		$this->chemin_minimal = $chemin;
		return count($chemin);
	}
	
	# retourne le chemin minimal sous forme de chaîne de caractères
	public function get_string_chemin() {
		$path = "";
		foreach($this->chemin_minimal as $etape) {
			$path .= ", " . $etape->getNom();
		}
		$path = substr($path, 2);
		return $path;
	}
}

$n = array();
$i = 0; $n[$i] = new Noeud($i, 'Combs'); 
$i = 1; $n[$i] = new Noeud($i, 'Veneux'); 
$i = 2; $n[$i] = new Noeud($i, 'Melun'); 
$i = 3; $n[$i] = new Noeud($i, 'Fontainebleau'); 
$i = 4; $n[$i] = new Noeud($i, 'Montereau'); 
$i = 5; $n[$i] = new Noeud($i, 'Sens'); 
$i = 6; $n[$i] = new Noeud($i, 'Souppes'); 
$i = 7; $n[$i] = new Noeud($i, 'Corbeil'); 
$i = 8; $n[$i] = new Noeud($i, 'Marles'); 
$i = 9; $n[$i] = new Noeud($i, 'Crécy'); 
$i = 10; $n[$i] = new Noeud($i, 'Meaux'); 
$i = 11; $n[$i] = new Noeud($i, 'La Ferté-Sous-Jouarre'); 
$i = 12; $n[$i] = new Noeud($i, 'Saâcy'); 
$i = 13; $n[$i] = new Noeud($i, 'Provins');
$i = 14; $n[$i] = new Noeud($i, 'La Ferté Gaucher'); 
$i = 15; $n[$i] = new Noeud($i, 'Crégy'); 
$i = 16; $n[$i] = new Noeud($i, 'Crouy'); 
$i = 17; $n[$i] = new Noeud($i, 'Lizy'); 
$i = 18; $n[$i] = new Noeud($i, 'Coulommiers'); 
$i = 19; $n[$i] = new Noeud($i, 'Nangis'); 
$i = 20; $n[$i] = new Noeud($i, 'Fontaine-le-Port'); 
$i = 21; $n[$i] = new Noeud($i, 'St Mard'); 
$i = 22; $n[$i] = new Noeud($i, 'Pommeuse'); 

$i = 23; $n[$i] = new Noeud($i, 'Fosses'); 
$i = 24; $n[$i] = new Noeud($i, 'Viarmes'); 
$i = 25; $n[$i] = new Noeud($i, 'Auvers-sur-Oise'); 
$i = 26; $n[$i] = new Noeud($i, 'Chars'); 
$i = 27; $n[$i] = new Noeud($i, 'Persan'); 
$i = 28; $n[$i] = new Noeud($i, 'Bonnières'); 
$i = 29; $n[$i] = new Noeud($i, 'Houdan'); 
$i = 30; $n[$i] = new Noeud($i, 'Rambouillet'); 
$i = 31; $n[$i] = new Noeud($i, 'St Arnoult'); 
$i = 32; $n[$i] = new Noeud($i, 'Angerville'); 
$i = 33; $n[$i] = new Noeud($i, 'Malesherbes'); 
$i = 34; $n[$i] = new Noeud($i, 'Etampes'); 
$i = 35; $n[$i] = new Noeud($i, 'Le Perray'); 
$i = 36; $n[$i] = new Noeud($i, 'Les Essarts-le-Roi'); 
$i = 37; $n[$i] = new Noeud($i, 'St Rémy lès Chevreuse'); 
$i = 38; $n[$i] = new Noeud($i, 'Massy'); 
$i = 39; $n[$i] = new Noeud($i, 'Châtenay-Malabry'); 
$i = 40; $n[$i] = new Noeud($i, 'Paris 15'); 
$i = 41; $n[$i] = new Noeud($i, 'Villeneuve St G.'); 
$i = 42; $n[$i] = new Noeud($i, 'Rungis'); 
$i = 43; $n[$i] = new Noeud($i, 'Jouy-en-Josas'); 
$i = 44; $n[$i] = new Noeud($i, 'Fontenay-le-Fleury'); 
$i = 45; $n[$i] = new Noeud($i, 'St Nom la B.'); 
$i = 46; $n[$i] = new Noeud($i, 'St Germain en Laye'); 
$i = 47; $n[$i] = new Noeud($i, 'Conflans Ste H.'); 
$i = 48; $n[$i] = new Noeud($i, 'Pontoise'); 
$i = 49; $n[$i] = new Noeud($i, 'La Défense'); 
$i = 50; $n[$i] = new Noeud($i, 'Rueil-Malmaison'); 
$i = 51; $n[$i] = new Noeud($i, 'St Cloud'); 
$i = 52; $n[$i] = new Noeud($i, 'Boulogne-Billancourt'); 
$i = 53; $n[$i] = new Noeud($i, 'Alfortville'); 
$i = 53; $n[$i] = new Noeud($i, 'Montgeron'); 
$i = 54; $n[$i] = new Noeud($i, 'Ballancourt'); 
$i = 55; $n[$i] = new Noeud($i, 'Paris Gare de Lyon'); 
$i = 56; $n[$i] = new Noeud($i, 'Paris République'); 
$i = 57; $n[$i] = new Noeud($i, 'Paris Etoile'); 

$i = 58; $n[$i] = new Noeud($i, 'Fontenay-sous-Bois'); 
$i = 59; $n[$i] = new Noeud($i, 'Maisons-Alfort'); 
$i = 60; $n[$i] = new Noeud($i, 'Chelles'); 
$i = 61; $n[$i] = new Noeud($i, 'Esbly'); 
$i = 62; $n[$i] = new Noeud($i, 'Pontault-Combault'); 
$i = 63; $n[$i] = new Noeud($i, 'Tournan'); 

$tab_arc = array(
			new Arc($n[0], $n[1], 42), 
			new Arc($n[0], $n[2], 26), 
			new Arc($n[0], $n[8], 46), 
			new Arc($n[1], $n[0], 42), 
			new Arc($n[1], $n[3], 8), 
			new Arc($n[1], $n[4], 20), 
			new Arc($n[1], $n[6], 40), 
			new Arc($n[1], $n[20], 24),
			new Arc($n[2], $n[1], 25), 
			new Arc($n[2], $n[3], 17), 
			new Arc($n[3], $n[1], 8), 
			new Arc($n[4], $n[1], 18), 
			new Arc($n[4], $n[13], 40),
			new Arc($n[5], $n[4], 43), 
			new Arc($n[6], $n[1], 41), 
			new Arc($n[6], $n[4], 46), 
			new Arc($n[7], $n[0], 11), 
			new Arc($n[7], $n[3], 40),
			new Arc($n[8], $n[9], 21),
			new Arc($n[8], $n[22], 18),
			new Arc($n[9], $n[10], 15),
			new Arc($n[9], $n[22], 20),
			new Arc($n[10], $n[11], 15),
			new Arc($n[10], $n[15], 2),
			new Arc($n[11], $n[12], 7),
			new Arc($n[11], $n[18], 25),
			new Arc($n[12], $n[16], 27),
			new Arc($n[13], $n[14], 40),
			new Arc($n[14], $n[12], 40),
			new Arc($n[15], $n[16], 21),
			new Arc($n[16], $n[17], 13),
			new Arc($n[17], $n[11], 15),
			new Arc($n[18], $n[19], 40),
			new Arc($n[19], $n[20], 40),
			new Arc($n[20], $n[1], 16),
			new Arc($n[15], $n[21], 18),
			new Arc($n[16], $n[21], 36),
			new Arc($n[21], $n[23], 15),
			new Arc($n[22], $n[18], 19),
			
			new Arc($n[21], $n[23], 19),
			new Arc($n[23], $n[24], 15),
			new Arc($n[24], $n[25], 27),
			new Arc($n[25], $n[26], 26),
			new Arc($n[26], $n[27], 42),
			new Arc($n[26], $n[28], 41),
			new Arc($n[27], $n[29], 40),
			new Arc($n[28], $n[30], 35),
			new Arc($n[30], $n[31], 14),
			new Arc($n[31], $n[32], 41),
			new Arc($n[32], $n[33], 38),
			new Arc($n[32], $n[34], 26),
			new Arc($n[34], $n[33], 40),
			new Arc($n[33], $n[1], 38),
			new Arc($n[33], $n[6], 43),
			new Arc($n[33], $n[54], 42),
			new Arc($n[54], $n[7], 16),
			new Arc($n[30], $n[35], 10),
			new Arc($n[35], $n[36], 5),
			new Arc($n[36], $n[37], 15),
			new Arc($n[37], $n[38], 19),
			new Arc($n[38], $n[39], 7),
			new Arc($n[39], $n[40], 12),
			new Arc($n[40], $n[55], 7),
			new Arc($n[55], $n[56], 4),
			new Arc($n[56], $n[57], 5),
			new Arc($n[57], $n[49], 5),
			new Arc($n[49], $n[50], 6),
			new Arc($n[50], $n[51], 3),
			new Arc($n[51], $n[52], 2),
			new Arc($n[52], $n[40], 4),
			new Arc($n[50], $n[46], 8),
			new Arc($n[46], $n[47], 12),
			new Arc($n[47], $n[48], 8),
			new Arc($n[48], $n[26], 18),
			new Arc($n[38], $n[43], 10),
			new Arc($n[43], $n[44], 13),
			new Arc($n[44], $n[45], 7),
			new Arc($n[45], $n[46], 8),
			new Arc($n[0], $n[41], 12),
			new Arc($n[41], $n[42], 10),
			new Arc($n[42], $n[38], 8),
			new Arc($n[55], $n[53], 7),
			new Arc($n[53], $n[41], 8),
			new Arc($n[41], $n[53], 5),
			new Arc($n[53], $n[0], 12),
			
			new Arc($n[55], $n[58], 12),
			new Arc($n[58], $n[60], 7),
			new Arc($n[60], $n[61], 18),
			new Arc($n[61], $n[10], 9),
			new Arc($n[61], $n[9], 10),
			new Arc($n[58], $n[62], 12),
			new Arc($n[62], $n[63], 18),
			new Arc($n[63], $n[8], 10)
		);

$graphe = new Graphe($n, $tab_arc);
$dij = new Dijkstra($graphe);

//echoln("Liste des arcs du graphe :");
//$graphe->print_arcs();


//echoln("exemple 1 : distance minimale entre deux villes pour lesquelles il y a au moins un chemin");
$rc = $dij->setDepart($n[rand(0, 63)]);
$rc = $dij->setArrivee($n[rand(0, 63)]);
if ($rc === true) {
	if ($dij->recherche()) {
		$chemin_str = $dij->get_string_chemin();
		echoln("chemin : $chemin_str");
		echoln("la distance la plus courte entre le noeud " . $dij->getDepart() . " et le noeud " . $dij->getArrivee() . " est " . $dij->getDistance_minimale());
	}
	else echoln("Il n'y a pas de chemin entre " . $dij->getDepart() . " et " . $dij->getArrivee());
}
else {
	echoln("Erreur d'initialisation");
}
/*
echoln("");
echoln("exemple 2 : distance minimale entre deux villes pour lesquelles il n'y a pas de chemin");
$rc = $dij->setDepart($n[18]);
$rc = $dij->setArrivee($n[5]);
if ($rc === true) {
	if ($dij->recherche()) {
		$chemin_str = $dij->get_string_chemin();
		echoln("chemin : $chemin_str");
		echoln("la distance la plus courte entre le noeud " . $dij->getDepart() . " et le noeud " . $dij->getArrivee() . " est " . $dij->getDistance_minimale());
	}
	else echoln("Il n'y a pas de chemin entre " . $dij->getDepart() . " et " . $dij->getArrivee());
}
else {
	echoln("Erreur d'initialisation");
}




# pour calculer tous le chemin le plus court pour toute les paires de noeuds du graphe
# prévoir d'allonger la durée maximale d'exécution du script php à 5 minutes en modifiant le fichier php.ini : 
# max_execution_time = 300
foreach($graphe->getTab_noeud() as $noeud_depart) {
	foreach($graphe->getTab_noeud() as $noeud_arrivee) {
		$rc = $dij->setDepart($noeud_depart);
		$rc = $dij->setArrivee($noeud_arrivee);
		
		if ($dij->recherche()) {
			$chemin_str = $dij->get_string_chemin();
			echoln("chemin : $chemin_str");
			echoln("la distance la plus courte entre le noeud " . $dij->getDepart() . " et le noeud " . $dij->getArrivee() . " est " . $dij->getDistance_minimale());
		}
		else echoln("Il n'y a pas de chemin entre " . $dij->getDepart() . " et " . $dij->getArrivee());
	}	
}
*/
?>