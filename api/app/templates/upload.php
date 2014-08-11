<?php include("/secr/login.php"); ?>
<?php require($DOCUMENT_ROOT . "/inc/header.html"); ?>
	
	<title>Ballot Path - Bulk Upload</title>
</head>

<body>

    <?php require($DOCUMENT_ROOT . "/inc/navBar.html"); ?>

    <div class="intro-header">
        <div class="container">
		<div	class="panel panel-default" >
			<div class="panel-heading">
				<h3 class="panel-title"> Donate</h3>
			</div>
			<div  class="panel-body" >
			
      <div class="header">
        <h3 class="text-muted">Upload a File</h3>
      </div>
      <hr/>
      <div>
      
      <form action="upload" method="post" enctype="multipart/form-data">
        <input type="file" name="file"><br /><br />
        <input type="submit" value="upload">
      </form>
      </div>
	  
	  </div>
	  </div>
    </div>
	</div>
  </body>
</html>

