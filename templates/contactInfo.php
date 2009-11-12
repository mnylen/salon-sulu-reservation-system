<?php $activeTab = "reservation"; include 'templates/header.php'; ?>

	<h2>Make reservation</h2>
	
	<dl>
		<dt>Selected service:</dt>
		<dd><?= htmlspecialchars($service->name); ?> (<?= $service->duration; ?> minutes, <?= $service->price; ?> EUR)
			(<a href="selectService.php">Change</a>)</dd>
		
		<dt>Employee:</dt>
		<dd><?= htmlspecialchars($employee->first_name." ".$employee->last_name); ?> (<a href="calendar.php">Change</a>)</dd>
		
		<dt>Time:</dt>
		<dd><?= date("Y-m-d H:i", $datetime); ?> (<a href="calendar.php">Change</a>)</dd>
	</dl>
	
	<h3>Contact info</h3>
	
	<p>In this step, please enter your contact information. This information will be used
	   to contact you if there's a problem with your reservation.</p>
	   
	<form action="contactInfo.php" method="post">
		<dl>
			<dt><label for="id_cust_fname">First name:</label></dt>
			<dd><input type="text" id="id_cust_fname" name="cust_fname" value="<?=htmlspecialchars($form->getData('cust_fname'));?>" />
				<?php display_errors($form, 'cust_fname'); ?></dd>
			
			<dt><label for="id_cust_lname">Last name:</label></dt>
			<dd><input type="text" id="id_cust_lname" name="cust_lname" value="<?=htmlspecialchars($form->getData('cust_lname'));?>" />
				<?php display_errors($form, 'cust_lname'); ?></dd>
				
			<dt><label for="id_cust_email">Email address:</label></dt>
			<dd><input type="text" id="id_cust_email" name="cust_email" value="<?=htmlspecialchars($form->getData('cust_email'));?>" />
				<?php display_errors($form, 'cust_email'); ?></dd>
				
			<dt><label for="id_cust_phone">Phone number:</label></dt>
			<dd><input type="text" id="id_cust_phone" name="cust_phone" value="<?=htmlspecialchars($form->getData('cust_phone'));?>" />
				<?php display_errors($form, 'cust_phone'); ?></dd>
				
			<dt><input type="submit" name="next" value="Next" /></dt>
		</dl>
	</form>
<?php include 'templates/footer.php'; ?>