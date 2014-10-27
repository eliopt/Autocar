<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=centrocar', 'emmanuelcoppey', 'azerty');
} catch (PDOException $e) {
    print 'Erreur !'.$e->getMessage().'<br/>';
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Centrocar — Votre partenaire dans les trajets d'autobus</title>
  <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
  <script src="http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places"></script>
  <script src="./js/jquery.geocomplete.js"></script>
  <script src="./js/autocar.js"></script>
  <link rel="stylesheet" href="./css/style.css">
  <script src="./js/sweet-alert.js"></script> 
  <link rel="stylesheet" type="text/css" href="./css/sweet-alert.css">
</head>
<body style="background:#fff;">
	<div class="intro">
		<form onSubmit="introFormVerif();" class="introFormItineraire">
			<h1>Centrocar, le renouveau de l'autobus<small>Choisissez, achetez, partez</small></h1>
			<div class="introItineraire">
				<div class="introItemContainer">
					<div class="introDepart"></div>
				</div>
				<div class="introItemContainer">
					<div class="introArrivee"></div>
				</div>
			</div>
			<div class="introInput">
				<input type="text" id="depart" placeholder="Départ">
				<input type="text" id="arrivee" placeholder="Arrivée">
			</div>
			<input type="submit" onClick="introFormVerif();return false;" value="C'est parti !">
		</form>
	</div>
	<div class="promo">
		<h2><strong>200 000</strong> km de lignes d'autobus, pourquoi ne pas en tirer parti ?</h2>
		<p>Avec plus de <strong>2000</strong> lignes quotidiennes, Centrocar vous permet de relier <strong>9000</strong> destinations en France, pour un cout inférieur de <strong>37%</strong> en moyenne au train.</p>
	</div>
	<div class="map" id="map">
	</div>
	<div class="features">

	</div>
	<div class="footer">
		<p><strong class="brand">Centrocar</strong> Votre partenaire dans les trajets d'autobus</p>
		<div class="links">
			<a href="cgu">CGU</a> · <a href="a-propos">À propos</a> · <a href="mentions-legales">Mentions légales</a> · <a href="qui-sommes-nous">Qui sommes-nous?</a> · <a href="contact">Contact</a>
		</div>
	</div>
	<script>
	var map;
	function initialize() {
	  var mapOptions = {
	    zoom: 6,
	    center: new google.maps.LatLng(46.8534100, 2.3488000)
	  };
	  map = new google.maps.Map(document.getElementById('map'), mapOptions);
	  var flightPlanCoordinates = [
	<?php
	/*$query = $db->prepare('SELECT * FROM gares LIMIT 0, 5000');
	$query->execute();
	while ($row = $query->fetch()) {
		echo 'new google.maps.LatLng('.number_format($row['latitude_wgs84']).', '.number_format($row['longitude_wgs84']).'),';
	}*/
	?>
	  ];
	  var flightPath = new google.maps.Polyline({
	    path: flightPlanCoordinates,
	    geodesic: true,
	    strokeColor: '#FF0000',
	    strokeOpacity: 1.0,
	    strokeWeight: 2
	  });

	  flightPath.setMap(map);
	}
	google.maps.event.addDomListener(window, 'load', initialize);
    </script>
	<script type="text/javascript">
	$("input").geocomplete();
	$(document).ready(function() {
		$('body').fadeIn(300);
	});
	</script>
</body>
</html>