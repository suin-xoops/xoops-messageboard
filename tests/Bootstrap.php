<?php

// For composer
require_once 'Vendor/autoload.php';

// Load test target classes
spl_autoload_register(function($c) { @include_once strtr($c, '\\_', '//').'.php'; });
set_include_path(get_include_path() . PATH_SEPARATOR .__DIR__.'/../html/modules/messageboard/class');

// Load XOOPS
require_once __DIR__.'/../html/mainfile.php';

/**
 * @return \XoopsMySQLDatabase
 */
function test_get_xoops_db()
{
	static $db;

	if ( $db === null )
	{
		$db = Database::getInstance();
	}

	return $db;
}

