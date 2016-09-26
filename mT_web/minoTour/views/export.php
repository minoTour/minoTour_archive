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
            <h1>Export Data <small> - run: <?php echo cleanname($_SESSION['active_run_name']);; ?></small></h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-edit"></i> External Links</a></li>
            <li class="active">Here</li>
          </ol>

        </section>

        <!-- Main content -->
        <section class="content"><?php include 'includes/run_check.php';?>


                <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Export Read Data and Alignments</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">

        			<div class='panel-group'>
        			<div class='panel panel-default'>
        			<div class='panel-heading'>
        				<h4>Download All Sequences</h4>
        			</div>
        		<div class='panel-body'>




        		<table class='table table-condensed'>
        		<thead>
        		<tr>
        		<th>Sequence Type</th>
        		<th>Result</th>
        		<th>Fasta</th>
        		<th>Fastq</th>

        		</tr>
        		</thead>
        		<tbody>
        		<tr>
        					<td>Template Sequence</td>
        					<td>Generated</td>
        					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=template&prev=0' type='button' class='btn btn-success btn-xs'>Download Fasta</a></td>
        					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=template&prev=0&type=fastq' type='button' class='btn btn-success btn-xs'>Download Fastq</a></td>

        					</tr>
        			<tr>
        					<td>Complement Sequence</td>
        					<td>Generated</td>
        					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=complement&prev=0' type='button' class='btn btn-success btn-xs'>Download Fasta</a></td>
        					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=complement&prev=0&type=fastq' type='button' class='btn btn-success btn-xs'>Download Fastq</a></td>
        					</tr>
        					<tr>
        					<td>2D Sequence</td>
        					<td>Generated</td>
        					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=2d&prev=0' type='button' class='btn btn-success btn-xs'>Download Fasta</a></td>
        					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=2d&prev=0&type=fastq' type='button' class='btn btn-success btn-xs'>Download Fastq</a></td>
        					</tr>
        </tbody>
        		</table>
        		</div>
        		</div>
        		</br>
        		<?php if (count($_SESSION['activerefnames']) > 0) {?>
        		<div class='panel-group'>
        			<div class='panel panel-default'>
        			<div class='panel-heading'>
        				<h4>Download Aligned Sequences Only</h4>
        			</div>
        		<div class='panel-body'>
        		Note that alignment files can be very large. These are the raw files reported either from last or BWA.
        		<table class='table table-condensed'>
        		<thead>
        		<tr>
        		<th>Sequence Type</th>
        		<th>Result</th>
        		<th>Fasta</th>
        		<th>Fastq</th>
        		<th>Alignments</th>

        		</tr>
        		</thead>
        		<tbody>
        		<tr>
        					<td>Template Sequence</td>
        					<td>Generated</td>
        					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=template&align=1&prev=0' type='button' class='btn btn-success btn-xs'>Download Fasta</a></td>
        					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=template&align=1&prev=0&type=fastq' type='button' class='btn btn-success btn-xs'>Download Fastq</a></td>
        					<td><?php if ($_SESSION['focusmaf'] == "MAF") { ?><a href='includes/fetchmaf.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=template&prev=0' type='button' class='btn btn-success btn-xs'>Download MAF</a><?php }?>
        					<?php if ($_SESSION['focusmaf'] == "SAM") { ?><a href='includes/fetchsam.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=template&prev=0' type='button' class='btn btn-success btn-xs'>Download SAM</a><?php }?>
        					</td>
        					</tr>
        			<tr>
        					<td>Complement Sequence</td>
        					<td>Generated</td>
        					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=complement&align=1&prev=0' type='button' class='btn btn-success btn-xs'>Download Fasta</a></td>
        					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=complement&align=1&prev=0&type=fastq' type='button' class='btn btn-success btn-xs'>Download Fastq</a></td>
        					<td><?php if ($_SESSION['focusmaf'] == "MAF") { ?><a href='includes/fetchmaf.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=complement&prev=0' type='button' class='btn btn-success btn-xs'>Download MAF</a><?php }?>
        					<?php if ($_SESSION['focusmaf'] == "SAM") { ?><a href='includes/fetchsam.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=complement&prev=0' type='button' class='btn btn-success btn-xs'>Download SAM</a><?php }?></td>

        					</tr>
        					<tr>
        					<td>2D Sequence</td>
        					<td>Generated</td>
        					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=2d&align=1&prev=0' type='button' class='btn btn-success btn-xs'>Download Fasta</a></td>
        					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=2d&align=1&prev=0&type=fastq' type='button' class='btn btn-success btn-xs'>Download Fastq</a></td>
        					<td><?php if ($_SESSION['focusmaf'] == "MAF") { ?><a href='includes/fetchmaf.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=2d&prev=0' type='button' class='btn btn-success btn-xs'>Download MAF</a><?php }?>
        					<?php if ($_SESSION['focusmaf'] == "SAM") { ?><a href='includes/fetchsam.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=2d&prev=0' type='button' class='btn btn-success btn-xs'>Download SAM</a><?php }?></td>
        					</tr>
        </tbody>
        		</table>
        		</div>
        		</div>
        		</br>
        		<?php }; ?>


        		<?php if ($_SESSION['currentbarcode'] >= 1 ) {?>
        				<div class='panel-group'>
        			<div class='panel panel-default'>
        			<div class='panel-heading'>
        				<h4>Download Sequences By Barcode</h4>
        			</div>
        		<div class='panel-body'>
                    <p> This code will retrieve labelled barcode sequences regardless of their presence in the PASS or FAIL folders. Currently the download buttons for all combinations are shown, but may not generate data.</p>
        		<table class='table table-condensed'>
        		<thead>
        		<tr>
        		<th>Barcode</th>
                <th>Template Fasta</th>
                <th>Template Fastq</th>
                <th>Complement Fasta</th>
                <th>Complement Fastq</th>
                <th>2D Fasta</th>
        		<th>2D Fastq</th>
        		</tr>
        		</thead>
        		<?php //Barcode List
        			$barcodes = ["01","02","03","04","05","06","07","08","09","10","11","12"];
        			?>
        		<tbody>

        		<?php foreach ($barcodes as $barcode) { ?>
        		<tr>

        					<td><?php echo $barcode;?></td>
        					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=template&code=<?php echo $barcode;?>&align=0&prev=0&type=fasta' type='button' class='btn btn-success btn-xs'>Download Fasta</a></td>
        					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=template&code=<?php echo $barcode;?>&align=0&prev=0&type=fastq' type='button' class='btn btn-success btn-xs'>Download Fastq</a></td>
                            <td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=complement&code=<?php echo $barcode;?>&align=0&prev=0&type=fasta' type='button' class='btn btn-success btn-xs'>Download Fasta</a></td>
        					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=complement&code=<?php echo $barcode;?>&align=0&prev=0&type=fastq' type='button' class='btn btn-success btn-xs'>Download Fastq</a></td>
                            <td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=2d&code=<?php echo $barcode;?>&align=0&prev=0&type=fasta' type='button' class='btn btn-success btn-xs'>Download Fasta</a></td>
        					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=2d&code=<?php echo $barcode;?>&align=0&prev=0&type=fastq' type='button' class='btn btn-success btn-xs'>Download Fastq</a></td>

        					</tr>

        					<?php };?>

        </tbody>
        		</table>
        		</div>
        		</div>
        		</br>
        		<?php };?>




        		<div class='panel-group'>
        			<div class='panel panel-default'>
        			<div class='panel-heading'>
        				<h4>Run Summary</h4>
        			</div>
        			<div class='panel-body'>
        		<?php runsummary(); ?>
        		</div>
        		</div>
        		</div>


                        </div>


        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

      <?php include 'includes/reporting-new.php'; ?>
      <script src="js/plugins/dataTables/jquery.dataTables.js" type="text/javascript" charset="utf-8"></script>
      <script src="js/plugins/dataTables/dataTables.bootstrap.js" type="text/javascript" charset="utf-8"></script>


  </body>
</html>
<?php
} else {

	    // the user is not logged in. you can do whatever you want here.
	    // for demonstration purposes, we simply show the "you are not logged in" view.
	    include("views/not_logged_in.php");
	}

	?>
