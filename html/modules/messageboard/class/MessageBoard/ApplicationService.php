<?php

namespace MessageBoard;

use \Exception;
use \RuntimeException;
use \InvalidArgumentException;
use \XCube_Root;
use \XoopsTpl;
use \MessageBoard\AssetManager;

class ApplicationService
{
	/**
	 * @param string $clientKey
	 * @param null $board
	 * @throws \RuntimeException
	 */
	public static function createBoard($clientKey, &$board = null)
	{
		try
		{
			$assetManager = static::_getAssetManager();
			$boardService = $assetManager->getBoardService();
			$board = $boardService->create($clientKey);
		}
		catch ( Exception $e )
		{
			throw new RuntimeException('Failed to create board', 0, $e);
		}
	}

	/**
	 * @param string $clientKey
	 * @param int $userId
	 * @throws \RuntimeException
	 */
	public static function addMember($clientKey, $userId)
	{
		try
		{
			$assetManager = static::_getAssetManager();
			$boardService = $assetManager->getBoardService();
			$boardService->addMember($userId, $clientKey);
		}
		catch ( Exception $e )
		{
			throw new RuntimeException('Failed to add user', 0, $e);
		}
	}

	public static function showBoard(&$buffer, array $params)
	{
		$params = array_merge(array(
			'clientKey' => null,
			'userId'    => null,
			'returnUrl' => static::_getSelfURL(),
		), $params);

		if ( isset($params['clientKey']) === false )
		{
			throw new InvalidArgumentException('clientKey is missing');
		}

		if ( isset($params['userId']) === false )
		{
			throw new InvalidArgumentException('userId is missing');
		}

		try
		{
			$assetManager = static::_getAssetManager();
			$boardService = $assetManager->getBoardService();
			$board = $boardService->getBoard($params['clientKey']);

			if ( $board === false )
			{
				$buffer = '';
				return;
			}

			$user = $assetManager->getUser();

			if ( $user->hasMembershipTo($board) === false )
			{
				$buffer = '';
				return;
			}

			$previousPost = null;

			if ( isset($_SESSION['messageboard.previous.post']) )
			{
				$previousPost = $_SESSION['messageboard.previous.post'];
				unset($_SESSION['messageboard.previous.post']);
			}

			$smarty = new XoopsTpl();
			$smarty->assign(array(
				'board'       => $board,
				'previousPost' => $previousPost,
				'returnUrl'    => $params['returnUrl'],
			));
			$buffer = $smarty->fetch('db:messageboard_inc_board.html');
		}
		catch ( Exception $e )
		{
			throw new RuntimeException('Failed to show board list', 0, $e);
		}
	}

	/**
	 * @return AssetManager
	 */
	protected static function _getAssetManager()
	{
		$root = XCube_Root::getSingleton();
		$assetManager = AssetManager::getInstance();
		$assetManager->setDirname('messageboard');
		$assetManager->setDatabase($root->mController->mDB);
		return $assetManager;
	}

	/**
	 * @return string
	 */
	protected static function _getSelfURL()
	{
		if ( isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on' )
		{
			$protocol = 'https://';
		}
		else
		{
			$protocol = 'http://';
		}

		return $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
}
