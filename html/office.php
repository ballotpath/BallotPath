<?php require($DOCUMENT_ROOT . "inc/header.html"); ?>

    <script src="js/jsonp.js" type="text/javascript"></script>
    <script src="js/purl.js"></script>
    <script src="js/office.js"></script>
	<title>Ballot Path - Office</title>
</head>

<body onload="initialize()" class="bgprimary">

    <?php require($DOCUMENT_ROOT . "inc/navBar.html"); ?>

<div class="intro-header">
	<div class="container-fluid">
	
	<div class="col-md-10 col-md-offset-1">
				<h1 class="alert alert-dangerCus">What it takes to become <span id="position-title"> </span></h1>
	</div>
	
	<div class="panel-group col-md-10 col-md-offset-1" id="accordion">
	<div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
          Requirements and Salary
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse in">
		<div class="panel-body">
			<dl id="reqSal">
					  
			</dl>
		</div>
    </div>
  </div>
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
          Time Frames
        </a>
      </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse">
      <div class="panel-body">
		<dl id="timeFra">
							
		</dl>
      </div>
    </div>
  </div>
  <div class="panel panel-dangerCus">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
          Basic Duties
        </a>
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse">
      <div class="panel-body">
			<dl id="basDut">
			
			</dl>
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
          Filing Documents
        </a>
      </h4>
    </div>
    <div id="collapseFour" class="panel-collapse collapse">
      <div class="panel-body">
			<dl id="filDoc">

			</dl>
      </div>
    </div>
  </div>
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseFive">
          Where and How to File
        </a>
      </h4>
    </div>
    <div id="collapseFive" class="panel-collapse collapse">
      <div class="panel-body">
		<dl id="wheHow">
		
		</dl>
      </div>
    </div>
  </div>
</div>
	</div>
<div id="map_canvas"></div>
<div class="col-md-6 col-md-offset-3 well well-sm" id="google_tos">Users of Google Maps must comply with the <a id="google_ref" href="http://code.google.com/apis/maps/terms.html" target="_blank">Google Maps Terms and Conditions</a></div>
</div>
</body>
</html>
