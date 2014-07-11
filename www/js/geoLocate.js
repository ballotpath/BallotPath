
var geocoder = null;

function initialize() {
  geocoder = new MQA.Geocoder();
}

function responseHandler( point ) {
  if( !point ) {
    alert(address + " not found");
  }
  else {
    document.getElementById('geolocation').innerHTML += '<p>Your location: ' + point + '</p>';
  }
}

function showAddress( address ) {
  geocoder.geocode( address, { maxResults: 1 }, null, responseHandler );
}
