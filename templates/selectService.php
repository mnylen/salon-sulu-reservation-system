<?php $activeTab = "reservation"; include 'header.php'; ?>
    
    <h2>Make a reservation</h2>
    
    <p>Welcome to the initial step in making a new reservation! Please select the service
       you would like to be performed by one of Salon Lulu employees.</p>
    
	<form action="selectService.php" id="selectServiceForm" method="post">
		<?php display_errors($form, 'service'); ?>
        <table class="dataTable">
            <tr>
                <th id="serviceNameCol">Service name</th>
                <th id="durationCol">Duration</th>
                <th id="priceCol">Price</th>
                <th id="descriptionCol">Description</th>
            </tr>
		<?php $i = 0; foreach ($form->getField('service')->getChoices() as $id => $service) { ?>
			<tr class="<?= ($i % 2 == 0) ? 'even' : 'odd';?>">
                <td><label><input type="radio" name="service" value="<?= $id; ?>" <?php if ($id == $form->getData('service')) { print 'checked="checked"'; } ?> /> <?= htmlspecialchars($service->name); ?></label></td>
                <td><?= $service->duration; ?> min</td>
                <td><?= $service->price; ?> EUR</td>
				<td><?= htmlspecialchars($service->description); ?></td>
			</tr>
		<?php $i++; }?>
		</table>
        
		<p><input type="submit" name="next" value="Next" /></p>
	</form>
<?php include 'footer.php'; ?>