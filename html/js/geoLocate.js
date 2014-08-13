
var map = null;
var geocoder = null;

google.maps.event.addDomListener(window, 'load', initialize);

function initialize() {
    var map_canvas = document.getElementById('map_canvas');
    var map_options = {
        center: new google.maps.LatLng(44.9308, -123.0289),
        zoom: 8,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
	scrollwheel: false
    }
    map = new google.maps.Map(map_canvas, map_options);
    geocoder = new google.maps.Geocoder();

    var marker = new google.maps.Marker({
        position: map_options.center, 
        map: map, 
        title: 'Coordinates: ' + map_options.center.toString(), 
        draggable: true
    });
        
    google.maps.event.addListener(marker, 'mouseup', function()  {
        marker.setTitle('Coordinates: ' + marker.getPosition().toString());
	document.getElementById('markerlat').value = marker.getPosition().lat();
	document.getElementById('markerlong').value = marker.getPosition().lng();
    });
}

function showAddress(address) {
  if (geocoder) {
    geocoder.geocode({'address':address}, function(point) {
        if (!point) {
          alert(address + " not found");
        }
        else {
	  submitCoord(point[0].geometry.location.lat(), point[0].geometry.location.lng());
        }
      }
    );
  }
}
function submitCoord(latitude, longitude) {
	var url = window.location.protocol + "//" + window.location.host + "/positions.php" + "?lat=" + latitude + "&lng=" + longitude;
	window.open(url, "_self");
}
