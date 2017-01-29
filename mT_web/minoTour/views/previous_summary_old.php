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
                  <li class="active"><a href="previous_summary.php">Read Summaries</a></li>
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
                  <li><a href="previous_bases.php">Base Coverage</a></li>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane active" id="tab_1">
                      <div class="panel panel-default">
          			  <div class="panel-heading">
          			    <h3 class="panel-title"><!-- Button trigger modal -->
          <button class="btn btn-info" data-toggle="modal" data-target="#modal1">
           <i class="fa fa-info-circle"></i> Reads And Coverage Summary
          </button>

          <!-- Modal -->
          <div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="myModalLabel">Reads And Coverage Summary</h4>
                </div>
                <div class="modal-body">
                  This panel provides information on the number of reads of each type generated by the metrichor analysis. The avergae read lengths and the maximum read length for each are shown.<br><br>
          		Where a reference sequence is available for mapping, the proportion of the reference covered by reads is shown as "Percentage of Reference with Read".<br><br>
          		The average depth of sequencing over these positions is shown as "Average Depth Of Sequenced Positions".<br>
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
          						<div class="col-md-6" id="readnum" style="width:25%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Read Numbers</div>
          						<div class="col-md-6" id="yield" style="width:25%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Yield</div>
          						<div class="col-md-6" id="avglen" style="width:25%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Read Average Length</div>
          						<div class="col-md-6" id="maxlen" style="width:25%; height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Read Max Length</div>
                                  <div class="col-md-12" id="boxplotlength" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Box Plots</div>
          					</div>
                              <div id="lengthtimewindow" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> Read Lengths Over Time.</div>

          				<div class="row">
          					<?php if ($_SESSION['focusreference'] != "NOREFERENCE") {?>

          								<?php foreach ($_SESSION['focusrefnames'] as $key => $value) {
          									//echo $key . " " . $value . "<br>";?>
          									<div class="col-md-6" id="percentcoverage<?php echo $key;?>" style="width:50%; height:200px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Reference Coverage for <?php echo $value;?></div>
          									<div class="col-md-6" id="depthcoverage<?php echo $key;?>" style="width:50%; height:200px;"><i class="fa fa-cog fa-spin fa-3x"></i> Calculating Reference Depth for <?php echo $value;?></div><?php
          								}
          								?>
                                          <div class="col-md-12" id="mappabletime" style="height:400px;"><i class="fa fa-cog fa-spin fa-3x"></i> 2D Reads Mapping Over Time.</div>
          							<?php }else { ?>
          															<div><p class="text-center"><small>This dataset has not been aligned to a reference sequence.</small></p></div>
          							<?php }; ?>
          				</div>
          			  </div>
          			</div>

          			<div class="panel panel-default">
          			  <div class="panel-heading">
          			    <h3 class="panel-title"><!-- Button trigger modal -->
          <button class="btn btn-info" data-toggle="modal" data-target="#modal6">
           <i class="fa fa-info-circle"></i> Run Summary</h4>
          </button>

          <!-- Modal -->
          <div class="modal fade" id="modal6" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h4 class="modal-title" id="myModalLabel"> Run Summary</h4>
                </div>
                <div class="modal-body">
          Key details on the run.<br><br>
          		  </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>
          			  </div>
          			 <div class="panel-body">
          			 	  			<?php prevrunsummary(); ?>
          			 					  </div>
          			 			</div>



                          </div>
                          <!-- /.col-lg-12 -->

                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="tab_2">
                      Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                      Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,
                      when an unknown printer took a galley of type and scrambled it to make a type specimen book.
                      It has survived not only five centuries, but also the leap into electronic typesetting,
                      remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset
                      sheets containing Lorem Ipsum passages, and more recently with desktop publishing software
                      like Aldus PageMaker including versions of Lorem Ipsum.

                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="tab_3">
                    Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                    Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,
                    when an unknown printer took a galley of type and scrambled it to make a type specimen book.
                    It has survived not only five centuries, but also the leap into electronic typesetting,
                    remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset
                    sheets containing Lorem Ipsum passages, and more recently with desktop publishing software
                    like Aldus PageMaker including versions of Lorem Ipsum.
                  </div>
                  <!-- /.tab-pane -->
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
                    type: 'boxplot',
                    renderTo: 'boxplotlength'
                },
                plotOptions: {
                    boxplot: {
                        animation: false,
                        //colorByPoint: true
                    }
                },
                title: {
                  text: 'Boxplot of Read Lengths'
                },
                legend: {
                    enabled: false
                },

                xAxis: {
                    categories: ['Template', 'Complement', '2D'],
                    title: {
                        text: 'Read Type'
                    }
                },
                yAxis: {
                    //type: 'logarithmic',
                    title: {
                        text: 'Read Length'
                    },
                    //type: 'logarithmic',
                    min :0,

                },




                series: []
            };
            $.getJSON('jsonencode/boxplotlength.php?prev=1&callback=?', function(data) {

                options.series = data; // <- just assign the data to the series property.

            var chart = new Highcharts.Chart(options);

            });
});
                //]]>

</script>


<script>
        $(document).ready(function() {
            var options = {
                chart: {
                    renderTo: 'mappabletime',
                    zoomType: 'x',
                    type: 'spline',
                },
                title: {
                  text: 'Proportion of reads mapping over time'
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
                    spline: {
                                    animation: false,
                                    marker: {
                                        enabled: false
                                    }

                },



        },
                xAxis: {
                    type: 'datetime',
                        dateTimeLabelFormats: { // don't display the dummy year
                            month: '%e. %b',
                            year: '%b'
                            },
                            title: {
                                text: 'Time/Date'
                            }
                        },
                        yAxis:
                            {

                                labels: {
                                    align: 'right',
                                    x: -3
                                },

                                title: {
                                    text: 'Proportion Reads'
                                },
                                height: '100%',
                                lineWidth: 1,
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
                    //verticalAlign: 'middle',
                    borderWidth: 0
                },
                series: []
            };
            $.getJSON('jsonencode/mappabletime.php?prev=1&callback=?', function(data) {

                options.series = data; // <- just assign the data to the series property.

            var chart = new Highcharts.Chart(options);

            });
});
                //]]>

</script>

<script>
        $(document).ready(function() {
            var options = {
                chart: {
                    renderTo: 'lengthtimewindow',
                    zoomType: 'x',
                    type: 'spline',
                },
                title: {
                  text: 'Average Read Lengths Over Time'
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
                    spline: {
                                    animation: false,
                                    marker: {
                                        enabled: false
                                    }

                },



        },
                xAxis: {
                    type: 'datetime',
                        dateTimeLabelFormats: { // don't display the dummy year
                            month: '%e. %b',
                            year: '%b'
                            },
                            title: {
                                text: 'Time/Date'
                            }
                        },
                        yAxis:
                            {

                                labels: {
                                    align: 'right',
                                    x: -3
                                },

                                title: {
                                    text: 'Average Read Length'
                                },
                                height: '100%',
                                lineWidth: 1,
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
                    //verticalAlign: 'middle',
                    borderWidth: 0
                },
                series: []
            };

            $.getJSON('jsonencode/lengthtimewindow.php?prev=1&callback=?', function(data) {

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
                        renderTo: 'readnum',
                        type: 'column',
                        //type: 'line'
                    },
                    plotOptions: {
                                column: {
                                    animation: false

                                }
                            },
                    title: {
                      text: 'Reads Called'
                    },
                    xAxis: {
                                title: {
                                    text: 'Strand'
                                }
                            },
                            yAxis: {
                                        title: {
                                            text: 'Number of Reads Called'
                                        }
                                    },
                                    credits: {
                                        enabled: false
                                      },
                    legend: {
                        layout: 'vertical',
                        align: 'center',
                        verticalAlign: 'bottom',
                        borderWidth: 0
                    },
                    series: []
                };
                    $.getJSON('jsonencode/readnumber.php?prev=1&callback=?', function(data) {
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
                            renderTo: 'avglen',
                            type: 'column'
                            //type: 'line'
                        },
                        plotOptions: {
                                    column: {
                                        animation: false

                                    }
                                },
                        title: {
                          text: 'Average Read Length'
                        },
                        xAxis: {
                                    title: {
                                        text: 'Strand'
                                    }
                                },
                                yAxis: {
                                            title: {
                                                text: 'Average Read Length'
                                            }
                                        },
                                        credits: {
                                            enabled: false
                                          },
                        legend: {
                            layout: 'vertical',
                            align: 'center',
                            verticalAlign: 'bottom',
                            borderWidth: 0
                        },
                        series: []
                    };

                        $.getJSON('jsonencode/avelen.php?prev=1&callback=?', function(data) {
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
                            renderTo: 'maxlen',
                            type: 'column',
                            //type: 'line'
                        },
                        plotOptions: {
                                    column: {
                                        animation: false

                                    }
                                },
                        title: {
                          text: 'Maximum Read Length'
                        },
                        xAxis: {
                                    title: {
                                        text: 'Strand'
                                    }
                                },
                                yAxis: {
                                            title: {
                                                text: 'Maximum Read Length'
                                            }
                                        },
                                        credits: {
                                            enabled: false
                                          },
                        legend: {
                            layout: 'vertical',
                            align: 'center',
                            verticalAlign: 'bottom',
                            borderWidth: 0
                        },
                        series: []
                    };
                            $.getJSON('jsonencode/maxlen.php?prev=1&callback=?', function(data) {
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
                        renderTo: 'yield',
                        type: 'column',
                        //type: 'line'
                    },
                    plotOptions: {
                                column: {
                                    animation: false

                                }
                            },
                    title: {
                      text: 'Yield'
                    },
                    xAxis: {
                                title: {
                                    text: 'Strand'
                                }
                            },
                            yAxis: {
                                        title: {
                                            text: 'Yield'
                                        }
                                    },
                                    credits: {
                                        enabled: false
                                      },
                    legend: {
                        layout: 'vertical',
                        align: 'center',
                        verticalAlign: 'bottom',
                        borderWidth: 0
                    },
                    series: []
                };
                    $.getJSON('jsonencode/volume.php?prev=1&callback=?', function(data) {
                                //alert("success");

                        options.series = data; // <- just assign the data to the series property.

                                 var chart = new Highcharts.Chart(options);
                                });

            });



                //]]>

                </script>
                <?php if ($_SESSION['focusreference'] != "NOREFERENCE") {?>
                <?php foreach ($_SESSION['focusrefnames'] as $key => $value) {
                    //echo $key . " " . $value . "<br>";?>


                    <script>

                                                            $(document).ready(function() {
                                                                var options = {
                                                                    chart: {
                                                                        renderTo: 'percentcoverage<?php echo $key;?>',
                                                                        type: 'bar'
                                                                        //type: 'line'
                                                                    },
                                                                    plotOptions: {
                                                                                bar: {
                                                                                    animation: false

                                                                                }
                                                                            },
                                                                    title: {
                                                                      text: 'Percentage of Reference (<?php echo $value;?>) with Read'
                                                                    },
                                                                    xAxis: {
                                                                                title: {
                                                                                    text: 'Strand'
                                                                                }
                                                                            },
                                                                            yAxis: {
                                                                                        title: {
                                                                                            text: 'Percentage'
                                                                                        },
                                                                                        max: 100
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
                                                                     $.getJSON('jsonencode/percentcoverage.php?prev=1&refid=<?php echo $key;?>&callback=?', function(data) {
                                                                    //alert("success");

                                                            options.series = data; // <- just assign the data to the series property.


                                                                    var chart = new Highcharts.Chart(options);
                                                                    });

                                                            });


                                                                //]]>

                                                                </script>
                                                                <script>

                                                                $(document).ready(function() {
                                                                    var options = {
                                                                        chart: {
                                                                            renderTo: 'depthcoverage<?php echo $key;?>',
                                                                            type: 'bar'
                                                                            //type: 'line'
                                                                        },
                                                                        plotOptions: {
                                                                                    bar: {
                                                                                        animation: false

                                                                                    }
                                                                                },
                                                                        title: {
                                                                          text: 'Average Depth of Sequenced Positions (<?php echo $value;?>)'
                                                                        },
                                                                        xAxis: {
                                                                                    title: {
                                                                                        text: 'Strand'
                                                                                    }
                                                                                },
                                                                                yAxis: {
                                                                                            title: {
                                                                                                text: 'Depth'
                                                                                            },
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

                                                             $.getJSON('jsonencode/depthcoverage.php?prev=1&refid=<?php echo $key;?>&callback=?', function(data) {

                                                                    //alert("success");

                                                            options.series = data; // <- just assign the data to the series property.


                                                                    var chart = new Highcharts.Chart(options);
                                                                    });
                                                                });

                                                                    //]]>

                                                                    </script>




                    <?php
                    }
                    ?>
                    <?php }
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
