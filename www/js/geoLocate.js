
function responseHandler( point ) {
  if( !point ) {
    alert("Address not found");
  }
  else {
    var location = point.results[0].locations[0];
    //document.getElementById('geolocation').innerHTML = '<p>Your location: ' + location.latLng.lat + ", " + location.latLng.lng + '</p>';
    var url = window.location.protocol + "//" + window.location.host + "/office/" + location.latLng.lat + "/" + location.latLng.lng;
    window.open(url, "_self");
  }
  return false;
}

function showAddress( address ) {
  MQA.withModule('geocoder', function() {
    MQA.Geocoder.geocode( address, { maxResults: 1 }, null, responseHandler );
  });
}
