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
            Previous Summary
            <small> - run: <?php echo cleanname($_SESSION['focusrun']); ?></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-table"></i> Previous Run</a></li>
            <li><a href="#"><i class="fa fa-bar-chart-o"></i> Data Summary</a></li>
            <li class="active">Here</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content"><?php include 'includes/run_check.php';?>
            
            <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo cleanname($_SESSION['focusrun']);?></h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div class="row">
            <div class="col-md-12">
              <!-- Custom Tabs -->
              <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                  <li><a href="previous_summary.php">Read Summaries</a></li>
                  <?php if ($_SESSION['focusbasesum'] > 0){?>
                  <li><a href="previous_basecalling.php">Basecaller Summary</a></li>
                  <?php }; ?>
                  <li><a href="previous_histogram.php">Read Histograms</a></li>
                  <li><a href="previous_rates.php">Sequencing Rates</a></li>
                  <li><a href="previous_pores.php">Pore Activity</a></li>
                  <li><a href="previous_quality.php">Read Quality</a></li>
                  <?php if ($_SESSION['focusreference'] != "NOREFERENCE") {?>
                  <li><a href="previous_coverage.php">Coverage Detail</a></li>
                  <?php }; ?>
                  <li class="active"><a href="previous_bases.php">Base Coverage</a></li>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane active" id="tab_1">
                      <div class="panel panel-default">
          			  <div class="panel-heading">
          			    <h3 class="panel-title"><!-- Button trigger modal -->
          <button class="btn btn-info" data-toggle="modal" data-target="#modal3">
           <i class="fa fa-info-circle"></i> Coverage
          </button></h3>

          <!-- Modal -->
          <div class="modal fade" id="modal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="myModalLabel"> New Views
                </div>
                <div class="modal-body">
          		<h3>Base Coverage</h3>
          		These plots show a view of base coverage across the entire reference. <br>To generate these plots, we take the MAF/SAM alignment data from Last/BWA and use it to call bases where they align to the reference. We call either the base which is aligned at that position, a single deletion if no base is placed at that point, or an insertion if extra bases are present. The insertion count is incremented by the number of inserted bases at that position. This is something which will be addressed in more detail in the future.
          		<br>
          		  </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div></h3>
          			  </div>
          			  <div class="panel-body">
                            <?php if ($_SESSION['focusBASE'] > 0) {?>
          					<?php if ($_SESSION['focusreference'] != "NOREFERENCE") {?>


          						<?php if (!empty($_POST)): ?>
          			  	<?php
          			  	$type = $_POST["type"];
          			  	$coverage = $_POST["coverage"];
          			  	$center = $_POST["center"];
          			  	$start = $center - ($coverage/2);
          			  	$end = $center + ($coverage/2);
          			  	$reference = $_POST["reference"];
          			  	$leftshift = $_POST["freddy"];
          			  	$center = $center-$leftshift;
          			  	$_POST["shift"]=0;

          			  			?>
             <?php else: ?>
          				<?php
          				$type = '2d';
          				$coverage = 100;
          				$center = 500;
          			  	$start = $center - ($coverage/2);
          			  	$end = $center + ($coverage/2);
          			  	$reference =1;
          				 ?>
          				<?php endif; ?>

          						<form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post" name="form" class="form-inline">
          						<div class="form-group">
          						<label for "reference">Reference:</label>
          						 <select class="form-control" name="reference" id="reference" onchange="this.form.submit()">
          						<?php foreach ($_SESSION['focusrefnames'] as $key => $value) {?>
          							<option <?php if ($reference == $key){echo "selected=\"selected\"";};?> value="<?php echo $key;?>"><?php echo $value;?></option>
          							<?php
          					}
          					?>
          						</select>


              <label for="type">Read Type:</label>
             <select class="form-control" name="type" id="type" onchange="this.form.submit()">
                  <option <?php if ($type == 'template'){echo "selected=\"selected\"";};?> value="template">Template</option>
                  <option <?php if ($type == 'complement'){echo "selected=\"selected\"";};?> value="complement">Complement</option>
                  <option <?php if ($type == '2d'){echo "selected=\"selected\"";};?> value="2d">2d</option>
               </select>

               <label for="coverage">Window Size:</label>
               <select class="form-control" name="coverage" id="coverage" onchange="this.form.submit()">
               	<option <?php if ($coverage == 100){echo "selected=\"selected\"";};?> value="100">100bp</option>
               	<option <?php if ($coverage == 500){echo "selected=\"selected\"";};?> value="500">500bp</option>
               	<option <?php if ($coverage == 1000){echo "selected=\"selected\"";};?> value="1000">1kb</option>
               	<option <?php if ($coverage == 2000){echo "selected=\"selected\"";};?> value="2000">2kb</option>
               	<option <?php if ($coverage == 5000){echo "selected=\"selected\"";};?> value="5000">5kb</option>
               </select>
               </div>
               <br><br>
               <button id="button1" type="button" class="btn btn-default" aria-label="Left Align">
            <span class="fa fa-fast-backward" aria-hidden="true"></span>
          </button>
          <button id="button2" type="button" class="btn btn-default" aria-label="Left Align">
            <span class="fa fa-backward" aria-hidden="true"></span>
          </button>
          <button id="button3" type="button" class="btn btn-default" aria-label="Left Align">
            <span class="fa fa-step-backward" aria-hidden="true"></span>
          </button>
               Genome Position: <input type="text" name="center" id="center" value="<?php echo $center;?>" onchange="this.form.submit()">
          <button id="button4" type="button" class="btn btn-default" aria-label="Left Align">
            <span class="fa fa-step-forward" aria-hidden="true"></span>
          </button>
          <button id="button5" type="button" class="btn btn-default" aria-label="Left Align">
            <span class="fa fa-forward" aria-hidden="true"></span>
          </button>
          <button id="button6" type="button" class="btn btn-default" aria-label="Left Align">
            <span class="fa fa-fast-forward" aria-hidden="true"></span>
          </button>

              </form>
          						<div id="basesnpcoverage" style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x" ></i> Calculating 'Base Coverage Plots' for <?php echo $value;?></div>

          				<?php }else { ?>
          												<div><p class="text-center"><small>This dataset has not been aligned to a reference sequence and so no SNPs can be called.</small></p></div>
          				<?php }; ?>
                          <?php } else { echo "Bases cannot be shown from the raw data alone. You must upload basecalled data to see these features.";};?>


          			  </div>
          			</div>
                          <!-- /.col-lg-12 -->


                </div>
                <!-- /.tab-content -->
              </div>
              <!-- nav-tabs-custom -->
            </div>
            <!-- /.col -->


            <!-- /.col -->
          </div>
        </div>
    </div>
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

      <?php include 'includes/reporting-new.php'; ?>
      <script>
  				$(document).ready(function(){
  					$('#runsummary').load('includes/runsummary.php');
  					setInterval(function(){
      			 	$('#runsummary').load('includes/runsummary.php');
      				}, 1000);
  				});
  				</script>

                <script src="js/jquery.nouislider.all.min.js"></script>




                    <?php foreach ($_SESSION['focusrefnames'] as $key => $value) {
                        //echo $key . " " . $value . "<br>";?>
                            <script>
                        $(document).ready(function() {
                            var start = <?php echo $start;?>;
                            var end = <?php echo $end;?>;
                            var reference = '<?php echo $reference;?>';
                            var type = '<?php echo $type;?>';
                            var options = {
                                chart: {
                                    renderTo: 'basesnpcoverage',
                                    zoomType: 'x',
                                    type: 'column',
                                },
                                title: {
                                  text: 'Base Coverage <?php echo $value;?>'
                                },
                                resetZoomButton: {
                                position: {
                                    // align: 'right', // by default
                                    // verticalAlign: 'top', // by default
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
                                                text: 'Base Number from reference'
                                            }
                                        },
                                        yAxis: [{
                                                labels: {
                                                    align: 'right',
                                                    x: -3
                                                },
                                                title: {
                                                    text: 'Coverage'
                                                },
                                                height: '100%',
                                                lineWidth: 1
                                            },],
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

                            $.getJSON('jsonencode/basesnpcoverage.php?prev=1&start=<?php echo $start;?>&end=<?php echo $end; ?>&type=<?php echo $type;?>&refid=<?php echo $reference;?>&callback=?', function(data) {
                                //alert("success");
                                options.series = data; // <- just assign the data to the series property.



                                //options.series = JSON2;
                                var chart = new Highcharts.Chart(options);
                                });

                                $( "#button1" ).click(function() {
                                start = start - 1000;
                                end = end - 1000;
                                $("#center").val($("#center").val()-1000);
                                //alert ("HOORAY!");
                                $.getJSON('jsonencode/basesnpcoverage.php?prev=1&start='+start+'&end='+end+'&type=<?php echo $type;?>&refid=<?php echo $reference;?>&callback=?', function(data) {
                                //alert("success");
                                options.series = data; // <- just assign the data to the series property.



                                //options.series = JSON2;
                                var chart = new Highcharts.Chart(options);
                                });

                            });
                                $( "#button2" ).click(function() {
                                start = start - 500;
                                end = end - 500;
                                $("#center").val($("#center").val()-500);
                                //alert ("HOORAY!");
                                $.getJSON('jsonencode/basesnpcoverage.php?prev=1&start='+start+'&end='+end+'&type=<?php echo $type;?>&refid=<?php echo $reference;?>&callback=?', function(data) {
                                //alert("success");
                                options.series = data; // <- just assign the data to the series property.



                                //options.series = JSON2;
                                var chart = new Highcharts.Chart(options);
                                });

                            });
                                $( "#button3" ).click(function() {
                                start = start - 100;
                                end = end - 100;
                                $("#center").val($("#center").val()-100);
                                //alert ("HOORAY!");
                                $.getJSON('jsonencode/basesnpcoverage.php?prev=1&start='+start+'&end='+end+'&type=<?php echo $type;?>&refid=<?php echo $reference;?>&callback=?', function(data) {
                                //alert("success");
                                options.series = data; // <- just assign the data to the series property.



                                //options.series = JSON2;
                                var chart = new Highcharts.Chart(options);
                                });

                            });
                            $( "#button6" ).click(function() {
                                start = start + 1000;
                                end = end + 1000;
                                $("#center").val(parseFloat($("#center").val())+1000);
                                //alert ("HOORAY!");
                                $.getJSON('jsonencode/basesnpcoverage.php?prev=1&start='+start+'&end='+end+'&type=<?php echo $type;?>&refid=<?php echo $reference;?>&callback=?', function(data) {
                                //alert("success");
                                options.series = data; // <- just assign the data to the series property.



                                //options.series = JSON2;
                                var chart = new Highcharts.Chart(options);
                                });

                            });
                                $( "#button5" ).click(function() {
                                start = start + 500;
                                end = end + 500;
                                $("#center").val(parseFloat($("#center").val())+500);
                                //alert ("HOORAY!");
                                $.getJSON('jsonencode/basesnpcoverage.php?prev=1&start='+start+'&end='+end+'&type=<?php echo $type;?>&refid=<?php echo $reference;?>&callback=?', function(data) {
                                //alert("success");
                                options.series = data; // <- just assign the data to the series property.



                                //options.series = JSON2;
                                var chart = new Highcharts.Chart(options);
                                });

                            });
                                $( "#button4" ).click(function() {
                                start = start + 100;
                                end = end + 100;
                                $("#center").val(parseFloat($("#center").val())+100);
                                //alert ("HOORAY!");
                                $.getJSON('jsonencode/basesnpcoverage.php?prev=1&start='+start+'&end='+end+'&type=<?php echo $type;?>&refid=<?php echo $reference;?>&callback=?', function(data) {
                                //alert("success");
                                options.series = data; // <- just assign the data to the series property.



                                //options.series = JSON2;
                                var chart = new Highcharts.Chart(options);
                                });

                            });
                        });

                            //]]>


                            </script>
                        <?php
                    }
                    ?>


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
