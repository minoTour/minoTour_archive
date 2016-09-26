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
            <h1>Manage Data: <small> - run: <?php echo cleanname($_SESSION['focusrun']);; ?></small></h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-edit"></i> External Links</a></li>
            <li class="active">Here</li>
          </ol>

        </section>

        <!-- Main content -->
        <section class="content"><?php include 'includes/run_check.php';?>
            

                <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Manage Your Run Data</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-12">
                        				<h4>Database Management</h4>

                        				<br>
                        			 	<div id="messages"></div>
                        				<br>
                        				For speed purposes, the data analyses for each run are stored to a database table after the run is complete. This maximises speed for the website. These stored files are generated on the first viewing of a comlpleted run. These tables can be reset here.<br>
                        				It is also possible to delete all non essential data from the database to save space. Essentially this archives a run such that it can no longer be reprocessed without re-uploading the data to the website again. You should only archive data from the database if you understand what you are doing.<br>

                        				<br><br>
                        				<?php
                        				$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['focusrun']);
                        				if (!$mindb_connection->connect_errno) {
                        					$sqlcheck = "select * from jsonstore where name = 'do_not_delete' and json = '1';";
                        					$sqlchecksecurity = $mindb_connection->query($sqlcheck);
                        					if ($sqlchecksecurity->num_rows == 1){
                        						echo "<!-- Button trigger modal -->
                        				<button class='btn btn-warning disabled' data-toggle='modal' data-target='#resetmodal'>
                        				  <i class='fa fa-exclamation-triangle'></i> Reset Database Optimisations
                        				</button> This database has been archived, so cannot be reset. <br><br>
                        				<button id='archivebutton' class='btn btn-danger disabled' data-toggle='modal' data-target='#deletemodal'>
                        				  <i class='fa fa-exclamation-triangle'></i> Archive Database
                        				</button> This database has already been archived.
                        				";
                        					}else{
                        						//Check if entry already exists in jsonstore table:
                        						echo "<!-- Button trigger modal -->
                        				<button id = 'optobutton' class='btn btn-warning' data-toggle='modal' data-target='#resetmodal'>
                        				  <i class='fa fa-exclamation-triangle'></i> Reset Database Optimisations
                        				</button>

                        				<!-- Modal -->
                        				<div class='modal fade' id='resetmodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                        				  <div class='modal-dialog'>
                        				    <div class='modal-content'>
                        				      <div class='modal-header'>
                        				        <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                        				        <h4 class='modal-title' id='myModalLabel'>Reset Database Optimisations</h4>
                        				      </div>
                        				      <div class='modal-body'>
                        				        <p>This action will reset the optimised values for this run in the database. This data can be regenerated on the fly as required.</p>
                        						<p>If you are sure you wish to do this, click reset below. Otherwise close this window.</p>
                        				      </div>
                        				      <div class='modal-footer'>
                        				        <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                        				        <button id='resetopt' type='button' class='btn btn-warning'>Reset</button>
                        				      </div>
                        				    </div><!-- /.modal-content -->
                        				  </div><!-- /.modal-dialog -->
                        				</div><!-- /.modal -->
                        				<br><br>";
                                        if ($_SESSION['user_name'] != "demo") {
                                            echo "
                        				<!-- Indicates a dangerous or potentially negative action -->
                        				<!-- Button trigger modal -->
                        								<button id='archivebutton' class='btn btn-danger' data-toggle='modal' data-target='#deletemodal'>
                        								  <i class='fa fa-exclamation-triangle'></i> Archive Database
                        								</button>

                        								<!-- Modal -->
                        								<div class='modal fade' id='deletemodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                        								  <div class='modal-dialog'>
                        								    <div class='modal-content'>
                        								      <div class='modal-header'>
                        								        <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                        								        <h4 class='modal-title' id='myModalLabel'>Archive Database</h4>
                        								      </div>
                        								      <div class='modal-body'>
                        								       <div id='archiveinfo'>
                        								        <p>This action will archive this database. It will remove all data used to calculate rates of sequencing and coverage data. After carrying out this process you will only be able to view previously optimised data on the website. You will currently still be able to download sequences.</p>
                        										<p><strong>The only way to undo this operation is to reupload data to the database.</strong></p>
                        										<p>If you are sure you wish to do this, click archive below. Otherwise close this window.</p>
                        								      </div>
                        								      <div id='archiveworking'>
                        								      <p class='text-center'>We're working to archive your database. Please be patient and don't navigate away from this page.</p>
                        								      <p class='text-center'><img src='images/loader.gif' alt='loader'></p>
                        								      </div>
                        								      <div class='modal-footer'>
                        								        <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                        								        <button id='archiveopt' type='button' class='btn btn-warning'>Archive</button>
                        								      </div>
                        								    </div><!-- /.modal-content -->
                        								  </div><!-- /.modal-dialog -->
                        								</div><!-- /.modal -->
                                                        </div>
                                                        <br><br>
                                                        <!-- Indicates a dangerous or potentially negative action -->
                                                        <!-- Button trigger modal -->
                                                        <button id='deletebutton' class='btn btn-danger' data-toggle='modal' data-target='#realdeletemodal'>
                                                          <i class='fa fa-exclamation-triangle'></i> Delete Database
                                                        </button>
                                                        <!-- Modal -->
                        								<div class='modal fade' id='realdeletemodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                        								  <div class='modal-dialog'>
                        								    <div class='modal-content'>
                        								      <div class='modal-header'>
                        								        <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                        								        <h4 class='modal-title' id='myModalLabel'>Delete Database</h4>
                        								      </div>
                        								      <div class='modal-body'>
                        								       <div id='deleteinfo'>
                        								        <p>This action will completely delete this database. You don't need to do this as you can reupload data using the -d option. However this facility is provided at users request.</p>
                        										<p><strong>The only way to undo this operation is to reupload data to the database.</strong></p>
                        										<p>If you are sure you wish to do this, click delete below. Otherwise close this window.</p>
                        								      </div>
                        								      <div id='deleteworking'>
                        								      <p class='text-center'>We're working to delete your database. Please be patient and don't navigate away from this page.</p>
                        								      <p class='text-center'><img src='images/loader.gif' alt='loader'></p>
                        								      </div>
                        								      <div class='modal-footer'>
                        								        <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                        								        <button id='deleteopt' type='button' class='btn btn-warning'>Delete</button>
                        								      </div>
                        								    </div><!-- /.modal-content -->
                        								  </div><!-- /.modal-dialog -->
                        								</div><!-- /.modal -->
                                                        </div>


                        				";
                        					}
                                        }
                        				}

                        				?>






                                         </div>

                                 </div>


        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

      <?php include 'includes/reporting-new.php'; ?>
      <script src="js/plugins/dataTables/jquery.dataTables.js" type="text/javascript" charset="utf-8"></script>
      <script src="js/plugins/dataTables/dataTables.bootstrap.js" type="text/javascript" charset="utf-8"></script>
      <script>

          $(function(){
              $('#resetopt').on('click', function(e){
                                  e.preventDefault(); // preventing default click action
                  $.ajax({
                      url: 'jsonencode/clearjson.php?prev=1',
                      success: function(data){
                          //alert ('success');
                          $('#resetmodal').modal('hide')
                          //alert(data);
                          $("#messages").html(data);
                      }, error: function(){
                          alert('ajax failed');
                      },
                  })
                  //alert ("button clicked");
              })
          })
          $(function(){
              $('#archiveworking').hide();
              $('#archiveopt').on('click', function(e){
                  $('#archiveinfo').hide();
                  $('#archiveworking').show();
                  $('#archiveopt').addClass('disabled');
                  e.preventDefault(); // preventing default click action
                  $.ajax({
                      url: 'jsonencode/archive.php?prev=1',
                      success: function(data){
                          //alert ('success');
                          $('#deletemodal').modal('hide')
                          //alert(data);
                          $("#messages").html(data);
                          $('#optobutton').addClass('disabled');
                          $('#archivebutton').addClass('disabled');
                      }, error: function(){
                          alert('ajax failed');
                      },
                  })
                  //alert ("button clicked");
              })
          })
          $(function(){
              $('#deleteworking').hide();
              $('#deleteopt').on('click', function(e){
                  $('#deleteinfo').hide();
                  $('#deleteworking').show();
                  $('#deleteopt').addClass('disabled');
                  e.preventDefault(); // preventing default click action
                  $.ajax({
                      url: 'jsonencode/delete.php?prev=1',
                      success: function(data){
                          //alert ('success');
                          $('#realdeletemodal').modal('hide')
                          //alert(data);
                          $("#messages").html(data);
                          $('#optobutton').addClass('disabled');
                          $('#deletebutton').addClass('disabled');
                          var delay = 3000; //Your delay in milliseconds
                          URL="index.php";
                          setTimeout(function(){ window.location = URL; }, delay);
                      }, error: function(){
                          alert('ajax failed');
                      },
                  })
                  //alert ("button clicked");
              })
          })

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
