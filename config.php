<?php

/**
 * Configuration
 */ 

$config['siteName'] = 'ezRPG';
$config['siteURL'] = 'http://localhost:9000/GitHub/Rework-Revisited/public/';

$config['db'] = array(
	'driver'   => 'mysql',
	'host'     => '127.0.0.1',
	'database' => 'ezrpg',
	'username' => 'root',
	'password' => 'password',
	'port' => '3306',
	'prefix' => ''
);


//Routing

$config['routes'] = array(
	'Login' => '\ezRPG\Module\Login\Index',
	'Error404' => '\ezRPG\Module\Error404\Error404',
	'Home' => '\ezRPG\Module\Home\Home',



);