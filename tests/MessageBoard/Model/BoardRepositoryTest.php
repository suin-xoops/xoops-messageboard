<?php

namespace MessageBoard\Model;

class BoardRepositoryTest extends \PHPUnit_Framework_TestCase
{
	public function newBoardRepository($methods = null)
	{
		return $this->getMock('\MessageBoard\Model\BoardRepository', $methods, array(), '', false);
	}

	public function testGetByClientKey()
	{
		$clientKey = 'bulletin.story.123';

		$criteria = new \CriteriaCompo();
		$criteria->add(new \Criteria('client_key', $clientKey));

		$board = $this->getMock('stdClass');
		$boards = array($board);

		$boardRepository = $this->newBoardRepository(array('getObjects'));
		$boardRepository
			->expects($this->once())
			->method('getObjects')
			->with($this->equalTo($criteria))
			->will($this->returnValue($boards));

		$actual = $boardRepository->getByClientKey($clientKey);
		$this->assertSame($board, $actual);
	}

	public function testGetByClientKey_and_no_board_found()
	{
		$clientKey = 'bulletin.story.123';

		$boardRepository = $this->newBoardRepository(array('getObjects'));
		$boardRepository
			->expects($this->once())
			->method('getObjects')
			->will($this->returnValue(array()));

		$actual = $boardRepository->getByClientKey($clientKey);
		$this->assertSame(false, $actual);
	}
}
