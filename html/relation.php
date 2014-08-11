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
			<div class="inpCenter panel-body">


				<form name="buildrel" method="post" action="submitrel.php">
					<label for="split">Split:</label><br>
					<select name="split">
						<option value="">Select A Split</option>
<?php

$dbconn = pg_connect("host=ec2-54-213-36-220.us-west-2.compute.amazonaws.com port=5432 dbname=BallotPath user=BallotPath password=Democracy!")
	or die ("</select><p>Could not connect to server\n");
$query = "SELECT gid, state, county, precinct, split FROM splits ORDER BY state, county, precinct, split;";
$rs = pg_query($dbconn, $query) or die("Cannot execute query: $query\n");
while ($row = pg_fetch_row($rs)) {
  echo "						<option value=" . $row[0] . "> " . $row[1] . "-" . $row[2] . "-" . $row[3] . "-" . $row[4] . "</option>\n";
}
pg_close($dbconn);
?>
					</select><br>
					<label for="district">District:</label><br>
					<select name="district">
						<option value="">Select A District</option>
<?php
$dbconn = pg_connect("host=ec2-54-213-36-220.us-west-2.compute.amazonaws.com port=5432 dbname=BallotPath user=BallotPath password=Democracy!")
	or die ("</select><p>Could not connect to server\n");
$query2 = "SELECT district.id, district.name, election_div.name FROM district INNER JOIN election_div ON district.election_div_id = election_div.id ORDER BY district.name, election_div.name;";
$rs2 = pg_query($dbconn, $query2) or die("Cannot execute query: $query2\n");
while ($row2 = pg_fetch_row($rs2)) {
  echo "						<option value=" . $row2[0] . " > " . $row2[1] . " - " . $row2[2] . "</option>\n";
}
pg_close($dbconn);

?>
					</select>
					<p><input type="submit" name="submit" value="Create Relation"/>
				</form>
			</div>
		</div>
	</div>
</div>
	
	
</body>
</html>
