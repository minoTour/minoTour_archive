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
            Change Password
            <small> - User: <?php echo $_SESSION['user_name']; ?></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-question-circle"></i> Change Password</a></li>
            <li class="active">Here</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content"><?php include 'includes/run_check.php';?>
            <div class="box">
            <div class="box-header">
              <h3 class="box-title">Password</h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div id="messages"></div>
				<br>
				<div class="row">
				<form id="passwordreset" class="col-md-4" role="form">
				  <div class="form-group">
				    <p class="help-block">To change your password enter your old password and new password twice below.</p>
				    <label for="exampleInputEmail1">Old Password</label>
				    <input type="password" class="form-control" id="old_password" >
				  </div>
				  <div class="form-group">
				    <label for="InputPassword1">New Password</label>
				    <input type="password" class="form-control" id="InputPassword1" >
				  </div>
				  <div class="form-group">
				    <label for="InputPassword2">Confirm Password</label>
				    <input type="password" class="form-control" id="InputPassword2" >
				  </div>
				  <button id="formgo" type="submit" class="btn btn-default">Submit</button>
				</form>



          </div>
      </div>
  </div>
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

      <?php include 'includes/reporting-new.php'; ?>

      <script>
          $(function(){
              $('#formgo').on('click', function(e){
                  e.preventDefault(); // preventing default click action
                  if ($( "#InputPassword1" ).val().length < 8){
                      $("#messages").append( "<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button><strong>Warning!</strong> New Password must be at least 8 characters long.</div>" );
                  }else{
                      if ($( "#InputPassword1" ).val() == $( "#InputPassword2" ).val()) {
                          $.post( "jsonencode/psswd_change.php", { current: $("#old_password").val(), new: $( "#InputPassword1" ).val()  })
                            .done(function( data ) {
                              $("#messages").html(data);
                          });
                      }else {
                          $("#messages").append( "<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button><strong>Warning!</strong> New Passwords Do Not Match.</div>" );
                      }
                  }
                  document.getElementById("passwordreset").reset();

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
