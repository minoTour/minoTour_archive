<?php

// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("libraries/password_compatibility_library.php");
}

// include the configs / constants for the database connection
require_once("config/db.php");

// load the login class
require_once("classes/Login.php");

// load the functions
require_once("includes/functions.php");



// create a login object. when this object is created, it will do all login/logout stuff automatically
// so this single line handles the entire login process. in consequence, you can simply ...
$login = new Login();

// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == true) {
    // the user is logged in. you can do whatever you want here.
    // for demonstration purposes, we simply show the "you are logged in" view.
    //include("views/index_old.php");
	?>

<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<!--
Import the header.
-->
<?php
include 'includes/head-new.php';
?>
  <!--
  BODY TAG OPTIONS:
  =================
  Apply one or more of the following classes to get the
  desired effect
  |---------------------------------------------------------|
  | SKINS         | skin-blue                               |
  |               | skin-black                              |
  |               | skin-purple                             |
  |               | skin-yellow                             |
  |               | skin-red                                |
  |               | skin-green                              |
  |---------------------------------------------------------|
  |LAYOUT OPTIONS | fixed                                   |
  |               | layout-boxed                            |
  |               | layout-top-nav                          |
  |               | sidebar-collapse                        |
  |               | sidebar-mini                            |
  |---------------------------------------------------------|
  -->
  <body class="hold-transition skin-blue sidebar-mini fixed">
    <div class="wrapper">

        <!--Import the header-->
        <?php
        include 'navbar-header-new.php';
        ?>

        <!--Import the left hand navigation-->
        <?php
        include 'navbar-top-links-new.php';
        #include 'test.php';
        ?>


      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">
            <h1>
                XML Reports
                <small> - run: <?php echo cleanname($_SESSION['focusrun']); ?></small>
            </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-edit"></i> External Links</a></li>
            <li class="active">Here</li>
          </ol>

        </section>

        <!-- Main content -->
        <section class="content"><?php include 'includes/run_check.php';?>
            

                <div class="box">
                <div class="box-header">
                  <h3 class="box-title">XML Files Uploaded from the ENA</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <h4>ENA metaData</h4>
            				<br>


            				<?php
            				date_default_timezone_set('UTC');
            				$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['focusrun']);
            				if (!$mindb_connection->connect_errno) {
            					$masterxml = "SELECT * FROM XML;";
            					$masterxmlquery = $mindb_connection->query($masterxml);

            					foreach ($masterxmlquery as $row) {
            						//echo $row['type'];
            						$xmlresult = (string)$row['xml'];
            						$xml = new SimpleXMLElement($xmlresult);
            						//var_dump($xml);
            						//echo $xmlresult;
            						//echo $xmlresult;
            						switch ($row['type']) {
            						    case "study":
            						    	echo "<div class='panel panel-default'>";
            								echo "<div class='panel-heading'>";
            								echo "<h5>" . ucwords($row['type']) . ": " . substr($row['filename'],0,9) .  "</h5>";
            								echo "</div>";
            								echo "<div class='panel-body'>";
            								foreach ($xml->STUDY as $record) {
            									echo "Submitting Center: ";
            									echo $record['center_name'] . "<br>";
            									#echo $record->IDENTIFIERS->PRIMARY_ID;
            									echo "Study Title: <i>";
            									echo $record->DESCRIPTOR->STUDY_TITLE . "</i><br>";
            									echo "Study Type: ";
            									echo $record->DESCRIPTOR->STUDY_TYPE['existing_study_type'] . "<br>";
            									echo "Study Abstract: ";
            									echo $record->DESCRIPTOR->STUDY_ABSTRACT . "<br>";
            								}
            						        echo "</div>";
            						        echo "</div>";
            						        break;
            						    case "experiment":
            						        echo "<div class='panel panel-default'>";
            								echo "<div class='panel-heading'>";
            								echo "<h5>" . ucwords($row['type']) . ": " . substr($row['filename'],0,9) .  "</h5>";
            								echo "</div>";
            								echo "<div class='panel-body'>";
            								foreach ($xml->EXPERIMENT as $record) {
            									echo "Library Name: ";
            									echo $record->DESIGN->LIBRARY_DESCRIPTOR->LIBRARY_NAME . "<br>";
            									#echo $record->IDENTIFIERS->PRIMARY_ID;
            									echo "Library Strategy: ";
            									echo $record->DESIGN->LIBRARY_DESCRIPTOR->LIBRARY_STRATEGY . "<br>";
            									echo "Library Source: ";
            									echo $record->DESIGN->LIBRARY_DESCRIPTOR->LIBRARY_SOURCE . "<br>";
            									echo "Library Selection: ";
            									echo $record->DESIGN->LIBRARY_DESCRIPTOR->LIBRARY_SELECTION . "<br>";
            									echo "Library Construction Protocol: ";
            									echo $record->DESIGN->LIBRARY_DESCRIPTOR->LIBRARY_CONSTRUCTION_PROTOCOL . "<br>";
            									echo "<br>";
            									echo "Experiment Attributes:<br>";
            									foreach ($record->EXPERIMENT_ATTRIBUTES->EXPERIMENT_ATTRIBUTE as $attribute) {
            										echo $attribute->TAG;
            										echo ": ";
            										echo $attribute->VALUE . "<br>";
            									}
            								}
            						        echo "</div>";
            						        echo "</div>";
            						        break;
            						    case "run":
            						        echo "<div class='panel panel-default'>";
            								echo "<div class='panel-heading'>";
            								echo "<h5>" . ucwords($row['type']) . ": " . substr($row['filename'],0,9) .  "</h5>";
            								echo "</div>";
            								echo "<div class='panel-body'>";
            								foreach ($xml->RUN as $record) {
            									//var_dump($record);
            									echo "Run Attributes:<br>";
            									foreach ($record->RUN_ATTRIBUTES->RUN_ATTRIBUTE as $attribute) {
            										echo $attribute->TAG;
            										echo ": ";
            										echo $attribute->VALUE . "<br>";
            									}
            								}
            						        echo "</div>";
            						        echo "</div>";
            						        break;
            						    case "sample":
            						        echo "<div class='panel panel-default'>";
            								echo "<div class='panel-heading'>";
            								echo "<h5>" . ucwords($row['type']) . ": " . substr($row['filename'],0,9) .  "</h5>";
            								echo "</div>";
            								echo "<div class='panel-body'>";
            								foreach ($xml->SAMPLE as $record) {
            									echo "Taxon ID: ";
            									echo $record->SAMPLE_NAME->TAXON_ID . "<br>";
            									echo "Scientific Name: <i>";
            									echo $record->SAMPLE_NAME->SCIENTIFIC_NAME . "</i><br>";

            								}
            						        echo "</div>";
            						        echo "</div>";
            						        break;

            						    default:
            						        echo "I'm sorry, but minoTour doesn't recognise this XML file type!";
            						}
            					}







            					}
            				?>

            </div>
            			 	<div id="messages"></div>

                             </div>


        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

      <?php include 'includes/reporting-new.php'; ?>
      <script src="js/plugins/dataTables/jquery.dataTables.js" type="text/javascript" charset="utf-8"></script>
      <script src="js/plugins/dataTables/dataTables.bootstrap.js" type="text/javascript" charset="utf-8"></script>


      <script type="text/javascript">
  	jQuery(document).ready(function($){
  		$("#formthing").click(function(e){
  		    cname = $( "#name" ).val();
  		    cmessage = $("#message").val();
  		    cuser = $("#user").val();
  		    crun = $("#run").val();
  		    var currentdate = new Date();
  			var datetime = currentdate.getFullYear() + "/" + (currentdate.getMonth()+1)  + "/" + currentdate.getDate() + "/" +  " "
              + currentdate.getHours() + ":"
              + currentdate.getMinutes() + ":"
              + currentdate.getSeconds();
  		    ctime = datetime;

  			if( cname=="" || cmessage=="" ) {
  				$("#errAll").html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>You have left a required field blank.</div>');
  			}else {
  				var data = {
  					name: cname,
  					user: cuser,
  					time: ctime,
  					message: cmessage,
  					run: crun,
  				};

  				$.post( "includes/ajax.php", data, function( response ) {
    					//alert(response);
    					$('#name').val("");
    					$('#message').val("");
    					var test = response.toString();
    					$("#commentpost").prepend(test);
  				});
  			}
  		});



  	});
  	</script>


  </body>
</html>
<?php
} else {

	    // the user is not logged in. you can do whatever you want here.
	    // for demonstration purposes, we simply show the "you are not logged in" view.
	    include("views/not_logged_in.php");
	}

	?>
