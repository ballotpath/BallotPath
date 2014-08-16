<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create a Split - District Relation</title>
    <link href="css/custom.css" rel="stylesheet"> 

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">

<!-- Remain consistent with home page theme -->    
<link href="css/landing-page.css" rel="stylesheet">
    
	<script src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script src="js/officeCard.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
</head>

<body class="bgprimary">
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/">Ballot Path</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse navbar-right navbar-ex1-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="#about">About</a>
                    </li>
                    <li><a href="#services">Partners</a>
                    </li>
                    <li><a href="#contact">Contact Us</a>
                    </li>
                    <li><a href="#contact">Help</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>
	


<div class="intro-header">
	<div class="container-fluid col-md-10 col-md-offset-1">
		<div class="panel panel-primary ">
			<div class="panel-heading">
				<h4 class="panel-title">
					Shapefile Upload Database Insertion
				</h4>
			</div>
			<div class="inpCenter panel-body">

<?php

function uploadProcess($extension) {
  if($_FILES["$extension"]["error"] > 0) {
    echo "Error uploading .$extension: " . printUploadError($_FILES["$extension"]["error"]) . "<br>";
  return 0;
  } else {
    if(substr($_FILES["$extension"]["name"], -3) == "$extension") {
      echo "File ". $_FILES["$extension"]["name"] . " upload successful!<br>";
      move_uploaded_file($_FILES["$extension"]["tmp_name"], "/tmp/" . $_FILES["$extension"]["name"]);
      return 1;
    } else {
      echo "Incorrect file type uploaded for .$extension file, please return to the previous page and submit the correct file type.<br>";
      return 0;
    }
  }
}

function printUploadError($errcode) {
  $output;
  switch($errcode) {
    case 0:
      $output = "There is no error, the file uploaded with success";
      break;
    case 1:
      $output = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
      break;
    case 2:
      $output = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
      break;
    case 3:
      $output = "The uploaded file was only partially uploaded";
      break;
    case 4:
      $output = "No file was uploaded";
      break;
    case 6:
      $output = "Missing a temporary folder";
      break;
    case 7:
      $output = "Failed to write file to disk";
      break;
    case 8:
      $output = "A PHP extensioned the file upload";
      break;
  }
  return $output;
}

function cleanup() {
  $cleartmp = shell_exec('rm /tmp/' . $_FILES["shp"]["name"] 
. ' /tmp/' . $_FILES["shx"]["name"] 
. ' /tmp/' . $_FILES["dbf"]["name"] 
. ' /tmp/' . $_FILES["prj"]["name"] 
. ' /tmp/' . substr($_FILES["shp"]["name"], 0, -4) . '.sql'
. ' /tmp/insert' . substr($_FILES["shp"]["name"], 0, -4) . '.sql' 
. ' /tmp/insert2' . substr($_FILES["shp"]["name"], 0, -4) . '.sql');
  echo $cleartmp;
}

//array for storing upload status
$upload = array(
  "shp" => 0,
  "shx" => 0,
  "dbf" => 0,
  "prj" => 0,
);

//call function to process each upload
$upload["shp"] = uploadProcess("shp");
$upload["shx"] = uploadProcess("shx");
$upload["dbf"] = uploadProcess("dbf");
$upload["prj"] = uploadProcess("prj");

//perform action and remove temp files in directory
if(($upload["shp"] == 0) || ($upload["shx"] == 0) || ($upload["dbf"] == 0) || ($upload["prj"] == 0)) {

  //display error message
  echo "Error during shapefile upload!  Shapefile database insertion halted.";
  cleanup();
} else {
  //check for filename mismatch
  if ((substr($_FILES["shp"]["name"], 0, -4) != substr($_FILES["shx"]["name"], 0, -4)) || (substr($_FILES["shp"]["name"], 0, -4) != substr($_FILES["dbf"]["name"], 0, -4)) || (substr($_FILES["shp"]["name"], 0, -4) != substr($_FILES["prj"]["name"], 0, -4))) {
    echo "Base Filename mismatch!  Make sure all files are from the same shapefile.<br>Shapefile database insertion halted.";
  } else {

    //generate raw insertion .sql
    $sqlconv = shell_exec('shp2pgsql -s 4326 /tmp/' . $_FILES["shp"]["name"] . ' > /tmp/' . substr($_FILES["shp"]["name"], 0, -4) . '.sql');
    echo $sqlconv;

    //grep to select only insert statements
    $grepins = shell_exec('grep -e "INSERT INTO" /tmp/' . substr($_FILES["shp"]["name"], 0, -4) . '.sql > /tmp/insert' . substr($_FILES["shp"]["name"], 0, -4) . '.sql');
    echo $grepins;

    //sed to replace table name with correct value (\x22 = ")
    $tablesed = shell_exec('sed -i -e "s/INSERT INTO \x22[^\x22]*\x22/INSERT INTO \x22splits\x22/g" -e "s/countyname/county/g" -e "s/statename/state/g" /tmp/insert' . substr($_FILES["shp"]["name"], 0, -4) . '.sql');
    echo $tablesed;

    //perform database insertion and print status
	$dbenv = shell_exec('export PGPASSWORD=Democracy!');
    $dbload = shell_exec('psql -U ballotpath BallotPath < /tmp/insert' . substr($_FILES["shp"]["name"], 0, -4) . '.sql 2>&1');
    if (substr_count($dbload, 'ERROR') > 0) {
      //print error message
      echo "Errors were encountered!";
      $dbload = str_replace('ERROR', '<br>ERROR', $dbload);
      echo $dbload;
    } else {
      //count number of successful insertions and display message
      echo "<br>" . substr_count($dbload, 'INSERT 0 1') . " records inserted successfully!.";
    }
	$dbenvrem = shell_exec('export PGPASSWORD=');
    cleanup();
  }
}

?>

			</div>
		</div>
	</div>
</div>
	
	
</body>
</html>
