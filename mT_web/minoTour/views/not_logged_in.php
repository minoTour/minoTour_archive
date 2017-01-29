<!DOCTYPE html>
<html>

<!--
Import the header.
-->
<?php
include 'includes/head-new.php';
?>

<body class="hold-transition skin-blue sidebar-mini layout-top-nav fixed">
<div class="wrapper">

<header class="main-header">
<nav class="navbar navbar-static-top">
<a href="index.php" class="logo">
<!-- mini logo for sidebar mini 50x50 pixels -->
<span class="logo-mini"><b>m</b>T<small></small></span>
<!-- logo for regular state and mobile devices -->
<span class="logo-lg"><b>mino</b>Tour</span>
</a>

<div class="container-fluid">
<div class="navbar-header">
</div><!-- /.navbar-collapse -->
</div><!-- /.container-fluid -->
</nav>
</header>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

<!-- Content Header (Page header) -->

<section class="content-header">
<h4 class="text-center"><img style="max-width:100px;" src="images/minotaurlogo.png" alt="minoTour_logo"><br>- <em>real time data analysis for minION data</em> -</h4>
<?php if (gethostname() == "minotour.nottingham.ac.uk") { ?>
<p class="text-center" >Welcome to minoTour - to test out this site log in as user 'demo' with password 'demouser'. Datasets published by Joshua Quick, Aaron R Quinlan and Nicholas J Loman in <a href ="http://www.gigasciencejournal.com/content/3/1/22/abstract" target="_blank">GigaScience</a> are presented under the 'Previous Runs' (initially under 'Current Sequencing Runs') option in the left hand navigation.</p>
<p class="text-center">For access to minoTour or to set up your own servers please contact Matt Loose -> <a href="mailto:matt.loose@nottingham.ac.uk?Subject=minoTour%20information%20request" target="_top"><i class="fa fa-envelope-o"></i></a> <i class="fa fa-twitter-square" ></i> @mattloose.</p>
<?php } ?>


</section>

<section class="content">
    <div class="row">
    <div class="col-md-6 col-md-offset-3">
    <div class="box">
    <div class="box-header">
      <h3 class="box-title">Login or Register</h3>
    </div><!-- /.box-header -->
    <div class="box-body">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Please Sign In</h3>
                    </div>
                    <div class="panel-body">
                        <form role="form" method="post" action="index.php" name="loginform">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" id="login_input_username" placeholder="User Name" name="user_name" type="text" autofocus>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" id="login_input_password" placeholder="Password" name="user_password" autocomplete="off" type="password" required />
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <input type="submit"  class="btn btn-lg btn-success btn-block" name="login" value="Log in" />
                            </fieldset>
                        </form>
                        <h5><a href="register_new.php">Click Here To Register New account</a></h5>
                    </div>
                </div>

            </div>

        <?php
        // show potential errors / feedback (from login object)
        if (isset($login)) {
        if ($login->errors) {
            foreach ($login->errors as $error) {
                echo $error;
            }
        }
        if ($login->messages) {
            foreach ($login->messages as $message) {
                echo $message;
            }
        }
        }
        ?>
        </div>
    </div>
</div>
<div class="row">
<div class="col-md-6 col-md-offset-3">
  <div class="box box-solid">
    <div class="box-header with-border">
      <h3 class="box-title">minoTour Screenshots</h3>
    </div><!-- /.box-header -->
    <div class="box-body">
      <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
          <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
          <li data-target="#carousel-example-generic" data-slide-to="1" class=""></li>
          <li data-target="#carousel-example-generic" data-slide-to="2" class=""></li>
        </ol>
        <div class="carousel-inner">
          <div class="item active">
            <img src="images/real_time_monitoring.JPG" alt="First slide">
            <div class="carousel-caption">
              Real Time Run Monitoring
            </div>
          </div>
          <div class="item">
            <img src="images/remote_control.jpg" alt="Second slide">
            <div class="carousel-caption">
              Full Remote Control of MinKNOW
            </div>
          </div>
          <div class="item">
            <img src="images/run_archive.jpg" alt="Third slide">
            <div class="carousel-caption">
              Archive of Real Time Stats from MinKNOW
            </div>
          </div>
        </div>
        <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
          <span class="fa fa-angle-left"></span>
        </a>
        <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
          <span class="fa fa-angle-right"></span>
        </a>
      </div>
    </div><!-- /.box-body -->
  </div><!-- /.box -->
</div><!-- /.col -->


</div>
<div class="row">
<div class="col-md-6 col-md-offset-3">
<div class="box">
<div class="box-header">
  <h3 class="box-title">Video Tour of minoTour</h3>
</div><!-- /.box-header -->
<div class="box-body">


        <iframe src="//www.youtube.com/embed/gbyvhJOrjZw" frameborder="0" allowfullscreen></iframe>


    </div>
    </div>
</div>
</div>
</section>

</div>



</div>
<?php include 'includes/reporting-new.php'; ?>
</body>

</html>
