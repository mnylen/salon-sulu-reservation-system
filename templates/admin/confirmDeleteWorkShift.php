<?php $activeTab = "employees"; include 'templates/admin/header.php'; ?>

    <h2>Confirm deletion of work shift</h2>
    
    
    <p>You are about to delete a work shift starting at <?= date('H:i', $shift->start_time);?>
         and ending at <?= date('H:i', $shift->end_time); ?> on <?= date('Y-m-d', $shift->start_time); ?>
         from employee <?php $emp = Employee::find($id); print htmlspecialchars($emp->first_name." ".$emp->last_name); ?></p>
    
    <?php if ($shift->reservationCount != 0) { ?>
        <p>There's total of <?= $shift->reservationCount; ?> reservations made for the time period
            of the shift. <strong>All of these reservations will be cancelled.</strong>.</p>
    <?php } ?>
    
    <form action="deleteWorkShift.php?id=<?= $shift->id; ?>" method="post">
        <input type="submit" name="confirm" value="Confirm" />
    </form>
<?php include 'templates/admin/footer.php'; ?>