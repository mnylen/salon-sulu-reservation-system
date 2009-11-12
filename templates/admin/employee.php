<?php $activeTab = "employees"; include 'templates/admin/header.php'; ?>

    <h2><a href="employees.php">Employees</a> &raquo; <?= $employee->first_name.' '.$employee->last_name; ?></h2>
    
    <?php if ($message != null) { ?><div class="message"><?= $message; ?></div> <?php } ?>
    
    <form action="employee.php?id=<?= $_GET['id']; ?>" method="post">
        <dl>
            <dt>First name:</dt>
            <dd><input type="text" name="first_name" value="<?= htmlspecialchars($form->getData('first_name')); ?>" />
                    <?php display_errors($form, 'first_name'); ?></dd>
            
            <dt>Last name:</dt>
            <dd><input type="text" name="last_name" value="<?= htmlspecialchars($form->getData('last_name')); ?>" />
                    <?php display_errors($form, 'last_name'); ?></dd>
            
            <dt>Active?</dt>
            <dd>
        		<label><input type="radio" name="active" value="true"  <?= ($form->getData('active') == 'true') ? 'checked="checked"' : ''; ?> /> Yes</label>
        		<label><input type="radio" name="active" value="false" <?= ($form->getData('active') != 'true') ? 'checked="checked"' : ''; ?> /> No</label>
       		</dd>
       		
       		<dt class="actionRow"><input type="submit" value="Save" name="save" /></dt>
        </dl>
    </form>
    
    <h3>Upcoming work shifts</h3>
    
    <div class="buttonRow">
        <ul>
            <li><a href="addWorkShift.php?employee_id=<?= $employee->id;?>">Add work shift</a></li>
        </ul>
    </div>
    
    <table id="workShiftsTbl" class="dataTable">
        <tr>
            <th>Date</th>
            <th>Start</th>
            <th>End</th>
            <th>Reservations</th>
            <th>Actions</th>
        </tr>
        <?php $i = 0; $month = null; while ( ($shift = $workShifts->next()) !== null ) { ?>
        <?php if (!($month) || (idate('m', $shift->start_time) != $month)) {?>
        <tr class="group"><th colspan="5"><?= date('F Y', $shift->start_time);?></th></tr>
        <?php $month = idate('m', $shift->start_time); } ?>
        <tr class="<?= ($i % 2 == 0) ? 'even' : 'odd';?>">
            <td><a href="workSchedule.php?employee_id=<?= $employee->id; ?>&date=<?= date('Y-m-d', $shift->start_time);?>"><?= date('l j/m', $shift->start_time); ?></a></td>
            <td><?= date('H:i', $shift->start_time); ?></td>
            <td><?= date('H:i', $shift->end_time); ?></td>
            <td><?= $shift->reservationCount; ?></td>
            <td><a href="deleteWorkShift.php?id=<?= $shift->id; ?>">Delete</a></td>
        </tr>
        <?php $i++; } ?>
    </table>

<?php include 'templates/admin/footer.php'; ?>