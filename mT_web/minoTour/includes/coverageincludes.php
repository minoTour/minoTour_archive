<?php



	$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['focusrun']);
		//echo cleanname($_SESSION['active_run_name']);;

	//echo '<br>';

	if (!$mindb_connection->connect_errno) {

		//$sql_template = "select refpos, count(*) as count from last_align_basecalled_template where refpos != \'null\' and (cigarclass = 7 or cigarclass = 8) group by refpos;";

		$sql_template = "SELECT refid, max(refpos) as max_length FROM last_align_basecalled_template_5prime group by refid;";

		$template=$mindb_connection->query($sql_template);

		$array;
		if ($template->num_rows >= 1){
			foreach ($template as $row) {
				$array[] = $row['refid'];

				if ($row['max_length'] > $maxlengththreshold){
					//echo "LONG MAN LONG";
					echo "As this is a long sequence use this slider to set the mid point for the coverage plot.";
					?>
					    <div>
					        <input type="text" id="range<?php echo $row['refid'];?>" value="" name="range" />
					    </div>


					<?php
				}
				echo "<div id='coverage" . $row['refid'] . "'  style='width:100%; height:400px;'><i class='fa fa-cog fa-spin fa-3x'></i> Calculating Coverage Plots for " . $row['refid'] . "</div>";


			}
		}

		foreach ($array as $value){
			echo "<div id='5primecoverage". $value . "'  style='width:100%; height:400px;'><i class='fa fa-cog fa-spin fa-3x'></i> Calculating 5' Mapped Coverage " . $value . "</div>
			<div id='3primecoverage". $value . "'  style='width:100%; height:400px;'><i class='fa fa-cog fa-spin fa-3x'></i> Calcularing 3' Mapped Coverage  " . $value . "</div>";

		}
	}

?>
