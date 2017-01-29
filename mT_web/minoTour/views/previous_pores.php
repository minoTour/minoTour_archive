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
                  <li class="active"><a href="previous_pores.php">Pore Activity</a></li>
                  <li><a href="previous_quality.php">Read Quality</a></li>
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
          <button class="btn btn-info" data-toggle="modal" data-target="#modal3">
           <i class="fa fa-info-circle"></i> Pore Activity</h4>
          </button>

          <!-- Modal -->
          <div class="modal fade" id="modal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="myModalLabel"> Pore Activity
                </div>
                <div class="modal-body">
                  Active Channels Over Time<br>
          		The number of channels actively generating sequence data over the course of the run in 1 minute intervals.<br><br>
          		Reads Per Pore<br>
          		The number of reads generated by each pore in total over the lifetime of the run.<br><br>
          		Traces Per Pore<br>
          		The number of traces generated by each pore in total over the lifetime of the run.<br><br>
          		Reads Per Pore/Mux<br>
          		The number of reads generated by each pore in each mux in total over the lifetime of the run.<br><br>
          		Baes Per Pore/Mux<br>
          		The number of bases generated by each pore in each mux in total over the lifetime of the run.<br><br>
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
          					<div id="activechannels" style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x" ></i> Calculating Active Channels Over Time</div>
                              <div id="occupancyrate" style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x" ></i> Calculating Pore Occupancy Over Time</div>
          					<div id="poreproduction" style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Pore Read Productivity</div>
          					<div id="baseproduction" style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Pore Base Productivity</div>
          					<div id="traceproduction" style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Pore Trace Productivity</div>
          					<?php if ($_SESSION['focus_minup'] >= 0.37) {?>
          					<div id="readmuxproduction" style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Pore Trace Productivity</div>
          					<?php } ?>
          					<?php if ($_SESSION['focus_minup'] >= 0.37) {?>
          					<div id="basemuxproduction" style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Pore Base Productivity</div>
                              <div id="passfailperporemux" style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Pore Pass Fail Rates</div>
                              <div id="passfailcountperporemux" style="width:100%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Pore Pass Fail Rates</div>
          					<?php } ?>
                          <?php }else { echo "Currently pore data is oncly calculated from basecalled data. This will change in the future."; }?>
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
              renderTo: 'activechannels',
              zoomType: 'x'
              //type: 'line'
          },
          title: {
            text: 'Active Channels Over Time'
          },
          xAxis: {
              type: 'datetime',
                  dateTimeLabelFormats: { // don't display the dummy year
                          month: '%e. %b',
                          year: '%b',
                      },
                      title: {
                          text: 'Time/Date'
                      }
                  },
                  yAxis: {
                              title: {
                                  text: 'Number of Active Channels'
                              },
                              min: 0
                          },
                          credits: {
                              enabled: false
                            },
          legend: {
              title: {
            text: 'Read Type <span style="font-size: 9px; color: #666; font-weight: normal">(Click to hide)</span>',
            style: {
                fontStyle: 'italic'
            }
        },
                  layout: 'horizontal',
                                    align: 'center',
                                    verticalAlign: 'bottom',
                  borderWidth: 0
          },
          series: []
      };

      $.getJSON('jsonencode/active_channels_over_time.php?prev=1&callback=?', function(data) {
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
              renderTo: 'occupancyrate',
              zoomType: 'x'
              //type: 'line'
          },
          title: {
            text: 'Occupancy Rate'
          },
          xAxis: {
              type: 'datetime',
                  dateTimeLabelFormats: { // don't display the dummy year
                          month: '%e. %b',
                          year: '%b',
                      },
                      title: {
                          text: 'Time/Date'
                      }
                  },
                  yAxis: [{ // Primary yAxis
        labels: {
            format: '{value} %',
            style: {
                color: Highcharts.getOptions().colors[0]
            }
        },
        min: 0,
        max: 100,
        title: {
            text: 'Occupancy %',
            style: {
                color: Highcharts.getOptions().colors[0]
            }
        }
    }, { // Secondary yAxis
        title: {
            text: 'Channels',
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        },
        labels: {
            format: '{value}',
            style: {
                color: Highcharts.getOptions().colors[1]
            }
        },

        min: 0,
        opposite: true
    }],
                          credits: {
                              enabled: false
                            },
          legend: {
              title: {
            text: 'Read Type <span style="font-size: 9px; color: #666; font-weight: normal">(Click to hide)</span>',
            style: {
                fontStyle: 'italic'
            }
        },
                  layout: 'horizontal',
                                    align: 'center',
                                    verticalAlign: 'bottom',
                  borderWidth: 0
          },
          series: []
      };

      $.getJSON('jsonencode/occupancyrate.php?prev=1&callback=?', function(data) {
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
                  renderTo: 'poreproduction',
                  type: 'heatmap',
                  marginTop: 40,
                  marginBottom: 40
              },


              title: {
                  text: 'Reads Per Pore'
              },

              xAxis: {
                  title: null,
                  labels: {
                      enabled: false
                    },

              },

              yAxis: {
                  title: null,
                  labels: {
                      enabled: false
                    },

              },
              credits: {
                  enabled: false
                },
              colorAxis: {
                  min: 0,
                  minColor: '#FFFFFF',
                  maxColor: Highcharts.getOptions().colors[0]
              },

              legend: {
                  align: 'right',
                  layout: 'vertical',
                  margin: 0,
                  verticalAlign: 'top',
                  y: 25,
                  symbolHeight: 320
              },

              series: []

          };
          $.getJSON('jsonencode/readsperpore.php?prev=1&callback=?', function(data) {
              //alert("success");
              options.series = data; // <- just assign the data to the series property.



              //options.series = JSON2;
              var chart = new Highcharts.Chart(options);
          });
      });
      </script>
      <script>
      $(document).ready(function() {
          var options = {
              chart: {
                  renderTo: 'baseproduction',
                  type: 'heatmap',
                  marginTop: 40,
                  marginBottom: 40
              },


              title: {
                  text: 'Bases (Kb) Per Pore'
              },

              xAxis: {
                  title: null,
                  labels: {
                      enabled: false
                    },

              },

              yAxis: {
                  title: null,
                  labels: {
                      enabled: false
                    },

              },
              credits: {
                  enabled: false
                },
              colorAxis: {
                  min: 0,
                  minColor: '#FFFFFF',
                  maxColor: Highcharts.getOptions().colors[0]
              },

              legend: {
                  align: 'right',
                  layout: 'vertical',
                  margin: 0,
                  verticalAlign: 'top',
                  y: 25,
                  symbolHeight: 320
              },

              series: []

          };
          $.getJSON('jsonencode/basesperpore.php?prev=1&callback=?', function(data) {
              //alert("success");
              options.series = data; // <- just assign the data to the series property.



              //options.series = JSON2;
              var chart = new Highcharts.Chart(options);
          });
      });
      </script>
      <script>
      $(document).ready(function() {
          var options = {
              chart: {
                  renderTo: 'traceproduction',
                  type: 'heatmap',
                  marginTop: 40,
                  marginBottom: 40
              },


              title: {
                  text: 'Traces Per Pore'
              },

              xAxis: {
                  title: null,
                  labels: {
                      enabled: false
                    },

              },

              yAxis: {
                  title: null,
                  labels: {
                      enabled: false
                    },

              },
              credits: {
                  enabled: false
                },
              colorAxis: {
                  min: 0,
                  minColor: '#FFFFFF',
                  maxColor: Highcharts.getOptions().colors[0]
              },

              legend: {
                  align: 'right',
                  layout: 'vertical',
                  margin: 0,
                  verticalAlign: 'top',
                  y: 25,
                  symbolHeight: 320
              },

              series: []

          };
          $.getJSON('jsonencode/tracesperpore.php?prev=1&callback=?', function(data) {
              //alert("success");
              options.series = data; // <- just assign the data to the series property.



              //options.series = JSON2;
              var chart = new Highcharts.Chart(options);
          });
      });
      </script>

      <script>
      $(document).ready(function() {
          var options = {
              chart: {
                  renderTo: 'readmuxproduction',
                  type: 'heatmap',
                  marginTop: 30,
                  marginBottom: 30
              },


              title: {
                  text: 'Reads Per Pore/Mux'
              },

              xAxis: {
                  categories: [],
                  title: 'Columns',
                  labels: {
                      enabled: false
                    },

              },

              yAxis: {
                  categories: [],
                  title: 'Rows',
                  labels: {
                      enabled: false
                    },

              },
              credits: {
                  enabled: false
                },
              colorAxis: {
                  min: 0,
                  minColor: '#FFFFFF',
                  maxColor: Highcharts.getOptions().colors[0]
              },

              legend: {
                  align: 'right',
                  layout: 'vertical',
                  margin: 0,
                  verticalAlign: 'top',
                  y: 25,
                  symbolHeight: 320
              },

              series: []

          };
          $.getJSON('jsonencode/readsperporemux.php?prev=1&callback=?', function(data) {
              //alert("success");
              options.series = data; // <- just assign the data to the series property.



              //options.series = JSON2;
              var chart = new Highcharts.Chart(options);
          });
      });
      </script>

                  <script>
      $(document).ready(function() {
          var options = {
              chart: {
                  renderTo: 'basemuxproduction',
                  type: 'heatmap',
                  marginTop: 30,
                  marginBottom: 30
              },


              title: {
                  text: 'Bases Per Pore/Mux'
              },

              xAxis: {
                  categories: [],
                  title: 'Columns',
                  labels: {
                      enabled: false
                    },

              },

              yAxis: {
                  categories: [],
                  title: 'Rows',
                  labels: {
                      enabled: false
                    },

              },
              credits: {
                  enabled: false
                },
              colorAxis: {
                  min: 0,
                  minColor: '#FFFFFF',
                  maxColor: Highcharts.getOptions().colors[0]
              },

              legend: {
                  align: 'right',
                  layout: 'vertical',
                  margin: 0,
                  verticalAlign: 'top',
                  y: 25,
                  symbolHeight: 320
              },

              series: []

          };
          $.getJSON('jsonencode/basesperporemux.php?prev=1&callback=?', function(data) {
              //alert("success");
              options.series = data; // <- just assign the data to the series property.



              //options.series = JSON2;
              var chart = new Highcharts.Chart(options);
          });
      });
      </script>



        <script>
$(document).ready(function() {
var options = {
    chart: {
        renderTo: 'passfailperporemux',
        type: 'heatmap',
        marginTop: 30,
        marginBottom: 30
    },


    title: {
        text: 'Percentage pass reads per pore'
    },

    xAxis: {
        categories: [],
        title: 'Columns',
        labels: {
            enabled: false
          },

    },

    yAxis: {
        categories: [],
        title: 'Rows',
        labels: {
            enabled: false
          },

    },
    credits: {
        enabled: false
      },
    colorAxis: {
        min: 0,
        minColor: '#FFFFFF',
        maxColor: Highcharts.getOptions().colors[0]
    },

    legend: {
        align: 'right',
        layout: 'vertical',
        margin: 0,
        verticalAlign: 'top',
        y: 25,
        symbolHeight: 320
    },

    series: []

};
$.getJSON('jsonencode/passfailperporemux.php?prev=1&callback=?', function(data) {
    //alert("success");
    options.series = data; // <- just assign the data to the series property.



    //options.series = JSON2;
    var chart = new Highcharts.Chart(options);
});
});
</script>
<script>
$(document).ready(function() {
    var options = {
        chart: {
            renderTo: 'passfailcountperporemux',
            type: 'scatter',
            zoomType: 'xy'
        },
        title: {
        text: 'Percentage Pass Reads against Number of Reads Generated'
    },
    xAxis: {
        title: {
            enabled: true,
            text: 'Read Counts',
        },
        min : 0,
        startOnTick: true,
        endOnTick: true,
        showLastLabel: true
    },
    yAxis: {
        max : 100,
        min : 0,
        title: {
            text: '% Pass Reads',
        }
    },
    credits: {
        enabled: false
      },
    legend: {
        enabled: false,
    },
    plotOptions: {
        scatter: {
            marker: {
                radius: 2,
                states: {
                    hover: {
                        enabled: true,
                        lineColor: 'rgb(100,100,100)'
                    }
                }
            },
            states: {
                hover: {
                    marker: {
                        enabled: false
                    }
                }
            },
            tooltip: {
                headerFormat: '<b>Reads and Pass</b><br>',
                pointFormat: '{point.x} read, {point.y} % pass'
            }
        }
    },
        series: []

    };
    $.getJSON('jsonencode/passfailcountperporemux.php?prev=1&callback=?', function(data) {
//alert("success");
        options.series = data; // <- just assign the data to the series property.
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
