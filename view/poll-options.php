<script type="text/javascript">
 $(document).ready(function(){

	<?php foreach($vars['poll']->Ways as $way) { ?>
		$('#way<?php echo $way->id; ?>').attr('checked', true);
	<?php } ?>
	
	$('input[type=checkbox]','#poll-options').click(function(){
		
		var check = $(this);
	 	$.ajax({
		   type: "POST",
		   url: "../../crud.php",
		   dataType: "html",
		   data: {view: 'poll-options', action: 'changeWay', pollid: '<?php echo $vars["poll"]->id; ?>', way: $(this).attr("way"), remove: $(this).attr('checked')},
		   success: function(data) {
		   		if(data=='true')
					check.attr('checked', 'checked');
				else
					check.removeAttr('checked');
				
				$('#way'+check.attr("way")+'-label').html("Changed successfully!").css("color","green").show('slow').hide('slow');
					
		   },
		   error: function(XMLHttpRequest, textStatus, errorThrown) {
				$('#way'+check.attr("way")+'-label').html("Unexpected Error!").css("color","red").show('slow').hide('slow');
		   }
	 	});
		return false;
	});

 });
</script>
<div id="poll-options">
	<h2 class="page-title">Poll <span>Options</span></h2>
	<form action="" class="box" method="post">
		<h4>Poll Features</h4>
		<!-- p>All the features are active by default, check the ones you dont want available.</p -->

		<ul>
        <?php foreach($vars['ways'] as $way){ ?>
			<li>
				<input type="checkbox" name="way<?php echo $way->id; ?>" id="way<?php echo $way->id; ?>" style="border:0;" way="<?php echo $way->id; ?>"/>
				<label>Allow <?php echo $way->name; ?></label>
				<label id="way<?php echo $way->id; ?>-label" style="color: green; font-weight:bold; display:none;">Changed successfully!</label>
			</li>
       <?php } ?>
			<li>			
		<input type="checkbox" id="multiple_responses"<?php echo $vars['repeatAnswer'] == "0" ? "" : " checked='checked'"  ?> way="5"/>
		<label for="multiple_responses">People may respond more than once</label>
			</li>
		</ul>
	</form>
	<div id="poll-actions" class="box">
		<h4>Actions</h4>
		<dl>
	                        <?php if (($_SESSION["user"]->roleName=='invalid')&&($vars['poll']->sesion_id == $vars['cookie'])){ ?>
								<?php if($vars['poll']->status=="active"){ ?>
			<dt><a href="<?php echo $GLOBALS["baseURL"]; ?>crud.php?view=poll-results&action=pausePollInResults&idPoll=<?php echo $vars['poll']->id; ?>">Close Poll</a></dt>
			<dd>Stops your poll temporarily</dd>
								<?php } else { ?>
			<dt><a href="<?php echo $GLOBALS["baseURL"]; ?>crud.php?view=poll-results&action=playPollinResults&idPoll=<?php echo $vars['poll']->id; ?>">Start Poll</a></dt>
			<dd>Starts you poll again.</dd>
								<?php }?>
						    <?php } else {?>
						         <?php if(($_SESSION["user"]->roleName!='invalid')&&($vars['poll']->id_user==$_SESSION["user"]->id) ) {?>
			<dt><a href="<?php echo $GLOBALS["baseURL"]; ?>view/edit-poll&idPoll=<?php echo $vars['poll']->id; ?>">Edit Poll</a></dt>
			<dd>Edit you poll question or options.</dd>
										<?php if($vars['poll']->status=="active"){ ?>
			<dt><a href="<?php echo $GLOBALS["baseURL"]; ?>crud.php?view=poll-results&action=pausePollInResults&idPoll=<?php echo $vars['poll']->id; ?>">Close Poll</a></dt>
			<dd>Stops your poll temporarily</dd>
										<?php } else { ?>
			<dt><a href="<?php echo $GLOBALS["baseURL"]; ?>crud.php?view=poll-results&action=playPollinResults&idPoll=<?php echo $vars['poll']->id; ?>">Start Poll</a></dt>
			<dd>Starts you poll again.</dd>
										<?php }?>
						         <?php } ?>
						    <?php } ?>
							    <?php if (($_SESSION["user"]->roleName == "invalid")&& ($vars['poll']->sesion_id == $vars['cookie'])){?>
			<dt><a href="<?php echo $GLOBALS["baseURL"]; ?>crud.php?view=poll-results&action=deleteAnswers&idPoll=<?php echo $vars['poll']->id; ?>">Clear Results</a></dt>
			<dd>Clears the graph and resets your poll’s answers</dd>
			<dt><a class="warning" href="<?php echo $GLOBALS["baseURL"]; ?>crud.php?view=poll-results&action=deletePoll&idPoll=<?php echo $vars['poll']->id; ?>">Delete Poll</a></dt>
			<dd>Deletes your Poll permanently</dd>
							    <?php } else {?>
								    <?php if(($_SESSION["user"]->roleName!='invalid')&&($vars['poll']->id_user==$_SESSION["user"]->id)){?>
			<dt><a href="<?php echo $GLOBALS["baseURL"]; ?>crud.php?view=poll-results&action=deleteAnswers&idPoll=<?php echo $vars['poll']->id; ?>">Clear Results</a></dt>
			<dd>Clears the graph and resets your poll’s answers</dd>
			<dt><a class="warning" href="<?php echo $GLOBALS["baseURL"]; ?>crud.php?view=poll-results&action=deletePoll&idPoll=<?php echo $vars['poll']->id; ?>">Delete Poll</a></dt>
			<dd>Deletes your Poll permanently</dd>
		                            <?php } ?>
							    <?php }?>
		</dl>
		<span class="clear">&nbsp;</span>
	</div>
</div>