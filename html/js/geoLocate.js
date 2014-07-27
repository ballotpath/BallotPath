
var map = null;
var geocoder = null;

function initialize() {
  if (GBrowserIsCompatible()) {
    geocoder = new google.maps.Geocoder();
  }
}

function showAddress(address) {
  if (geocoder) {
    geocoder.geocode({'address':address}, function(point) {
        if (!point) {
          alert(address + " not found");
        }
        else {
          //document.getElementById('geolocation').innerHTML += '<p>Your location: ' + point[0].geometry.location + '</p>';
          var api_url = "http://ec2-54-213-36-220.us-west-2.compute.amazonaws.com/api/office/" + point[0].geometry.location.lat() + "/" + point[0].geometry.location.lng();
          var url = window.location.protocol + "//" + window.location.host + "/page2.html" + "?lat=" + point[0].geometry.location.lat() + "&lng=" + point[0].geometry.location.lng();
        }
      }
    );
  }
}
