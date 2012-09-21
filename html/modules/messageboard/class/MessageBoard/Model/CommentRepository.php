<?php

namespace MessageBoard\Model;

use \CriteriaCompo;
use \Criteria;

class CommentRepository extends \MessageBoard\Model\Repository implements \MessageBoard\Model\CommentRepositoryInterface
{
	public $mTable = '{dirname}_comment';
	public $mPrimary = 'id';
	public $mClass = '\MessageBoard\Model\Comment';

	/**
	 * Find and return comments with board ID
	 * @param int $boardId
	 * @return \MessageBoard\Model\Comment[]
	 */
	public function findByBoardId($boardId)
	{
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('board_id', $boardId));
		$criteria->setSort('created', 'ASC');
		return $this->getObjects($criteria, null, null, true);
	}
}
