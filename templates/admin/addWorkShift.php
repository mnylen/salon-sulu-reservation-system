<?php $activeTab = "employees"; include 'templates/admin/header.php'; ?>

    <h2><a href="employees.php">Employees</a> &raquo; <a href="employee.php?id=<?= $employee->id;?>"><?= $employee->first_name." ".$employee->last_name;?></a> &raquo; Add work shift</h2>
    
    <?php if ($message) { ?><div class="message"><?= $message; ?></div><?php } ?>
    
    <?php display_errors($form); ?>
        
    <form action="addWorkShift.php?employee_id=<?= $employee->id;?>" method="post">
        <dl>
            <dt>Date:</dt>
            <dd><input type="text" name="date" value="<?= htmlspecialchars($form->getData('date')); ?>" />
                <span class="format_info">(YYYY-MM-DD)</span>
                <?php display_errors($form, 'date'); ?></dd>
                
            <dt>Start time:</dt>
            <dd><input type="text" name="start_time" value="<?= htmlspecialchars($form->getData('start_time')); ?>" />
                <span class="format_info">(HH:MM)</span>
                <?php display_errors($form, 'start_time'); ?></dd>
                
            <dt>End time:</dt>
            <dd><input type="text" name="end_time" value="<?= htmlspecialchars($form->getData('end_time')); ?>" />
                <span class="format_info">(HH:MM)</span>
                <?php display_errors($form, 'end_time'); ?></dd>
                
            <dt><input type="submit" name="save" value="Save" />
                <input type="submit" name="saveAndAddNew" value="Save and add new" />
                <input type="submit" name="cancel" value="Cancel" /></dt>
        </dl>
    </form>

<?php include 'templates/admin/footer.php'; ?>