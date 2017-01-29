<!-- Main Header -->
<?php
//As user is logged in, we can now look at the memcache to retrieve data from here and so reduce the load on the mySQL server
	// Connection creation
	$memcache = new Memcache;
	#$cacheAvailable = $memcache->connect(MEMCACHED_HOST, MEMCACHED_PORT) or die ("Memcached Failure");
	$cacheAvailable = $memcache->connect(MEMCACHED_HOST, MEMCACHED_PORT);

    ?>
<header class="main-header">

  <!-- Logo -->
  <a href="index.php" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><img style="max-width:20px;" src="images/minitour.png" alt="minoTour_logo"><small><?php echo $_SESSION['minotourversion'];?></small></span>
    <!--<span class="logo-mini"><b>m</b>T<small><?php #echo $_SESSION['minotourversion'];?></small></span>-->
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg"><img style="max-width:100px;" src="images/minotaurlogo.png" alt="minoTour_logo"> <?php echo $_SESSION['minotourversion'];?></span>
    <!--<span class="logo-lg"><b>mino</b>Tour v <?php #echo $_SESSION['minotourversion'];?></span>-->
  </a>

  <!-- Header Navbar -->
  <nav class="navbar navbar-static-top" role="navigation">
      <div id='API'></div>
      <div id='APIPREV'></div>
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a>
    <!-- Navbar Right Menu -->
    <div id="sumdat">
    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <!-- Messages: style can be found in dropdown.less-->
        <li class="dropdown messages-menu">
          <!-- Menu toggle button -->
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-table"></i>
            <span class="label label-success">
                {{livenumruns}}/{{prevnumruns}}
            </span>
          </a>
          <ul class="dropdown-menu">

            <li class="header">You have {{livenumruns}} live runs</li>
            <li>
              <!-- inner menu: contains the messages -->
              <ul class="menu">
                <li v-for="run in liverunsnames"><!-- start message -->
                  <a href="#">
                    <div class="pull-left">
                      <!-- User Image -->
                      <!--<img src="images/minitour144.png" class="img-circle" alt="User Image">-->
                  <!--</div>-->
                    <!-- Message title and timestamp -->
                    <h4>
                      <small>{{run}}</small>
                      <!--<small><i class="fa fa-clock-o"></i> 5 mins</small>-->
                    </h4>
                    <!-- The message -->
                    <!--<p>Why not buy a new awesome theme?</p></div>-->
                  </a>
                </li><!-- end message -->
              </ul><!-- /.menu -->
            </li>
            <li class="footer"><a href="#">You have {{prevnumruns}} archived runs.</a></li>
          </ul>
        </li><!-- /.messages-menu -->

        <!-- Notifications Menu -->
        <li class="dropdown notifications-menu">
          <!-- Menu toggle button -->
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-bolt"></i>
            <span class="label label-danger">{{activealerts}}</span>
          </a>
          <ul class="dropdown-menu">
            <li class="header">You have {{activealerts}} active alerts</li>
            <li>
              <!-- Inner Menu: contains the notifications -->
              <ul class="menu">
                <li><!-- start notification -->
                  <!--<a href="#">
                    <i class="fa fa-users text-aqua"></i> 5 new members joined today
                </a>-->
                </li><!-- end notification -->
              </ul>
            </li>
            <li class="footer"><a href="#">View all</a></li>
          </ul>
        </li>
        <!-- Tasks Menu -->
        <li class="dropdown tasks-menu">
          <!-- Menu Toggle Button -->
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-flag-o"></i>
            <span class="label label-warning">{{completedalerts}}</span>
          </a>
          <ul class="dropdown-menu">
            <li class="header">You have {{completedalerts}} completed alerts</li>
            <li>
              <!-- Inner menu: contains the tasks -->
              <ul class="menu">
                <li><!-- Task item -->
                    <!--
                  <a href="#">
                    <!-- Task title and progress text -->
                    <!--<h3>
                      Design some buttons
                      <small class="pull-right">20%</small>
                  </h3> -->
                    <!-- The progress bar -->

                  <!--</a>-->
                </li><!-- end task item -->
              </ul>
            </li>
            <li class="footer">
              <a href="#">View all tasks</a>
            </li>
          </ul>
        </li>
        <!-- User Account Menu -->
        <li class="dropdown user user-menu">
          <!-- Menu Toggle Button -->
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <!-- The user image in the navbar-->
            <img src="images/minitour.png" class="user-image" alt="User Image">
            <!-- hidden-xs hides the username on small devices so only the image appears. -->
            <span class="hidden-xs"><?php echo $_SESSION['user_name'];?></span>
          </a>
          <ul class="dropdown-menu">
            <!-- The user image in the menu -->
            <li class="user-header">
              <img src="images/minitour.png" class="img-circle" alt="User Image">
              <p>
                <?php echo $_SESSION['user_name'];?>
                <small>Manage your user account here.</small>
              </p>
            </li>
            <!-- Menu Body -->
            <li class="user-body">
              <div class="col-xs-4 text-center">
                <a href="settings.php">Change Password</a>
              </div>
              <div class="col-xs-4 text-center">
                <a href="profile.php" >Profile</a>
              </div>
              <!--<div class="col-xs-4 text-center">
                <a href="#">Friends</a>
            </div>-->
            </li>
            <!-- Menu Footer-->
            <li class="user-footer">
              <!--<div class="pull-left">
                <a href="profile.php" class="btn btn-default btn-flat">Profile</a>
            </div>-->

          <div class="pull-right">
                <a href="index.php?logout" class="btn btn-default btn-flat">Sign out</a>
              </div>
            </li>
          </ul>
        </li>
        <!-- Control Sidebar Toggle Button -->
        <li>
          <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
        </li>
      </ul>
    </div>
    </div>
  </nav>
</header>
