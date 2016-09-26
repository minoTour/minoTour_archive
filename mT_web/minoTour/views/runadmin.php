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
            <h1>Run Administration<small> - run: <?php echo cleanname($_SESSION['active_run_name']);; ?></small></h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-edit"></i> External Links</a></li>
            <li class="active">Here</li>
          </ol>

        </section>

        <!-- Main content -->
        <section class="content"><?php include 'includes/run_check.php';?>


                <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Run Administration</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <h4>Basic Run Control</h4>
                            <br>
            			 	<div id="messages"></div>
            				<br>
                            <p> In the future a number of basic tasks will be able to be completed here.</p>

                            <p> Currently you can use this page to switch a run from active to inactive. You shouldn't normally need to do this as minUP will automatically switch a run to inactive when it is complete. This catches the odd use case when this process fails for some reason  <i class="fa fa-smile-o"></i>.</p>
                            <button id='resetcache' class='btn btn-warning' data-toggle='modal' data-target='#resetcachemodal'>
            				    <i class='fa fa-exclamation-triangle'></i> Reset Cache
            				</button>
                            <!-- Button trigger modal -->
            				<button id='archivebutton' class='btn btn-warning' data-toggle='modal' data-target='#deletemodal'>
            				    <i class='fa fa-exclamation-triangle'></i> Inactivate Run
            				</button>
                            <br><br>
                            <?php echo '<pre>';
                            //echo "Hello";
                            $list = array();
                $allSlabs = $memcache->getExtendedStats('slabs');
                $items = $memcache->getExtendedStats('items');
                foreach($allSlabs as $server => $slabs) {
                    foreach($slabs AS $slabId => $slabMeta) {
                        $cdump = $memcache->getExtendedStats('cachedump',(int)$slabId);
                        foreach($cdump AS $keys => $arrVal) {
                            if (!is_array($arrVal)) continue;
                            foreach($arrVal AS $k => $v) {
                                if (strpos($k,$_SESSION['active_run_name']) !== false) {
                                    echo $k .'<br>';
                                }
                            }
                        }
                    }
                }
               echo '</pre>';



               ?>
               <!-- Modal -->
               <div class='modal fade' id='resetcachemodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                   <div class='modal-dialog'>
                       <div class='modal-content'>
                           <div class='modal-header'>
                               <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                               <h4 class='modal-title' id='myModalLabel'>Reset Cache</h4>
                           </div>
                               <div class='modal-body'>
                                   <div id='resetcache'>
                                       <p>This action will reset the data cache. If you have started reuploading a run and the data looks wrong - try hitting this button!</p>
                                   </div>
                                   <div id='resetcacheworking'>
                                       <p class='text-center'>We're working to reset the cache. Please be patient and don't navigate away from this page.</p>
                                       <p class='text-center'><img src='images/loader.gif' alt='loader'></p>
                                   </div>
                                   <div class='modal-footer'>
                                       <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                                       <button id='resetcacheopt' type='button' class='btn btn-warning'>Reset Cache</button>
                                   </div>
                               </div><!-- /.modal-content -->
                           </div><!-- /.modal-dialog -->
                       </div><!-- /.modal -->
                   </div>
               </div>
            				<!-- Modal -->
            				<div class='modal fade' id='deletemodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
            				    <div class='modal-dialog'>
            					    <div class='modal-content'>
            					        <div class='modal-header'>
            							    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
            							    <h4 class='modal-title' id='myModalLabel'>Inactivate Run</h4>
            							</div>
            								<div class='modal-body'>
            								    <div id='archiveinfo'>
            								        <p>This action will switch the current live run to an inactive run. It will then appear under the previosu runs collection.</p>
            										<p><strong>If data is currently being uploaded to minoTour for this run, the run will not revert back to being an actie run.</strong></p>
            										<p>To try and finish this run, first check minUP isn't running or inactivate it by typing ctrl-c. If this does not switch the run from active to inactive then use the inactivate run button below.</p>
            								    </div>
            								    <div id='archiveworking'>
            								        <p class='text-center'>We're working to archive your database. Please be patient and don't navigate away from this page.</p>
            								        <p class='text-center'><img src='images/loader.gif' alt='loader'></p>
            								    </div>
            								    <div class='modal-footer'>
            								        <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
            								        <button id='inactivateopt' type='button' class='btn btn-warning'>Inactivate</button>
            								    </div>
            							    </div><!-- /.modal-content -->
            							</div><!-- /.modal-dialog -->
            						</div><!-- /.modal -->


                            <!-- /.col-lg-12 -->
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
  	    	$('#resetcacheworking').hide();
  	        $('#resetcacheopt').on('click', function(e){
  	        	$('#resetcache').hide();
         		    $('#resetcacheworking').show();
         		    $('#resetcacheopt').addClass('disabled');
  	            e.preventDefault(); // preventing default click action
  	            $.ajax({
  	                url: 'jsonencode/resetcache.php',
  	                success: function(data){
  						//alert ('success');
  	                    $('#resetcachemodal').modal('hide')
  						//alert(data);
  						$("#messages").html(data);
  						//$('#optobutton').addClass('disabled');
  						//$('#archivebutton').addClass('disabled');
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
          $(function(){
  	    	$('#archiveworking').hide();
  	        $('#inactivateopt').on('click', function(e){
  	        	$('#archiveinfo').hide();
         		    $('#archiveworking').show();
         		    $('#inactivateopt').addClass('disabled');
  	            e.preventDefault(); // preventing default click action
  	            $.ajax({
  	                url: 'jsonencode/inactivate.php',
  	                success: function(data){
  						//alert ('success');
  	                    $('#deletemodal').modal('hide')
  						//alert(data);
  						$("#messages").html(data);
  						//$('#optobutton').addClass('disabled');
  						$('#archivebutton').addClass('disabled');
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
