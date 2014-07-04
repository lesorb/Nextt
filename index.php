<?php

error_reporting( E_ALL );

require_once('/lib/NexttLoader.php');

//include test code for 
require_once('/tests/BookTest.php');

//configreation of this testing
$config = require_once('/conf/tests.php');
Nextt\UnitFramework::run( $config );
