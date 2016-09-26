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
                  <li class="active"><a href="previous_quality.php">Read Quality</a></li>
                  <?php if ($_SESSION['focusreference'] != "NOREFERENCE") {?>
                  <li><a href="previous_coverage.php">Coverage Detail</a></li>
                  <?php }; ?>
                  <li><a href="previous_bases.php">Base Coverage</a></li>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane active" id="tab_1">
                      <div class="panel panel-default">
          			  <div class="panel-heading">
          			    <h3 class="panel-title"><!-- Button trigger modal -->
          <button class="btn btn-info" data-toggle="modal" data-target="#modal4">
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
          			        Read Quality Over Length<br>
          					This plot shows the average quality of each position of every read which maps to the reference.<br><br>
          			        Read Number Over Length<br>
          					This plot shows the numbers of reads at each length which align.<br><br>
          					Read Quality For 100 Random Reads<br>
          		This plot shows the average quality for 1000 random reads from the run. Note that the 100 reads are not the same for the three classes of read.
          		  </div>
          					  </div>
          			      <div class="modal-footer">
          			        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          			      </div>
          			    </div>
          			  </div></h3>
          			</div>

          						  <div class="panel-body">
          				  			<?php if ($_SESSION['focusreference'] != "NOREFERENCE") {?>
          				  			<!--<div id="avgquallength"  style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Quality Scores for Aligned Reads</div>-->
          				  			<div id="numberoverlength"  style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Number of Aligned Reads By Length</div>
          				  			<?php }else { ?>
          															<div><p class="text-center"><small>This dataset has not been aligned to a reference sequence.</small></p></div>
          							<?php }; ?>
                                      <?php if ($_SESSION['focusBASE'] > 0) {?>
          				  		  	<div id="allqualities"  style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Quality Scores for 100 Random Reads</div>
                                      <?php };?>
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
  $(document).ready(function() {
      var options = {
          chart: {
              renderTo: 'avgquallength',
              zoomType: 'x'
              //type: 'line'
          },
          title: {
            text: 'Read Quality Over Length For Aligned Reads'
          },
          xAxis: {
                      title: {
                          text: 'Basepairs'
                      }
                  },
                  yAxis: {
                              title: {
                                  text: 'Average Quality Score'
                              }
                          },
                          credits: {
                              enabled: false
                            },
          legend: {
              layout: 'vertical',
              align: 'right',
              verticalAlign: 'middle',
              borderWidth: 0
          },
          series: []
      };

      $.getJSON('jsonencode/readlengthqual.php?prev=1&callback=?', function(data) {
          //alert("success");
          options.series = data; // <- just assign the data to the series property.



          //options.series = JSON2;
          var chart = new Highcharts.Chart(options);
          });
  });

      //]]>

      </script>
          <script>
      $(document).ready(function() {
          var options = {
              chart: {
                  renderTo: 'allqualities',
                  zoomType: 'x',
                  type: 'scatter',
                  //type: 'line'
              },
              title: {
                text: 'Read Quality For 100 Random Reads'
              },
              xAxis: {
                          title: {
                              text: 'Basepairs'
                          }
                      },
                      yAxis: {
                                  title: {
                                      text: 'Average Quality Score'
                                  }
                              },
                              plotOptions: {
                                         scatter: {
                                             marker: {
                                                 radius: 2
                                             }
                                         }
                                     },
                              credits: {
                                  enabled: false
                                },
              legend: {
                  layout: 'vertical',
                  align: 'right',
                  verticalAlign: 'middle',
                  borderWidth: 0
              },
              series: []
          };

          $.getJSON('jsonencode/allqualities.php?prev=1&callback=?', function(data) {
              //alert("success");
              options.series = data; // <- just assign the data to the series property.



              //options.series = JSON2;
              var chart = new Highcharts.Chart(options);
              });
      });

          //]]>

          </script>
                          <script>
                  $(document).ready(function() {
                      var options = {
                          chart: {
                              renderTo: 'numberoverlength',
                              zoomType: 'x'
                              //type: 'line'
                          },
                          title: {
                            text: 'Read Number Over Length'
                          },
                          xAxis: {
                                      title: {
                                          text: 'Basepairs'
                                      }
                                  },
                                  yAxis: {
                                              title: {
                                                  text: 'Number of Reads of this Length'
                                              }
                                          },
                                          credits: {
                                              enabled: false
                                            },
                          legend: {
                              layout: 'vertical',
                              align: 'right',
                              verticalAlign: 'middle',
                              borderWidth: 0
                          },
                          series: []
                      };

                          $.getJSON('jsonencode/readnumberlength.php?prev=1&callback=?', function(data) {
                                      //alert("success");

                              options.series = data; // <- just assign the data to the series property.


                              //options.series = JSON2;
                                      var chart = new Highcharts.Chart(options);
                              });
                  });


                      //]]>

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
