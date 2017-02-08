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
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

<link href="css/jquery.nouislider.min.css" rel="stylesheet">
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
            Assembly Summary
            <small> - run: <?php echo cleanname($_SESSION['active_run_name']); ?></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-table"></i> Current Run</a></li>
            <li><a href="#"><i class="fa fa-puzzle-piece"></i> Assembly Summary</a></li>
            <li class="active">Here</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content"><?php include 'includes/run_check.php';?>

            <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo cleanname($_SESSION['active_run_name']);?></h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                <div class="panel-heading">
                  <h3 class="panel-title"><!-- Button trigger modal -->
    <button class="btn btn-info  btn-sm" data-toggle="modal" data-target="#modalassembly">
    <i class="fa fa-info-circle"></i> Assembly
    </button>

    <!-- Modal -->
    <div class="modal fade" id="modalassembly" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close  btn-sm" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Assembly Summary</h4>
        </div>
        <div class="modal-body">
          minoTour can be used to trigger a basic minmap/miniasm assembly pipeline. This can be set here.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default  btn-sm" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
    </div>
                </div>
                <div id="assmbly">
                <div class="panel-body">
                          <div class="row">
                              <?php $cachecheck = $memcache->get("perl_mem_cache_connection");
                              if($cachecheck === false){
                                  echo "<strong>Your minotour installation is not using background scripts which provide these functions - please consult with the user manual to set these up.<br></strong>";
                              }else{?>


                              <p>To set a basic minasm/minimap assembly running, trigger the button below. </p>

                              <p>Note that these jobs are entirely dependent on the current load on the server. </p>

                              <p>If multiple groups are running assemblies, only one assmebly runs at any time. </p>

                              <p>Assemblies run every 30 minutes or 20,000 reads.</p>

                              <p>Assemblies will stop when a run is finished or by inactivating the switch below.</p>
                              Run Assembly: <input type="checkbox"  data-toggle="toggle" id="assemblyswitch"
                              <?php
                              $vartoget = "align_".$_SESSION['active_run_name'];
                              $cachecheck = $memcache->get($vartoget);
                              if ($cachecheck=='True' ){
                                  echo " CHECKED";
                              }else {
                                  echo " ";
                              }
                              ?>>


                              <?php }
                              ?>

                      </div>

                </div>
              </div>

          </div>







    <br>
      <!-- /.col-lg-12 -->
    </div>
    </div>
    </div>
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

      <?php include 'includes/reporting-new.php'; ?>
      <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<script>
      $(document).ready(function() {

    $('#assemblyswitch').change(function() {
        if ($(this).prop('checked')) {
            //alert("You have elected to show your checkout history."); //checked
            //e.preventDefault(); // preventing default click action
            $.ajax({
                url: 'jsonencode/jobs.php?job=startassembly',
                success: function(data){
                    //alert ('success');
                    //alert(data);
                    //$("#messages").html(data);
                    //$('#optobutton').addClass('disabled');
                    //$('#archivebutton').addClass('disabled');
                }, error: function(){
                    alert('ajax failed');
                },
            })
        }
        else {
            $.ajax({
                url: 'jsonencode/jobs.php?job=stopassembly',
                success: function(data){
                    //alert ('success');
                    //alert(data);
                    //$("#messages").html(data);
                    //$('#optobutton').addClass('disabled');
                    //$('#archivebutton').addClass('disabled');
                }, error: function(){
                    alert('ajax failed');
                },
            })
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
