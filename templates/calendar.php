<?php $activeTab = "reservation"; include 'templates/header.php'; ?>
	<h2>Make reservation</h2>
	
	<dl>
		<dt>Selected service:</dt>
		<dd><?= $service->name; ?> (<?= $service->duration; ?> minutes, <?= $service->price; ?> EUR)
			(<a href="selectService.php">Change</a>)</dd>
	</dl>
	
	<h3>Pick date &amp; time</h3>
	
	<?php if ($pickedDateTime != null) { ?>
		<div id="confirmSelection">
            <form action="calendar.php?employee=<?= $employee->id; ?>&datetime=<?= $pickedDateTime; ?>" method="post">
                <dl>
                    <dt>Selected employee:</dt>
                    <dd><?= htmlspecialchars($employee->first_name.' '.$employee->last_name); ?></dd>
            
                    <dt>Selected date &amp; time:</dt>
                    <dd><?= date('Y-m-d H:i', $pickedDateTime); ?></dd>
			
                    <dt><input type="submit" value="Confirm" /></dt>
                </dl>
            </form>
		</div>
	<?php } ?>
	
	<p>You can change the employee, who's schedule you are viewing, by clicking the
	   employees' name. To pick a date &amp; time, click any cell marked as
       <strong>free</strong>.</p>
	
	<?php if ($errors) { ?>
		<ul class="errorlist">
			<?php foreach ($errors as $error) { ?>
				<li><?= $error; ?></li>
			<?php } ?>
		</ul>
	<?php } ?>
	
	<div id="employeeList" class="buttonRow">
        <ul>
		<?php while ( ($emp = $employees->next()) !== null ) { ?>
			<li <?php if ($employee->id == $emp->id) { print ' class="selected"'; } ?>>
				<a href="calendar.php?employee=<?= $emp->id; ?>"><?= htmlspecialchars($emp->first_name." ".$emp->last_name); ?></a></li>
		<?php } ?>
        </ul>
	</div>
	
	<div id="calendarControls" class="buttonRow">
        <ul>
        <?php if ($prevWeek) { ?><li><a href="calendar.php?employee=<?= $employee->id; ?>&week=<?=$prevWeek; ?>">&laquo; Previous week</a></li><?php }?>
		<li><a href="calendar.php?employee=<?= $employee->id;?>&week=<?= $nextWeek; ?>">Next week &raquo;</a></li>
        </ul>
    </div>
	
	<table id="calendar" class="dataTable">
		<tr>
			<th>&nbsp;</th>
			<?php foreach ($calendar->getDates() as $date) { ?>
				<th><?= date('D j/n', $date); ?></td>
			<?php } ?>
		</tr>
		<?php $i = 0; $cells = $calendar->getCellRows(); foreach ($cells as $row) { ?>
			<tr class="<?= ($i % 2 == 0) ? 'even' : 'odd';?>">
				<th><?= date('H:i', $row[0]->getStartTime()); ?></td>
				<?php foreach ($row as $cell) { ?>
					<?php if ($cell->isReservable()) { ?>
						<td class="free"><a href="calendar.php?employee=<?= $employee->id; ?>&week=<?= $week; ?>&datetime=<?= $cell->getStartTime();?>">Free</a></td>
					<?php } elseif ($cell->isReserved() || $cell->isEmpty()) { ?>
						<td class="reserved">&nbsp;</td>
					<?php } elseif ($cell->isEmpty()) { ?>
						<td></td>
					<?php } else { ?>
						<td>&nbsp;</td>
					<?php } ?>
				<?php } ?>
			</tr>
		<?php $i++; } ?>
	</table>
<?php include 'templates/footer.php'; ?>