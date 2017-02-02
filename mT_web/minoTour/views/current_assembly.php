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
            Assembly Summary
            <small> - run: <?php echo cleanname($_SESSION['active_run_name']); ?></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-table"></i> Current Run</a></li>
            <li><a href="#"><i class="fa fa-puzzle-piece"></i> Assembly Summary</a></li>
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
                <div class="panel panel-default">
                <div class="panel-heading">
                  <h3 class="panel-title"><!-- Button trigger modal -->
    <button class="btn btn-info  btn-sm" data-toggle="modal" data-target="#modalassembly">
    <i class="fa fa-info-circle"></i> Assembly Summary
    </button>

    <!-- Modal -->
    <div class="modal fade" id="modalassembly" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close  btn-sm" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Assembly Summary</h4>
        </div>
        <div class="modal-body">
          minoTour can be used to trigger a basic minmap/miniasm assembly pipeline. The results of which are presented here.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default  btn-sm" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
    </div>
                </div>
                <div id="assmbly">
                <div class="panel-body">
                          <div class="row">


                      </div>

                </div>
              </div>

          </div>




    


    <br>
      <!-- /.col-lg-12 -->
    </div>
    </div>
    </div>
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

      <?php include 'includes/reporting-new.php'; ?>
      <?php $arr = array("template", "complement", "2d");?>
  					<?php foreach ($arr as $key => $value) {
  						//echo $key . " " . $value . "<br>";?>

  			<script>
  		$(document).ready(function() {
  		    var options = {
  		        chart: {
  		            renderTo: 'barcodwimm<?php echo $key;?>',
  					zoomType: 'x',
  		            type: 'area',
  		        },
  		        title: {
  		          text: '<?php echo $value;?> Barcode Plot'
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
  				                text: 'Time (S)'
  				            }
  				        },
  						yAxis: [{
  				                labels: {
              				        align: 'right',
              	    			    x: -3
              	   				},
              	    			title: {
              	        			text: '<?php echo $value;?>'
  				                },
  				                height: '100%',
  				                lineWidth: 1
  				            }],
  								credits: {
  								    enabled: false
  								  },
  		        legend: {
  		        	title: {
                  text: 'Barcode<br/><span style="font-size: 9px; color: #666; font-weight: normal">(Click to hide)</span>',
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

  		    $.getJSON('jsonencode/barcodwimm.php?prev=0&type=<?php echo $value; ?>&callback=?', function(data) {
  				//alert("success");
  		        options.series = data; // <- just assign the data to the series property.



  		        //options.series = JSON2;
  				var chart = new Highcharts.Chart(options);
  				});
  		});

  			//]]>

  			</script>
  		<?php } ?>

        <!-- Detailed Barcode Coverage Plots -->

    <script>
        $(document).ready(function() {
            chartsetup = {
                chart: {
        		   	renderTo: 'barcodcovdet',
        			//zoomType: 'x',
        			type: 'scatter',
        		   	//type: 'line'
        		},
        		title: {
        		    text: 'Coverage Depth By Barcode',
    	        },
    	        xAxis: {
    				title: {
    					text: 'Position (bp)'
    			    },
    		        //min: tmin,
        			//max: tmax,
        		},
        		yAxis: [
                    {
        		        labels: {
                            //align: 'right',
        	                //x: -3
                	    },
                	    title: {
                	           text: 'Barcode 1'
        			    },
        			    height: '7%',
        			    lineWidth: 1
        		    },
                    {
        		        labels: {
                            align: 'right',
        	                //x: -3
                	    },
                	    title: {
                	           text: 'Barcode 2'
        			    },
                        top: '8%',
                        offset: 0,
        			    height: '7%',
        			    lineWidth: 1
        		    },{
        		        labels: {
                            align: 'right',
        	                //x: -3
                	    },
                	    title: {
                	           text: 'Barcode 3'
        			    },
                        top: '16%',
                        offset: 0,
        			    height: '7%',
        			    lineWidth: 1
        		    },{
        		        labels: {
                            align: 'right',
        	                //x: -3
                	    },
                	    title: {
                	           text: 'Barcode 4'
        			    },
                        top: '24%',
                        offset: 0,
        			    height: '7%',
        			    lineWidth: 1
        		    },{
        		        labels: {
                            align: 'right',
        	                //x: -3
                	    },
                	    title: {
                	           text: 'Barcode 5'
        			    },
                        top: '32%',
                        offset: 0,
        			    height: '7%',
        			    lineWidth: 1
        		    },{
        		        labels: {
                            align: 'right',

        	                //x: -3
                	    },
                	    title: {
                	           text: 'Barcode 6',

        			    },
                        top: '40%',
                        offset: 0,
        			    height: '7%',
        			    lineWidth: 1
        		    },{
        		        labels: {
                            align: 'right',
        	                //x: -3
                	    },
                	    title: {
                	           text: 'Barcode 7'
        			    },
                        top: '48%',
                        offset: 0,
        			    height: '7%',
        			    lineWidth: 1
        		    },{
        		        labels: {
                            align: 'right',
        	                //x: -3
                	    },
                	    title: {
                	           text: 'Barcode 8'
        			    },
                        top: '56%',
                        offset: 0,
        			    height: '7%',
        			    lineWidth: 1
        		    },{
        		        labels: {
                            align: 'right',
        	                //x: -3
                	    },
                	    title: {
                	           text: 'Barcode 9'
        			    },
                        top: '64%',
                        offset: 0,
        			    height: '7%',
        			    lineWidth: 1
        		    },{
        		        labels: {
                            align: 'right',
        	                //x: -3
                	    },
                	    title: {
                	           text: 'Barcode 10'
        			    },
                        top: '72%',
                        offset: 0,
        			    height: '7%',
        			    lineWidth: 1
        		    },{
        		        labels: {
                            align: 'right',
        	                //x: -3
                	    },
                	    title: {
                	           text: 'Barcode 11'
        			    },
                        top: '80%',
                        offset: 0,
        			    height: '7%',
        			    lineWidth: 1
        		    },{
        		        labels: {
                            align: 'right',
        	                //x: -3
                	    },
                	    title: {
                	           text: 'Barcode 12'
        			    },
                        top: '88%',
                        offset: 0,
        			    height: '7%',
        			    lineWidth: 1
        		    },
            	],
        		scrollbar: {
              	    enabled: false
            	},
            	navigator: {
         	  	    enabled: true
            	},
        		plotOptions: {

        		line: {
        			marker: {
        		 		enabled: false
        		 	}
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
                $.getJSON('jsonencode/coverage_barcodes.php?prev=0&start=-1&end=-1&seqid=1&callback=?', function(data){
    				//alert(data);
                    chartsetup.series = data;
    			    var chart = new Highcharts.Chart(chartsetup);
    			});
                //chartsetup.series = data;
    			//var chart = new Highcharts.Chart(chartsetup);
        	});

    </script>

  	<!-- Barcode Coverage Information -->

  <script>

                              $(document).ready(function() {
                                  var options = {
                                      chart: {
                                          renderTo: 'barcodcov',
                                          type: 'column',
                                          //type: 'line'
                                      },
                                      plotOptions: {
                                      	column: {
                                          	animation: false,
  										    //colorByPoint: true
                                          }
                                      },
                                      //colors: [
  								      //  '#4A6D8E',
  								       // '#7cb5ec',
  								       // '#A3CBF2',
  								       // '#CBE1F7',
  								    //],
                                      title: {
                                        text: 'Coverage Depth'
                                      },
                                      xAxis: {
                                                  title: {
                                                      text: 'Barcodes'
                                                  },
                                                  labels: {
  						            	enabled:true,
  						            	},
  						            	categories: [

  									                ]


                                              },
                                              yAxis: {
                                                          title: {
                                                              text: 'Barcode Coverage Depth'
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
                                  $.getJSON('jsonencode/barcodingcov.php?prev=0&callback=?', function(data) {
  					                //alert("success");
                                    options.xAxis.categories = data[0]['data'];
                                    options.series = data.slice(1,);
  					        //options.series = data; // <- just assign the data to the series property.

  					        //options.series = JSON2;
  					                var chart = new Highcharts.Chart(options);
  					                });

  				});




                                  //]]>

                                  </script>



  <!-- Barcode Information -->

  <script>
  $(document).ready(function() {
  			    var options = {
  			        chart: {
  						renderTo: 'barcod',
  			            type: 'pie',
  			            marginTop: 30,
  			            marginBottom: 30
  			        },


  			        title: {
  			            text: 'Barcoding Proportions'
  			        },

  					credits: {
  					    enabled: false
  					  },
  	            plotOptions: {
              pie: {
              	animation: false,
                  allowPointSelect: false,
                  cursor: 'pointer',
                  dataLabels: {
                      enabled: true,
                      format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                      style: {
                          color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                      }
                  }
              }
          },
  				    series: []

  			    };
  			    $.getJSON('jsonencode/barcodingpie.php?prev=0&callback=?', function(data) {
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
