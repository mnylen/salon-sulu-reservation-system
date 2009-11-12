<?php $activeTab = "services"; include 'templates/admin/header.php'; ?>
    <h2><a href="serviceList.php">Services</a> &raquo;
        <?= ($service != null) ? $service->name : "Add new"; ?></h1>
    
    <?php if ($message) { ?><div class="message"><?= $message; ?></div><?php } ?>
    
    <form action="editService.php?id=<?= $servid; ?>" method="post">
        <h3>Basic info</h3>

        <dl class="form">
        	<dt>Is the service available?</dt>
        	<dd>
        		<label><input type="radio" name="available" value="true"  <?= ($form->getData('available') == 'true') ? 'checked="checked"' : ''; ?> /> Yes</label>
        		<label><input type="radio" name="available" value="false" <?= ($form->getData('available') != 'true') ? 'checked="checked"' : ''; ?> /> No</label>
       		</dd>
       		
            <dt><label for="id_name">Service name:</label></dt>
            <dd><input type="text" name="name" id="id_name" value="<?=htmlspecialchars($form->getData('name'));?>"/>
            	<?php display_errors($form, 'name'); ?></dd>

            <dt><label for="id_description">Description:</label></dt>
            <dd><textarea name="description" id="id_description"><?=htmlspecialchars($form->getData('description'));?></textarea>
            	<?php display_errors($form, 'description'); ?></dd>

            <dt><label for="id_price">Price:</label></dt>
            <dd><input type="text" name="price" id="id_price" value="<?=htmlspecialchars($form->getData('price')); ?>"/> <span class="format_info">(xxx.xx)</span>
            	<?php display_errors($form, 'price'); ?></dd>
            <dd>Changes to the price will affect only new reservations.</dd>

            <dt><label for="id_duration">Duration in minutes:</label></dt>
            <dd><input type="text" name="duration" id="id_duration" value="<?=htmlspecialchars($form->getData('duration')); ?>"/>
            	<?php display_errors($form, 'duration');?></dd>
            <dd>Changes to the duration will affect only new reservations.</dd>
        </dl>

        <h3>Associated employees</h3>

        <dl class="form">
            <dt>Select the employees that can perform this service:</dt>
            
            <?php
                $choices = $form->getField('perf_emp')->getChoices();

                foreach ($choices as $id => $employee) {
                    print "<dd><label><input type=\"checkbox\" name=\"perf_emp[]\" value=\"".$id."\" ";

                    if (is_array($form->getData('perf_emp')) && in_array($id, $form->getData('perf_emp')))
                        print ' checked="checked" ';

                    print "/> ".htmlspecialchars($employee->first_name." ".$employee->last_name)."</label></dd>";
                }
            ?>
        </dl>
        
        <?php display_errors($form, 'perf_emp'); ?>

        <p><input type="submit" name="save" value="Save" /></p>
    </form>
<?php include 'templates/admin/footer.php'; ?>
