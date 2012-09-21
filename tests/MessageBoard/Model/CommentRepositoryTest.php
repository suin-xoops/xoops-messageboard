<?php

namespace MessageBoard\Model;

use \CriteriaCompo;
use \Criteria;

class CommentRepositoryTest extends \PHPUnit_Framework_TestCase
{
	public function newCommentRepository($methods = null)
	{
		return $this->getMock('\MessageBoard\Model\CommentRepository', $methods, array(), '', false);
	}

	public function testFindByBoardId()
	{
		$boardId = 12345;

		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('board_id', $boardId));
		$criteria->addSort('created', 'ASC');

		$comments = array('comment1', 'comment2', 'comment3');

		$commentRepository = $this->newCommentRepository(array('getObjects'));
		$commentRepository
			->expects($this->once())
			->method('getObjects')
			->with($this->equalTo($criteria))
			->will($this->returnValue($comments));
		$this->assertSame($comments, $commentRepository->findByBoardId($boardId));
	}
}
