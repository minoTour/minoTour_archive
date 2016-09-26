<!-- Main Footer -->
<footer class="main-footer">
  <!-- To the right -->
  <div class="pull-right hidden-xs">
    <img style="max-width:100px;" src="images/minotaurlogo.png" alt="minoTour_logo">
  </div>
  <!-- Default to the left -->
  <strong>Copyright &copy; Matt Loose 2016 <a href="www.nottingham.ac.uk">University of Nottingham</a>.</strong> Contact @mattloose for more information.
</footer>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
  <!-- Create the tabs -->
  <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
    <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
    <li><a href="#control-sidebar-twitter-tab" data-toggle="tab"><i class="fa fa-twitter"></i></a></li>
    <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
  </ul>
  <!-- Tab panes -->
  <div class="tab-content">
    <!-- Home tab content -->
    <div class="tab-pane active" id="control-sidebar-home-tab">
      <h3 class="control-sidebar-heading">Recent Activity</h3>
      <p> These buttons will report bugs and feature requests direct to the developers. For specific problems with your local installation you should contact the host of your site.</p>
      <?php include 'includes/bugsandfeatures2.php';?>


    </div><!-- /.tab-pane -->
    <!-- Twitter Tab Content -->
    <div class="tab-pane" id="control-sidebar-twitter-tab">
        <h3 class = "control-sidebar-heading">Twitter Feed</h3>

    <a class="twitter-timeline" href="https://twitter.com/minoTourSoftwar" data-widget-id="709828056492408834">Tweets by @minoTourSoftwar</a> <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
    </div><!-- /.tab-pane -->
    <!-- Stats tab content -->
    <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div><!-- /.tab-pane -->
    <!-- Settings tab content -->
    <div class="tab-pane" id="control-sidebar-settings-tab">
      <form method="post">
        <h3 class="control-sidebar-heading">General Settings</h3>
        <div class="form-group">
          <label class="control-sidebar-subheading">
            Report panel usage
            <input type="checkbox" class="pull-right" checked>
          </label>
          <p>
            Some information about this general settings option
          </p>
        </div><!-- /.form-group -->
      </form>
    </div><!-- /.tab-pane -->
  </div>
</aside><!-- /.control-sidebar -->
<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>
</div><!-- ./wrapper -->



<!-- jQuery 2.1.4 -->
<script src="plugins/jQuery/jQuery-2.1.4.min.js"></script>
<!-- Bootstrap 3.3.5 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>


<script type="text/javascript" src="js/pnotify.custom.min.js"></script>
<script type="text/javascript">
PNotify.prototype.options.styling = "fontawesome";
</script>

<script>
  $("#bugsubmit").click(function()
    {
	    var contentType ="application/x-www-form-urlencoded; charset=utf-8";
	    if(window.XDomainRequest)
	        contentType = "text/plain";
		var postData = $('form#bugform').serialize();
		//alert(postData);
       $.ajax({
         url:"http://www.nottingham.ac.uk/~plzloose/minoTourhome/bug_receive.php",
         data:postData,
         type:"POST",
         dataType:"json",
         contentType:contentType,
         success:function(data)
         {
	       $.each(data, function(key,value){
			  //checking version info.
			  if (key == 'version'){
				  if (value == '<?php echo $_SESSION['minotourversion'];?>'){
				  	  $('#bugcontent').html("You are running the most recent version of minoTour - version "+value+".<br>");
				  }else if (value < '<?php echo $_SESSION['minotourversion'];?>'){
					  $('#bugcontent').html("You appear to be in the fortunate position of running a future version of the minoTour web application "+value+". If you have modified the code yourself - great. If not then there might be an issue somewhere!.<br>");
				  }else if (value > '<?php echo $_SESSION['minotourversion'];?>'){
					  $('#bugcontent').html("You are running an outdated version of the minoTour web application. The most recent version of minoTour is version "+value+".<br>"+"Instructions for upgrading will be posted below.<br>");
				  }


			  }else if (key.substring(0, 7) == 'message') {
				  $('#bugcontent').html(value + "<br>");
			  	}
	       });
            //alert("Data from Server"+JSON.stringify(data));
         },
         error:function(jqXHR,textStatus,errorThrown)
         {
            alert("You can not send Cross Domain AJAX requests: "+errorThrown);
         }
        });

    });



</script>


<script>
    $("#featuresubmit").click(function()
    {
	    var contentType ="application/x-www-form-urlencoded; charset=utf-8";

	    if(window.XDomainRequest)
	        contentType = "text/plain";

		var postData = $('form#featureform').serialize();
        $.ajax({
         url:"http://www.nottingham.ac.uk/~plzloose/minoTourhome/feature_receive.php",
		 data:postData,
         type:"POST",
         dataType:"json",
         contentType:contentType,
         success:function(data)
         {
			 $.each(data, function(key,value){
			 	  //checking version info.
			 		  if (key == 'version'){
			 			  if (value == '<?php echo $_SESSION['minotourversion'];?>'){
			 			  	  $('#featurecontent').html("You are running the most recent version of minoTour - version "+value+".<br>");
			 			  }else if (value < '<?php echo $_SESSION['minotourversion'];?>'){
			 				  $('#featurecontent').html("You appear to be in the fortunate position of running a future version of the minoTour web application "+value+". If you have modified the code yourself - great. If not then there might be an issue somewhere!.<br>");
			 			  }else if (value > '<?php echo $_SESSION['minotourversion'];?>'){
			 				  $('#featurecontent').html("You are running an outdated version of the minoTour web application. The most recent version of minoTour is version "+value+".<br>"+"Instructions for upgrading will be posted below.<br>");
			 			  }


			 		  }else if (key.substring(0, 7) == 'message') {
			 			  $('#featurecontent').html(value + "<br>");
			 		  	}
				});
         },
         error:function(jqXHR,textStatus,errorThrown)
         {
            alert("You can not send Cross Domain AJAX requests: "+errorThrown);
         }
        });

    });




</script>

<script>
   $( "#infodiv" ).load( "alertcheck.php" ).fadeIn("slow");

   eval(document.getElementById("infodiv").innerHTML);
   var auto_refresh = setInterval(function ()
       {
       $( "#infodiv" ).load( "alertcheck.php" ).fadeIn("slow");
       eval(document.getElementById("infodiv").innerHTML);
       }, 10000); // refresh every 5000 milliseconds
</script>

<script>
   $( "#API" ).load( "api/api.php" );

   eval(document.getElementById("API").innerHTML);
   var auto_refresh = setInterval(function ()
       {
       $( "#API" ).load( "api/api.php" );
       eval(document.getElementById("API").innerHTML);
       }, 10000); // refresh every 5000 milliseconds
</script>
<script>
   $( "#API" ).load( "api/api_prev.php" );

   eval(document.getElementById("APIPREV").innerHTML);
   var auto_refresh = setInterval(function ()
       {
       $( "#API" ).load( "api/api_prev.php" );
       eval(document.getElementById("APIPREV").innerHTML);
       }, 10000); // refresh every 5000 milliseconds
</script>
<script type="text/javascript" src="js/pnotify.custom.min.js"></script>
<script type="text/javascript">
PNotify.prototype.options.styling = "fontawesome";
</script>
<!-- Highcharts Addition -->
<script src="http://code.highcharts.com/highcharts.js"></script>
<script type="text/javascript" src="js/themes/grid-light.js"></script>
<script src="http://code.highcharts.com/4.0.3/modules/heatmap.js"></script>
<script src="http://code.highcharts.com/modules/exporting.js"></script>
<script src="http://code.highcharts.com/highcharts-more.js"></script>
<script src="https://raw.githubusercontent.com/highcharts/export-csv/master/export-csv.js"></script>
<script src="js/ion.rangeSlider.js"></script>
<script src="http://code.highcharts.com/modules/no-data-to-display.js"></script>
