
  <p class="text-center"><small>This website and database backend were developed at the University of Nottingham by the DeepSeq Informatics Team. <br><img style="max-width:30px;" src="images/minotaurlogosmall.png" alt="minoTour_logo"><br> Please contact us <a href="mailto:matt.loose@nottingham.ac.uk"><i class="fa fa-envelope-square"></i></a> for more information.</small></p>

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
