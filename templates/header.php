<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>Salon Lulu | <?= $title; ?></title>

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="media/css/screen.css" />
        <link rel="stylesheet" href="media/css/undohtml.css" />
    </head>

    <body>
    <div id="wrapper">
        <div id="header">
            <h1>Salon Lulu</h1>
            <p>Online reservation system for Salon Lulu.</p>
        </div>
        
    	<div id="navigation">
    		<ul>
    			<li<?php if ($activeTab == "home") { print ' class="active"'; } ?>><a href="index.php">Home</a></li>
    			
                <li<?php if ($activeTab == "reservation") { print ' class="active"'; } ?>><a href="selectService.php">Make reservation</a></li></a>
    			<li<?php if ($activeTab == "cancel") { print ' class="active"'; } ?>><a href="cancelReservation.php">Cancel reservation</a></li>
                
    		</ul>
    	</div>
        
        <div id="content-wrapper">
            <div id="content">