<?php include("secr/login.php"); ?>
<?php require($DOCUMENT_ROOT . "inc/header.html"); ?>

<title>Developer Access</title>
</head>

<body>
    
	<?php require($DOCUMENT_ROOT . "inc/navBar.html"); ?>

<div class="intro-header">
	<div class="container">
		<div class="panel panel-default ">
			<div class="panel-heading">
				<h4 class="panel-title">
					Developer Access
				</h4>
			</div>
			<div class="inpCenter panel-body">
				<a href="secr/manager.php">User Manager (Administrator)</a><br><br>
				<a href="../api/bulkupload">Bulk Upload Page</a><br><br>
				<a href="bpadmin/">Developer Database</a><br><br>
				<a href="shapefilesubmit.php">Shape File Upload</a><br><br>
				<a href="relation.php">Relation File Upload</a><br><br>
				<a href="districtnamefix.php">Bulk District Name Change Upload</a><br><br>
				<a href="devAcc.php?logout=1">Logout</a>
				<br>
			</div>
		</div>
	</div>
</div>

	
</body>
</html>

