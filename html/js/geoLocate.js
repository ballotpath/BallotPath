
var map = null;
var geocoder = null;

google.maps.event.addDomListener(window, 'load', initialize);

function initialize() {
    var map_canvas = document.getElementById('map_canvas');
    var map_options = {
        center: new google.maps.LatLng(44.9308, -123.0289),
        zoom: 8,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(map_canvas, map_options);
    geocoder = new google.maps.Geocoder();
}

function showAddress(address) {
  if (geocoder) {
    geocoder.geocode({'address':address}, function(point) {
        if (!point) {
          alert(address + " not found");
        }
        else {
          var url = window.location.protocol + "//" + window.location.host + "/positions.html" + "?lat=" + point[0].geometry.location.lat() + "&lng=" + point[0].geometry.location.lng();
          window.open(url, "_self");
        }
      }
    );
  }
}
