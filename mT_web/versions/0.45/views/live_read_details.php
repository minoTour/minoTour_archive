<?php
ini_set('max_execution_time', 300);

// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("../libraries/password_compatibility_library.php");
}

// include the configs / constants for the database connection
require_once("../config/db.php");

// load the login class
require_once("../classes/Login.php");

// load the functions
require_once("../includes/functions.php");

// create a login object. when this object is created, it will do all login/logout stuff automatically
// so this single line handles the entire login process. in consequence, you can simply ...
$login = new Login();

// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == true) {
    // the user is logged in. you can do whatever you want here.
    // for demonstration purposes, we simply show the "you are logged in" view.
    //include("views/index_old.php");*/
	if($_GET["prev"] == 1){
		$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['focusrun']);
	}else{
		$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);
	}
	//echo cleanname($_SESSION['active_run_name']);;

	//echo '<br>';

	if (!$mindb_connection->connect_errno) {
		//echo "Try this...";
		echo "<div class='panel panel-primary'>\n";
		echo "<div class='panel-heading'>\n";
		echo "<h5 class='panel-title' id='" .$_POST['readname']  . "'>Read Details for " . $_POST['readname']."</h5>\n";
		echo "</div>";
		echo "<div class='panel-body'>";
		$sql = "SELECT basename_id,config_general.basename,asic_id,asic_temp,device_id,exp_script_purpose,exp_start_time,flow_cell_id,heatsink_temp,run_id,version_name,local_folder,workflow_script,workflow_name,read_id,use_local,tag,model_path,complement_model,max_events,input,min_events,config,template_model,channel,metrichor_version,metrichor_time_stamp, basecalled_template.seqid as btseqid, basecalled_template.duration as btduration, basecalled_template.start_time as btstarttime, basecalled_template.scale as btscale, basecalled_template.shift as btshift, basecalled_template.gross_shift as btgross_shift, basecalled_template.drift as btdrift, basecalled_template.scale_sd as btscalesd, basecalled_template.var_sd as btvarsd, basecalled_template.var as btvar, basecalled_template.sequence as btsequence, basecalled_template.qual as qual, basecalled_complement.seqid as ctseqid, basecalled_complement.duration as ctduration, basecalled_complement.start_time as ctstarttime, basecalled_complement.scale as ctscale, basecalled_complement.shift as ctshift, basecalled_complement.gross_shift as ctgross_shift, basecalled_complement.drift as ctdrift, basecalled_complement.scale_sd as ctscalesd, basecalled_complement.var_sd as ctvarsd, basecalled_complement.var as ctvar, basecalled_complement.sequence as ctsequence, basecalled_complement.qual as qual, basecalled_2d.seqid as b2dseqid, basecalled_2d.sequence as b2dsequence, basecalled_2d.qual as b2dqual FROM tracking_id inner join config_general using (basename_id) left join basecalled_template using (basename_id) left join basecalled_complement using (basename_id) left join basecalled_2d using (basename_id) where config_general.basename = '".$_POST['readname'] ."';";
		$sql_result = $mindb_connection->query($sql);
		//echo $sql . "<br>";
		$resultsarray;
		//var_dump($sql_result);
			echo "<div id='allqualities'  style='width:100%; height:400px;'><i class='fa fa-cog fa-spin fa-3x'></i> Calculating Quality Scores for Reads - note even reads which are not basecalled will have quality scores</div>";
			echo "<div id='templatesquiggles'  style='width:100%; height:400px;'><i class='fa fa-cog fa-spin fa-3x'></i> Template Squiggles...</div>";
			echo "<div id='complementsquiggles'  style='width:100%; height:400px;'><i class='fa fa-cog fa-spin fa-3x'></i> Complement Squiggles...</div>";


			  
		

		if ($sql_result->num_rows >=1){
			foreach ($sql_result as $row){
				while ($property = mysqli_fetch_field($sql_result)) {
					//echo "<p>" . $property->name . " : " . $row[$property->name] . "</p>";
					$resultsarray[$property->name]=$row[$property->name];
				}
			}	
		}
		#echo "<div class='panel panel-default'>";
		#echo "<div class='panel-heading'>";
		#echo "<h3 class='panel-title'>Read Sequence</h3>";
		#echo	 " </div>";
		#echo	 " <div class='panel-body'>";
		#echo "<pre>" . $resultsarray['btsequence'] . "\n" . $resultsarray['btsequence'] . "</pre>";
		#echo "</div>
		#	</div>";
		
		echo "<div class='panel panel-default'>";
		echo "<div class='panel-heading'>";
		echo "<h3 class='panel-title'>Read Details</h3>";
		echo	 " </div>";
		echo	 " <div class='panel-body'>";
		echo "Basename: " . $resultsarray['basename'] . "<br>";
		echo "Metrichor Version: " . $resultsarray['metrichor_version'] . "<br>";
		echo "Asic ID: " . $resultsarray['asic_id'] . "<br>";
		echo "Asic Temp: " . $resultsarray['asic_temp'] . "˚C<br>";
		echo "Heat Sink Temp: " . $resultsarray['heatsink_temp'] . "˚C<br>";
		echo "Device ID: " . $resultsarray['device_id'] . "<br>";
		echo "Flow Cell ID: " . $resultsarray['flow_cell_id'] . "<br>";
		echo "Read ID: " . $resultsarray['read_id'] . "<br>";
		echo "Channel ID: " . $resultsarray['channel'] . "<br>";
		echo "Experiment Purpose: " . $resultsarray['exp_script_purpose'] . "<br>";
		$timestamp = $resultsarray['exp_start_time'];
		echo "Experiment Start Time: " . gmdate("Y-m-d\ H:i:s\ ", $timestamp). "<br>";
		$timestamp2 = $timestamp + $resultsarray['btstarttime'];
		echo "Template called at: ". gmdate("Y-m-d\ H:i:s\ ", $timestamp2). "<br>";

		echo "</div>
			</div>";
		echo "
		<div class='panel-group'>
		  <div class='panel panel-default'>
		    <div class='panel-heading clearfix'>";
				if (!empty($resultsarray['btsequence'])){
					echo "<h4 class='panel-title pull-left' style='padding-top: 7.5px;'>";
		        	//echo "<a data-toggle='collapse' data-parent='#accordion' href='#collapseOne'>";
					echo "Template Sequence: Generated";
					echo "</h4>";
					echo "<div class='btn-group pull-right'>";
						echo "<a href='includes/fetchreads.php?db=" . $_SESSION['active_run_name'] . "&job=template&readname=".$resultsarray['basename_id']."&prev=0' type='button' class='btn btn-success'>Download Fasta</a>  <a href='includes/fetchreads.php?db=" . $_SESSION['active_run_name'] ."&job=template&readname=".$resultsarray['basename_id']."&prev=0&type=fastq' type='button' class='btn btn-success'>Download Fastq</a>";
					echo "</div>";
					//echo ">" .$resultsarray['btseqid']. "<br>\n";
					//echo "<p style='word-wrap: break-word;'>" . $resultsarray['btsequence'] . "</p><br>\n";
				}else{
	  	  	     	echo "<h4 class='panel-title' >";
	  	  	      	//echo "<a data-toggle='collapse' data-parent='#accordion' href='#collapseOne'>";
					echo "Template Sequence Not Generated";
					echo "</h4>";
					
			
				}
echo "
		    </div>
		</div>
		<div class='panel panel-default'>
			<div class='panel-heading'>";
	      
			if (!empty($resultsarray['ctsequence'])){
	  	    	echo "<h4 class='panel-title pull-left' style='padding-top: 7.5px;'>";
	  	      	//echo "<a data-toggle='collapse' data-parent='#accordion' href='#collapseTwo'>";
				echo "Complement Sequence: Generated";
				echo "</h4>";
				echo "<div class='btn-group pull-right'>";
					echo "<a href='includes/fetchreads.php?db=" . $_SESSION['active_run_name'] . "&job=complement&readname=".$resultsarray['basename_id']."&prev=0' type='button' class='btn btn-success'>Download Fasta</a>  <a href='includes/fetchreads.php?db=" . $_SESSION['active_run_name'] ."&job=complement&readname=".$resultsarray['basename_id']."&prev=0&type=fastq' type='button' class='btn btn-success'>Download Fastq</a>";
				echo "</div>";
				
				//echo ">" .$resultsarray['btseqid']. "<br>\n";
				//echo "<p style='word-wrap: break-word;'>" . $resultsarray['btsequence'] . "</p><br>\n";
			}else{
			
  	  	    	echo "<h4 class='panel-title '>";
  	  	    	//echo "<a data-toggle='collapse' data-parent='#accordion' href='#collapseTwo'>";
				echo "Complement Sequence Not Generated";
				echo "</h4>";
			}
			echo " 
		</div>
		
		</div>
		
		<div class='panel panel-default'>
		    <div class='panel-heading'>";
			if (!empty($resultsarray['b2dsequence'])){
				echo "<h4 class='panel-title pull-left' style='padding-top: 7.5px;'>";
	        	//echo "<a data-toggle='collapse' data-parent='#accordion' href='#collapseThree'>";
				echo "2D Sequence: Generated";
				echo "</h4>";
				echo "<div class='btn-group pull-right'>";
					echo "<a href='includes/fetchreads.php?db=" . $_SESSION['active_run_name'] . "&job=2d&readname=".$resultsarray['basename_id']."&prev=0' type='button' class='btn btn-success'>Download Fasta</a>  <a href='includes/fetchreads.php?db=" . $_SESSION['active_run_name'] ."&job=2d&readname=".$resultsarray['basename_id']."&prev=0&type=fastq' type='button' class='btn btn-success'>Download Fastq</a>";
				echo "</div>";
				//echo ">" .$resultsarray['btseqid']. "<br>\n";
				//echo "<p style='word-wrap: break-word;'>" . $resultsarray['btsequence'] . "</p><br>\n";
			}else{
  	  	     	echo "<h4 class='panel-title' >";
				//echo "<a data-toggle='collapse' data-parent='#accordion' href='#collapseThree'>";
				echo "2d Sequence Not Generated";
				echo "</h4>";
				
		
			}
		      
		         echo "
		        
		    </div>
		   
		    </div>
		  </div>
		</div>";
		//var_dump($resultsarray);
		
		
		
		
		
		
		
		echo "</div>";
		echo "</div>";

					
		
		
		
	
			

				
	}
} else {
	echo "ERROR";
}
?>
