<?php

namespace MessageBoard\Model\Service;

use \Expose\Expose as e;
use \Mockery as m;

class BoardApiTest extends \PHPUnit_Framework_TestCase
{
	public function newBoardApi($methods = null)
	{
		return $this->getMock('\MessageBoard\Model\Service\BoardApi', $methods, array(), '', false);
	}

	public function newBoardRepository($methods = null)
	{
		return $this->getMock('\MessageBoard\Model\BoardRepository', $methods, array(), '', false);
	}

	public function newMemberRepository($methods = null)
	{
		return $this->getMock('\MessageBoard\Model\MemberRepository', $methods, array(), '', false);
	}

	public function newCommentRepository($methods = null)
	{
		return $this->getMock('\MessageBoard\Model\CommentRepository', $methods, array(), '', false);
	}

	public function testSetUser()
	{
		$user = new \MessageBoard\Model\User();
		$boardApi = new BoardApi();
		$this->assertNull($boardApi->setUser($user));
		$this->assertAttributeSame($user, 'user', $boardApi);
	}

	public function testSetBoardRepository()
	{
		$boardRepository = $this->newBoardRepository();
		$boardApi = $this->newBoardApi();
		$this->assertNull($boardApi->setBoardRepository($boardRepository));
		$this->assertAttributeSame($boardRepository, 'boardRepository', $boardApi);
	}

	public function testSetMemberRepository()
	{
		$memberRepository = $this->newMemberRepository();
		$boardApi = $this->newBoardApi();
		$this->assertNull($boardApi->setMemberRepository($memberRepository));
		$this->assertAttributeSame($memberRepository, 'memberRepository', $boardApi);
	}

	public function testSetCommentRepository()
	{
		$commentRepository = $this->newCommentRepository();
		$boardApi = $this->newBoardApi();
		$this->assertNull($boardApi->setCommentRepository($commentRepository));
		$this->assertAttributeSame($commentRepository, 'commentRepository', $boardApi);
	}

	public function testCreate()
	{
		$clientKey = 'bulletin.story.1';

		$board = $this->getMock('stdClass', array('set'));
		$board
			->expects($this->once())
			->method('set')
			->with('client_key', $clientKey);

		$boardRepository = $this->getMock('stdClass', array('create', 'insert'));
		$boardRepository
			->expects($this->at(0))
			->method('create')
			->will($this->returnValue($board));
		$boardRepository
			->expects($this->at(1))
			->method('insert')
			->with($this->equalTo($board))
			->will($this->returnValue(true));

		$boardApi = $this->newBoardApi();
		e::expose($boardApi)->attr('boardRepository', $boardRepository);

		$this->assertSame($board, $boardApi->create($clientKey));
	}

	/**
	 * @expectedException \MessageBoard\Model\DomainException
	 * @expectedExceptionMessage Failed to create board client key of clientkey
	 */
	public function testCreate_and_failed_to_save()
	{
		$board = m::mock('stdClass');
		$board->shouldReceive('set');

		$boardRepository = m::mock('stdClass');
		$boardRepository->shouldReceive('create')->andReturn($board);
		$boardRepository->shouldReceive('insert')->andReturn(false);

		$boardApi = new BoardApi();
		$boardApi->setBoardRepository($boardRepository);
		$boardApi->create('clientkey');
	}

	public function testAddMember()
	{
		$memberId = 1234;
		$clientKey = 'bulletin.story.123';
		$boardId = 9876;

		$board = $this->getMock('stdClass', array('get'));
		$board->expects($this->once())->method('get')->with('id')->will($this->returnValue($boardId));

		$boardRepository = $this->getMock('stdClass', array('getByClientKey'));
		$boardRepository->expects($this->once())->method('getByClientKey')->with($clientKey)->will($this->returnValue($board));

		$memberRepository = $this->getMock('stdClass', array('hasMembership', 'addMembership'));
		$memberRepository->expects($this->once())->method('hasMembership')->with($memberId, $boardId)->will($this->returnValue(false));
		$memberRepository->expects($this->once())->method('addMembership')->with($memberId, $boardId);

		$boardApi = $this->newBoardApi();
		e::expose($boardApi)
			->attr('boardRepository', $boardRepository)
			->attr('memberRepository', $memberRepository);

		$this->assertNull($boardApi->addMember($memberId, $clientKey));
	}

	/**
	 * @expectedException \MessageBoard\Model\DomainException
	 * @expectedExceptionMessage Board not found for client key: bulletin.story.123
	 */
	public function testAddMember_with_non_existing_board()
	{
		$boardRepository = $this->getMock('stdClass', array('getByClientKey'));
		$boardRepository
			->expects($this->once())
			->method('getByClientKey')
			->with('bulletin.story.123')
			->will($this->returnValue(null));

		$boardApi = $this->newBoardApi();
		e::expose($boardApi)
			->attr('boardRepository', $boardRepository);

		$boardApi->addMember(1234, 'bulletin.story.123');
	}

	/**
	 * @expectedException \MessageBoard\Model\DomainException
	 * @expectedExceptionMessage Failed to add member to board client key of bulletin.story.123
	 */
	public function testAddMember_and_fails_to_add_member()
	{
		$memberId = 1234;
		$clientKey = 'bulletin.story.123';
		$boardId = 9876;

		$board = $this->getMock('stdClass', array('get'));
		$board->expects($this->once())->method('get')->with('id')->will($this->returnValue($boardId));

		$boardRepository = $this->getMock('stdClass', array('getByClientKey'));
		$boardRepository->expects($this->once())->method('getByClientKey')->with($clientKey)->will($this->returnValue($board));

		$memberRepository = $this->getMock('stdClass', array('hasMembership', 'addMembership'));
		$memberRepository->expects($this->once())->method('hasMembership')->with($memberId, $boardId)->will($this->returnValue(false));
		$memberRepository->expects($this->once())->method('addMembership')->with($memberId, $boardId)->will($this->returnValue(false));

		$boardApi = $this->newBoardApi();
		e::expose($boardApi)
			->attr('boardRepository', $boardRepository)
			->attr('memberRepository', $memberRepository);

		$boardApi->addMember($memberId, $clientKey);
	}

	public function testAddMember_doesnt_throws_exception_even_if_try_to_add_existing_member()
	{
		$clientKey = 'foo.bar.1234';
		$boardId  = 1234;
		$userId    = 9876;

		$board = m::mock('stdClass');
		$board->shouldReceive('get')->with('id')->andReturn($boardId);

		$boardRepository = m::mock('stdClass');
		$boardRepository->shouldReceive('getByClientKey')->with($clientKey)->andReturn($board);

		$memberRepository = m::mock('stdClass');
		$memberRepository->shouldReceive('addMembership')->never();
		$memberRepository->shouldReceive('hasMembership')->with($userId, $boardId)->andReturn(true);

		$boardApi = new BoardApi();
		$boardApi->setBoardRepository($boardRepository);
		$boardApi->setMemberRepository($memberRepository);
		$boardApi->addMember($userId, $clientKey);
	}

	public function testAddComment()
	{
		$clientKey = 'bulletin.story.345';
		$userId = 123;
		$body = "Comment comment comment...";
		$boardId = 9876;

		$board = $this->getMock('stdClass', array('get'));
		$board
			->expects($this->once())
			->method('get')
			->with('id')
			->will($this->returnValue($boardId));

		$boardRepository = $this->getMock('stdClass', array('getByClientKey'));
		$boardRepository
			->expects($this->once())
			->method('getByClientKey')
			->with($clientKey)
			->will($this->returnValue($board));

		$user = $this->getMock('stdClass', array('hasMembershipTo'));
		$user
			->expects($this->once())
			->method('hasMembershipTo')
			->with($board)
			->will($this->returnValue(true));

		$comment = $this->getMock('stdClass', array('setVars'));
		$comment
			->expects($this->once())
			->method('setVars')
			->with(array(
				'board_id' => $boardId,
				'user_id'   => $userId,
				'body'      => $body,
		));

		$commentRepository = $this->getMock('stdClass', array('create', 'insert'));
		$commentRepository
			->expects($this->once())
			->method('create')
			->will($this->returnValue($comment));
		$commentRepository
			->expects($this->once())
			->method('insert')
			->with($comment);

		$boardApi = $this->newBoardApi();
		e::expose($boardApi)
			->attr('boardRepository', $boardRepository)
			->attr('user', $user)
			->attr('commentRepository', $commentRepository);

		$actual = $boardApi->addComment($clientKey, $userId, $body);
		$this->assertSame($comment, $actual);
	}

	/**
	 * @expectedException \MessageBoard\Model\DomainException
	 * @expectedExceptionMessage Board not found for client key: bulletin.story.345
	 */
	public function testAddComment_but_board_not_found()
	{
		$clientKey = 'bulletin.story.345';
		$userId = 123;
		$body = "Comment comment comment...";

		$boardRepository = $this->getMock('stdClass', array('getByClientKey'));
		$boardRepository
			->expects($this->once())
			->method('getByClientKey')
			->with($clientKey)
			->will($this->returnValue(false));

		$boardApi = $this->newBoardApi();
		e::expose($boardApi)
			->attr('boardRepository', $boardRepository);

		$boardApi->addComment($clientKey, $userId, $body);
	}

	/**
	 * @expectedException \MessageBoard\Model\DomainException
	 * @expectedExceptionMessage User (user_id: 123) has no membership to the board client key of bulletin.story.345
	 */
	public function testAddComment_but_user_has_no_membership()
	{
		$clientKey = 'bulletin.story.345';
		$userId = 123;
		$body = "Comment comment comment...";
		$boardId = 123456;

		$board = $this->getMock('stdClass', array('get'));


		$boardRepository = $this->getMock('stdClass', array('getByClientKey'));
		$boardRepository
			->expects($this->once())
			->method('getByClientKey')
			->with($clientKey)
			->will($this->returnValue($board));

		$user = $this->getMock('stdClass', array('hasMembershipTo'));
		$user
			->expects($this->once())
			->method('hasMembershipTo')
			->with($board)
			->will($this->returnValue(false));

		$boardApi = $this->newBoardApi();
		e::expose($boardApi)
			->attr('boardRepository', $boardRepository)
			->attr('user', $user);

		$boardApi->addComment($clientKey, $userId, $body);
	}

	/**
	 * @expectedException \MessageBoard\Model\DomainException
	 * @expectedExceptionMessage Failed to add comment to board client key of bulletin.story.345
	 */
	public function testAddComment_but_failed_to_save_comment()
	{
		$clientKey = 'bulletin.story.345';
		$userId = 123;
		$body = "Comment comment comment...";
		$boardId = 9876;

		$board = $this->getMock('stdClass', array('get'));
		$board
			->expects($this->once())
			->method('get')
			->with('id')
			->will($this->returnValue($boardId));

		$boardRepository = $this->getMock('stdClass', array('getByClientKey'));
		$boardRepository
			->expects($this->once())
			->method('getByClientKey')
			->with($clientKey)
			->will($this->returnValue($board));

		$user = $this->getMock('stdClass', array('hasMembershipTo'));
		$user
			->expects($this->once())
			->method('hasMembershipTo')
			->with($board)
			->will($this->returnValue(true));

		$comment = $this->getMock('stdClass', array('setVars'));
		$comment
			->expects($this->once())
			->method('setVars')
			->with(array(
			'board_id' => $boardId,
			'user_id'   => $userId,
			'body'      => $body,
		));

		$commentRepository = $this->getMock('stdClass', array('create', 'insert'));
		$commentRepository
			->expects($this->once())
			->method('create')
			->will($this->returnValue($comment));
		$commentRepository
			->expects($this->once())
			->method('insert')
			->with($comment)
			->will($this->returnValue(false));

		$boardApi = $this->newBoardApi();
		e::expose($boardApi)
			->attr('boardRepository', $boardRepository)
			->attr('user', $user)
			->attr('commentRepository', $commentRepository);

		$boardApi->addComment($clientKey, $userId, $body);
	}

	public function testGetBoard()
	{
		$clientKey = 'bulletin.story.890';
		$boardId = 12345;
		$comments = array('comment', 'comment', 'comment');
		$attachments = array();

		$board = m::mock();
		$board->shouldReceive('get')->with('id')->andReturn($boardId)->once();
		$board->shouldReceive('setComments')->with($comments)->once();

		$boardRepository = m::mock('\MessageBoard\Model\BoardRepository');
		$boardRepository->shouldReceive('getByClientKey')->with($clientKey)->andReturn($board)->once();

		$commentRepository = m::mock('\MessageBoard\Model\CommentRepository');
		$commentRepository->shouldReceive('findByBoardId')->with($boardId)->andReturn($comments)->once();

		$attachmentRepository = m::mock('\MessageBoard\Model\AttachmentRepository');
		$attachmentRepository->shouldReceive('findByBoardId')->with($boardId)->andReturn($attachments)->once();

		$boardApi = new BoardApi();
		$boardApi->setBoardRepository($boardRepository);
		$boardApi->setCommentRepository($commentRepository);
		$boardApi->setAttachmentRepository($attachmentRepository);
		$this->assertSame($board, $boardApi->getBoard($clientKey));
	}

	public function testGetBoard_but_board_not_found()
	{
		$clientKey = 'bulletin.story.890';
		$boardRepository = m::mock('\MessageBoard\Model\BoardRepository');
		$boardRepository->shouldReceive('getByClientKey')->with($clientKey)->andReturn(false)->once();

		$boardApi = new BoardApi();
		$boardApi->setBoardRepository($boardRepository);

		$this->assertFalse($boardApi->getBoard($clientKey));
	}
}
