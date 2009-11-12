<?php $activeTab = "employees"; include 'templates/admin/header.php'; ?>

<h2><a href="employees.php">Employees</a> &raquo;
    <a href="employee.php?id=<?= $employee->id; ?>"><?= htmlspecialchars($employee->first_name." ".$employee->last_name); ?></a> &raquo;
    Work schedule for <?= $date->format('Y-m-d'); ?></h2>

<?php if ($reservations->count() == 0) { ?>
    <p>There weren't any reservations for the selected date.</p>
<?php } else { ?>
    <?php while ( ($reservation = $reservations->next()) !== null ) { ?>
        <div class="reservation_data">
            <h4><?= date("H:i", $reservation->start_time);?> - <?= date("H:i", $reservation->end_time); ?>
                <?= htmlspecialchars(Service::find($reservation->service_id)->name); ?> (<?= $reservation->price; ?> EUR)</h4>
            
            <p>Customer: <?= htmlspecialchars($reservation->cust_fname). " ".htmlspecialchars($reservation->cust_lname); ?>
               (<?= htmlspecialchars($reservation->cust_phone); ?>; email:
                 <a href="mailto:<?= htmlspecialchars($reservation->cust_email);?>"><?= htmlspecialchars($reservation->cust_email);?></p>
        </div>
    <?php } ?>
<?php } ?>

<?php include 'templates/admin/footer.php'; ?>