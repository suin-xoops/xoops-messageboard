<?php

namespace MessageBoard\Model;

class BoardTest extends \PHPUnit_Framework_TestCase
{
	public function testSetComments()
	{
		$comments = array('comment1', 'comment2', 'comment3');

		$board = new Board();
		$this->assertNull($board->setComments($comments));
		$this->assertAttributeSame($comments, 'comments', $board);
	}

	public function testGetComments()
	{
		$comments = array('comment1', 'comment2', 'comment3');
		$board = new Board();
		$board->setComments($comments);
		$this->assertSame($comments, $board->getComments());
	}
}
