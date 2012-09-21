<?php

namespace MessageBoard\Model;

use \Expose\Expose as e;

class MemberRepositoryTest extends \PHPUnit_Framework_TestCase
{
	public function newMemberRepository($methods = null)
	{
		return $this->getMock('\MessageBoard\Model\MemberRepository', $methods, array(), '', false);
	}

	public function testAddMembership()
	{
		$userId = 1234;
		$boardId = 9876;

		$member = $this->getMock('stdClass', array('setVars'));
		$member
			->expects($this->once())
			->method('setVars')
			->with(array(
				'user_id'   => $userId,
				'board_id' => $boardId,
			));

		$memberRepository = $this->newMemberRepository(array('create', 'insert'));
		$memberRepository
			->expects($this->once())
			->method('create')
			->will($this->returnValue($member));
		$memberRepository
			->expects($this->once())
			->method('insert')
			->with($member)
			->will($this->returnValue(true));

		$this->assertTrue($memberRepository->addMembership($userId, $boardId));
	}

	/**
	 * @group justTesting
	 */
	public function testHasMembership()
	{
		$userId = 123456;
		$boardId = 98765;

		$criteria = new \CriteriaCompo();
		$criteria->add(new \Criteria('user_id', $userId));
		$criteria->add(new \Criteria('board_id', $boardId));

		$memberRepository = $this->newMemberRepository(array('getCount'));
		$memberRepository
			->expects($this->at(0))
			->method('getCount')
			->with($this->equalTo($criteria))
			->will($this->returnValue(0));
		$memberRepository
			->expects($this->at(1))
			->method('getCount')
			->with($this->equalTo($criteria))
			->will($this->returnValue(1));

		$this->assertFalse($memberRepository->hasMembership($userId, $boardId));
		$this->assertTrue($memberRepository->hasMembership($userId, $boardId));
	}
}
