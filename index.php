<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Autocar</title>
  <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
  <script src="http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places"></script>
  <script src="./js/jquery.geocomplete.js"></script>
  <script src="./js/autocar.js"></script>
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>
	<div class="intro">
		<h1>Centrocar, le renouveau de l'autobus</h1>
		<form onSubmit="introFormVerif();">
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
	<script type="text/javascript">
	$("input").geocomplete();
	</script>
</body>
</html>