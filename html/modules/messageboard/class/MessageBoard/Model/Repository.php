<?php

namespace MessageBoard\Model;

use \RuntimeException;

abstract class Repository extends \XoopsObjectGenericHandler
{
	public function __construct($db, $dirname)
	{
		$this->mTable = strtr($this->mTable, array('{dirname}' => $dirname));
		parent::__construct($db);
	}

	public function &create($isNew = true)
	{
		if ( class_exists($this->mClass) === false )
		{
			throw new RuntimeException(sprintf('No such a class: %s', $this->mClass));
		}

		$obj = new $this->mClass();
		$obj->mDirname = $this->getDirname();

		if ( $isNew )
		{
			$obj->setNew();
		}

		return $obj;
	}

	/**
	 * @param object $obj
	 * @param bool   $force
	 * @return bool
	 */
	public function insert(&$obj, $force = true)
	{
		return parent::insert($obj, $force);
	}
}
