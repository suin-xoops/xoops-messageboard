<?php

namespace MessageBoard\Model;

use \CriteriaCompo;
use \Criteria;

class BoardRepository extends \MessageBoard\Model\Repository
{
	public $mTable = '{dirname}_board';
	public $mPrimary = 'id';
	public $mClass = '\MessageBoard\Model\Board';

	/**
	 * @param string $clientKey
	 * @return \MessageBoard\Model\Board|bool Returns FALSE if board was not found
	 */
	public function getByClientKey($clientKey)
	{
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('client_key', $clientKey));
		$boards = $this->getObjects($criteria);

		if ( count($boards) === 0 )
		{
			return false;
		}

		return $boards[0];
	}
}
