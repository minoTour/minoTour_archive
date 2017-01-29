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
            Switch Active Run Monitoring
            <small> - select a run for more information.</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-edit"></i> External Links</a></li>
            <li class="active">Here</li>
          </ol>

        </section>

        <!-- Main content -->
        <section class="content"><?php include 'includes/run_check.php';?>

            <div id="messages"></div>
            <div class="box">
            <div class="box-header">
              <h3 class="box-title">Run Stats</h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <?php checkuserruns(); ?>
                <br>
                <?php // getallruns();?>
            </div>
        </div>
                <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Current Run Data</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                     <table id="example" class="table table-bordered table-striped table-hover">
                         <thead>
                             <tr>
                                 <th>Date</th>
                                 <th>Flow Cell ID</th>
                                 <th>Comment</th>
                                 <th>Flow Cell Owner</th>
                                 <th>Run Name</th>
                                 <th>Run Order</th>
                                 <th>Ref Sequence</th>
                                 <th>Ref Length</th>
                             </tr>
                         </thead>

                         <tfoot>
                             <tr>
                                 <th>Date</th>
                                 <th>Flow Cell ID</th>
                                 <th>Comment</th>
                                 <th>Flow Cell Owner</th>
                                 <th>Run Name</th>
                                 <th>Run Order</th>
                                 <th>Ref Sequence</th>
                                 <th>Ref Length</th>
                             </tr>
                         </tfoot>
                     </table>
                 </div>
             </div>


        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

      <?php include 'includes/reporting-new.php'; ?>
      <!--<script src="js/plugins/dataTables/jquery.dataTables.js" type="text/javascript" charset="utf-8"></script>-->
       <script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.10.12/cr-1.3.2/datatables.min.js"></script>
      <script src="js/plugins/dataTables/dataTables.bootstrap.js" type="text/javascript" charset="utf-8"></script>
      <script>
      $(document).ready(function() {
          $('#example').dataTable( {
              //"scrollX":true,
              //"paging": true,
              //"ordering": true,
              //"processing": true,
        //"serverSide": true,
              "sAjaxSource": "data_tables/data_table_active_runs.php"
          } );
          $('#example tbody').on('click', 'tr', function () {
                  var name = $('td', this).eq(0).text();

                  $.post( "views/read_details.php?prev=0", { readname: name })
                    .done(function( data ) {
                      $("#read_details").html(data);
                      $('html, body').animate({
                                 'scrollTop':   $('#'+name).offset().top
                      }, 1000);

                  });
              } );

              $(document).on('click', '#example tr', function(){
                      var tableData = $(this).children("td").map(function() {
                           return $(this).text();
                      }).get();
                      var targetrun = $.trim(tableData[4]);
                      var cleanedtarget = targetrun.replace(/ /g, "_");
                      //alert (cleanedtarget);
                      checklen = cleanedtarget.length;

                      var url = "live_data.php?actru=" + cleanedtarget;
                      if (checklen > 0){
                          window.location.href = url;
                      }
                  });

      } );

          </script>


         <script>


             $.getJSON('http://www.nottingham.ac.uk/~plzloose/minoTourhome/message.php?callback=?', function(result) {

                       $.each(result, function(key,value){
                          //checking version info.
                          if (key == 'version'){
                              if (value == '<?php echo $_SESSION['minotourversion'];?>'){
                                  $('#newstarget').html("You are running the most recent version of minoTour - version "+value+".<br>");
                              }else if (value < '<?php echo $_SESSION['minotourversion'];?>'){
                                  $('#newstarget').html("You appear to be in the fortunate position of running a future version of the minoTour web application "+value+". If you have modified the code yourself - great. If not then there might be an issue somewhere!.<br>");
                              }else if (value > '<?php echo $_SESSION['minotourversion'];?>'){
                                  $('#newstarget').html("You are running an outdated version of the minoTour web application. The most recent version of minoTour is version "+value+".<br>"+"Instructions for upgrading will be posted below.<br>");
                              }


                          }else if (key.substring(0, 7) == 'message') {
                              $('#newstarget').append(value + "<br>");
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
