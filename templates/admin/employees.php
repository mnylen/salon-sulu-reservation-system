<?php $activeTab = "employees"; include 'templates/admin/header.php'; ?>
    <h2>Employees</h2>
    
    <div class="buttonRow">
        <ul>
            <li><a href="addEmployee.php">Add employee</a></li>
        </ul>
    </div>
    
    <table id="employeesTbl" class="dataTable">
        <tr>
            <th id="idCol">#</th>
            <th id="nameCol">Name</th>
            <th id="activeCol">Active?</th>
        </tr>
        <?php $i = 0; while ( ($emp = $employees->next()) !== null ) { ?>
        <tr class="<?=($i % 2 == 0) ? "even" : "odd";?>">
            <td><?= $emp->id; ?></td>
            <td><a href="employee.php?id=<?= $emp->id;?>"><?= htmlspecialchars($emp->first_name." ".$emp->last_name);?></a></td>
            <td><?= ($emp->active) ? "Yes" : "No"; ?></td>
        </tr>
        <?php $i++; } ?>
    </table>

<?php include 'templates/admin/footer.php'; ?>