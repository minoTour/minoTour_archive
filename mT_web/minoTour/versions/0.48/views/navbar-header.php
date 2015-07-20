 <div class="navbar-header">
     <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
         <span class="sr-only">Toggle navigation</span>
         <span class="icon-bar"></span>
         <span class="icon-bar"></span>
         <span class="icon-bar"></span>
     </button>
     <a class="navbar-brand" href="index.php">
							  <i class="fa fa-bolt"></i> minoTour FRICKIN LASERBEAMS v <?php echo $_SESSION['minotourversion'];?>
  						     
								 </a>
 </div>
 <?php checksessionvars();
	if(isset($_GET["roi"])){
		$_SESSION['focusrun']=$_GET["roi"];
	}
 ?>
 

 <!-- /.navbar-header -->
            <ul class="nav navbar-top-links navbar-right">
                
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
			<?php if ($_SESSION['user_name'] != "demo") { ?>
                        <li><a href="profile.php"><i class="fa fa-user fa-fw"></i> User Profile</a>
                        </li>
                        <li><a href="settings.php"><i class="fa fa-gear fa-fw"></i> Settings</a>
                        </li>
                        <li class="divider"></li>
                        <?php } ?>
                        <li><a href="index.php?logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>           
		   
			
			
            
