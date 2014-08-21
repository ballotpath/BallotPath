<?php require($_SERVER['DOCUMENT_ROOT'] . "/inc/header.html");

$officeid=$_GET['id'];
$dbconn = pg_connect("host=localhost port=5432 dbname=ShawnTests user=BallotPath password=Democracy!")
	or die ("Could not connect to server\n");

$qrygeom = "SELECT ST_asKML(geom) FROM splits 
INNER JOIN split_district_rel ON splits.gid = split_district_rel.splits_gid 
INNER JOIN office_position ON split_district_rel.district_id = office_position.district_id 
WHERE office_position.office_id = $officeid;";
$rs = pg_query($dbconn, $qrygeom);
if ($rs == FALSE) {
  echo pg_last_error($dbconn);
} else {
  $kmlStr="";
  while ($row = pg_fetch_row($rs)) {
    $kmlStr .= $row[0];
  }
  $kmlStr = str_replace("</MultiGeometry><MultiGeometry>", "\n", $kmlStr);
  $filename = substr(tempnam("kml", "kml"), -13) . ".kml";
  file_put_contents($filename,'<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
  <Document><Placemark>
  <name>District</name>' . $kmlStr . '</Placemark>  </Document>
</kml>');
  chmod($filename, 0766);
}

pg_close($dbconn);

?>
<head>
    <script type="text/javascript">
    var kml = "<?php echo 'http://ec2-54-213-36-220.us-west-2.compute.amazonaws.com/' . $filename ?>";
    </script>
    <script src="js/jsonp.js" type="text/javascript"></script>
    <script src="js/purl.js"></script>
    <script src="js/office.js"></script>
	  <title>Ballot Path - Office</title>
</head>

<body onload="initialize()" class="bgprimary">

    <?php require($_SERVER['DOCUMENT_ROOT'] . "/inc/navBar.html"); ?>
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

	<span class="col-md-6 col-md-offset-3 well well-sm" id="warningmessage">Disclaimer: Geographic data is an approximation. Ballot Path does not guarantee the accuracy of search results.</span>
<div id="map_canvas"></div>
<br>
<div class="col-md-6 col-md-offset-3 well well-sm" id="google_tos">Users of Google Maps must comply with the <a id="google_ref" href="http://code.google.com/apis/maps/terms.html" target="_blank">Google Maps Terms and Conditions</a></div>
<br>
</div>
</body>
</html>
