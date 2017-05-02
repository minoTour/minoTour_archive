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
<!--<link href="css/jquery.nouislider.min.css" rel="stylesheet">-->
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
            <small> - run: <?php echo cleanname($_SESSION['focus_run_name']); ?></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-table"></i> Previous Run</a></li>
            <li><a href="#"><i class="fa fa-puzzle-piece"></i> Assembly Summary</a></li>
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


                <div id="assembly">
                <div class="panel-body">
                    <div id="demo">
                        <div is="my-component" :datain="newdata"></div>
                        <h3>Assembly Data Summary</h3>
                        <div class="row">
                        <div class='table-responsive'>
                        <table class='table table-condensed'>
                            <thead>
                        <tr>
                                <th>Assembly Number</th>
                                <th>Assembly Time</th>
                                <th>Number of Reads Used</th>
                                <th>Number of Contigs</th>
                                <th>Shortest Contig</th>
                                <th>Longest Contig</th>
                                <th>Contig N50</th>
                                <th>Total Assembly Length</th>
                                <th>Donwload Fasta Assembly</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="record in newdata">
                                    <td>{{record.timeid}}</td>
                                    <td>{{record.timeset}}</td>
                                    <td>{{record.no_reads}}</td>
                                    <td>{{record.no_contigs}}</td>
                                    <td>{{record.minlen}}</td>
                                    <td>{{record.maxlen}}</td>
                                    <td>{{record.n50}}</td>
                                    <td>{{record.totallen}}</td>
                                    <td><button v-on:click="getassembly" :id='record.timeid' type='button' class='btn btn-success btn-sm' >Get Assembly</button></td>
                              </tr>
                          </tbody>
                      </table>

                          </div>
                      </div>
                        <div class="row">
                        <div class="col-lg-6" id=""><div is="chartyield" :datain="newdata" ></div></div>
                        <div class="col-lg-6" id=""><div v-model="newdata" is="chartn50" :datain="newdata" ></div></div>
                        <div class="col-lg-6" id=""><div v-model="newdata" is="chartnumreads" :datain="newdata" ></div></div>
                        <div class="col-lg-6" id=""><div v-model="newdata" is="chartnumcontigs" :datain="newdata" ></div></div>
                        <div class="col-lg-12" id=""><div v-model="scatterdata" is="chartscatcontigs" :datain="scatterdata" ></div></div>
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

      <script>
      function timeConverter(UNIX_timestamp){
      var a = new Date(UNIX_timestamp);
      var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
      var year = a.getFullYear();
      var month = months[a.getMonth()];
      var date = a.getDate();
      var hour = a.getHours();
      var min = a.getMinutes();
      var sec = a.getSeconds();
      var time = date + ' ' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec ;
  return time;
    }



      var dataurl = "jsonencode/assemblyjson.php?prev=1&db=<?php echo $_SESSION['focusrun'];?>&callback=?";
      var dataurlalt = "jsonencode/assemblyscatjson.php?prev=1&db=<?php echo $_SESSION['focusrun'];?>&callback=?";

      Vue.component('chartyield', {
      template: '<div id="containeryield" style="margin: 0 auto"</div>',
      props: ['datain'],
      data: function() {
          //var d = new Date();
          //var t = d.getTime();
          //console.log(this.datain);
          return {
              opts: {
                  chart: {
                      renderTo: 'containeryield',
                      type:'spline',
                      zoomType: 'x',
                      height: 350,
                  },
                  title: {
                      text: 'Assembly Length Over Time'
                  },
                  xAxis: {
                  type: 'datetime',
                  tickPixelInterval: 150
              },
              yAxis: {
                  title: {
                      text: 'Bases'
                  },
                  plotLines: [{
                      value: 0,
                      width: 1,
                      color: '#808080'
                  }],
                  //min: 0,
              },
              credits: {
                  enabled: false
              },
              series: [{
                  name: 'Bases',
                  data: []
              }]
                      }
              }
      }
      ,
      ready: function() {
        this.$nextTick(function() {
              this.chart = new Highcharts.Chart(this.opts);
              var testload = function (self) {
                  var DataArray = []

                  for (var i = 0; i < self.datain.length; i++) {
                      var DataBit=[]
                      DataBit.push(parseInt(moment(self.datain[i].timeset).format('x')))
                      DataBit.push(parseInt(self.datain[i].totallen))
                      DataArray.push(DataBit)
                  }
                  self.chart.series[0].setData(DataArray);
                  self.chart.redraw();
              }
              setTimeout(function() {
                return testload(this)
            }.bind(this),500)
              setInterval(function () {
                  return testload(this)
              }.bind(this), 5000);
              });
          }
      })

      Vue.component('chartn50', {
      template: '<div id="containern50" style="margin: 0 auto"</div>',
      props: ['datain'],
      data: function() {
          //var d = new Date();
          //var t = d.getTime();
          //console.log(this.datain);
          return {
              opts: {
                  chart: {
                      renderTo: 'containern50',
                      type:'spline',
                      zoomType: 'x',
                      height: 350,
                  },
                  title: {
                      text: 'N50 Over Time'
                  },
                  xAxis: {
                  type: 'datetime',
                  tickPixelInterval: 150
              },
              yAxis: {
                  title: {
                      text: 'N50'
                  },
                  plotLines: [{
                      value: 0,
                      width: 1,
                      color: '#808080'
                  }],
                  //min: 0,
              },
              credits: {
                  enabled: false
              },
              series: [{
                  name: 'Bases',
                  data: []
              }]
                      }
              }
      }
      ,


      ready: function() {
        this.$nextTick(function() {
              this.chart = new Highcharts.Chart(this.opts);
              var testload = function (self) {
                  var DataArray = []

                  for (var i = 0; i < self.datain.length; i++) {
                      var DataBit=[]
                      DataBit.push(parseInt(moment(self.datain[i].timeset).format('x')))
                      DataBit.push(parseInt(self.datain[i].n50))
                      DataArray.push(DataBit)
                  }
                  self.chart.series[0].setData(DataArray);
                  self.chart.redraw();
              }
              setTimeout(function() {
                return testload(this)
              }.bind(this),500)
              setInterval(function () {
                  return testload(this)
              }.bind(this), 5000);
              });
          }
      })

      Vue.component('chartnumreads', {
      template: '<div id="containernumreads" style="margin: 0 auto"</div>',
      props: ['datain'],
      data: function() {
          //var d = new Date();
          //var t = d.getTime();
          //console.log(this.datain);
          return {
              opts: {
                  chart: {
                      renderTo: 'containernumreads',
                      type:'spline',
                      zoomType: 'x',
                      height: 350,
                  },
                  title: {
                      text: 'Number of Reads Used for Each Assembly'
                  },
                  xAxis: {
                  type: 'datetime',
                  tickPixelInterval: 150
              },
              yAxis: {
                  title: {
                      text: 'Number of Reads'
                  },
                  plotLines: [{
                      value: 0,
                      width: 1,
                      color: '#808080'
                  }],
                  //min: 0,
              },
              credits: {
                  enabled: false
              },
              series: [{
                  name: 'Read Number',
                  data: []
              }]
                      }
              }
      }
      ,


      ready: function() {
        this.$nextTick(function() {
              this.chart = new Highcharts.Chart(this.opts);
              var testload = function (self) {
                  var DataArray = []

                  for (var i = 0; i < self.datain.length; i++) {
                      var DataBit=[]
                      DataBit.push(parseInt(moment(self.datain[i].timeset).format('x')))
                      DataBit.push(parseInt(self.datain[i].no_reads))
                      DataArray.push(DataBit)
                  }
                  self.chart.series[0].setData(DataArray);
                  self.chart.redraw();
              }
              setTimeout(function() {
                return testload(this)
              }.bind(this),500)
              setInterval(function () {
                  return testload(this)
              }.bind(this), 5000);
              });

          }
      })

      Vue.component('chartnumcontigs', {
      template: '<div id="containernumcontigs" style="margin: 0 auto"</div>',
      props: ['datain'],
      data: function() {
          //var d = new Date();
          //var t = d.getTime();
          //console.log(this.datain);
          return {
              opts: {
                  chart: {
                      renderTo: 'containernumcontigs',
                      type:'spline',
                      zoomType: 'x',
                      height: 350,
                  },
                  title: {
                      text: 'Number of Contigs Generated'
                  },
                  xAxis: {
                  type: 'datetime',
                  tickPixelInterval: 150
              },
              yAxis: {
                  title: {
                      text: 'Number of Contigs'
                  },
                  plotLines: [{
                      value: 0,
                      width: 1,
                      color: '#808080'
                  }],
                  //min: 0,
              },
              credits: {
                  enabled: false
              },
              series: [{
                  name: 'Contig Number',
                  data: []
              }]
                      }
              }
      }
      ,


      ready: function() {
        this.$nextTick(function() {
              this.chart = new Highcharts.Chart(this.opts);
              var testload = function (self) {
                  var DataArray = []

                  for (var i = 0; i < self.datain.length; i++) {
                      var DataBit=[]
                      DataBit.push(parseInt(moment(self.datain[i].timeset).format('x')))
                      DataBit.push(parseInt(self.datain[i].no_contigs))
                      DataArray.push(DataBit)
                  }
                  self.chart.series[0].setData(DataArray);
                  self.chart.redraw();
              }
              setTimeout(function() {
                return testload(this)
              }.bind(this),500)
              setInterval(function () {
                  return testload(this)
              }.bind(this), 5000);
              });
          }
      })

      Vue.component('chartscatcontigs', {
      template: '<div id="chartscatcontigs" style="margin: 0 auto"</div>',
      props: ['datain'],
      data: function() {
          //var d = new Date();
          //var t = d.getTime();
          //console.log(this.datain);
          return {
              opts: {
                  chart: {
                      renderTo: 'chartscatcontigs',
                      type:'scatter',
                      zoomType: 'x',
                      height: 450,
                  },
                  title: {
                      text: 'Lengths of Contigs Generated'
                  },
                  xAxis: {
                  type: 'datetime',
                  tickPixelInterval: 150
              },
              yAxis: {
                  title: {
                      text: 'Length of Contigs'
                  },
                  type: 'logarithmic',
                  plotLines: [{
                      value: 0,
                      width: 1,
                      color: '#808080'
                  }],
                  //min: 0,
              },
              tooltip: {
                formatter: function() {
                    return 'Contig is <b>' + this.y + 'bp</b> at <b>' + timeConverter(this.x) + '</b>'
                }
            },
              credits: {
                  enabled: false
              },
              series: [{
                  name: 'Bases',
                  data: []
              }]
                      }
              }
      }
      ,


      ready: function() {
         // console.log("ready running")
        this.$nextTick(function() {
              this.chart = new Highcharts.Chart(this.opts);
              var testload = function (self) {
                  var DataArray = []

                  for (var i = 0; i < self.datain.length; i++) {
                      var DataBit=[]
                      DataBit.push(parseInt(moment(self.datain[i].timeset).format('x')))
                      DataBit.push(parseInt(self.datain[i].length))
                      DataArray.push(DataBit)
                  }
                  self.chart.series[0].setData(DataArray);
                  self.chart.redraw();
              }
              setTimeout(function() {
                return testload(this)
              }.bind(this),500)
              setInterval(function () {
                  return testload(this)
              }.bind(this), 5000);
              });
          }
      })

      var demo = new Vue({

      el: '#demo',

      data: {
        newdata: null,
        scatterdata: null,
      },

      created: function () {
        this.fetchData()
        this.fetchData2()
      },

     filters: {
        truncate: function (v) {
          var newline = v.indexOf('\n')
          return newline > 0 ? v.slice(0, newline) : v
        },
        formatDate: function (v) {
          return v.replace(/T|Z/g, ' ')
        }
      },

      methods: {
        fetchData: function () {
            //console.log('fetchdata called')
            var xhr = new XMLHttpRequest()
            var self = this
            xhr.open('GET', dataurl)
            xhr.onload = function () {
              self.newdata = JSON.parse(xhr.responseText)
              //console.log(xhr.responseText)
              //console.log(self.newdata)
            }
            xhr.send()

            setInterval(function () {
              var xhr = new XMLHttpRequest()
              var self = this
              xhr.open('GET', dataurl)
              xhr.onload = function () {
                self.newdata = JSON.parse(xhr.responseText)
                //console.log(xhr.responseText)
                //console.log(self.newdata)
              }
              xhr.send()

          }.bind(this), 10000);

      },
      fetchData2: function () {
          //console.log('fetchdata called')
          var xhr = new XMLHttpRequest()
          var self = this
          xhr.open('GET', dataurlalt)
          xhr.onload = function () {
            self.scatterdata = JSON.parse(xhr.responseText)
            //console.log(xhr.responseText)
            //console.log(self.newdata)
          }
          xhr.send()

          setInterval(function () {
            var xhr = new XMLHttpRequest()
            var self = this
            xhr.open('GET', dataurlalt)
            xhr.onload = function () {
              self.scatterdata = JSON.parse(xhr.responseText)
              //console.log(xhr.responseText)
              //console.log(self.newdata)
            }
            xhr.send()

        }.bind(this), 10000);

    },
      getassembly: function(event){
          //alert(event.target.id);
          var sequrl = "includes/fetchreads.php?db=<?php echo $_SESSION['focusrun']?>&type=assembly&timeid="+event.target.id+"&prev=1";
          window.open(sequrl);
      },

      }
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
