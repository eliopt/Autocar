function introFormVerif() {
  if($('#depart').val() && $('#arrivee').val()) {
    var geocoder = new google.maps.Geocoder();
    var ok = 0;
    var departLocation;
    var arriveeLocation;
    geocoder.geocode( { 'address': $('#depart').val()}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        ok++;
        departLocation = results[0].geometry.location;
        if(ok == 2) itineraire(departLocation, arriveeLocation);
      } else {
        sweetAlert('Erreur', 'L\'adresse n\'est pas valide.', "error");
      }
    });
    geocoder.geocode( { 'address': $('#arrivee').val()}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        ok++;
        arriveeLocation = results[0].geometry.location;
        if(ok == 2) itineraire(departLocation, arriveeLocation);
      } else {
        sweetAlert('Erreur','L\'adresse n\'est pas valide.', "error");
      }
    });
  } else {
    sweetAlert('Erreur', 'Les champs sont vides', "error");
  }
}
function extractFromAdress(components, type) {
  for (var i = 0; i < components.length; i++)
  for (var j = 0; j < components[i].types.length; j++)
  if (components[i].types[j] == type) return components[i].long_name;
  return "";
}
function itineraire(departLocation, arriveeLocation) {
  var socket = io.connect();
  socket.emit('loadItineraire', {
      'de': ''+departLocation,
      'a': ''+arriveeLocation
  }).on('succes', function() {
    window.location = 'itineraire';
  }).on('erreur', function(mes) {
    swal(mes['titre'], mes['content'], "error");
  });
  /*$.ajax({
    url: 'http://localhost:8888/autocar/loadItineraire.php',
    type: 'POST',
    data: {
      'de': ''+departLocation,
      'a': ''+arriveeLocation
    },
    success: function(data, textStatus, xhr) {
      if(data == 'Success') {
        window.location = 'itineraire';
      } else {
        sweetAlert('Oooops', 'Une erreur est survenue. Veuillez réessayer.', "error");
      }
    },
    error: function(xhr, textStatus, errorThrown) {
      sweetAlert('Erreur', 'Vérifiez vos paramètres réseau.', "error");
    }
  });*/
}