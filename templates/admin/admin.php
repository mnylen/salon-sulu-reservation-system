<?php $activeTab = "home"; include 'templates/admin/header.php'; ?>
    <h2>Admin</h2>

    <dl id="adminTaskList">
        <dt><a href="serviceList.php">Services</a></dt>
        <dd>
            Manage the service catalog.
        </dd>
        
        <dt><a href="employees.php">Employees</a></dt>
        <dd>
            Manage employees, work shifts and view detailed work schedules
            for each employee.
        </dd>

        <dt><a href="weeklyReport.php">Weekly report</a></dt>
        <dd>
            Get the weekly report containing information about number of orders
            made, the amount of reservations carried by each employee and other
            interesting statistical information.
        </dd>
    </dl>
<?php include 'templates/admin/footer.php'; ?>