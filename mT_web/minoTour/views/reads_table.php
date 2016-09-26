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
            <h1>Reads Summary <small> - run: <?php echo cleanname($_SESSION['focusrun']);; ?></small></h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-edit"></i> External Links</a></li>
            <li class="active">Here</li>
          </ol>

        </section>

        <!-- Main content -->
        <section class="content"><?php include 'includes/run_check.php';?>
            

                <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Read Data</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">

                            <table id="example" class="display table table-condensed table-hover " cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                           <th>Channel</th>
                                           <th>Read</th>
                                        <th>Basename</th>
                                        <th>Template</th>
                                        <th>T Aligns</th>
                                        <th>T Length</th>
                                        <th>T Start</th>
                                        <th>T Duration</th>
                                        <th>Complement</th>
                                        <th>C Aligns</th>
                                        <th>C Length</th>
                                        <th>C Start</th>
                                        <th>C Duration</th>
                                        <th>2d</th>
                                        <th>2d Aligns</th>
                                        <th>2d Length</th>
                                    </tr>
                                </thead>

                                <tfoot>
                                    <tr>
                                           <th>Channel</th>
                                           <th>Read</th>
                                        <th>Basename</th>
                                        <th>Template</th>
                                        <th>T Aligns</th>
                                        <th>T Length</th>
                                        <th>T Start</th>
                                        <th>T Duration</th>
                                        <th>Complement</th>
                                        <th>C Aligns</th>
                                        <th>C Length</th>
                                        <th>C Start</th>
                                        <th>C Duration</th>
                                        <th>2d</th>
                                        <th>2d Aligns</th>
                                        <th>2d Length</th>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-lg-12">
                    <?php if ($_SESSION['focustelem'] >= 1) {?>
                    <h4>Viewer options</h4>
                    <input type='checkbox' id='toggle-two' data-onstyle='primary' data-offstyle='info' data-size='mini'>If ticked you will see data in base order.
               <script>
                     $(function() {
                       $('#toggle-two').bootstrapToggle({
                         on: 'Event View',
                         off: 'Time View'
                       });
                     })

                   </script>
                   <?php } ?>
                        <div id = "read_details">Click on a read from the table above to view specific details.</div>
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
              "columnDefs": [
                  { "visible": false, "targets": 2 }
                ],
              "sDom": '<"top"ilp>rt<"bottom"><"clear">',
              "processing": true,
              "serverSide": true,
              "sAjaxSource": "data_tables/data_table2.php?prev=1"
          } );
          oTable = $('#example').dataTable( );
          $('#example tbody').on('click', 'tr', function () {
                  var nameindex = $('td', this).eq(0).text();
                  var name = oTable.fnGetData(this,2);
              var channel = oTable.fnGetData(this,0).slice(-1);;
                  //alert(name);
                  var templength = $('td', this).eq(4).text();
                  var complength = $('td', this).eq(9).text();
                  var length2d = $('td', this).eq(14).text();

                  if (templength >= 1000 || complength >= 1000 || length2d >= 1000) {
                      var midpoint = templength/2;
                      var xmin = midpoint-250;
                      var xmax = midpoint+250;
                  }else{
                      var xmin;
                      var xmax;
                  }

                  var tstart = parseFloat($('td',this).eq(5).text());
                  var tdur = parseFloat($('td',this).eq(6).text());

                  if (tdur >= 15) {
                      //alert (tdur);
                      var mod = 5;
                      var tmin = tstart+(tdur/2)-mod;
                      var tmax = tstart+(tdur/2)+mod;
                      //alert (tmax);
                  }else {
                      var tmin;
                      var tmax;
                  }


                  var compstart = parseFloat($('td',this).eq(10).text());
                  var compdur = parseFloat($('td',this).eq(11).text());
                  //alert (compdur);
                  if (compdur >= 15) {
                      var mod = 5;
                      var cmin = compstart+(compdur/2)-mod;
                      var cmax = compstart+(compdur/2)+mod;
                  }else {
                      var cmin;
                      var cmax;
                  }

                  var ttrue = $('td',this).eq(2).text();
                  var ctrue = $('td',this).eq(7).text();
                      var d2true = $('td',this).eq(12).text();


                  $.post( "views/live_read_details.php?prev=1", { readname: name })
                    .done(function( data ) {
                        //alert('badger');
                      $("#read_details").html(data);
                      var optionssteve = {
                          chart: {
                              renderTo: 'allqualities',
                              zoomType: 'x',
                              //type: 'scatter',
                              type: 'line'
                          },
                          title: {
                            text: 'Read Qualities'
                          },
                          xAxis: {
                              title: {
                                  text: 'Basepairs'
                              },
                              min: xmin,
                              max: xmax,
                          },
                          yAxis: {
                              title: {
                                  text: 'Quality Score'
                              }
                          },
                          scrollbar: {
                                    enabled: false
                          },
                          navigator: {
                            enabled: true
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

                      var options = {
                          chart: {
                              renderTo: 'templatesquiggles',
                              zoomType: 'x',
                              //type: 'scatter',
                              type: 'line'
                          },
                          title: {
                            text: 'Template Squiggle Plot'
                          },
                          xAxis: {
                              title: {
                                  text: 'Time (s)'
                              },
                              min: tmin,
                              max: tmax,
                          },
                          yAxis: {
                              title: {
                                  text: 'Current'
                              }
                          },
                          navigator: {
                            enabled: true
                          },
                          scrollbar: {
                                    enabled: false
                          },
                          plotOptions: {
                              scatter: {
                                 marker: {
                                     radius: 1
                                 }
                               },
                               series: {
                                      lineWidth: 1
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

                      var optionscomplement = {
                          chart: {
                              renderTo: 'complementsquiggles',
                              zoomType: 'x',
                              //type: 'scatter',
                              type: 'line'
                          },
                          title: {
                            text: 'Complement Squiggle Plot'
                          },
                          xAxis: {
                              title: {
                                  text: 'Time (s)'
                              },
                              min: cmin,
                              max: cmax,
                          },
                          yAxis: {
                              title: {
                                  text: 'Current'
                              }
                          },
                          scrollbar: {
                                    enabled: false
                          },
                          navigator: {
                            enabled: true
                          },
                          plotOptions: {
                              scatter: {
                                 marker: {
                                     radius: 1
                                 }
                               },
                               series: {
                                      lineWidth: 1
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
                      var groupingUnits = [[
                              'millisecond',                         // unit name
                                  [1, 2, 5, 10, 20, 25, 50, 100, 200, 500]                             // allowed multiples
                              ], [
                              'second',
                                  [1, 2, 5, 10, 15, 30]
                              ]];

                      var newdata;
                      if ($('#toggle-two').prop('checked') == true) {
                          newdata = {
                           rangeSelector: {
                              selected: 1
                          },
                          chart: {
                              renderTo: 'templatefancy',
                              zoomType: 'x',
                              //type: 'scatter',
                              type: 'line'
                          },
                          title: {
                            text: 'Template Combined Squiggle Quality Probability and Called Base Plot',

                          },
                          xAxis: {
                              title: {
                                  text: 'Bases'
                              },
                              min: xmin,
                              max: xmax,
                          },
                           yAxis: [{
                              labels: {
                                  align: 'right',
                                  x: -3
                              },
                              title: {
                                  text: 'Squiggle'
                              },
                              height: '28%',
                              lineWidth: 1
                          }, {
                              labels: {
                                  align: 'right',
                                     x: -3
                                  },
                              title: {
                                  text: 'Quality'
                              },
                              top: '30%',
                              height: '28%',
                              offset: 0,
                              lineWidth: 1
                          }, {
                              labels: {
                                  align: 'right',
                                     x: -3
                                  },
                              title: {
                                  text: 'Base Probabilities'
                              },
                              top: '60%',
                              height: '28%',
                              offset: 0,
                              lineWidth: 1,
                              min:0
                          }, {
                              labels: {
                                  align: 'right',
                                     x: -3
                                  },
                              title: {
                                  text: 'Bases'
                              },
                              max: 1,
                              min: 0,
                              top: '90%',
                              height: '10%',
                              offset: 0,
                              lineWidth: 1
                          }],
                          scrollbar: {
                                    enabled: false
                          },
                          navigator: {
                            enabled: true
                          },
                          plotOptions: {
                              scatter: {
                                 marker: {
                                     enabled: false
                                 }
                               },
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


                      }else{
                          newdata = {
                           rangeSelector: {
                              selected: 1
                          },
                          chart: {
                              renderTo: 'templatefancy',
                              zoomType: 'x',
                              //type: 'scatter',
                              type: 'line'
                          },
                          title: {
                            text: 'Template Combined Squiggle Quality Probability and Called Base Plot',

                          },
                          xAxis: {
                              title: {
                                  text: 'Time (s)'
                              },
                              min: tmin,
                              max: tmax,
                          },
                           yAxis: [{
                              labels: {
                                  align: 'right',
                                  x: -3
                              },
                              title: {
                                  text: 'Squiggle'
                              },
                              height: '28%',
                              lineWidth: 1
                          }, {
                              labels: {
                                  align: 'right',
                                     x: -3
                                  },
                              title: {
                                  text: 'Quality'
                              },
                              top: '30%',
                              height: '28%',
                              offset: 0,
                              lineWidth: 1
                          }, {
                              labels: {
                                  align: 'right',
                                     x: -3
                                  },
                              title: {
                                  text: 'Base Probabilities'
                              },
                              top: '60%',
                              height: '28%',
                              offset: 0,
                              lineWidth: 1,
                              min:0
                          }, {
                              labels: {
                                  align: 'right',
                                     x: -3
                                  },
                              title: {
                                  text: 'Bases'
                              },
                              max: 1,
                              min: 0,
                              top: '90%',
                              height: '10%',
                              offset: 0,
                              lineWidth: 1
                          }],
                          scrollbar: {
                                    enabled: false
                          },
                          navigator: {
                            enabled: true
                          },
                          plotOptions: {
                              scatter: {
                                 marker: {
                                     enabled: false
                                 }
                               },
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
                      }
                      var newdata2;
                      if ($('#toggle-two').prop('checked') == true) {
                          newdata2 = {
                           rangeSelector: {
                              selected: 1
                          },
                          chart: {
                              renderTo: 'complementfancy',
                              zoomType: 'x',
                              //type: 'scatter',
                              type: 'line'
                          },
                          title: {
                            text: 'Complement Combi - Squiggle/Quality/Probability/Called Bases',

                          },
                          xAxis: {
                              title: {
                                  text: 'Bases'
                              },
                              min: xmin,
                              max: xmax,
                          },
                           yAxis: [{
                              labels: {
                                  align: 'right',
                                  x: -3
                              },
                              title: {
                                  text: 'Squiggle'
                              },
                              height: '28%',
                              lineWidth: 1
                          }, {
                              labels: {
                                  align: 'right',
                                     x: -3
                                  },
                              title: {
                                  text: 'Quality'
                              },
                              top: '30%',
                              height: '28%',
                              offset: 0,
                              lineWidth: 1
                          }, {
                              labels: {
                                  align: 'right',
                                     x: -3
                                  },
                              title: {
                                  text: 'Base Probabilities'
                              },
                              top: '60%',
                              height: '28%',
                              offset: 0,
                              lineWidth: 1,
                              min:0
                          }, {
                              labels: {
                                  align: 'right',
                                     x: -3
                                  },
                              title: {
                                  text: 'Bases'
                              },
                              max: 1,
                              min: 0,
                              top: '90%',
                              height: '10%',
                              offset: 0,
                              lineWidth: 1
                          }],
                          scrollbar: {
                                    enabled: false
                          },
                          navigator: {
                            enabled: true
                          },
                          plotOptions: {
                              scatter: {
                                 marker: {
                                     enabled: false
                                 }
                               },
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
                      }else{
                          newdata2 = {
                           rangeSelector: {
                              selected: 1
                          },
                          chart: {
                              renderTo: 'complementfancy',
                              zoomType: 'x',
                              //type: 'scatter',
                              type: 'line'
                          },
                          title: {
                            text: 'Complement Combi - Squiggle/Quality/Probability/Called Bases',

                          },
                          xAxis: {
                              title: {
                                  text: 'Time (s)'
                              },
                              min: cmin,
                              max: cmax,
                          },
                           yAxis: [{
                              labels: {
                                  align: 'right',
                                  x: -3
                              },
                              title: {
                                  text: 'Squiggle'
                              },
                              height: '28%',
                              lineWidth: 1
                          }, {
                              labels: {
                                  align: 'right',
                                     x: -3
                                  },
                              title: {
                                  text: 'Quality'
                              },
                              top: '30%',
                              height: '28%',
                              offset: 0,
                              lineWidth: 1
                          }, {
                              labels: {
                                  align: 'right',
                                     x: -3
                                  },
                              title: {
                                  text: 'Base Probabilities'
                              },
                              top: '60%',
                              height: '28%',
                              offset: 0,
                              lineWidth: 1,
                              min:0
                          }, {
                              labels: {
                                  align: 'right',
                                     x: -3
                                  },
                              title: {
                                  text: 'Bases'
                              },
                              max: 1,
                              min: 0,
                              top: '90%',
                              height: '10%',
                              offset: 0,
                              lineWidth: 1
                          }],
                          scrollbar: {
                                    enabled: false
                          },
                          navigator: {
                            enabled: true
                          },
                          plotOptions: {
                              scatter: {
                                 marker: {
                                     enabled: false
                                 }
                               },
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
                      }


                      //if 	(ttrue == 'Y') {
                      //$.getJSON('jsonencode/allqualities.php?prev=0&readname='+name+'&callback=?', function(data) {
                          //optionssteve.series = data; // <- just assign the data to the series property.
                      //	var chart = new Highcharts.Chart(optionssteve);
                      //});
                      //}else{
                      //	$( '#allqualities' ).remove();
                      //}
                      if 	(ttrue == 'Y') {
                          if ($('#toggle-two').prop('checked') == true) {
                              $.getJSON('jsonencode/squiggles3.php?channel='+channel+'&prev=1&readname='+name+'&type=template&callback=?', function(data){
                                  newdata.series = data;
                                  var chart = new Highcharts.Chart(newdata);
                              });
                          }else{
                              $.getJSON('jsonencode/squiggles2.php?channel='+channel+'&prev=1&readname='+name+'&type=template&callback=?', function(data){
                                  newdata.series = data;
                                  var chart = new Highcharts.Chart(newdata);
                              });
                          }
                      }else{
                          $( '#templatefancy' ).remove();
                      }
                      if (ctrue == 'Y') {
                          if ($('#toggle-two').prop('checked') == true) {
                              $.getJSON('jsonencode/squiggles3.php?channel='+channel+'&prev=1&readname='+name+'&type=complement&callback=?', function(data){
                                  newdata2.series = data;
                                  var chart = new Highcharts.Chart(newdata2);
                              });
                          }else{
                              $.getJSON('jsonencode/squiggles2.php?channel='+channel+'&prev=1&readname='+name+'&type=complement&callback=?', function(data){
                                  newdata2.series = data;
                                  var chart = new Highcharts.Chart(newdata2);
                              });
                          }
                      }else{
                          $( '#complementfancy' ).remove();
                      }

                      $('html, body').animate({
                                 'scrollTop':   $('#'+name).offset().top
                      }, 1000);

                  });
              } );
              $(function() {
                   $('#toggle-two').change(function() {
                      var checker = $(this).prop('checked');
                      if (checker == true) {
                          //$.getJSON('jsonencode/squiggles3.php?prev=0&readname='+name+'&type=template&callback=?', function(data){

                          //		var chart = $('#templatefancy').highcharts();
                                  //chart.destroy();
                          //		chart.series = data;
                                  //var chart = new Highcharts.Chart(newdata);
                          //		chart.redraw();
                          //});
                          //$.getJSON('jsonencode/squiggles3.php?prev=0&readname='+name+'&type=complement&callback=?', function(data){
                          //		newdata2.series = data;
                          //		var chart = new Highcharts.Chart(newdata2);
                          //});

                      }
                      if (checker == false) {
                          //$.getJSON('jsonencode/squiggles2.php?prev=0&readname='+name+'&type=template&callback=?', function(data){
                          //		newdata.series = data;
                          //		var chart = new Highcharts.Chart(newdata);
                          //});
                          //$.getJSON('jsonencode/squiggles3.php?prev=0&readname='+name+'&type=complement&callback=?', function(data){
                          //		newdata2.series = data;
                          //		var chart = new Highcharts.Chart(newdata2);
                          //});
                      }
                  })
                });

      } );

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
