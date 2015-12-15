<?php
// load the functions
require_once("includes/functions.php");

?>
<!DOCTYPE html>
<html>

<?php include "includes/head.php";?>
<body>

    <div id="wrapper">

        <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0">

            <!-- /.navbar-header -->
			<?php include 'navbar-header.php' ?>
            <!-- /.navbar-top-links -->
			<?php include 'navbar-top-links.php'; ?>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
						<?php include 'includes/run_check.php';?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Current Data Summary - run: <?php echo cleanname($_SESSION['active_run_name']);; ?> - Export Reads</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
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
		</div>
	</b>
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
					<td><?php if ($_SESSION['currentmaf'] == "MAF") { ?><a href='includes/fetchmaf.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=template&prev=0' type='button' class='btn btn-success btn-xs'>Download MAF</a><?php }?>
					<?php if ($_SESSION['currentmaf'] == "SAM") { ?><a href='includes/fetchsam.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=template&prev=0' type='button' class='btn btn-success btn-xs'>Download SAM</a><?php }?>
					</td>
					</tr>
			<tr>
					<td>Complement Sequence</td>
					<td>Generated</td>
					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=complement&align=1&prev=0' type='button' class='btn btn-success btn-xs'>Download Fasta</a></td>
					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=complement&align=1&prev=0&type=fastq' type='button' class='btn btn-success btn-xs'>Download Fastq</a></td>
					<td><?php if ($_SESSION['currentmaf'] == "MAF") { ?><a href='includes/fetchmaf.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=complement&prev=0' type='button' class='btn btn-success btn-xs'>Download MAF</a><?php }?>
					<?php if ($_SESSION['currentmaf'] == "SAM") { ?><a href='includes/fetchsam.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=complement&prev=0' type='button' class='btn btn-success btn-xs'>Download SAM</a><?php }?></td>

					</tr>
					<tr>
					<td>2D Sequence</td>
					<td>Generated</td>
					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=2d&align=1&prev=0' type='button' class='btn btn-success btn-xs'>Download Fasta</a></td>
					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=2d&align=1&prev=0&type=fastq' type='button' class='btn btn-success btn-xs'>Download Fastq</a></td>
					<td><?php if ($_SESSION['currentmaf'] == "MAF") { ?><a href='includes/fetchmaf.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=2d&prev=0' type='button' class='btn btn-success btn-xs'>Download MAF</a><?php }?>
					<?php if ($_SESSION['currentmaf'] == "SAM") { ?><a href='includes/fetchsam.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=2d&prev=0' type='button' class='btn btn-success btn-xs'>Download SAM</a><?php }?></td>
					</tr>
</tbody>
		</table>

		</div>
		</div>
		</br>
		<?php };?>

		<?php if ($_SESSION['currentbarcode'] >= 1 ) {?>
				<div class='panel-group'>
			<div class='panel panel-default'>
			<div class='panel-heading'>
				<h4>Download Sequences By Barcode</h4>
			</div>
		<div class='panel-body'>
		Barcodes are called from 2d data only. Downloaded reads from here include the barcodes and currently only 2d sequences are available.
		<table class='table table-condensed'>
		<thead>
		<tr>
		<th>Barcode</th>
		<th>Fasta</th>
		<th>Fastq</th>

		</tr>
		</thead>
		<?php //Barcode List
			$barcodes = ["BC01","BC02","BC03","BC04","BC05","BC06","BC07","BC08","BC09","BC10","BC11","BC12","NB01","NB02","NB03","NB04","NB05","NB06","NB07","NB08","NB09","NB10","NB11","NB12"];
			?>
		<tbody>

		<?php foreach ($barcodes as $barcode) { ?>
		<tr>

					<td><?php echo $barcode;?></td>
					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=2d&code=<?php echo $barcode;?>&align=0&prev=0&type=fasta' type='button' class='btn btn-success btn-xs'>Download Fasta</a></td>
					<td><a href='includes/fetchreads.php?db=<?php echo $_SESSION['active_run_name']; ?>&job=2d&code=<?php echo $barcode;?>&align=1&prev=0&type=fastq' type='button' class='btn btn-success btn-xs'>Download Fastq</a></td>
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
                <!-- /.col-lg-12 -->
            </div>
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->


    <!-- Core Scripts - Include with every page -->
    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>

    <!-- Page-Level Plugin Scripts - Dashboard -->
			    <script type="text/javascript" src="js/pnotify.custom.min.js"></script>
			    <script type="text/javascript">
				PNotify.prototype.options.styling = "fontawesome";
				</script>
    <script src="js/plugins/morris/raphael-2.1.0.min.js"></script>
    <script src="js/plugins/morris/morris.js"></script>

	<!-- Highcharts Addition -->
	<script src="js/highcharts.js"></script>
	<script type="text/javascript" src="js/themes/grid-light.js"></script>
	<script src="http://code.highcharts.com/4.0.3/modules/heatmap.js"></script>
	<script src="http://code.highcharts.com/modules/exporting.js"></script>



    <!-- SB Admin Scripts - Include with every page -->
    <script src="js/sb-admin.js"></script>

    <!-- Page-Level Demo Scripts - Dashboard - Use for reference -->
    <script src="js/demo/dashboard-demo.js"></script>

     <script>
        $( "#infodiv" ).load( "alertcheck.php" ).fadeIn("slow");
        var auto_refresh = setInterval(function ()
            {
            $( "#infodiv" ).load( "alertcheck.php" ).fadeIn("slow");
            //eval(document.getElementById("infodiv").innerHTML);
            }, 10000); // refresh every 5000 milliseconds
    </script>

<?php include "includes/reporting.php";?>
</body>

</html>
