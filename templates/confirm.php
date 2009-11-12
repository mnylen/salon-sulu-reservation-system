<?php $activeTab = "reservation"; include 'templates/header.php'; ?>
	<h2>Confirm reservation details</h2>
	
	<dl>
		<dt>Selected service: (<a href="selectService.php">Change</a>)</dt>
		<dd><?= $service->name; ?> (<?= $service->duration; ?> minutes, <?= $service->price; ?> EUR)</dd>
		
		<dt>Employee: (<a href="calendar.php">Change</a>)</dt>
		<dd><?= htmlspecialchars($employee->first_name." ".$employee->last_name); ?> </dd>
		
		<dt>Time:  (<a href="calendar.php?employee=<?= $employee->id; ?>">Change</a>)</dt>
		<dd><?= date('Y-m-d H:i', $datetime); ?></dd>
		
		<dt>Contact info: (<a href="contactInfo.php">Edit</a>)</dt>
		<dd><?= htmlspecialchars($_SESSION['reservation.cust_fname']. " ".$_SESSION['reservation.cust_lname']); ?></a></dd>
		<dd><?= htmlspecialchars($_SESSION['reservation.cust_email']); ?></dd>
		<dd>Phone: <?= htmlspecialchars($_SESSION['reservation.cust_phone']); ?></dd>
	</dl>
	
	<form method="post" action="confirm.php">
		<input type="submit" name="confirm" value="Confirm" />
	</form>
<?php include 'templates/footer.php'; ?>