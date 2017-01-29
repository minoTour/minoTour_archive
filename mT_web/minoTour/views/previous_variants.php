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
              Previous Variants
              <small> - run: <?php echo cleanname($_SESSION['focusrun']); ?></small>
            </h1>
            <ol class="breadcrumb">
              <li><a href="#"><i class="fa fa-table"></i> Previous Run</a></li>
              <li><a href="#"><i class="fa fa-bar-chart-o"></i> Nucleotide Variants</a></li>
              <li class="active">Here</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content"><?php include 'includes/run_check.php';?>
            
            <div id="messages"></div>
            <div class="box">
            <div class="box-header">
              <h3 class="box-title">Nucleotide Variant Detection <?php echo cleanname($_SESSION['focusrun']);?></h3>
            </div><!-- /.box-header -->
            <div class="box-body">
These pages aim to report potential nucleotide variant positions detected as sequencing is taking place. Potential variants are highlighted in one of two ways. Firstly consensus calling highlights those positions where the most commonly called base differs from the reference. The second method considers the average error rate of the sequencing process and identifies those positions whereby the variance is two standard deviations greater than the mean. This is all experimental.<br><br>
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="previous_variants.php">Consensus Variants</a></li>
                    <li><a href="previous_var.php">Variants</a></li>
                    <li><a href="previous_deletions.php">Deletions</a></li>
                    <li><a href="previous_insertions.php">Insertions</a></li>
                </ul>
            <div class="tab-content">
                <div class="panel panel-default">
    			  <div class="panel-heading">
    			    <h3 class="panel-title"><!-- Button trigger modal -->
    <button class="btn btn-info" data-toggle="modal" data-target="#modal1">
     <i class="fa fa-info-circle"></i> Consensus Variant Calling
    </button>

    <!-- Modal -->
    <div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title" id="myModalLabel">Consensus Variant Calling</h4>
          </div>
          <div class="modal-body">
            <body>This panel provides a table listing consensus variants.<br><br></body>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div></h3>
    			  </div>
    			  <div class="panel-body">
    			  <div class="row">
    			   <div class="col-md-4">
    			  	<?php if (!empty($_POST)): ?>
    			  	<?php
    			  	$type = $_POST["type"];
    			  	$coverage = $_POST["coverage"];
    			  			?>
       <?php else: ?>
    				<?php
    				$type = '2d';
    				$coverage = 20;
    				 ?>
    				<?php endif; ?>
        <form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post">
        <label for="type">Select read type for variant events.</label>
       <select class="form-control" name="type" id="type" onchange="this.form.submit()">
            <option <?php if ($type == 'template'){echo "selected=\"selected\"";};?> value="template">Template</option>
            <option <?php if ($type == 'complement'){echo "selected=\"selected\"";};?> value="complement">Complement</option>
            <option <?php if ($type == '2d'){echo "selected=\"selected\"";};?> value="2d">2d</option>
         </select>
         <label for="coverage">Select minimum coverage for variant events.</label>
         <select class="form-control" name="coverage" id="coverage" onchange="this.form.submit()">
         	<option <?php if ($coverage == 0){echo "selected=\"selected\"";};?> value="0">0</option>
         	<option <?php if ($coverage == 10){echo "selected=\"selected\"";};?> value="10">10</option>
         	<option <?php if ($coverage == 20){echo "selected=\"selected\"";};?> value="20">20</option>
         	<option <?php if ($coverage == 30){echo "selected=\"selected\"";};?> value="30">30</option>
         	<option <?php if ($coverage == 40){echo "selected=\"selected\"";};?> value="40">40</option>
         	<option <?php if ($coverage == 50){echo "selected=\"selected\"";};?> value="50">50</option>
         	<option <?php if ($coverage == 60){echo "selected=\"selected\"";};?> value="60">60</option>
         	<option <?php if ($coverage == 70){echo "selected=\"selected\"";};?> value="70">70</option>
         	<option <?php if ($coverage == 80){echo "selected=\"selected\"";};?> value="80">80</option>
         	<option <?php if ($coverage == 90){echo "selected=\"selected\"";};?> value="90">90</option>
         	<option <?php if ($coverage == 100){echo "selected=\"selected\"";};?> value="100">100</option>
         </select>
        </form>
    				</div>
    			  </div>
    						<div class="row">
    					 <div class="col-lg-12">
    					 	<div id = "consensus_details"><br><h5>Click on a position from the table below to view specific variants.</h5><h5>To download all variants with these parameters <a href="includes/export_variants.php?prev=1&type=<?php echo $type;?>&coverage=<?php echo $coverage;?>&job=consensus" type="button" class="btn btn-primary btn-xs">Download</a></h5>
    </div>
                			 <table id="example" class="display table table-condensed table-hover " cellspacing="0" width="100%">
    							 <thead>
    								 <tr>
    								 	<th>Ref ID</th>
    								 	<th>Reference Name</th>
    								 	<th>Reference Base</th>
    									 <th>Consensus Count</th>
    									 <th>Consensus Sequence</th>
    									 <th>Ref Position</th>
    									 <th>A</th>
    									 <th>T</th>
    									 <th>G</th>
    									 <th>C</th>
    									 <th>Total (called)</th>
    									 <th>Proportion Mismatched</th>
    									 <th>Proportion Most Common</th>

    								 </tr>
    							 </thead>

    							 <tfoot>
    								 <tr>
    								 	<th>Ref ID</th>
    								 	<th>Reference Name</th>
    								 	<th>Reference Base</th>
    									 <th>Consensus Count</th>
    									 <th>Consensus Sequence</th>
    									 <th>Ref Position</th>
    									 <th>A</th>
    									 <th>T</th>
    									 <th>G</th>
    									 <th>C</th>
    									 <th>Total (called)</th>
    									 <th>Proportion Mismatched</th>
    									 <th>Proportion Most Common</th>

    								 </tr>
    							 </tfoot>

    						 </table>
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
         $(document).ready(function() {
             $('#example').dataTable( {
                 "iDisplayLength": 25,
                 "sDom": '<"top"i>rt<"bottom"flp><"clear">',
                 "processing": true,
                 "serverSide": true,
                 "sAjaxSource": "data_tables/data_table_consensus.php?prev=1&type=<?php echo $type;?>&coverage=<?php echo $coverage;?>"
             } );
             oTable = $('#example').dataTable( );
             $('#example tbody').on('click', 'tr', function () {
                 var refpos = oTable.fnGetData(this,5);
                 var refid = oTable.fnGetData(this,0);
                 var type = '<?php echo $type;?>';
                 var refname = oTable.fnGetData(this,1);
                 //alert (refpos + " and " + refid + " and " + "<?php echo $type;?>");
                 var options = {
                     chart: {
                         renderTo: 'consensus_details',
                         zoomType: 'x',
                         type: 'column',
                     },
                     title: {
                       text: 'Base Variant Coverage <?php echo $type;?> on '+ refname + ' at ' +refpos+ ' with <?php echo $coverage;?> min coverage.'
                     },
                     resetZoomButton: {
                         position: {
                             x: -10,
                             y: 10
                         },
                         relativeTo: 'chart'
                     },
                     plotOptions: {
                         column: {
                             stacking: 'normal',
                         },
                         area: {
                             stacking: 'normal',
                             lineColor: '#666666',
                             lineWidth: 1,
                             marker: {
                                 enabled: false,
                                 lineWidth: 1,
                                 lineColor: '#666666'
                             }
                         }
                     },
                     xAxis: {
                         title: {
                             text: 'Reference Position'
                         }
                     },
                     yAxis: [{
                         labels: {
                             align: 'right',
                             x: -3
                         },
                         title: {
                             text: type
                         },
                         height: '80%',
                         lineWidth: 1
                     },{
                         labels: {
                             align: 'right',
                             x: -3
                         },
                         title: {
                             text: 'Ref'
                         },
                         max: 1,
                         top: '85%',
                         height: '15%',
                         offset: 0,
                         lineWidth: 1
                     }],
                     credits: {
                         enabled: false
                     },
                     legend: {
                         title: {
                             text: 'Base<br/><span style="font-size: 9px; color: #666; font-weight: normal">(Click to hide)</span>',
                             style: {
                                 fontStyle: 'italic'
                             }
                         },
                         layout: 'vertical',
                         align: 'right',
                         verticalAlign: 'top',
                         x: -10,
                         y: 100
                     },
                     series: []
                 };

                 $.getJSON('jsonencode/basesnpcoveragepos.php?prev=1&refid='+refid+'&position='+refpos+'&type='+type+'&callback=?', function(data) {
                     //alert("success");
                     options.series = data; // <- just assign the data to the series property.



                     //options.series = JSON2;
                     var chart = new Highcharts.Chart(options);
                     });
             });
  });
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
