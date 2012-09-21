<?php

require_once '../../mainfile.php';

if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
{
	redirect_header(XOOPS_URL);
}

$root = XCube_Root::getSingleton();
$assetManager = \MessageBoard\AssetManager::getInstance();
$assetManager->setDirname('messageboard')->setDatabase($root->mController->mDB);

$clientKey = $root->mContext->mRequest->getRequest('client_key');
$userId    = $root->mContext->mXoopsUser->get('uid');
$body      = $root->mContext->mRequest->getRequest('body');
$returnUrl = $root->mContext->mRequest->getRequest('return_url');

$backToForm = function($error) use ($body, $returnUrl) {
	$_SESSION['messageboard.previous.post'] = array(
		'body' => $body,
		'errorMessages' => array($error),
	);
	$root = XCube_Root::getSingleton();
	$root->mController->executeForward($returnUrl.'#messageboardAddComment');
};

$uploadingAttachments = \MessageBoard\Model\UploadingAttachment::getUploadingAttachments($_FILES['attachments']);

foreach ( $uploadingAttachments as $uploadingAttachment )
{
	if ( $uploadingAttachment->isValid() === false )
	{
		$backToForm("添付ファイルのアップロードに失敗しました。");
	}
}

try
{
	if ( mb_strlen(trim($body)) == 0 )
	{
		$backToForm("コメント本文を入力してください。");
	}

	$boardService = $assetManager->getBoardService();
	$boardService->addComment($clientKey, $userId, $body, $uploadingAttachments);
}
catch ( Exception $e )
{
	$backToForm("コメントの投稿に失敗しました。");
}

$root->mController->executeForward($returnUrl.'#messageboardListHead');
