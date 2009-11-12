<?php
$activeTab = "report";
include 'templates/admin/header.php';

$nextWeek = null;
$currentWeekStartDay = getFirstDayOfWeek(date("Y\-\WW"));
if ($report->getPeriodStartDate() < $currentWeekStartDay)
	$nextWeek = date('Y\-\WW', $report->getPeriodStartDate() + 7 * Calendar::SecondsInDay);
	
$prevWeek = date('Y\-\WW', $report->getPeriodStartDate() - 7 * Calendar::SecondsInDay);
?>

	<h2>Weekly report <?= date('Y-m-d', $report->getPeriodStartDate()); ?> - <?= date('Y-m-d', $report->getPeriodEndDate()); ?></h2>
	
	<div id="reportControls" class="buttonRow">
        <ul>
            <li><a href="weeklyReport.php?week=<?= $prevWeek; ?>">&laquo; Previous week</a></li>
            <?php if ($nextWeek) { ?><li><a href="weeklyReport.php?week=<?= $nextWeek; ?>">Next week &raquo;</a></li><?php } ?>
        </ul>
	</div>
	
	<dl>
		<dt>Total number of reservations:</dt>
		<dd><?= $report->reservation_count; ?></dd>
		
		<dt>Total number of cancelled reservations:</dt>
		<dd><?= $report->cancelled_count; ?></dd>
		
		<dt>Service stats:</dt>
		<dd>
			<table class="dataTable">
				<tr>
					<th>Name</th>
					<th>Number of reservations</th>
				</tr>
				<?php $i = 0; foreach ($report->serviceStats as $statRow) { ?>
				<tr class="<?= ($i % 2 == 0) ? 'even' : 'odd';?>">
					<td><?= htmlspecialchars($statRow['name']); ?></td>
					<td><?= $statRow['reservation_count']; ?></td>
				</tr>
				<?php $i++; }?>
				
			</table>
		</dd>
		
		<dt>Employee stats:</dt>
		<dd>
			<table class="dataTable">
				<tr>
					<th>Name</th>
					<th>Reservations</th>
				</tr>
				<?php $i = 0; foreach ($report->employeeStats as $statRow) { ?>
				<tr class="<?= ($i % 2 == 0) ? 'even' : 'odd';?>">
					<td><?= htmlspecialchars($statRow['first_name']." ".$statRow['last_name']); ?></td>
					<td><?= $statRow['reservation_count']; ?></td>
				</tr>
				<?php $i++; } ?>
			</table>
		</dd>
	</dl>

<?php include 'templates/admin/footer.php'; ?>