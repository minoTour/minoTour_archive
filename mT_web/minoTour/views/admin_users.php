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
            <h1>Enable Admin Users:</h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-edit"></i> Enable Admin Users</a></li>
            <li class="active">Here</li>
          </ol>

        </section>

        <!-- Main content -->
        <section class="content"><?php include 'includes/run_check.php';?>


                <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Enable Admin Users:</h3>
                  <div id="messages"></div>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">

                			<body>Here you can assign or revoke admin user status. Remember that admin users can create and delete other user profiles. You must have one admin user account.<br><br>
                                <div class = "col-md-6">
                                    <table class="table table-bordered table-striped table-hover">
                                    <tr>
                                      <th>User Name</th>
                                      <th>Admin?</th>
                                      <th>Change Status</th>
                                    </tr>
                                <?php
                                foreach (checkadmin() as $user=>$status){
                                    if ($_SESSION['user_name'] != $user){
                                        echo "<tr>";
                                        echo "<td>$user</td>";
                                        if ($status == 1) {
                                                echo "<td>Yes</td><td><button type='button' id='revoke-admin-".$user."' data-loading-text='Modifying Access...' data-complete-text='Admin Revoked' class='btn btn-alert btn-xs'>Remove Administrator Rights</button></td>";
                                        }else {
                                                echo "<td>No</td><td><button type='button' id='grant-admin-".$user."' data-loading-text='Modifying Access...' data-complete-text='Admin Granted' class='btn btn-warning btn-xs'>Grant Administrator Rights</button></td>";
                                        }

                                        echo "</tr>";
                                    }
                                }

                                ?>
                            </table>
                                 </div>

              </div>


        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

      <?php include 'includes/reporting-new.php'; ?>
      <script src="js/plugins/dataTables/jquery.dataTables.js" type="text/javascript" charset="utf-8"></script>
      <script src="js/plugins/dataTables/dataTables.bootstrap.js" type="text/javascript" charset="utf-8"></script>


      <script>

  	<?php foreach (getallusers() as $user) {?>

        $("#revoke-admin-<?php echo $user;?>").on('click', function(e){
                            e.preventDefault(); // preventing default click action
                            $.ajax({
                                url: 'jsonencode/revokeadmin.php?user=<?php echo $user;?>',
                                success: function(data){
                                    //alert ('success');
                                    //$('#resetmodal').modal('hide')
                                    //alert(data);
                                    $("#messages").html(data);
                                    var delay = 3000; //Your delay in milliseconds
                                    setTimeout(function(){ location.reload(); }, delay);
                                }, error: function(){
                                    alert('ajax failed');
                                },
                            })
                            })

  	<?php } ?>

  	</script>
    <script>

    <?php foreach (getallusers() as $user) {?>

      $("#grant-admin-<?php echo $user;?>").on('click', function(e){
                          e.preventDefault(); // preventing default click action
                          $.ajax({
                              url: 'jsonencode/grantadmin.php?user=<?php echo $user;?>',
                              success: function(data){
                                  //alert ('success');
                                  //$('#resetmodal').modal('hide')
                                  //alert(data);
                                  $("#messages").html(data);
                                  var delay = 3000; //Your delay in milliseconds
                                  setTimeout(function(){ location.reload(); }, delay);
                              }, error: function(){
                                  alert('ajax failed');
                              },
                          })
                      })


    <?php } ?>

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
