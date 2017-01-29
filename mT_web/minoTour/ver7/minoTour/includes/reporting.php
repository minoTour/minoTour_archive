<!-- Main Footer -->
<footer class="main-footer">
  <!-- To the right -->
  <div class="pull-right hidden-xs">
  </div>
  <!-- Default to the left -->
  <p class="text-center"><small>This website and database backend were developed at the University of Nottingham by the DeepSeq Informatics Team. <br><img style="max-width:30px;" src="images/minotaurlogosmall.png" alt="minoTour_logo"><br> Please contact us <a href="mailto:matt.loose@nottingham.ac.uk"><i class="fa fa-envelope-square"></i></a> for more information.</small></p>
</footer>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
  <!-- Create the tabs -->
  <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
    <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
    <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
  </ul>
  <!-- Tab panes -->
  <div class="tab-content">
    <!-- Home tab content -->
    <div class="tab-pane active" id="control-sidebar-home-tab">
      <h3 class="control-sidebar-heading">Recent Activity</h3>
      <ul class="control-sidebar-menu">
        <li>
          <a href="javascript::;">
            <i class="menu-icon fa fa-birthday-cake bg-red"></i>
            <div class="menu-info">
              <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>
              <p>Will be 23 on April 24th</p>
            </div>
          </a>
        </li>
      </ul><!-- /.control-sidebar-menu -->

      <h3 class="control-sidebar-heading">Tasks Progress</h3>
      <ul class="control-sidebar-menu">
        <li>
          <a href="javascript::;">
            <h4 class="control-sidebar-subheading">
              Custom Template Design
              <span class="label label-danger pull-right">70%</span>
            </h4>
            <div class="progress progress-xxs">
              <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
            </div>
          </a>
        </li>
      </ul><!-- /.control-sidebar-menu -->

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

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 2.1.4 -->
<script src="plugins/jQuery/jQuery-2.1.4.min.js"></script>
<!-- Bootstrap 3.3.5 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
   Both of these plugins are recommended to enhance the
   user experience. Slimscroll is required when using the
   fixed layout. -->
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
