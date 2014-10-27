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
  <title>Centrocar — Votre itinéraire</title>
  <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
  <script src="http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places"></script>
  <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="./css/jquery.datetimepicker.css"/ >
  <script src="./js/sweet-alert.js"></script> 
  <link rel="stylesheet" type="text/css" href="./css/sweet-alert.css">
  <script src="./js/jquery.geocomplete.js"></script>
  <script src="./js/jquery.datetimepicker.js"></script>
  <script src="./js/autocar.js"></script>
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>
	<div class="nav">
		<p><a href="../autocar/" class="brand">Centrocar</a> Votre partenaire dans les trajets d'autobus</p>
	</div>
	<div class="content">
		<h1>Votre itinéraire de <span class="de"></span> à <span class="a"></span></h1>
		<div class="steps">
			<div class="date step active">Date</div>
			<div class="places step">Places</div>
			<div class="itineraire step">Itineraire</div>
			<div class="payement step">Payement</div>
		</div>
		<div class="etape date active">
			<h2>Quand souhaitez-vous partir ?</h2>
			<time datetime="2014-09-20" class="icon datepicker">
				<input type="hidden" class="departDate" name="departDate">
			  <i class="fa fa-plus fa-4x"></i>
			  <div style="display:none">
			  	<em>Saturday</em>
			  	<strong>September</strong>
			  	<span>20</span>
			  </div>
			</time>
			<h2>Quand voulez-vous revenir ?</h2>
			<time datetime="2014-09-20" class="icon datepicker">
				<input type="hidden" class="retourDate" name="retourDate">
			  <i class="fa fa-plus fa-4x"></i>
			  <div style="display:none">
			  	<em>Saturday</em>
			  	<strong>September</strong>
			  	<span>20</span>
			  </div>
			</time>
			<div class="actions">
				<a href="#" class="btn" onClick="next('date');">Continuer <i class="fa fa-angle-right"></i></a>
			</div>
		</div>
		<div class="etape places">
			<h2>Places</h2>
			<label>Adultes <input type="text" class="adultes" name="adultes" value="0"></label>
			<label>Enfants <input type="text" class="enfants" name="enfants" value="0"></label>
			<div class="actions">
				<a href="#" class="btn btn-grey" onClick="prev('places');"><i class="fa fa-angle-left"></i> Retour</a>
				<a href="#" class="btn" onClick="next('places');">Continuer <i class="fa fa-angle-right"></i></a>
			</div>
		</div>
		<div class="etape itineraire">
			<h2>Itinéraire</h2>
			<div class="itineraireLoader">
				<i class="fa fa-circle-o-notch fa-spin"></i>
				Chargement...
			</div>
			<div class="itineraireContent">
				
			</div>
			<div class="actions">
				<a href="#" class="btn btn-grey" onClick="prev('itineraire');"><i class="fa fa-angle-left"></i> Retour</a>
				<a href="#" class="btn" onClick="next('itineraire');">Continuer <i class="fa fa-angle-right"></i></a>
			</div>
		</div>
		<div class="etape payement">
			<h2>Payement</h2>
			
			<div class="actions">
				<a href="#" class="btn btn-grey" onClick="prev('payement');"><i class="fa fa-angle-left"></i> Retour</a>
			</div>
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
	        sweetAlert('Erreur', '', "error");
	      }
	    });
	    geocoder.geocode({'latLng': latlngarrivee}, function(results, status) {
	      if (status == google.maps.GeocoderStatus.OK) {
	        if (results[0]) {
	          var town = extractFromAdress(results[0].address_components, "locality");
	          $('.a').text(town);
	        }
	      } else {
	        sweetAlert('Erreur', '', "error");
	      }
	    });
		$('body').fadeIn(300);
	});
	function next(now) {
		ok = 0;
		if(now == 'date') {
			if($('.departDate').val()) {
				ok = 1;
			} else {
				sweetAlert('Erreur','Vous devez choisir une date de départ pour continuer !', "error");
			}
		} else if(now == 'places') {
			if($.isNumeric($('.adultes').val()) && $.isNumeric($('.enfants').val())) {
				var count = parseInt($('.adultes').val()) + parseInt($('.enfants').val());
				if(count > 0) {
					ok = 1;
					$('.itineraireLoader').show();
					$('.itineraireContent').hide();
					$.ajax({
					    url: 'http://localhost:8888/autocar/computeItineraire.php',
					    type: 'POST',
					    data: {
					      'departDate': $('.departDate').val(),
					      'retourDate': $('.retourDate').val(),
					      'adultes': $('.adultes').val(),
					      'enfants': $('.enfants').val()
					    },
					    success: function(data, textStatus, xhr) {
					      if(data.indexOf('Erreur') > -1) {
					      	swal(data, '', "error");
					      	prev('itineraire');
					      } else {
					      	$('.itineraireContent').text(data);
					      	$('.itineraireContent').show();
					        $('.itineraireLoader').hide();
					      }
					    },
					    error: function(xhr, textStatus, errorThrown) {
					      prev('itineraire');
					      swal('Vérifiez vos paramètres réseau.', '', "error");
					    }
					});
				} else {
					swal('Vous devez prendre au moins une place !', '', "error");
				}
			} else {
				swal('Veuillez entrer un chiffre !', '', "error");
			}
		} else if(now == 'itineraire') {
			if($('.itineraireLoader').is(':visible')) {
				swal('Veuillez attendre la fin du chargement de l\'itineraire !', '', "error");
			} else {
				ok = 1;
			}
		}
		if(ok == 1) {
			$('.'+now).removeClass('active');
			$('.'+now).next().addClass('active');
		}
	}
	function prev(now) {
		$('.'+now).removeClass('active');
		$('.'+now).prev().addClass('active');
	}
	$('.datepicker').datetimepicker({
	  format:'d/m/Y H:i',
	  lang:'fr',
	  onChangeDateTime:function(dp,$input){
	  	$input.find('input').val(dp);
	  	var date = new Date(dp);
	  	var mois = new Array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
	  	var jours = new Array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
	  	$input.find('span').text(date.getDate());
	  	$input.find('strong').text(mois[date.getMonth()]);
	  	$input.find('em').text(jours[date.getDay()]);
	    $input.attr('datetime', $input.val());
	    $input.find('div').show();
	    $input.find('i').hide();
	  }
	});

	</script>
</body>
</html>