<?php require($DOCUMENT_ROOT . "/inc/header.html"); ?>

    <script src="js/jsonp.js" type="text/javascript"></script>
    <script src="js/purl.js"></script>
    <script src="js/positions.js"></script>
	<title>Ballot Path - Positions</title>
</head>

<body onload="initialize()" class="bgprimary">

<?php require($DOCUMENT_ROOT . "/inc/navBar.html"); ?>

<!-- HIDDEN / POP-UP DIV -->
<div id="pop-up">
  <h3>Additional Info</h3>
  <div id="popuptext"></div>
</div>

<div class="intro-header">
	<div class="col-md-10 col-md-offset-1">
	<h1 class="alert alert-info">
		Your Elected Representatives
		<form class="form-inline" role="form">
				  <label>
				    <input id="federal-check-box" type="checkbox" value="Federal" checked>
				    Federal
				  </label>
				  <label>
				    <input id="state-check-box" type="checkbox" value="State" checked>
				    State
				  </label>
				  <label>
				    <input id="county-check-box" type="checkbox" value="County" checked>
				    County
				  </label>
				  <label>
				    <input id="city-check-box" type="checkbox" value="City" checked>
				    City
				  </label>
				  <label>
				    <input id="local-check-box" type="checkbox" value="Local" checked>
				    Local
				  </label>
		</form> 
		</h1>
	</div>		


<div class="container-fluid" style="padding-left: 50px; padding-right 50px;">
    <div class="row" id="cards">

  	</div> <!-- end row -->
</div> <!-- End Container -->
<div id="map_canvas"></div>
<div class="col-md-6 col-md-offset-3 well well-sm" id="google_tos">Users of Google Maps must comply with the <a id="google_ref" href="http://code.google.com/apis/maps/terms.html" target="_blank">Google Maps Terms and Conditions</a></div>

</div> <!-- End Intro Header -->
<div>
</div>
</body>
</html>

