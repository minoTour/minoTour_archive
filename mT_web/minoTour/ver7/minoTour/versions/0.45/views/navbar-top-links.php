            <div class="navbar-default navbar-static-side" role="navigation">
                <div class="sidebar-collapse">
                    <ul class="nav" id="side-menu">
                        <li class="sidebar-search">
							<span class="fa-stack">
							  <i class="fa fa-circle fa-stack-2x"></i>
							  <i class="fa fa-user fa-stack-1x fa-inverse"></i>
							</span> User: <?php echo $_SESSION['user_name']; ?>
								
								
						    
                            <!--<div class="input-group custom-search-form">
                                <input type="text" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            </div>-->
                            <!-- /input-group -->
                        </li>
                        <li>
                            <a href="index.php"><i class="fa fa-dashboard fa-fw"></i> Overview</a>
                        </li>
                        <?php if (checkactiverun()) { ?>
						<li id="currentruns">
                            <a href="#"><i class="fa fa-bolt fa-fw"></i> Current Sequencing Run<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <?php if ( checknumactive() > 1){?>
                                <li>
                                    <a href="switch_run.php"><i class="fa fa-database fa-fw"></i> Switch Active Runs</a>
                                </li>
                                <?php }?>
                                
                                <li>
                                    <a href="live_data.php"><i class="fa fa-cog fa-spin"></i> Live Data</a>
                                </li>
                                
                                <li>
                                    <a href="current_summary.php"><i class="fa fa-bar-chart-o fa-fw"></i> Data Summary</a>
                                </li>
                                <li>
                                    <a href="live_reads_table.php"><i class="fa fa-eye fa-fw"></i> Individual Read Data</a>
                                </li>
                                <li>
                                    <a href="export.php"><i class="fa fa-file-text-o fa-fw"></i> Export Reads</a>
                                </li>
                                <li>
                                    <a href="set_alerts.php"><i class="fa fa-exclamation-circle"></i> Set Alerts</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
						<?php } ?>
						<?php if (checkallruns()) {?>
                        <li id="prevruns">
                            <a href="#"><i class="fa fa-table fa-fw"></i> Previous Runs<span class="fa arrow"></a>
                            <ul class="nav nav-second-level">
                                
								<?php if (isset($_SESSION['focusrun'])) {?>
	                            <li>
	                                <a href="previous_runs.php" id="SR"><i class="fa fa-database fa-fw"></i> Change Run</a>
	                            </li>
								<li>
                                    <a href="previous_summary.php"><i class="fa fa-bar-chart-o fa-fw"></i> Data Summary</a>
                                </li>
                                <li>
                                    <a href="reads_table.php"><i class="fa fa-eye fa-fw"></i> Individual Read Data</a>
                                </li>
                                <li>
                                    <a href="previous_export.php"><i class="fa fa-file-text-o fa-fw"></i> Export Reads</a>
                                </li>
                                <li>
                                    <a href="manage_data.php"><i class="fa fa-pencil-square-o"></i> Manage Data</a>
                                </li>
								<?php }else{?>
	                            <li>
	                                <a href="previous_runs.php" id="SR"><i class="fa fa-database fa-fw"></i> Select Run</a>
	                            </li>
								<?php };?>
                            </ul>
                        </li>
						<?php }?>
                        <?php if ($_SESSION['adminuser'] == 1){?>
						<li id = "admin">
                            <a href="#"><i class="fa fa-edit fa-fw"></i> Admin<span class="fa arrow"></span></a>
    						<ul class="nav nav-second-level">
    							<li>
    								<a href="enable.php"><i class="fa fa-child fa-fw"></i> Enable User Upload</a>
    							</li>
    	                        <li>
                                    <a href="admin.php"><i class="fa fa-database fa-fw"></i> Database Allocation</a>
 	                          </li>
    	                        <li>
                                    <a href="admin_users.php"><i class="fa fa-users fa-fw"></i> Set Administrators</a>
 	                          </li>
 	                        </ul>
                        </li>
                        <?php };?>
                        <li>
                        	<a href="minup.php"><i class="fa fa-cloud-upload fa-fw"></i>minUp Scripts</span></a>
                        <li>
                            <a href="exlinks.php"><i class="fa fa-files-o fa-fw"></i>External Links</span></a>
                            
                            <!-- /.nav-second-level -->
                        </li>
                    </ul>
					<br>
					<br>
					
						
                        <?php //if (isset($_SESSION['active_run_name'])) {
                            checkalerts();
							//}?>

                        <div class='alert alert-info' role='alert'>
                            <small>This website and database backend were developed at the University of Nottingham by the DeepSeq Informatics Team. Please contact us <a href="mailto:matt.loose@nottingham.ac.uk"><i class="fa fa-envelope-square"></i></a> for more information.</small>
                        </div>
					
                    <!-- /#side-menu -->
                </div>
                <!-- /.sidebar-collapse -->
            </div>
			
			<script>
				var pathname = window.location.pathname;
				//alert(pathname);
				parts = pathname.split("/");
							var filename = parts[parts.length - 1];
							//alert(filename);
							if (filename == "previous_runs.php" || filename  == "previous_summary.php" || filename  == "previous_histogram.php" || filename== "previous_export.php" || filename== "previous_rates.php" || filename== "previous_pores.php" || filename== "previous_quality.php" || filename== "previous_coverage.php" || filename=="reads_table.php" || filename=="manage_data.php"){
								var d = document.getElementById("prevruns");
								d.className = d.className + " active";
							}
							if (filename == "switch_run.php" || filename == "live_reads_table.php" || filename  == "current_histogram.php"|| filename == "live_data.php" || filename  == "current_summary.php" || filename== "export.php" || filename== "set_alerts.php" || filename== "current_export.php" || filename== "current_histogram.php" || filename== "current_rates.php" || filename== "current_pores.php" || filename== "current_quality.php" || filename== "current_coverage.php") {
								var d = document.getElementById("currentruns");
								d.className = d.className + " active";
							}
							if (filename == "admin.php" || filename =="admin_users.php"){
								var d = document.getElementById("admin");
								d.className = d.className + " active";
							}
				
						</script>
				