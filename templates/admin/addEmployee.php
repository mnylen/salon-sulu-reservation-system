<?php $activeTab = "employees"; include 'templates/admin/header.php'; ?>

    <h2><a href="employees.php">Employees</a> &raquo; Add new</h2>
    
    <form action="addEmployee.php" method="post">
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
    
<?php include 'templates/admin/footer.php'; ?>