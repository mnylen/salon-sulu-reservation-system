<?php $activeTab = "cancel"; include 'templates/header.php'; ?>

	<h2>Cancel reservation</h2>
	
	<form action="cancelReservation.php" method="post">
		<?php display_errors($form); ?>
		
		<dl>
			<dt><label for="id_cust_email">Email address:</label></dt>
			<dd><input type="text" id="id_cust_email" name="cust_email" value="<?= htmlspecialchars($form->getData('cust_email')); ?>" />
				<?php display_errors($form, 'cust_email'); ?></dd>
				
			<dt><label for="id_cancel_key">Cancel key:</label></dt>
			<dd><input type="text" id="id_cancel_key" name="cancel_key" value="<?= htmlspecialchars($form->getData('cancel_key')); ?>" />
				<?php display_errors($form, 'cancel_key'); ?></dd>
				
			<dt><input type="submit" value="Next" /></dt>
		</dl>
	</form>
	
<?php include 'templates/footer.php'; ?>