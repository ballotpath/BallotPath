var map = null;
var geocoder = null;

function initialize() {
  if (GBrowserIsCompatible()) {
    geocoder = new GClientGeocoder();
  }
}

function showAddress(address) {
  if (geocoder) {
    geocoder.getLatLng(
      address,
      function(point) {
        if (!point) {
          alert(address + " not found");
          
        } else {
          document.getElementById('geolocation').innerHTML += '<p>Your location: ' + point + '</p>';

        }
      }
    );
  }
}