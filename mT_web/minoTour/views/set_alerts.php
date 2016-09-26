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
            <h1>Set Alerts <small> - run: <?php echo cleanname($_SESSION['active_run_name']);; ?></small></h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-edit"></i> External Links</a></li>
            <li class="active">Here</li>
          </ol>

        </section>

        <!-- Main content -->
        <section class="content"><?php include 'includes/run_check.php';?>
            

                <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Set Alerts</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">

                            <div id="messages"></div>
                            <div class="row">
                                <div class="col-lg-12">
                			One of the benefits of a real time sequencing platform is interacting with the sequencer during the run. This page enables this.<br> You can browse around the website once these parameters are set and notifications will appear on any page. If you leave the website, notifications are only available to you via Twitter. You must set a twitter handle to receive messages at if you wish to receive real time notifications.
                            <br>


                                <!-- /.col-lg-12 -->
                		<div class="row">
                            <div class="col-lg-12">
                            <h4>Coverage Depth</h4>
                    			<div class="input-group">
                      				<input id="foldchange" type="text" class="form-control">
                      			  	<span class="input-group-btn">
                        				<button class="btn btn-default" id="gen_coverage" type="button">Set</button>
                      			  	</span>
                				</input>
                				</div><!-- /input-group -->

                				<label class="radio-inline">
                  					<input type="radio" name="coveragenoticeradio" id="inlineRadio1" value="All" checked="checked"> All
                				</label>
                				<label class="radio-inline">
                					<input type="radio" name="coveragenoticeradio" id="inlineRadio2" value="Template"> Template
                				</label>
                				<label class="radio-inline">
                  					<input type="radio" name="coveragenoticeradio" id="inlineRadio3" value="Complement"> Complement
                				</label>
                				<label class="radio-inline">
                  					<input type="radio" name="coveragenoticeradio" id="inlineRadio4" value="2D"> 2D
                				</label>
                    			  		  	</div><!-- /.col-lg-6 -->
                		</div>

                		<div class="row">
                			<div class="col-lg-12">
                            As an alternative example: set an alert for every X bases sequenced (again with reference to the template). This alert is non-persistent - it disappears.
                			<br>
                            <h4>Base Notification (strongly suggest minimum setting of 100000)</h4>
                    			<div class="input-group">
                      				<input id="basenotification" type="text" class="form-control">
                      			  	<span class="input-group-btn">
                        				<button class="btn btn-default" id="base_notification" type="button">Set</button>
                      			  	</span>
                    			</div><!-- /input-group -->
                    			<label class="radio-inline">
                  					<input type="radio" name="basenoticeradio" id="inlineRadio1" value="All" checked="checked"> All
                				</label>
                				<label class="radio-inline">
                					<input type="radio" name="basenoticeradio" id="inlineRadio2" value="Template"> Template
                				</label>
                				<label class="radio-inline">
                  					<input type="radio" name="basenoticeradio" id="inlineRadio3" value="Complement"> Complement
                				</label>
                				<label class="radio-inline">
                  					<input type="radio" name="basenoticeradio" id="inlineRadio4" value="2D"> 2D
                				</label>
                  		  	</div><!-- /.col-lg-6 -->

                		</div>

                		<?php if ($_SESSION['currentbarcode'] >= 1) {?>
                		We are looking at barcodes here...
                		<?php } ?>

                				<br><Strong>Tweeting Features</strong><br>
                					<?php if (isset($_SESSION['twittername'])) {
                						echo "You have set a twitter handle to receive messages at - it is " . $_SESSION['twittername'] . ".<br>";
                					}else { ?>
                						You can have your alerts tweeted to you if you so wish. If you specify a username below, all the alerts you set above will be sent to your twitter handle from the minoTour twitter account (@minoTour_01 on a standard minoTour install. This may be changed depending on your site admins preferences). The only exception to this is if you set a base notification for a value of less than 500000 base pairs.<br>
                			            <br><h4>Set Twitter Handle</h4>
                			    			<div class="input-group">
                								<input id="twitterhandle" type="text" class="form-control">
                								<span class="input-group-btn">
                			        				<button class="btn btn-default" id="twitter_handle" type="button">Set</button>
                			      			  	</span>
                			    			</div>
                						<?php } ?>

                					<!-- /input-group -->
                		  		  	</div>
                                </div>
                            </div>
                        </div>
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

      <?php include 'includes/reporting-new.php'; ?>
      <script src="js/plugins/dataTables/jquery.dataTables.js" type="text/javascript" charset="utf-8"></script>
      <script src="js/plugins/dataTables/dataTables.bootstrap.js" type="text/javascript" charset="utf-8"></script>
      <script>

              $(function(){
              $('#gen_coverage').on('click', function(e){
                  e.preventDefault(); // preventing default click action
                  var idClicked = e.target.id;
                  var idVal = $("#foldchange").val();
                  //alert('were getting there ' + idClicked + ' is ' + idVal);
          		//alert ($("input:radio[name='coveragenoticeradio']:checked").val());
          		var type = $("input:radio[name='coveragenoticeradio']:checked").val();
                   var monkey = 'jsonencode/set_alerts.php?twitterhandle=<?php echo $_SESSION['twittername'];?>&type='+type+'&task=gencoverage&threshold='+idVal;
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
      <script>

              $(function(){
              $('#base_notification').on('click', function(e){
                  e.preventDefault(); // preventing default click action
                  var idClicked = e.target.id;
                  var idVal = $("#basenotification").val()
                  //alert('were getting there ' + idClicked + ' is ' + idVal);
          		var type = $("input:radio[name='basenoticeradio']:checked").val();
                   var monkey = 'jsonencode/set_alerts.php?twitterhandle=<?php echo $_SESSION['twittername'];?>&type='+type+'&task=basenotification&threshold='+idVal;
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
      <script>
  	$(function(){
  		$('#twitter_handle').on('click', function(e){
  			e.preventDefault();
  			var idClicked=e.target.id;
  			var idVal = $('#twitterhandle').val();
  			var monkey = 'jsonencode/tweetset.php?username='+idVal;
  			//alert (monkey);
              $.ajax({
                  url: monkey,
                 // alert ('url'),
                  success: function(data){
                      //alert ('success');
                      //$('#resetmodal').modal('hide')
                      //alert(data);
                      $("#messages").html(data);
                  }, error: function(){
                      alert('ajax failed');
                  },
              })
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
