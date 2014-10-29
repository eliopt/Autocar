<?php
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
?>