<?php

namespace MessageBoard\Model;

use \CriteriaCompo;
use \Criteria;

class MemberRepository extends \MessageBoard\Model\Repository
{
	public $mTable = '{dirname}_member';
	public $mPrimary = 'id';
	public $mClass = '\MessageBoard\Model\Member';

	/**
	 * Add a user to the board as a new member
	 * @param int $userId
	 * @param int $boardId
	 * @return bool
	 */
	public function addMembership($userId, $boardId)
	{
		/** @var $member \MessageBoard\Model\Member */
		$member = $this->create();
		$member->setVars(array(
			'user_id'   => $userId,
			'board_id' => $boardId,
		));
		return $this->insert($member);
	}

	/**
	 * Determine if the user has membership of the board
	 * @param int $userId
	 * @param int $boardId
	 * @return bool
	 */
	public function hasMembership($userId, $boardId)
	{
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('user_id', $userId));
		$criteria->add(new Criteria('board_id', $boardId));

		if ( $this->getCount($criteria) == 1 )
		{
			return true;
		}

		return false;
	}
}
