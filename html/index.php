<?php require($_SERVER['DOCUMENT_ROOT'] . "/inc/header.html"); ?>

	<!-- Custom Google Web Font -->
    <link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic' rel='stylesheet' type='text/css'>
	
	<script src="js/jquery.tickertype.js" type="text/javascript"></script>
    <script src="js/geoLocate.js" type="text/javascript"></script>
	
	<title>Ballot Path - Your Path to Political Office</title>
</head>

<body onload="initialize()">

    <?php require($_SERVER['DOCUMENT_ROOT'] . "/inc/navBar.html"); ?>

    <div class="intro-header">
        <div class="container">

            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4">
				<br>
						<a href="donate.php" class="btn btn-danger btn-lg">Donate to Ballot Path</a>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4"></div>
                <div class=" col-lg-4 col-md-4 col-sm-4">
                    <h4>I want to run for:
                         <div id="ticker-area" class="displays">
                          <ul>
                            <li>President</li>
                            <li>Local School Board Member</li>
                            <li>Mayor</li>
                            <li>County Commissioner</li>
                            <li>Sheriff</li>
                            <li>City Council</li>
                            <li>US Senate</li>
                            <li>State Representative</li>
                            <li>US Congress</li>
                            <li>Secretary of State</li>
                            <li>State Attorney General</li>
                            <li>County Auditor</li>
                            <li>Governor</li>
                          </ul>
                        </div>
                    </h4>
                </div>
				</div>

                <div class="intro-message panel panel-default col-lg-8 col-lg-offset-2">
                    <div class="span12">
                        <div id="bg">
                            <div class="form-group" style="margin:auto;">
                                <form id="addressform" class="form-inline" role="form" action="#" onsubmit="showAddress(this.address.value); return false">

                                    <input type="text" class="form-control input-lg" style="width:100%; height:70px; font-size:30px;" name="address" id="exampleInputEmail2" placeholder="Enter your address...">
                                    <p></p>
                                    <button id="go" type="submit" class="btn btn-lg btn-primary">Find Out!</button>
                                        <h3>Who your representatives are</h3>
                                        <h3>What they're supposed to be doing</h3>
                                        <h3>How you can replace them</h3>
                                </form>
                            </div>
                        </div>
                        <h3>A Real World Path to Politics</h3>
                        <hr class="intro-divider">
                        <div id="geolocation"></div>
                    </div>

                        <ul class="list-inline intro-social-buttons">
                            <li><a href="#" class="btn btn-default btn-lg"><i class="fa fa-twitter fa-fw"></i> <span class="network-name">Twitter</span></a>
                            </li>
                            <li><a href="#" class="btn btn-default btn-lg"><i class="fa fa-github fa-fw"></i> <span class="network-name">Github</span></a>
                            </li>
                            <li><a href="#" class="btn btn-default btn-lg"><i class="fa fa-linkedin fa-fw"></i> <span class="network-name">Linkedin</span></a>
                            </li>
                        </ul>

                        <hr class="intro-divider">
                        <div id="map_canvas"></div>
                        <div id="google_tos">Users of Google Maps must comply with the <a id="google_ref" href="http://code.google.com/apis/maps/terms.html" target="_blank">Google Maps Terms and Conditions</a><p></p>
			<form id="coordform" class="form-inline" role="form" action="#" onsubmit="submitCoord(this.markerlat.value, this.markerlong.value); return false">
			<input type="hidden" id="markerlat" size="40">
			<input type="hidden" id="markerlong" size="40"><br>
			<button id="go2" type="submit" class="btn btn-lg btn-primary">Search By Map Position!</button>
			</form></div>
                    </div>
                
            </div>
        </div>
        <!-- /.container -->
    </div> 

</body>

</html>
