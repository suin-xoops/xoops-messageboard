<?php

require_once __DIR__.'/../../mainfile.php';

function messageboard_get_pathinfo()
{
	$baseURL    = $_SERVER['SCRIPT_NAME'];
	$requestURI = $_SERVER['REQUEST_URI'];

	$queryStringPosition = strpos($requestURI, '?');

	if ( $queryStringPosition !== false )
	{
		$requestURI = substr($requestURI, 0, $queryStringPosition);
	}

	$baseURLLength = strlen($baseURL);
	$pathInfo = substr($requestURI, $baseURLLength);
	$pathInfo = strval($pathInfo);

	return $pathInfo;
}

$pathinfo = messageboard_get_pathinfo();

$response404 = function() {
	header('HTTP', null, 404);
	echo '<h1>404 Not Found</h1>';
	exit;
};

$response403 = function() {
	header('HTTP', null, 403);
	echo '<h1>403 Forbidden</h1>';
	exit;
};

if ( ! preg_match('#^/(?P<attachmentId>[0-9]+)/(?P<attachmentName>[^/]+)$#', $pathinfo, $params) )
{
	$response404();
}

$root = XCube_Root::getSingleton();
$assetManager = \MessageBoard\AssetManager::getInstance();
$assetManager->setDirname('messageboard');
$assetManager->setDatabase($root->mController->mDB);

$attachmentRepository = $assetManager->getAttachmentRepository();
/** @var $attachment \MessageBoard\Model\Attachment */
$attachment = $attachmentRepository->get($params['attachmentId']);

if ( is_object($attachment) === false )
{
	$response404();
}

$boardManager = $assetManager->getBoardRepository();
/** @var $board \MessageBoard\Model\Board */
$board = $boardManager->get($attachment->get('board_id'));

if ( is_object($board) === false )
{
	$response404();
}

$user = $assetManager->getUser();

if ( $user->hasMembershipTo($board) === false )
{
	$response403();
}

// This is need for security.
if ( $attachment->get('name') !== urldecode($params['attachmentName']) )
{
	$response404();
}

if ( file_exists($attachment->getFilePath()) === false )
{
	$response404();
}

if ( is_readable($attachment->getFilePath()) === false )
{
	$response404();
}

// This is necessary for XOOPS
while ( ob_get_level() )
{
	ob_end_clean();
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment');
header('Content-Length: '.$attachment->get('size'));
readfile($attachment->getFilePath());

