<?php
session_start();
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
  <title>Centrocar — Votre itinéraire de Paris à Lyon</title>
  <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
  <script src="http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places"></script>
  <script src="./js/jquery.geocomplete.js"></script>
  <script src="./js/autocar.js"></script>
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>
	<div class="nav">
		<p><a href="../autocar/" class="brand">Centrocar</a> Votre partenaire dans les trajets d'autobus</p>
	</div>
	<div class="content">
		<h1>Votre itinéraire de <span class="de"></span> à <span class="a"></span></h1>
		<div class="etape">
			<h2>Paris <small>Bus 36</small><small class="prix">10€</small></h2>
		</div>
		<div class="etape">
			<h2>Tonnerre <small>Bus 35</small><small class="prix">20€</small></h2>
		</div>
		<div class="etape">
			<h2>Lyon</h2>
		</div>
		<div class="actions">
			<h2>Total: 30€</h2>
			<a href="reserver" class="btn">Réserver</a>
		</div>
	</div>
	<script type="text/javascript">
	$(document).ready(function() {
		var geocoder = new google.maps.Geocoder();
    	var latlng = new google.maps.LatLng(<?php echo $_SESSION['de'][0]; ?>, <?php echo $_SESSION['de'][1]; ?>);
	    var latlngarrivee = new google.maps.LatLng(<?php echo $_SESSION['a'][0]; ?>, <?php echo $_SESSION['a'][1]; ?>);
	    geocoder.geocode({'latLng': latlng}, function(results, status) {
	      if (status == google.maps.GeocoderStatus.OK) {
	        if (results[0]) {
	          var town = extractFromAdress(results[0].address_components, "locality");
	          $('.de').text(town);
	        }
	      } else {
	        alert('Erreur');
	      }
	    });
	    geocoder.geocode({'latLng': latlngarrivee}, function(results, status) {
	      if (status == google.maps.GeocoderStatus.OK) {
	        if (results[0]) {
	          var town = extractFromAdress(results[0].address_components, "locality");
	          $('.a').text(town);
	        }
	      } else {
	        alert('Erreur');
	      }
	    });
		$('body').fadeIn(300);
	});
	</script>
</body>
</html>