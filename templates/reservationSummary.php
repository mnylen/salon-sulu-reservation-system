<?php include 'templates/header.php'; ?>
	<h2>Reservation placed</h2>
	
	<p>Thanks for making a reservation for Salon Lulu!</p>
    
    <p>Please save the cancellation key if you want to cancel your reservation for some reason.
       It is required for online-cancellation.</p>
    
    <h3>Reservation details</h3>
	
	<dl>
		<dt>Reserved service:</dt>
		<dd><?= htmlspecialchars($service->name); ?></dd>
        
        <dt>Cancellation key:</dt>
        <dd><?= $reservation->cancel_key; ?></dd>
		
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
	
	   
<?php include 'templates/footer.php'; ?>