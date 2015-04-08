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
           
			<?php include 'navbar-header.php' ?>
            <!-- /.navbar-top-links -->
			<?php include 'navbar-top-links.php'; ?>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
						<?php include 'includes/run_check.php';?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Current Variants - run: <?php echo cleanname($_SESSION['active_run_name']);; ?></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
            <div class="panel panel-info">
            <div class="panel-heading">	
			    <h3 class="panel-title">Nucleotide Variant Detection</h3>
			  </div>
			  <div class="panel-body">
			    These pages aim to report potential nucleotide variant positions detected as sequencing is taking place. Potential variants are highlighted in one of two ways. Firstly consensus calling highlights those positions where the most commonly called base differs from the reference. The second method considers the average error rate of the sequencing process and identifies those positions whereby the variance is two standard deviations greater than the mean. This is all experimental.
			  </div>
            </div>
            </div>
			<ul class="nav nav-pills">
			  <li><a href="current_variants.php">Consensus Variants</a></li>
			  <li><a href="current_var.php">Variants</a></li>
			  <li><a href="current_deletions.php">Deletions</a></li>
			  <li class="active"><a href="current_insertions.php">Insertions</a></li>
		</ul>
			<div class="panel panel-default">
			  <div class="panel-heading">
			    <h3 class="panel-title"><!-- Button trigger modal -->
<button class="btn btn-info" data-toggle="modal" data-target="#modal1">
 <i class="fa fa-info-circle"></i> Insertions
</button>

<!-- Modal -->
<div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Average Variant Calling</h4>
      </div>
      <div class="modal-body">
        <body>This panel provides a table listing variants. Potential variants are calculated by considering the average variance from the reference across the data set and then selecting those positions which are more diverse than this average. It is an imperfect detection system.<br><br></body>
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
			   <div class="col-md-4">
			  	<?php if (!empty($_POST)): ?>
			  	<?php 
			  	$type = $_POST["type"]; 
			  	$coverage = $_POST["coverage"];
			  			?>
   <?php else: ?>
				<?php 
				$type = '2d';
				$coverage = 20;
				 ?>
				<?php endif; ?>
    <form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post">
    <label for="type">Select read type for variant events.</label>
   <select class="form-control" name="type" id="type" onchange="this.form.submit()">
        <option <?php if ($type == 'template'){echo "selected=\"selected\"";};?> value="template">Template</option>
        <option <?php if ($type == 'complement'){echo "selected=\"selected\"";};?> value="complement">Complement</option>
        <option <?php if ($type == '2d'){echo "selected=\"selected\"";};?> value="2d">2d</option>
     </select>
     <label for="coverage">Select minimum coverage for variant events.</label>
     <select class="form-control" name="coverage" id="coverage" onchange="this.form.submit()">
     	<option <?php if ($coverage == 0){echo "selected=\"selected\"";};?> value="0">0</option>
     	<option <?php if ($coverage == 10){echo "selected=\"selected\"";};?> value="10">10</option>
     	<option <?php if ($coverage == 20){echo "selected=\"selected\"";};?> value="20">20</option>
     	<option <?php if ($coverage == 30){echo "selected=\"selected\"";};?> value="30">30</option>
     	<option <?php if ($coverage == 40){echo "selected=\"selected\"";};?> value="40">40</option>
     	<option <?php if ($coverage == 50){echo "selected=\"selected\"";};?> value="50">50</option>
     	<option <?php if ($coverage == 60){echo "selected=\"selected\"";};?> value="60">60</option>
     	<option <?php if ($coverage == 70){echo "selected=\"selected\"";};?> value="70">70</option>
     	<option <?php if ($coverage == 80){echo "selected=\"selected\"";};?> value="80">80</option>
     	<option <?php if ($coverage == 90){echo "selected=\"selected\"";};?> value="90">90</option>
     	<option <?php if ($coverage == 100){echo "selected=\"selected\"";};?> value="100">100</option>
     </select>
    </form>
				</div>
			  </div>
						<div class="row">
					 <div class="col-lg-12">
					 <div id = "consensus_details"><br><h5>Click on a position from the table below to view specific variants.</h5><br></div>
            			 <div class='table-responsive'>
            			 <table id="example" class="display table table-condensed table-hover " cellspacing="0" width="100%">
							 <thead>
								 <tr>
								 	<th>Ref ID</th>
								 	<th>Reference Name</th>
								 	<th>Reference Base</th>
									 <th>Consensus Count</th>
									 <th>Consensus Sequence</th>
									 <th>Ref Position</th>
									 <th>A</th>
									 <th>T</th>
									 <th>G</th>
									 <th>C</th>
									 <th>I</th>
									 <th>Total (called)</th>
									 <th>Proportion Mismatched</th>
									 <th>Proportion Most Common</th>
									 
								 </tr>
							 </thead>
 
							 <tfoot>	
								 <tr>
								 	<th>Ref ID</th>
								 	<th>Reference Name</th>
								 	<th>Reference Base</th>
									 <th>Consensus Count</th>
									 <th>Consensus Sequence</th>
									 <th>Ref Position</th>
									 <th>A</th>
									 <th>T</th>
									 <th>G</th>
									 <th>C</th>
									 <th>I</th>
									 <th>Total (called)</th>
									 <th>Proportion Mismatched</th>
									 <th>Proportion Most Common</th>

								 </tr>
							 </tfoot>

						 </table>
					 </div>
				
					</div>
					</div>
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
    <script src="js/plugins/dataTables/jquery.dataTables.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/plugins/dataTables/dataTables.bootstrap.js" type="text/javascript" charset="utf-8"></script>

	
	<!-- Highcharts Addition -->
	<script src="js/highcharts.js"></script>
	<script type="text/javascript" src="js/themes/grid-light.js"></script>
	<script src="http://code.highcharts.com/4.0.3/modules/heatmap.js"></script>
	<script src="http://code.highcharts.com/modules/exporting.js"></script>
	<script>
		$(document).ready(function() {
		    $('#example').dataTable( {
		    	"iDisplayLength": 25,
				"sDom": '<"top"i>rt<"bottom"flp><"clear">',
		        "processing": true,
		        "serverSide": true,
		        "sAjaxSource": "data_tables/data_table_insertions.php?prev=0&type=<?php echo $type;?>&coverage=<?php echo $coverage;?>"
		    } );
		    oTable = $('#example').dataTable( );
		    $('#example tbody').on('click', 'tr', function () {
		    	var refpos = oTable.fnGetData(this,5);
		    	var refid = oTable.fnGetData(this,0);
		    	var type = '<?php echo $type;?>';
		    	var refname = oTable.fnGetData(this,1);
		    	//alert (refpos + " and " + refid + " and " + "<?php echo $type;?>");
		    	var options = {
			        chart: {
			            renderTo: 'consensus_details',
						zoomType: 'x',
			            type: 'column',
			        },
			        title: {
			          text: 'Base Variant Coverage <?php echo $type;?> on '+ refname + ' at ' +refpos+ ' with <?php echo $coverage;?> min coverage.'
			        },
			        resetZoomButton: {
	                	position: {
	                	    x: -10,
	                	    y: 10
	                	},
	                	relativeTo: 'chart'
	            	}, 
			        plotOptions: {
			        	column: {
	                		stacking: 'normal',
			        	},
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
					    	text: 'Reference Position'
					    }
					},
					yAxis: [{
						labels: {
	            			align: 'right',
	            	    	x: -3
	            	   	},
	            	    title: {
	            	    	text: type
					    },
					    height: '80%',
					    lineWidth: 1
					},{
	            		labels: {
	                    	align: 'right',
	         				x: -3
	                	},
	                	max: 1,
					    title: {
					    	text: 'Ref'
					    },
					    top: '85%',
					    height: '15%',
					    offset: 0,
					    lineWidth: 1
					}],
					credits: {
						enabled: false
					},
			        legend: {
	            		title: {
			                text: 'Base<br/><span style="font-size: 9px; color: #666; font-weight: normal">(Click to hide)</span>',
	    		            style: {
	            		        fontStyle: 'italic'
	                		}
	            		},
	            		layout: 'vertical',
	            		align: 'right',
	            		verticalAlign: 'top',
	            		x: -10,
	            		y: 100
	        		},
			        series: []
			    };
	
			    $.getJSON('jsonencode/basesnpcoveragepos.php?prev=0&refid='+refid+'&position='+refpos+'&type='+type+'&callback=?', function(data) {
					//alert("success");
			        options.series = data; // <- just assign the data to the series property.
	        
		 
		
			        //options.series = JSON2;
					var chart = new Highcharts.Chart(options);
					});
			});
});
			</script>
	
     
									
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
