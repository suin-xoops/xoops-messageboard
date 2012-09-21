<?php

defined('MESSAGE_BOARD_DIR') or define('MESSAGE_BOARD_DIR', XOOPS_ROOT_PATH . '/modules/messageboard');

$myDirName = basename(__DIR__);

$modversion['name'] = $myDirName;
$modversion['version'] = 1.00;
$modversion['description'] = '';
$modversion['author'] = 'suin';
$modversion['credits'] = 'suin';
$modversion['help'] = 'help.html';
$modversion['license'] = 'GPL';
$modversion['official'] = 0;
$modversion['image'] = 'images/module_icon.png';
$modversion['dirname'] = $myDirName;

$modversion['cube_style'] = true;

$modversion['disable_legacy_2nd_installer'] = false;

$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'] = array(
	'{prefix}_{dirname}_board',
	'{prefix}_{dirname}_comment',
	'{prefix}_{dirname}_file',
	'{prefix}_{dirname}_member',
);

$modversion['templates'] = array(
	array('file' => 'messageboard_inc_board.html'),
);

$modversion['hasAdmin'] = 0;
$modversion['adminindex'] = 'admin/index.php?action=Index';
$modversion['adminmenu'] = array();

$modversion['hasMain'] = 1;
$modversion['hasSearch'] = 0;
$modversion['sub'] = array();

$modversion['config'] = array();

$modversion['blocks'] = array();
