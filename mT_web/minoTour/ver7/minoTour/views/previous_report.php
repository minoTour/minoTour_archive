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
            <h1>Run Report: <small> - run: <?php echo cleanname($_SESSION['focusrun']);; ?></small></h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-edit"></i> External Links</a></li>
            <li class="active">Here</li>
          </ol>

        </section>

        <!-- Main content -->
        <section class="content"><?php include 'includes/run_check.php';?>
            

                <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Run Reports and Data</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">

                            <div class="row">
                                <div class="col-lg-12">
                                <h4>Basic Run Information</h4>
                                <br>


                                <?php
                                date_default_timezone_set('UTC');
                                $mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['focusrun']);
                                if (!$mindb_connection->connect_errno) {
                                    //Query to generate basic run report:

                                    $basicrunsql = "SELECT asic_id,AVG(asic_temp) as asic_temp_avg,std(asic_temp) as asic_temp_std,exp_script_purpose,exp_start_time,flow_cell_id,AVG(heatsink_temp) as heatsink_temp_avg,std(heatsink_temp) as heatsink_temp_std,run_id,version_name FROM tracking_id group by device_id,flow_cell_id,asic_id;";
                                    $basicrunresults = $mindb_connection->query($basicrunsql);
                                    echo "<div class='panel panel-default'>";
                                    echo "<div class='panel-heading'>";
                                    echo "<h5>MinKNOW run reporting:</h5>";
                                    echo "</div>";
                                    if ($basicrunresults->num_rows == 1){
                                        echo "<div class='panel-body'>";
                                        $basicrunresults_row = $basicrunresults->fetch_object();
                                        echo "<table class=\"table table-condensed\">";
                                        echo "<thead>";
                                        echo "<tr>";
                                        echo "<th>Parameter</th>";
                                        echo "<th>Value</th>";
                                        echo "</tr>";
                                        echo "</thead>";
                                        echo "<tbody>";
                                        echo "<tr>";
                                        echo "<td>Experiment Script Purpose</td>";
                                        echo "<td>" . $basicrunresults_row->exp_script_purpose . "</td>";
                                        echo "</tr>";
                                        echo "<tr>";
                                        echo "<td>Experiment Start Date/Time</td>";
                                        echo "<td>" . gmdate("H:i:s Y-m-d", $basicrunresults_row->exp_start_time) . "</td>";
                                        echo "</tr>";
                                        echo "<tr>";
                                        echo "<td>ASIC ID</td>";
                                        echo "<td>" . $basicrunresults_row->asic_id . "</td>";
                                        echo "</tr>";
                                        echo "<tr>";
                                        echo "<td>Average ASIC Temp (stand var)</td>";
                                        echo "<td>" . $basicrunresults_row->asic_temp_avg . " (" . $basicrunresults_row->asic_temp_std . ")</td>";
                                        echo "</tr>";
                                        echo "<tr>";
                                        echo "<td>Average Heatsink Temp (stand var)</td>";
                                        echo "<td>" . $basicrunresults_row->heatsink_temp_avg . " (" . $basicrunresults_row->heatsink_temp_std . ")</td>";
                                        echo "</tr>";
                                        echo "<tr>";
                                        echo "<td>Run ID</td>";
                                        echo "<td>" . $basicrunresults_row->run_id .  "</td>";
                                        echo "</tr>";
                                        echo "<tr>";
                                        echo "<td>MinKNOW Version</td>";
                                        echo "<td>" . $basicrunresults_row->version_name .  "</td>";
                                        echo "</tr>";
                                        echo "</tbody>";
                                        echo "</table>";
                                        echo "</div>";
                                        echo "</div>";
                                    }


                                    //Query to generate metrichor run information:

                                    $metrichorinfosql = "SELECT *, max(metrichor_time_stamp) as max, min(metrichor_time_stamp) as min FROM config_general group by workflow_name;";
                                    $metrichorinforesults = $mindb_connection->query($metrichorinfosql);

                                    echo "<div class='panel panel-default'>";
                                    echo "<div class='panel-heading'>";
                                    echo "<h5>Metrichor reporting:</h5>";
                                    echo "</div>";
                                    if ($metrichorinforesults->num_rows >= 1){
                                        echo "<div class='panel-body'>";
                                        echo "<table class=\"table table-condensed\">";
                                        echo "<thead>";
                                        echo "<tr>";
                                        echo "<th>Workflow_Name</th>";
                                        echo "<th>Parameter</th>";
                                        echo "<th>Value</th>";
                                        echo "</tr>";
                                        echo "</thead>";
                                        echo "<tbody>";
                                        foreach ($metrichorinforesults as $row) {
                                            echo "<tr>";
                                            echo "<td>".$row['workflow_name']."</td>";
                                            echo "<td>Workflow Script</td>";
                                            echo "<td>" . $row['workflow_script']. "</td>";
                                            echo "</tr>";
                                            echo "<tr>";
                                            echo "<td>"."</td>";
                                            echo "<td>Config</td>";
                                            echo "<td>" . $row['config']. "</td>";
                                            echo "</tr>";
                                            echo "<tr>";
                                            echo "<td>"."</td>";
                                            echo "<td>Metrichor Version</td>";
                                            echo "<td>" . $row['metrichor_version']. "</td>";
                                            echo "</tr>";
                                            echo "<tr>";
                                            echo "<td>"."</td>";
                                            echo "<td>First Called Read</td>";
                                            echo "<td>" . $row['min']. "</td>";
                                            echo "</tr>";
                                            echo "<tr>";
                                            echo "<td>"."</td>";
                                            echo "<td>Last Called Read</td>";
                                            echo "<td>" . $row['max']. "</td>";
                                            echo "</tr>";
                                        }
                                        echo "</tbody>";
                                        echo "</table>";
                                        echo "</div>";
                                        echo "</div>";
                                    }
                                    //Query to generate reference information:
                                    $referencesql = "select * from reference_seq_info;";
                                    $referenceresults = $mindb_connection->query($referencesql);
                                    echo "<div class='panel panel-default'>";
                                    echo "<div class='panel-heading'>";
                                    echo "<h5>Reference reporting:</h5>";
                                    echo "</div>";
                                    if ($referenceresults->num_rows >= 1){
                                        echo "<div class='panel-body'>";
                                        echo "<table class=\"table table-condensed\">";
                                        echo "<thead>";
                                        echo "<tr>";
                                        echo "<th>Reference File</th>";
                                        echo "<th>Sequence Name</th>";
                                        echo "<th>Sequence Length</th>";
                                        echo "</tr>";
                                        echo "</thead>";
                                        echo "<tbody>";
                                        foreach ($referenceresults as $row) {
                                            echo "<tr>";
                                            echo "<td>".$row['reffile']."</td>";
                                            echo "<td>".$row['refname']."</td>";
                                            echo "<td>" . $row['reflen']. "</td>";
                                            echo "</tr>";
                                        }
                                        echo "</tbody>";
                                        echo "</table>";
                                        echo "</div>";
                                        echo "</div>";
                                    }else {
                                        echo "<div class='panel-body'>";
                                        echo "<p>This run has not been aligned to a reference.</p>";
                                        echo "</div>";
                                        echo "</div>";
                                    }

                                //Query to get comment information:
                                        $grusql = "select * from Gru.minIONruns where runname = \"" . $_SESSION['focusrun'] . "\";";
                                        $grusqlresults = $mindb_connection->query($grusql);
                                        echo "<div class='panel panel-default'>";
                                        echo "<div class='panel-heading'>";
                                        echo "<h5>minoTour reporting:</h5>";
                                        echo "</div>";
                                        if ($grusqlresults->num_rows >= 1) {
                                            echo "<div class='panel-body'>";
                                            echo "<table class=\"table table-condensed\">";
                                            echo "<thead>";
                                            echo "<tr>";
                                            echo "<th>Date</th>";
                                            echo "<th>Flow Cell ID</th>";
                                            echo "<th>Flow Cell Owner</th>";
                                            echo "<th>Base Caller Algorithm</th>";
                                            echo "<th>Base Caller Version</th>";
                                            echo "</tr>";
                                            echo "</thead>";
                                            echo "<tbody>";
                                            foreach ($grusqlresults as $row) {
                                                echo "<tr>";
                                                echo "<td>".$row['date']."</td>";
                                                echo "<td>".$row['flowcellid']."</td>";
                                                echo "<td>" . $row['FlowCellOwner']. "</td>";
                                                echo "<td>" . $row['basecalleralg']. "</td>";
                                                echo "<td>" . $row['version']. "</td>";
                                                echo "</tr>";
                                            }
                                            echo "</tbody>";
                                            echo "</table>";
                                            echo "</div>";
                                            echo "</div>";
                                            echo "<div class='panel panel-default'>";
                                            echo "<div class='panel-heading'>";
                                            echo "<h5>Run Upload Comment:</h5>";
                                            echo "</div>";
                                            echo "<div class='panel-body'>";
                                            echo "<p>" . $row['comment'] . "</p";
                                            echo "</div>";
                                            echo "</div>";
                                            echo "</div>";
                                        }
                                    }
                                ?>
                                    <div class="text-left">
                                        <div class="well well-sm">
                                            <h4>Run reporting and comments.</h4>
                                            <div class="input-group col-lg-12">
                                            <label for="message">Add new comment:</label>
                                            <textarea class="form-control" rows="3" name="message" id="message" placeholder="Write your comment here..."></textarea>
                                            </div>
                                            <div class="input-group col-lg-4">
                                            <label for="message">Please provide your name:</label>
                                            <input type="text" name = "name" id = "name" class="form-control" placeholder="Please enter a name here..." />
                                            </div>
                                            <p><input type="hidden" id="user" name="user" value="<?php echo $_SESSION['user_name'];?>"/></p>
                                    <p><input type="hidden" id="run" name="run" value="<?php echo $_SESSION['focusrun'];?>"/></p>
                                            <span class="input-group-btn" onclick="addComment()">
                                                    <a id="formthing" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-comment"></span> Add Comment</a>
                                            </span>


                            <hr data-brackets-id="12673">
                            <!--Error and success message wrapper-->
                                        <div id="errAll"></div>
                            <!--New comments appear here.-->
                            <ul data-brackets-id="12674" id="sortable" class="list-unstyled ui-sortable">
                            <div id="commentpost"></div>
                            <?php

                                    $grucomsql = "select * from Gru.comments where runname = \"" . $_SESSION['focusrun'] . "\" order by date desc;";
                                    $grucomsqlresults = $mindb_connection->query($grucomsql);
                                    if ($grucomsqlresults->num_rows >= 1) {
                                        foreach ($grucomsqlresults as $row) {
                                            echo "<strong class=\"pull-left primary-font\">" . $row['name'] . "</strong>";
                                            echo "<small class=\"pull-right text-muted\">";
                                            echo "<span class=\"glyphicon glyphicon-time\"></span>" . $row['date'] . "</small>";
                                            echo "</br>";
                                            echo "<li class=\"ui-state-default\">";
                                            echo $row['comment'];
                                            echo "</br>";
                                            echo "<hr>";
                                        }
                                    }

                                    ?>



                            </ul>
                            </div>
                            </div>
                                <div id="messages"></div>

                                 </div>


        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

      <?php include 'includes/reporting-new.php'; ?>
      <script src="js/plugins/dataTables/jquery.dataTables.js" type="text/javascript" charset="utf-8"></script>
      <script src="js/plugins/dataTables/dataTables.bootstrap.js" type="text/javascript" charset="utf-8"></script>
      <script type="text/javascript">
      jQuery(document).ready(function($){
          $("#formthing").click(function(e){
              cname = $( "#name" ).val();
              cmessage = $("#message").val();
              cuser = $("#user").val();
              crun = $("#run").val();
              var currentdate = new Date();
              var datetime = currentdate.getFullYear() + "/" + (currentdate.getMonth()+1)  + "/" + currentdate.getDate() + "/" +  " "
              + currentdate.getHours() + ":"
              + currentdate.getMinutes() + ":"
              + currentdate.getSeconds();
              ctime = datetime;

              if( cname=="" || cmessage=="" ) {
                  $("#errAll").html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>You have left a required field blank.</div>');
              }else {
                  var data = {
                      name: cname,
                      user: cuser,
                      time: ctime,
                      message: cmessage,
                      run: crun,
                  };

                  $.post( "includes/ajax.php", data, function( response ) {
                      //alert(response);
                      $('#name').val("");
                      $('#message').val("");
                      var test = response.toString();
                      $("#commentpost").prepend(test);
                  });
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
