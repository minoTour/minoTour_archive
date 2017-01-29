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
            <h1>Enable User Upload:</h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-edit"></i> Enable User Upload</a></li>
            <li class="active">Here</li>
          </ol>

        </section>

        <!-- Main content -->
        <section class="content"><?php include 'includes/run_check.php';?>


                <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Enable User Upload:</h3>
                  <div id="messages"></div>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">

                			<body>These pages allow you to provide upload access for individual users to add data to the mySQL account associated with this installation of minoTour. <strong>Any admin user can enable others to upload data so exercise caution!</strong><br>
                                It is your responsibility to communicate the password you specify to the user. This will be automated in the future.<br><br>
                                <div class = "col-md-6">
                                <p>The following users are not authorised to upload data to the database:</p>
                                <table class="table table-bordered table-striped table-hover">
      <tr>
        <th>User Name</th>
        <th>Action</th>
        <th>Password</th>
        <th>Password</th>
      </tr>
                    			<?php foreach (getallusers() as $user) {
                    				if (checkminup($user) == 0) {
                                        echo "<tr>";
                    					echo "<td><i>$user </i></td><td><button type='button' id='grant-btn-".$user."' data-loading-text='Granting Access...' data-complete-text='Access Granted' class='btn btn-warning btn-xs'>Grant Access</button></td><td><input type='password' id='password1-".$user."'></td><td><input type='password' id='password2-".$user."'></td>";
                                        echo "</tr>";
                    				}
                    			} ?>
                                </table>
                                     </div>



                                <div class = "col-md-6">
                                    <p>The following users are currently authorised to upload data to the database:</p>
                                <table class="table table-bordered table-striped table-hover">
      <tr>
        <th>User Name</th>
        <th>Action</th>
      </tr>





                			<?php foreach (getallusers() as $user) {
                				if (checkminup($user) > 0) {
                                    echo "<tr>";
                					echo "<td><i>$user </i></td><td><button type='button' id='revoke-btn-".$user."' data-loading-text='Revoking Access...' data-complete-text='Access Revoked' class='btn btn-alert btn-xs'>Revoke Access</button></td>";
                                    echo "</tr>";
                				}
                			} ?>

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

        $("#revoke-btn-<?php echo $user;?>").on('click', function(e){
                            e.preventDefault(); // preventing default click action

            $.ajax({
                url: 'jsonencode/revokeuser.php?user=<?php echo $user;?>',
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
            //alert ("button clicked");
        })


  	<?php } ?>

  	</script>
    <script>

    <?php foreach (getallusers() as $user) {?>

      $("#grant-btn-<?php echo $user;?>").on('click', function(e){
                          e.preventDefault(); // preventing default click action
        if ( $("#password1-<?php echo $user;?>").val().length > 0 && $("#password1-<?php echo $user;?>").val() == $("#password2-<?php echo $user;?>").val()  ){
            password = $("#password1-<?php echo $user;?>").val();
            $.ajax({
                url: 'jsonencode/createuser.php?user=<?php echo $user;?>&pass=' + password,
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
        }else{
            alert("Passwords don't match or have zero length. Please try again.");
        }


          //alert ("button clicked");
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
