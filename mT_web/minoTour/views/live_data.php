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
            Current Data Summary
            <small> - run: <?php echo cleanname($_SESSION['active_run_name']); ?></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-bolt"></i> Current Sequencing Run</a></li>
            <li><a href="#"><i class="fa fa-cog fa-spin"></i> Live Data</a></li>
            <li class="active">Here</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!--<div class="box">
                <div class="box-header">
                    <h3 class="box-title">Testing shizzle</h3>
                </div>
                <div class="box-body">
                    <?php
                    //echo "<br>starttimes<br>";
                    //var_dump(retrievefromsession($_SESSION["active_run_name"],"starttimes",""));
                    //echo "<br>readnumberstats<br>";
                    //var_dump(retrievefromsession($_SESSION["active_run_name"],"readnumberstats",""));
                    //echo "<br>Summary stats<br>";
                    //echo "<pre>";
                    //var_dump(retrievefromsession($_SESSION["active_run_name"],"summarystats",""));
                    //echo "</pre>";
                    ?>
                </div>
            </div>-->
            <?php include 'includes/run_check.php';?>

            <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo cleanname($_SESSION['active_run_name']); ?></h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div class="row">



                    <div class="col-lg-12">

    			<div class="panel panel-default">
    				<div class="panel-heading">
    					<h3 class="panel-title"><button class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal0"><i class="fa fa-info-circle"></i> Processing Activity</button>
    					<div class="modal fade" id="modal0" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    						<div class="modal-dialog">
    							<div class="modal-content">
    								<div class="modal-header">
    									<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    									<h4 class="modal-title" id="myModalLabel">Processing Activity</h4>
    								</div>
    								<div class="modal-body">
    									This panel shows you how reads have been processed and uploaded by the minUp scripts and the background alignment data processing.
    								</div>
    								 <div class="modal-footer">
    			        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
    			      </div>
    			    </div>
    			  </div>
    			</div>
    						  </div>
    						  <div id="processingcoverage">
    						  <div class="panel-body">
    						  <div class="row">
    						  <div class="col-md-12" id="processing" style="height:300px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Processing Rates</div>
    						  </div>
    						   </div>
    						</div>

    					</div>


    						<div class="panel panel-default">
    						  <div class="panel-heading">
    						    <h3 class="panel-title"><input type="checkbox" name="colorCheckbox" id="readsummarycheck" value="RC" checked><!-- Button trigger modal -->
    			<button class="btn btn-info  btn-sm" data-toggle="modal" data-target="#modal1">
    			 <i class="fa fa-info-circle"></i> Reads And Coverage Summary
    			</button>

    			<!-- Modal -->
    			<div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    			  <div class="modal-dialog">
    			    <div class="modal-content">
    			      <div class="modal-header">
    			        <button type="button" class="close  btn-sm" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    			        <h4 class="modal-title" id="myModalLabel">Reads And Coverage Summary</h4>
    			      </div>
    			      <div class="modal-body">
    			        This panel provides information on the number of reads of each type generated by the metrichor analysis. The avergae read lengths and the maximum read length for each are shown.<br><br>
    					Where a reference sequence is available for mapping, the proportion of the reference covered by reads is shown as "Percentage of Reference with Read".<br><br>
    					The average depth of sequencing over these positions is shown as "Average Depth Of Sequenced Positions".<br>
    			      </div>
    			      <div class="modal-footer">
    			        <button type="button" class="btn btn-default  btn-sm" data-dismiss="modal">Close</button>
    			      </div>
    			    </div>
    			  </div>
    			</div>
    						  </div>
    						  <div id="readsncoverage">
    						  <div class="panel-body">
    									<div class="row">
    									<div class="col-md-3" id="readnum" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Read Numbers</div>
    									<div class="col-md-3" id="yield" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Yield</div>
    									<div class="col-md-3" id="avglen" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Read Average Length</div>
    									<div class="col-md-3" id="maxlen" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Read Max Length</div>
                                        <div class="col-md-12" id="boxplotlength" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Box Plots</div>


    								</div>
                                    <div id="lengthtimewindow" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Read Lengths Over Time.</div>
    								<div class="row">
    								<?php if ($_SESSION['activereference'] != "NOREFERENCE") {?>
                                        <?php if (count($_SESSION['activerefnames']) >= 3){
                                            //echo "We need something else to handle this bad boy.";?>
                                            <div class="col-md-6" id="percentcoverageglob" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Reference Coverage for sequences</div>
    										<div class="col-md-6" id="depthcoverageglob" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Reference Depth for sequences</div>
                                            <?php
                                        }else {?>
                                            <?php //var_dump($_SESSION['activerefnames']); ?>
    									<?php foreach ($_SESSION['activerefnames'] as $key => $value) {?>
    										<div class="col-md-6" id="percentcoverage<?php echo $key;?>" style="height:200px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Reference Coverage for <?php echo $value;?></div>
    										<div class="col-md-6" id="depthcoverage<?php echo $key;?>" style="height:200px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Reference Depth for <?php echo $value;?></div>
                                            <?php
    									}
                                    }
    									?>
                                        <div class="col-md-12" id="mappabletime" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> 2D Reads Mapping Over Time.</div>
    								<!---<div class="col-md-6" id="percentcoverage" style="width:50%; height:200px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Reference Coverage</div>--->
    								<!---<div class="col-md-6" id="depthcoverage" style="width:50%; height:200px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Reference Depth</div>--->


    							<?php }else { ?>
    															<div><p class="text-center"><small>This dataset has not been aligned to a reference sequence.</small></p></div>
    							<?php }; ?>
    							</div>


    						  </div>
    						</div>

    					</div>
    					<?php if ($_SESSION['currentbarcode'] >= 1) {?>
    <div class="panel panel-default">
    						  <div class="panel-heading">
    						    <h3 class="panel-title"><input type="checkbox" name="colorCheckbox" id="barcodingcheck" value="BC" checked><!-- Button trigger modal -->
    			<button class="btn btn-info  btn-sm" data-toggle="modal" data-target="#modalbarcode">
    			 <i class="fa fa-info-circle"></i> Barcoding Summary
    			</button>

    			<!-- Modal -->
    			<div class="modal fade" id="modalbarcode" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    			  <div class="modal-dialog">
    			    <div class="modal-content">
    			      <div class="modal-header">
    			        <button type="button" class="close  btn-sm" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    			        <h4 class="modal-title" id="myModalLabel">Barcoding Summary</h4>
    			      </div>
    			      <div class="modal-body">
    			        This panel provides information on the number of reads assigned to each barcode using the Oxford Nanopore barcoding protocol.<br><br>
    			        The standard ONT barcoding analysis only searches for barcodes in PASS reads - i.e those reads generating full 2D sequence. Reads which cannot be classified are moved to the fail bin. We therefore show as unclassified (UC) those reads which generated 2D sequence but could not be barcoded by the ONT pipeline in the charts below.<br><br>
    					Note that further barcoding analysis options are availble under the specific barcoding tab in the left hand menu.<br>
    			      </div>
    			      <div class="modal-footer">
    			        <button type="button" class="btn btn-default  btn-sm" data-dismiss="modal">Close</button>
    			      </div>
    			    </div>
    			  </div>
    			</div>
    						  </div>
    						  <div id="barcoding">
    						  <div class="panel-body">
    									<div class="row">
    									<div class="col-md-5" id="barcod" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Barcoding</div>
    									<div class="col-md-7" id="barcodcov" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Barcode Coverage</div>


    								</div>

    						  </div>
    						</div>

    					</div>
    					<?php }; ?>


    						<div class="panel panel-default">
    						  <div class="panel-heading">
    						    <h3 class="panel-title"><input type="checkbox" name="colorCheckbox" id="sequencingratecheck" value="SRM" checked><!-- Button trigger modal -->
    			<button class="btn btn-info  btn-sm" data-toggle="modal" data-target="#sequencingratemodal">
    			 <i class="fa fa-info-circle"></i> Sequencing Rate Information</h4>
    			</button>

    			<!-- Modal -->
    			<div class="modal fade" id="sequencingratemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    			  <div class="modal-dialog">
    			    <div class="modal-content">
    			      <div class="modal-header">
    			        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    			        <h4 class="modal-title" id="myModalLabel"> Sequencing Rate Information
    			      </div>
    			      <div class="modal-body">
    			        Rate of Basecalling<br>
    					This plot show the number of reads generated in one minute intervals over the course of the sequencing run.<br><br>
    								      </div>
    			      <div class="modal-footer">
    			        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
    			      </div>
    			    </div>
    			  </div>
    			</div></h3>
    						  </div>
                              <div id="sequencerate">
    						  <div>
    							<div class="row">
    								<div class="col-md-12" id="cumulativeyield" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Cumulative Yield.</div>
                                    <div class="col-md-12" id="sequencingrate" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Sequencing Rates.</div>
                                    <!--<div class="col-md-12" id="ratio2dtemplate" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Ratio 2D to Template.</div>-->
                                    <?php if ($_SESSION['currentBASE'] > 0) {?>
                                    <div class="col-md-12" id="ratiopassfail" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Pass Fail Reads.</div>
                                    <?php };?>
    							</div>
    							</div>

    						  <div class="panel-body">
                                  <?php if ($_SESSION['currentBASE'] > 0) {?>
    								<div id="readrate" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Read Rate</div>
                                    <?php }else{echo "This run is pre aligned data only.";};?>
    						  </div>
    						</div>
    					</div>



    						<div class="panel panel-default">
    						  <div class="panel-heading">
    						    <h3 class="panel-title"><input type="checkbox" name="colorCheckbox" id="qualityinfocheck" value="QI" checked> <!-- Button trigger modal -->
    			<button class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal4">
    			 <i class="fa fa-info-circle"></i> Quality Information</h4>
    			</button>

    			<!-- Modal -->
    			<div class="modal fade" id="modal4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    			  <div class="modal-dialog">
    			    <div class="modal-content">
    			      <div class="modal-header">
    			        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    			        <h4 class="modal-title" id="myModalLabel"> Quality Information
    			      </div>
    			      <div class="modal-body">
    			        Read Number Over Length<br>
    					This plot shows the numbers of reads at each length which align.<br><br>
    					  </div>
    			      <div class="modal-footer">
    			        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
    			      </div>
    			    </div>
    			  </div>
    			</div></h3>
    						  </div>
    						  <div id="qualityinfo">
    						  <div class="panel-body">
    						  <?php if ($_SESSION['activereference'] != "NOREFERENCE") {?>
    				  			<div id="numberoverlength"  style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Number of Aligned Reads By Length</div>
    				  			<?php }else { ?>
    															<div><p class="text-center"><small>This dataset has not been aligned to a reference sequence.</small></p></div>
    							<?php }; ?>


    								  </div>
    						</div>
    					</div>

    						<div class="panel panel-default">
    						  <div class="panel-heading">
    						    <h3 class="panel-title"><input type="checkbox" name="colorCheckbox" value="RS" checked><!-- Button trigger modal -->
    			<button class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal6">
    			 <i class="fa fa-info-circle"></i> Run Summary</h4>
    			</button>

    			<!-- Modal -->
    			<div class="modal fade" id="modal6" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    			  <div class="modal-dialog">
    			    <div class="modal-content">
    			      <div class="modal-header">
    			        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    			        <h4 class="modal-title" id="myModalLabel"> Run Summary
    			      </div>
    			      <div class="modal-body">
    			      <div class="row">
    						  <div class="col-md-12">
    			Key details on the run.<br><br>
    			</div></div>
    					  </div>
    			      <div class="modal-footer">
    			        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
    			      </div>
    			    </div>
    			  </div>
    			</div></h3>
    						  </div>
    						  <div id="runinfo">
    						  <div class="panel-body" id="runsummary">
    				  			Content
    								  </div>


    						</div>

    			</div>

    			                </div>
        </div>
    </div>
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

      <?php include 'includes/reporting-new.php'; ?>

      <script type="text/javascript">
          $(document).ready(function(){
              $('input[type="checkbox"]').click(function(){
                  if($(this).attr("value")=="RC"){
                      $("#readsncoverage").toggle();
                  }
                  if($(this).attr("value")=="RH"){
                      $("#readhistograms").toggle();
                  }
                  if($(this).attr("value")=="SRM"){
                      $("#sequencerate").toggle();
                  }
                  if($(this).attr("value")=="PI"){
                      $("#poreinfo").toggle();
                  }
                  if($(this).attr("value")=="QI"){
                      $("#qualityinfo").toggle();
                  }
                  if($(this).attr("value")=="RS"){
                      $("#runinfo").toggle();
                  }
                   if($(this).attr("value")=="BC"){
                      $("#barcoding").toggle();
                  }
              });
          });
      </script>
      <script>
      $(document).ready(function(){
          $('#runsummary').load('includes/runsummary.php');
          setInterval(function(){
          $('#runsummary').load('includes/runsummary.php');
          }, <?php echo $_SESSION['pagerefresh'] ;?>);
      });
      </script>

      <?php if (isset($_SESSION['first_visit'])) {}else{?>
      <script type="text/javascript">
          $(function(){
              new PNotify({
              title: 'Auto Updates',
              text: 'This page autoupdates - you do not need to manually refresh. It contains a subset of data available.',
              icon: 'fa fa-info-circle',
              type: 'info'
          });
      });
      </script>
      <?php }; $_SESSION['first_visit']=1;?>

      <?php include 'includes/livecharts.php'; ?>



  </body>
</html>
<?php
} else {

	    // the user is not logged in. you can do whatever you want here.
	    // for demonstration purposes, we simply show the "you are not logged in" view.
	    include("views/not_logged_in.php");
	}

	?>
