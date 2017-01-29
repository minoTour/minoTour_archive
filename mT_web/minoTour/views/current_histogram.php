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
            Current Summary
            <small> - run: <?php echo cleanname($_SESSION['active_run_name']); ?></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-table"></i> Current Run</a></li>
            <li><a href="#"><i class="fa fa-bar-chart-o"></i> Data Summary</a></li>
            <li class="active">Here</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content"><?php include 'includes/run_check.php';?>

            <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?php echo cleanname($_SESSION['active_run_name']);?></h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div class="row">
            <div class="col-md-12">
              <!-- Custom Tabs -->
              <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                  <li><a href="current_summary.php">Read Summaries</a></li>
                  <?php if ($_SESSION['currentbasesum'] > 0){?>
                  <li><a href="current_basecalling.php">Basecaller Summary</a></li>
                  <?php }; ?>
                  <li class="active"><a href="current_histogram.php">Read Histograms</a></li>
                  <li><a href="current_rates.php">Sequencing Rates</a></li>
                  <li><a href="current_pores.php">Pore Activity</a></li>
                  <li><a href="current_quality.php">Read Quality</a></li>
                  <?php if ($_SESSION['activereference'] != "NOREFERENCE") {?>
                  <li><a href="current_coverage.php">Coverage Detail</a></li>
                  <li><a href="current_bases.php">Base Coverage</a></li>
                  <?php }; ?>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane active" id="tab_1">
                      <div class="panel panel-default">
          			  <div class="panel-heading">
          			    <h3 class="panel-title"><!-- Button trigger modal -->
          <button class="btn btn-info" data-toggle="modal" data-target="#modal1">
           <i class="fa fa-info-circle"></i> Reads Histograms
          </button>

          <!-- Modal -->
          <div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="myModalLabel">Read Histograms</h4>
                </div>
                <div class="modal-body">
                  This panel provides a histogram of actual read lengths for template, complement and 2d reads. It can be extremely informative to remove individual read types from the plot by clicking on the legend!<br>
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
          					<div class="col-md-12" id="container" style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Histograms - read numbers</div>
          				</div>
          				<div class="row">
          					<div class="col-md-12" id="container2" style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Histograms - base counts</div>
          				</div>
          			  </div>
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

  					        <script type="text/javascript">
  					        $(document).ready(function() {
  					            var options = {
  					                chart: {
  					                    renderTo: 'container',
  					                    type: 'column',
                                          zoomType: 'x',
  					                },
  									plotOptions: {
  									            column: {
  									                animation: false

  									            },
  									            series: {
  									                cursor: 'pointer',
  									                point: {
  									                    events: {
  									                        click: function () {
  									                            //alert('Category: ' + this.category + ', series: ' + this.series.name);
  									                            var sequrl = "includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']?>&type=histogram&job="+this.series.name+"&prev=0&length="+this.category;
  									                            window.open(sequrl);

  									                        }
  									                    }
  									                }
  									            },
  									        },
  					                title: {
  					                    text: 'Histogram of Read Lengths'
  					                },
  									credits: {
  									    enabled: false
  									  },
  					                subtitle: {
  					                    text: '',
  					                    x: -20
  					                },
  					                xAxis: {
  					                    categories: [],
  										labels: {
  										                rotation: -45,
  													},
  					                },
  					                yAxis: {
  					                    title: {
  					                        text: 'Number of Reads'
  					                    },

  					                },
  					                tooltip: {
  					                    formatter: function() {
  					                            return '<b>'+ this.series.name +'</b><br/>'+
  					                            this.x +': '+ this.y;
  					                    }
  					                },
  					                legend: {
  					                    layout: 'vertical',
  					                    align: 'right',
  					                    verticalAlign: 'middle',
  					                    borderWidth: 0
  					                },
  					                series: [],
  									groupPadding: 0,
  					            };
  								    $.getJSON("jsonencode/histograms.php?prev=0&db=<?php echo $_SESSION['active_run_name'];?>&callback=?", function(json) {
  					                options.xAxis.categories = json[0]['data'];
                                      <?php if ($_SESSION['currentBASE'] > 0 && $_SESSION['currentPRE'] > 0) {?>
  					                options.series[0] = json[1];
  					                options.series[1] = json[2];
  					                options.series[2] = json[3];
                                      options.series[3] = json[4];
                                      options.series[4] = json[5];
                                      <?php }else if ($_SESSION['currentBASE'] == 0 && $_SESSION['currentPRE'] > 0) {?>
                                      options.series[0] = json[4];
      					            options.series[1] = json[5];
                                      <?php }else if ($_SESSION['currentBASE'] > 0 && $_SESSION['currentPRE'] == 0) {?>
                                      options.series[0] = json[1];
      					            options.series[1] = json[2];
                                      options.series[2] = json[3];
                                      <?php  }; ?>

  								        //options.series = JSON2;
  								                var chart = new Highcharts.Chart(options);
  								                });

  							});


  					        </script>

  					        <script type="text/javascript">
  					        $(document).ready(function() {
  					            var options = {
  					                chart: {
  					                    renderTo: 'container2',
  					                    type: 'column',
                                          zoomType: 'x',
  					                },
  									plotOptions: {
  									            column: {
  									                animation: false

  									            },
  									            series: {
  									                cursor: 'pointer',
  									                point: {
  									                    events: {
  									                        click: function () {
  									                            //alert('Category: ' + this.category + ', series: ' + this.series.name);
  									                            var sequrl = "includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']?>&type=histogram&job="+this.series.name+"&prev=0&length="+this.category;
  									                            window.open(sequrl);

  									                        }
  									                    }
  									                }
  									            },
  									        },
  					                title: {
  					                    text: 'Bases sequenced by Read Length'
  					                },
  									credits: {
  									    enabled: false
  									  },
  					                subtitle: {
  					                    text: '',
  					                    x: -20
  					                },
  					                xAxis: {
  					                    categories: [],
  										labels: {
  										                rotation: -45,
  													},
  					                },
  					                yAxis: {
  					                    title: {
  					                        text: 'Number of Bases'
  					                    },

  					                },
  					                tooltip: {
  					                    formatter: function() {
  					                            return '<b>'+ this.series.name +'</b><br/>'+
  					                            this.x +': '+ this.y;
  					                    }
  					                },
  					                legend: {
  					                    layout: 'vertical',
  					                    align: 'right',
  					                    verticalAlign: 'middle',
  					                    borderWidth: 0
  					                },
  					                series: [],
  									groupPadding: 0,
  					            };
  								    $.getJSON("jsonencode/histogrambases.php?prev=0&db=<?php echo $_SESSION['active_run_name'];?>&callback=?", function(json) {
  					                options.xAxis.categories = json[0]['data'];
                                      <?php if ($_SESSION['currentBASE'] > 0 && $_SESSION['currentPRE'] > 0) {?>
  					                options.series[0] = json[1];
  					                options.series[1] = json[2];
  					                options.series[2] = json[3];
                                      options.series[3] = json[4];
                                      options.series[4] = json[5];
                                      <?php }else if ($_SESSION['currentBASE'] == 0 && $_SESSION['currentPRE'] > 0) {?>
                                      options.series[0] = json[4];
      					            options.series[1] = json[5];
                                      <?php }else if ($_SESSION['currentBASE'] > 0 && $_SESSION['currentPRE'] == 0) {?>
                                      options.series[0] = json[1];
      					            options.series[1] = json[2];
                                      options.series[2] = json[3];
                                      <?php  }; ?>

  								        //options.series = JSON2;
  								                var chart = new Highcharts.Chart(options);
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
