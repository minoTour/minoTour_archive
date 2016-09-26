<?php checksessionvars();
    if(isset($_GET["roi"])){
        $_SESSION['focusrun']=$_GET["roi"];
    }
?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">

    <!-- Sidebar user panel (optional) -->
    <div class="user-panel">
      <div class="pull-left image">
        <img src="images/minitour144.png" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info">
        <p> <?php echo $_SESSION['user_name']; ?></p>
        <!-- Status -->
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>

      </div>
    </div>
    <!-- search form (Optional) -->
        <!--
    <form action="#" method="get" class="sidebar-form">
      <div class="input-group">
        <input type="text" name="q" class="form-control" placeholder="Search...">
        <span class="input-group-btn">
          <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
        </span>
      </div>
    </form>
    -->
    <!-- /.search form -->

    <!-- Sidebar Menu -->
    <ul class="sidebar-menu">
        <li class="header"><?php //if (isset($_SESSION['active_run_name'])) {
            checkalerts();
            ?></li>
        <li id = "overview">
            <a href="index.php"><i class="fa fa-dashboard fa-fw"></i><span> Overview</span></a>
        </li>
        <li id = "livecontrol">
            <a href="live_control.php"><i class="fa fa-dashboard fa-fw"></i><span> Remote Control</span></a>
        </li>
        <?php if (checkactiverun()) { ?>
            <li id="currentruns" class="treeview">
                <a href="#"><i class="fa fa-bolt fa-fw"></i><span> Current Sequencing Run</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <?php if ( checknumactive() > 1){?>
                                <li>
                                    <a href="switch_run.php"><i class="fa fa-database fa-fw"></i><span> Switch Active Runs</span></a>
                                </li>
                                <?php }; ?>
                                <li>
                                    <a href="live_data.php"><i class="fa fa-cog fa-spin"></i> Live Data</a>
                                </li>

								<?php if ($_SESSION['currentbarcode'] >= 1) {?>
								<li>
                                    <a href="current_barcodes.php"><i class="fa fa-barcode"></i><span> Barcodes</span></a>
                                </li>

								<?php }; ?>

                                <li>
                                    <a href="current_summary.php"><i class="fa fa-bar-chart-o fa-fw"></i><span> Data Summary</span></a>
                                </li>
                                <?php if ($_SESSION['activereference'] != "NOREFERENCE") {?>
                                    <?php if ($_SESSION['currentBASE'] >=1){?>
                                <li>
                                	<a href="current_variants.php"><i class="fa fa-code-fork"></i><span> Nucleotide Variants</span></a>
                                	</li>
                                    <?php }; ?>
                                	<?php }; ?>
                                <?php if ($_SESSION['currentBASE'] >=1){?>
                                <li>
                                    <a href="live_reads_table.php"><i class="fa fa-eye fa-fw"></i><span> Individual Read Data</span></a>
                                </li>
                                <li>
                                    <a href="export.php"><i class="fa fa-file-text-o fa-fw"></i><span> Export Reads/Alignments</span></a>
                                </li>
                                <?php }; ?>
                                <?php if ($_SESSION['currentXML'] >= 1) {?>
								<li>
									<a href="current_XML.php"><i class="fa fa-info-circle"></i><span> ENA Submission Details</span></a>
								</li>
								<?php }; ?>
                                <li>
                                    <a href="live_report.php"><i class="fa fa-comments-o fa-fw"></i><span> Run Report</span></a>
                                </li>
                                <li>
                                    <a href="set_alerts.php"><i class="fa fa-exclamation-circle"></i><span> Set Alerts</span></a>
                                </li>
                                <?php if ($_SESSION['currentINT'] >=1){?>
                                <li>
                                	<a href="live_interaction.php"><i class="fa fa-cogs"></i><span> minION control</span></a>
                                </li>
                                <?php }?>
                                <li>
                                    <a href="runadmin.php"><i class="fa fa-stop"></i><span> Run Admin</span></a>
                                </li>

                            </ul>
                        </li>
						<?php } ?>
						<?php if (checkallruns()) {?>
                            <li id="prevruns" class="treeview">
                                <a href="#"><i class="fa fa-table fa-fw"></i><span> Previous Run</span> <i class="fa fa-angle-left pull-right"></i></a>
                                <ul class="treeview-menu">
                    		<?php if (isset($_SESSION['focusrun'])) {?>
	                            <li>
	                                <a href="previous_runs.php" id="SR"><i class="fa fa-database fa-fw"></i><span> Change Run</span></a>
	                            </li>
	                            <?php if (isset($_SESSION['focusbarcode']) && $_SESSION['focusbarcode'] >= 1) {?>
								<li>
                                    <a href="previous_barcodes.php"><i class="fa fa-barcode"></i><span> Barcodes</span></a>
                                </li>

								<?php }; ?>
								<li>
                                    <a href="previous_summary.php"><i class="fa fa-bar-chart-o fa-fw"></i><span> Data Summary</span></a>
                                </li>
                                <?php if (isset($_SESSION['previousbarcode']) && $_SESSION['previousbarcode'] >= 1) {?>
								<li>
                                    <a href="prev_barcode.php"><i class="fa fa-barcode"></i><span> Barcodes</span></a>
                                </li>

								<?php }; ?>
                                <?php if (isset($_SESSION['focusreference']) && $_SESSION['focusreference'] != "NOREFERENCE") {?>
                                <?php if ($_SESSION['focusBASE'] >=1){?>
                                <li>
                                	<a href="previous_variants.php"><i class="fa fa-code-fork"></i><span> Nucleotide Variants</span></a>
                                	</li>
                                    <?php }; ?>
                                	<?php }; ?>
                                    <?php if ($_SESSION['focusBASE'] >=1){?>
                                <li>
                                    <a href="reads_table.php"><i class="fa fa-eye fa-fw"></i><span> Individual Read Data</span></a>
                                </li>
								<!--<li>
									<a href="prev_kmers.php"><i class="fa fa-cogs fa-fw"></i><span> K-mer summaries</span></a>
								</li>-->

                                <li>
                                    <a href="previous_export.php"><i class="fa fa-file-text-o fa-fw"></i><span> Export Reads/Alignments</span></a>
                                </li>
                                <?php }; ?>
                                <?php if (isset($_SESSION['focusXML']) && $_SESSION['focusXML'] >= 1) {?>
								<li>
									<a href="previous_XML.php"<i class="fa fa-info-circle"></i><span> ENA Submission Details</span></a>
								</li>
								<?php }; ?>
                                <li>
                                    <a href="previous_report.php"><i class="fa fa-comments-o fa-fw"></i><span> Run Report</span></a>
                                </li>
                                <li>
                                    <a href="manage_data.php"><i class="fa fa-pencil-square-o"></i><span> Manage Data</span></a>
                                </li>
								<?php }else{?>
	                            <li>
	                                <a href="previous_runs.php" id="SR"><i class="fa fa-database fa-fw"></i><span> Select Run</span></a>
	                            </li>
								<?php };?>
                            </ul>
                        </li>


						<?php }?>
                        <?php if ($_SESSION['adminuser'] == 1){?>
                        <li id = "admin" class="treeview">
                                <a href="#"><i class="fa fa-edit fa-fw"></i> <span>Admin</span> <i class="fa fa-angle-left pull-right"></i></a>
                                <ul class="treeview-menu">
    							<li>
    								<a href="enable.php"><i class="fa fa-child fa-fw"></i><span> Enable User Upload</span></a>
    							</li>
    	                        <li>
                                    <a href="admin.php"><i class="fa fa-database fa-fw"></i><span> Database Allocation</span></a>
 	                          </li>
                              <li>
                                  <a href="admin_manage.php"><i class="fa fa-database fa-fw"></i><span> Database Management</span></a>
                            </li>
    	                    <li>
                                    <a href="admin_users.php"><i class="fa fa-users fa-fw"></i><span> Set Administrators</span></a>
 	                          </li>
 	                          <li>
 	                          		<a href="cache_manage.php"><i class="fa fa-database fa-fw"></i><span> Cache Administration</span></a>
 	                          </li>
 	                        </ul>
                        </li>
                        <?php };?>
                        <li>
                        	<a href="tutorials.php"><i class="fa fa-question-circle fa-fw"></i><span> Tutorials</span></a>
                        <li>
                        <li>
                        	<a href="minup.php"><i class="fa fa-cloud-upload fa-fw"></i><span> minUp Scripts</span></a>
                        <li>
                            <a href="exlinks.php"><i class="fa fa-files-o fa-fw"></i><span> External Links</span></a>

                            <!-- /.nav-second-level -->
                        </li>
                        <li id="versions" class="treeview">
            <a href="#"><i class="fa fa-archive fa-fw"></i> <span>Previous Versions</span> <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
            <!--<li>
                <a href="versions/0.46/index.php">Version 0.46</a>
                </li>-->
                    <li>
                        <a href="versions/0.45/index.php">Version 0.45</a>
                        </li>
                    <li>
                        <a href="versions/0.43/index.php">Version 0.43</a>
                        </li>
                    <li>
                        <a href="versions/0.41/index.php">Version 0.42</a>
                        </li>
            </ul>
            </li>


    </ul><!-- /.sidebar-menu -->

  </section>
  <!-- /.sidebar -->
</aside>
<script>
    var pathname = window.location.pathname;
    //alert(pathname);
    parts = pathname.split("/");
                var filename = parts[parts.length - 1];
                //alert(filename);
                if (filename == "previous_bases.php" || filename == "previous_basecalling.php" || filename == "previous_insertions.php" || filename == "previous_deletions.php" || filename == "previous_variants.php" || filename == "previous_var.php" || filename == "previous_report.php" || filename == "previous_barcodes.php" || filename == "previous_runs.php" || filename  == "previous_summary.php" || filename  == "previous_histogram.php" || filename== "previous_export.php" || filename== "previous_rates.php" || filename== "previous_pores.php" || filename== "previous_quality.php" || filename== "previous_coverage.php" || filename== "previous_development.php" || filename=="reads_table.php" || filename=="manage_data.php" || filename=="prev_kmers.php"){
                    var d = document.getElementById("prevruns");
                    d.className = d.className + " active";
                }
                if (filename == "runadmin.php" || filename == "live_interaction.php" || filename == "current_barcodes.php" || filename == "current_bases.php" || filename == "current_insertions.php" || filename == "current_deletions.php" || filename == "current_variants.php" || filename == "current_var.php" || filename == "live_report.php" || filename == "switch_run.php" || filename == "live_reads_table.php" || filename  == "current_histogram.php"|| filename == "live_data.php" || filename  == "current_summary.php" || filename== "export.php" || filename== "set_alerts.php" || filename== "current_export.php" || filename== "current_histogram.php" || filename== "current_rates.php" || filename== "current_pores.php" || filename== "current_quality.php" || filename== "current_coverage.php" || filename== "current_development.php") {
                    var d = document.getElementById("currentruns");
                    d.className = d.className + " active";
                }
                if (filename == "admin_manage.php" || filename == "admin.php" || filename =="admin_users.php" || filename=="cache_manage.php" || filename =="enable.php"){
                    var d = document.getElementById("admin");
                    d.className = d.className + " active";
                }
                if (filename == "index.php"){
                    var d = document.getElementById("overview");
                    d.className = d.className + " active";
                }

            </script>
