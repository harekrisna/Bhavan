<?php

// Uncomment this line if you must temporarily take down your site for maintenance.
/*
if($_SERVER['REMOTE_ADDR'] != "94.112.79.165")
	require '.maintenance.php';
*/

$container = require __DIR__ . '/../app/bootstrap.php';

$container->getService('application')->run();
