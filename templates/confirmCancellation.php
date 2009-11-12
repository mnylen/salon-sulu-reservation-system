<?php $activeTab = "reservation"; include 'templates/header.php'; ?>
	<h2>Confirm cancelling your reservation</h2>
	
	<p>Confirm the details below and click confirm to confirm the cancellation of your
	   reservation. This can not be undone!</p>
	
	<dl>
		<dt>Reserved service:</dt>
		<dd><?= htmlspecialchars($service->name); ?></dd>
		
		<dt>Price:</dt>
		<dd><?= $reservation->price; ?> EUR</dd>
		
		<dt>Employee:</dt>
		<dd><?= htmlspecialchars($employee->first_name." ".$employee->last_name); ?></dd>
		
		<dt>Time:</dt>
		<dd><?= date('Y-m-d H:i', $reservation->start_time); ?> - <?= date('H:i', $reservation->end_time); ?></dd>
		
		<dt>Contact info:</dt>
		<dd><?= htmlspecialchars($reservation->cust_fname. " ".$reservation->cust_lname); ?></dd>
		<dd><?= htmlspecialchars($reservation->cust_email);?></dd>
		<dd>Phone: <?= htmlspecialchars($reservation->cust_phone); ?></dd>
	</dl>
	
	<form method="post" action="confirmCancellation.php?cust_email=<?= htmlspecialchars($_GET['cust_email']);?>&cancel_key=<?= htmlspecialchars($_GET['cancel_key']); ?>">
		<input type="submit" name="confirm" value="Confirm" />
	</form>
<?php include 'templates/footer.php'; ?>