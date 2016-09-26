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
            <h1>Database Administration:</h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-edit"></i> External Links</a></li>
            <li class="active">Here</li>
          </ol>

        </section>

        <!-- Main content -->
        <section class="content"><?php include 'includes/run_check.php';?>


                <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Database Administration:</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <h4>Database Management</h4>

                            <br>

                            <br>

                            Runs from the associated minION computer are automatically added to administrators accounts. When a run is uploaded to the database it can also be associated with specific users. It can be useful to change these assignments later and this page allows this process.<br><br>

                            <?php if(isset ($_GET['roi'])) {?>
                                <div id="messages"></div>
                                 <div class="row">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Update users for <?php echo $_GET['roi'];?></h3>
                                 </div>
                                  <div class="panel-body">

                                 <p>Highlight users for access to this database.<br>
                                 <strong>Note currently highlighted users already have access - deselecting them will remove access.</strong></p>

                            <?php
                                getusers();

                            ?>

                                <div>
                            <div class="col-sm-offset-2 col-sm-10"><br><br>
                            <!--<button type="submit" class="btn btn-default">Update Users</button>-->
                            <button id = 'optobutton' class='btn btn-warning' data-toggle='modal' data-target='#resetmodal'>
                              <i class='fa fa-exclamation-triangle'></i> Update Users
                            </button>
                            </div>
                            </div>
                            </div>





                                  </div>



                            <!-- Modal -->
                            <div class='modal fade' id='resetmodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                              <div class='modal-dialog'>
                                <div class='modal-content'>
                                  <div class='modal-header'>
                                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                                    <h4 class='modal-title' id='myModalLabel'>Update Users</h4>
                                  </div>
                                  <div class='modal-body'>
                                    <p>This action will set the users for <?php echo $_GET['roi']; ?>.</p>
                                    <p>The following users will be assigned: <div id ="names"></div></p>
                                  </div>
                                  <div class='modal-footer'>
                                    <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                                    <button id='resetopt' type='button' class='btn btn-warning'>Update</button>
                                  </div>
                                </div><!-- /.modal-content -->
                              </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->
                            </div>

                                            </div>


                                 <?php
                            }else { ?>

                            <div id = "db_assign">
                            To change user assignments for a run first select the run:<br>

                            <div class="row">
                                 <div class="col-lg-12">

                                     <table id="example" class="display table table-condensed table-hover " cellspacing="0" width="100%">
                                         <thead>
                                             <tr>
                                             <th>Run Index</th>
                                                <th>date</th>
                                                 <th>Upload User Name</th>
                                                 <th>Flow Cell ID</th>
                                                 <th>Run Name</th>
                                                 <th>Active</th>
                                                 <th>Comment</th>
                                                 <th>Flow Cell Owner</th>
                                                 <th>Run Number</th>
                                                 <th>Reference</th>
                                                 <th>Ref Length</th>
                                                 <th>Base Caller Algorithm</th>
                                                 <th>Version</th>
                                             </tr>
                                         </thead>

                                         <tfoot>
                                             <tr>
                                                 <th><small>Run Index</small></th>
                                                 <th><small>date</small></th>
                                                 <th><small>Upload User Name</small></th>
                                                 <th><small>Flow Cell ID</small></th>
                                                 <th><small>Run Name</small></th>
                                                 <th><small>Active</small></th>
                                                 <th><small>Comment</small></th>
                                                 <th><small>Flow Cell Owner</small></th>
                                                 <th><small>Run Number</small></th>
                                                 <th><small>Reference</small></th>
                                                 <th><small>Ref Length</small></th>
                                                 <th><small>Base Caller Algorithm</small></th>
                                                 <th><small>Version</small></th>
                                             </tr>
                                         </tfoot>
                                     </table>
                                 </div>
                             </div>

                            </div>
                            </div>
                            </div>



                             <?php } ?>
                                 </div>


        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

      <?php include 'includes/reporting-new.php'; ?>
      <script src="js/plugins/dataTables/jquery.dataTables.js" type="text/javascript" charset="utf-8"></script>
      <script src="js/plugins/dataTables/dataTables.bootstrap.js" type="text/javascript" charset="utf-8"></script>


      <script>
      $(document).ready(function() {
          $('#example').dataTable( {
              "processing": true,
              "serverSide": true,
              "sAjaxSource": "data_tables/data_table_admin.php"
          } );
          $('#example tbody').on('click', 'tr', function () {
                  var name = $('td', this).eq(0).text();
                  //alert( name );
                  //$.post( "views/read_details.php?prev=1", { readname: name })
                   // .done(function( data ) {
                     // $("#read_details").html(data);
                  //	$('html, body').animate({
                  //	           'scrollTop':   $('#'+name).offset().top
                  //	}, 1000);

                  //});
              } );

              $(document).on('click', '#example tr', function(){
                      var tableData = $(this).children("td").map(function() {
                           return $(this).text();
                      }).get();
                      var url = "admin_manage.php?roi=" + $.trim(tableData[4]);
                      //alert (url);
                      window.location.href = url;
                  });

      } );

          </script>


          <script>
              $( "#checkboxes" )
               .change(function() {
                  var str = "";
                  $( "#checkboxes input:checked" ).each(function() {
                    str += $( this ).attr('name') + "<br>";
                  });
                  $( "#names" ).html( str );
                  //alert(str);
})
.trigger( "change" );
</script>
<script>
      $(function(){
          $('#resetopt').on('click', function(e){
              e.preventDefault(); // preventing default click action
               var str = "";
                  $( "#checkboxes input:checked" ).each(function() {
                    str += $( this ).attr('name') + "_";
                  });
              var monkey = 'jsonencode/dballoc.php?db=<?php echo $_GET['roi'];?>&names='+str;
              //alert (monkey);
              $.ajax({
                  url: monkey,
                 // alert ('url'),
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
