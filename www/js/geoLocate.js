
var geocoder = null;

function initialize() {
  geocoder = new MQA.Geocoder();
}

function responseHandler( point ) {
  if( !point ) {
    alert(address + " not found");
  }
  else {
    //document.getElementById('geolocation').innerHTML += '<p>Your location: ' + point + '</p>';
    var location = point.results[0].locations[0];
    var url = window.location.protocol + "//" + window.location.host + "/office/" + location.latLng.lat + "/" + location.latLng.lng;
    window.open(url, "_self");
  }
  return false;
}

function showAddress( address ) {
  geocoder.geocode( address, { maxResults: 1 }, null, responseHandler );
}
