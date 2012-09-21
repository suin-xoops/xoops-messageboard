<?php

namespace MessageBoard;

use \Legacy_RoleManager;
use \XoopsMySQLDatabase;
use \XCube_Root;
use \MessageBoard\Model\Repository;
use \MessageBoard\Model\User;

class AssetManager
{
	/** @var $this */
	protected static $instance;
	/** @var \XoopsMySQLDatabase */
	protected $database;
	/** @var string */
	protected $dirname;

	private function __construct()
	{
		$roleManager = new Legacy_RoleManager();
		$roleManager->loadRolesByDirname('messageboard');
	}

	private function __clone()
	{
	}

	/**
	 * @return $this
	 */
	public static function getInstance()
	{
		if ( static::$instance === null )
		{
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * @param \XoopsMySQLDatabase $database
	 * @return $this
	 */
	public function setDatabase(XoopsMySQLDatabase $database)
	{
		$this->database = $database;
		return $this;
	}

	/**
	 * @param string $dirname
	 * @return $this
	 */
	public function setDirname($dirname)
	{
		$this->dirname = $dirname;
		return $this;
	}

	/**
	 * Return BoardRepository object
	 * @return \MessageBoard\Model\BoardRepository
	 */
	public function getBoardRepository()
	{
		return $this->_getRepository('Board');
	}

	/**
	 * Return MemberRepository object
	 * @return \MessageBoard\Model\MemberRepository
	 */
	public function getMemberRepository()
	{
		return $this->_getRepository('Member');
	}

	/**
	 * Return AttachmentRepository object
	 * @return \MessageBoard\Model\AttachmentRepository
	 */
	public function getAttachmentRepository()
	{
		/** @var $attachmentRepository \MessageBoard\Model\AttachmentRepository */
		$attachmentRepository = $this->_getRepository('Attachment');
		$attachmentRepository->setAttachmentDir(XOOPS_TRUST_PATH.'/uploads/messageboard/attachment');
		return $attachmentRepository;
	}

	/**
	 * Return Board service object
	 * @return \MessageBoard\Model\Service\BoardApi
	 */
	public function getBoardService()
	{
		/** @var $board \MessageBoard\Model\Service\BoardApi */
		$board = $this->_getService('Board');
		$board->setUser($this->getUser());
		return $board;
	}

	/**
	 * @return \MessageBoard\Model\User
	 */
	public function getUser()
	{
		$root = XCube_Root::getSingleton();
		$user = new User();

		if ( is_object($root->mContext->mXoopsUser) )
		{
			$user->setUser($root->mContext->mXoopsUser);
		}

		$user->setPrincipal($root->mContext->mUser);
		$user->setMemberRepository($this->getMemberRepository());
		return $user;
	}

	/**
	 * Return Repository object
	 * @param string $entityName
	 * @return \MessageBoard\Model\Repository
	 */
	protected function _getRepository($entityName)
	{
		$className = sprintf('\MessageBoard\Model\%sRepository', $entityName);
		$repository = new $className($this->database, $this->dirname);
		$this->injectDependingRepositories($repository);
		return $repository;
	}

	/**
	 * Return service object
	 * @param string $serviceName
	 * @return object
	 */
	protected function _getService($serviceName)
	{
		$className = sprintf('\MessageBoard\Model\Service\%sApi', $serviceName);
		$service = new $className();
		$this->injectDependingRepositories($service);
		return $service;
	}

	/**
	 * Inject depending repositories to the given object
	 * @param object $object
	 */
	public function injectDependingRepositories($object)
	{
		$methods = get_class_methods($object);

		foreach ( $methods as $method )
		{
			if ( preg_match('/^set(?P<entityName>[A-Z][a-z]+)Repository$/', $method, $matches) )
			{
				$entityName = $matches['entityName'];
				$getter = sprintf('get%sRepository', $entityName);

				if ( method_exists($this, $getter) )
				{
					$object->$method($this->$getter());
				}
				else
				{
					$object->$method($this->_getRepository($entityName));
				}
			}
		}
	}
}
