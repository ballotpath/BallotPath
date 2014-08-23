<?php require($_SERVER['DOCUMENT_ROOT'] . "/inc/BPWebConfig.php"); ?>
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
					Split <-> District Relation Creation
				</h4>
			</div>
			<div class="inpAdmin panel-body">

<?php

$split=$_POST['split'];
$district=$_POST['district'];

if (($split == "") || ($district == "")) {
  echo "Invalid selection made, please select a valid Split and District.";
} else {
  $dbconn = pg_connect("host=" . $dbhost . " port=" . $dbport . " dbname=" . $dbname . " user=" . $dbuser . " password=" . $dbpassword)
	or die ("Could not connect to server\n");

  $qryinsert = "INSERT INTO split_district_rel(
            splits_gid, district_id)
	    VALUES ($split, $district);";
  $rs = pg_query($dbconn, $qryinsert);
  if ($rs == FALSE) {
    echo '<p class="hangingindent">' . pg_last_error($dbconn);
  } else {

    $qrysplit = "SELECT state, county, precinct, split FROM splits where gid = $split;";
    $rs = pg_query($dbconn, $qrysplit) or die('<p class="hangingindent">Cannot execute query: ' . $querysplit);
    $row = pg_fetch_row($rs);

    $qrydist = "SELECT district.name, election_div.name FROM district INNER JOIN election_div ON district.election_div_id = election_div.id WHERE district.id = $district;";
    $rs2 = pg_query($dbconn, $qrydist) or die('<p class="hangingindent">Cannot execute query: ' . $querydist);
    $row2 = pg_fetch_row($rs2);

    echo "Relationship " . $row[0] . " - " . $row[1] . " - " . $row[2] . " - " . $row[3] . " <-> " . $row2[0] . " - " . $row2[1] . " created!<br/>";
  }
}

pg_close($dbconn);
?>

			</div>
		</div>
	</div>
</div>
	
	
</body>
</html>
