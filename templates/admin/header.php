<?php $authenticated = is_authenticated(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>Salon Lulu Management | <?= $title; ?></title>

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="media/css/screen.css" />
        <link rel="stylesheet" href="../media/css/undohtml.css" />
    </head>

    <body>
    <div id="wrapper">
        <div id="header">
            <h1>Salon Lulu Management</h1>
            <p>Management system for Salon Lulu.</p>
        </div>
        
    	<div id="navigation">
    		<ul>
    			<li<?php if ($activeTab == "home") { print ' class="active"'; } ?>><a href="admin.php">Home</a></li>
    			<?php if ($authenticated) { ?>
                <li<?php if ($activeTab == "employees") { print ' class="active"'; } ?>><a href="employees.php">Employees</a></li></a>
    			<li<?php if ($activeTab == "services") { print ' class="active"'; } ?>><a href="serviceList.php">Services</a></li>
    			<li<?php if ($activeTab == "report") { print ' class="active"'; } ?>><a href="weeklyReport.php">Weekly report</a></li>
                <?php } ?>
    		</ul>
    	</div>
        
        <div id="content-wrapper">
            <div id="content">