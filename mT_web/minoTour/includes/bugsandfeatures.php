<!-- Button trigger modal -->

<div class="modal fade" id="bugreport" tabindex="-1" role="dialog" aria-labelledby="bugreport" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="bugreport"><i class="fa fa-bug"></i> Bug Report!</h4>
			</div>
			<div class="modal-body">
				<div id="bugcontent">
				<form role="form" id='bugform'>
				  <div class="form-group">
				    <label for="bugEmail">Email address</label>
				    <input type="email" class="form-control" name="bugEmail" id="bugEmail" placeholder="Enter email so we can contact you about this bug.">
				  </div>
				  <div class="form-group">
				    <label for="bugusername">User Name</label>
				    <input type="value" class="form-control" name="bugusername" id="bugusername" placeholder="User Name" value="<?php echo $_SESSION['user_name']; ?>">
				  </div>
				  <div class="form-group">
				    <label for="bugcurrentpage">Current Page</label>
				    <input type="value" class="form-control" name="bugcurrentpage" id="bugcurrentpage" placeholder="Current Page" value="<?php echo $_SERVER['PHP_SELF'];?>">
				  </div>
				  <div class="form-group">
				    <label for="bugip">Your IP</label>
				    <input type="value" class="form-control" name="bugip" id="bugip" placeholder="IP Address" value="<?php echo $_SERVER['REMOTE_ADDR'];?>">
				  </div>
				  <div class="form-group">
				    <label for="bugdatetime">Date/Time</label>
				    <input type="value" class="form-control" name="bugdatetime" id="bugdatetime" placeholder="Date Time" value="<?php date_default_timezone_set('UTC'); echo date("F j, Y, g:i a");?>">
				  </div>
				  <div class="form-group">
				    <label for="bugversion">minoTour Version</label>
				    <input type="value" class="form-control" name="bugversion" id="bugversion" placeholder="minoTour Version" value="<?php echo $_SESSION['minotourversion'];?>">
				  </div>
				  <div class="form-group">
				    <label for="bugcomment">Bug Report Details</label>
					  <textarea class="form-control" name="bugcomment" id="bugcomment" placeholder="Please try and keep this bug report concise!" rows="3"></textarea>
				  </div>
				  <button type="button" id="bugsubmit" class="btn btn-default">Submit</button>
				</form>
			</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<!-- Button trigger modal -->
			
<div class="modal fade" id="feature" tabindex="-1" role="dialog" aria-labelledby="feature" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="feature"><i class="fa fa-lightbulb-o"></i> Feature Request!</h4>
			</div>
			<div class="modal-body">
				<div id="featurecontent">
					<form role="form" id='featureform'>
				  <div class="form-group">
				    <label for="Email">Email address</label>
				    <input type="email" class="form-control" name="featureEmail" id="featureEmail" placeholder="Enter email so we can contact you about this feature.">
				  </div>
				  <div class="form-group">
				    <label for="featureusername">User Name</label>
				    <input type="value" class="form-control" name="featureusername" id="featureusername" placeholder="User Name" value="<?php echo $_SESSION['user_name']; ?>">
				  </div>
				  <div class="form-group">
				    <label for="featurecurrentpage">Current Page</label>
				    <input type="value" class="form-control" name="featurecurrentpage" id="featurecurrentpage" placeholder="Current Page" value="<?php echo $_SERVER['PHP_SELF'];?>">
				  </div>
				  <div class="form-group">
				    <label for="featureip">Your IP</label>
				    <input type="value" class="form-control" name="featureip" id="featureip" placeholder="IP Address" value="<?php echo $_SERVER['REMOTE_ADDR'];?>">
				  </div>
				  <div class="form-group">
				    <label for="featuredatetime">Date/Time</label>
				    <input type="value" class="form-control" name="featuredatetime" id="featuredatetime" placeholder="Date Time" value="<?php date_default_timezone_set('UTC'); echo date("F j, Y, g:i a");?>">
				  </div>
				  <div class="form-group">
				    <label for="featureversion">minoTour Version</label>
				    <input type="value" class="form-control" name="featureversion" id="featureversion" placeholder="minoTour Version" value="<?php echo $_SESSION['minotourversion'];?>">
				  </div>
				  <div class="form-group">
				    <label for="featurecomment">Feature Report Details</label>
					  <textarea class="form-control" name="featurecomment" id="featurecomment" placeholder="Please try and keep this feature report concise!" rows="3"></textarea>
				  </div>
				  <button type="button" id="featuresubmit" class="btn btn-default">Submit</button>
				</form>
			</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
