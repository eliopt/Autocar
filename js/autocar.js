function introFormVerif() {
  if($('#depart').val() && $('#arrivee').val()) {
    var geocoder = new google.maps.Geocoder();
    var ok = 0;
    geocoder.geocode( { 'address': $('#depart').val()}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        ok++;
        departLocation = results[0].geometry.location;
        if(ok == 2) itineraire(departLocation, arriveeLocation);
      } else {
        alert('Erreur, l\'adresse n\'est pas valide');
      }
    });
    geocoder.geocode( { 'address': $('#arrivee').val()}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        ok++;
        arriveeLocation = results[0].geometry.location;
        if(ok == 2) itineraire(departLocation, arriveeLocation);
      } else {
        alert('Erreur, l\'adresse n\'est pas valide');
      }
    });
    var departPremiereValeur = $('#depart').val();
    var arriveePremiereValeur = $('#arrivee').val();
  } else {
    alert('Erreur, les champs sont vides');
  }
}
function itineraire(departLocation, arriveeLocation) {
  jQuery.ajax({
    url: 'loadItineraire.php',
    type: 'POST',
    data: {
      de: departLocation,
      a: arriveeLocation
    },
    dataType : 'json',
    success: function(data, textStatus, xhr) {
      alert(data);
    },
    error: function(xhr, textStatus, errorThrown) {
      alert(textStatus.reponseText);
    }
  });
  //window.location = 'itineraire';
}