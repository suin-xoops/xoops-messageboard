<?php

namespace JoinTest\MessageBoard;

use \MessageBoard\AssetManager;
use \Database;
use \PDO;

class BoardServiceTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$assetManager = AssetManager::getInstance();
		$assetManager->setDatabase(test_get_xoops_db());
		$assetManager->setDirname('messageboard');
	}

	public function getPDO()
	{
		static $pdo;

		if ( $pdo === null )
		{
			$dsn = sprintf('mysql:dbname=%s;host=%s', XOOPS_DB_NAME, XOOPS_DB_HOST);
			$options = array(
				PDO::ATTR_ORACLE_NULLS       => PDO::NULL_NATURAL,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_AUTOCOMMIT         => true,
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
				PDO::ATTR_EMULATE_PREPARES   => false,
			);

			$pdo = new PDO( $dsn, XOOPS_DB_USER, XOOPS_DB_PASS, $options );
		}

		return $pdo;
	}

	public function createBoardTable()
	{
		$pdo = $this->getPDO();
		$table = $this->getBoardTableName();
		$pdo->query("TRUNCATE TABLE $table");
	}

	public function createMemberTable()
	{
		$pdo = $this->getPDO();
		$table = $this->getMemberTableName();
		$pdo->query("TRUNCATE TABLE $table");
	}

	public function createCommentTable()
	{
		$pdo = $this->getPDO();
		$table = $this->getCommentTableName();
		$pdo->query("TRUNCATE TABLE $table");
	}

	public function getBoardTableName()
	{
		return XOOPS_DB_PREFIX.'_messageboard_board';
	}

	public function getMemberTableName()
	{
		return XOOPS_DB_PREFIX.'_messageboard_member';
	}

	public function getCommentTableName()
	{
		return XOOPS_DB_PREFIX.'_messageboard_comment';
	}

	public function testCreate()
	{
		$this->createBoardTable();

		$boardApi = AssetManager::getInstance()->getBoardService();
		$board1 = $boardApi->create('bulletin.story.1');
		$board2 = $boardApi->create('bulletin.story.2');
		$this->assertInstanceOf('\MessageBoard\Model\Board', $board1);
		$this->assertInstanceOf('\MessageBoard\Model\Board', $board2);
		$this->assertNotEquals($board1, $board2);

		$table = $this->getBoardTableName();
		$pdo = $this->getPDO();

		$expect = array(
			array(
				'id'         => 1,
				'client_key' => 'bulletin.story.1',
				'created'    => time(),
			),
			array(
				'id'         => 2,
				'client_key' => 'bulletin.story.2',
				'created'    => time(),
			),
		);
		$actual = $pdo->query("SELECT * FROM $table")->fetchAll();
		$this->assertSame($expect, $actual);
	}

	public function testCreate_with_duplicated_client_key()
	{
		$this->createBoardTable();
		$boardApi = AssetManager::getInstance()->getBoardService();
		$boardApi->create('bulletin.story.100');

		$this->setExpectedException('\MessageBoard\Model\DomainException', 'Failed to create board client key of bulletin.story.100');

		$boardApi->create('bulletin.story.100');
	}

	public function testAddMember()
	{
		$this->createBoardTable();
		$this->createMemberTable();

		$boardTable = $this->getBoardTableName();
		$memberTable = $this->getMemberTableName();

		// Create board data
		$pdo = $this->getPDO();
		$pdo->query("INSERT INTO $boardTable (id, client_key) VALUES(100, 'bulletin.story.234')");

		$boardApi = AssetManager::getInstance()->getBoardService();
		$this->assertNull($boardApi->addMember(1, 'bulletin.story.234'));

		$expect = array(
			array(
				'id'        => 1,
				'user_id'   => 1,
				'board_id' => 100,
				'created'   => time(),
			),
		);
		$actual = $pdo->query("SELECT * FROM $memberTable")->fetchAll();
		$this->assertSame($expect, $actual);
	}

	public function testAddComment()
	{
		$this->createBoardTable();
		$this->createMemberTable();
		$this->createCommentTable();

		$boardTable = $this->getBoardTableName();
		$memberTable = $this->getMemberTableName();
		$commentTable = $this->getCommentTableName();

		$pdo = $this->getPDO();

		// Create board data
		$pdo->query("INSERT INTO $boardTable (id, client_key) VALUES(100, 'bulletin.story.234')");

		// Create member data
		$pdo->query("INSERT INTO $memberTable (user_id, board_id) VALUES(123, 100)");

		$user = $this->getMock('\MessageBoard\Model\User');
		$user
			->expects($this->once())
			->method('hasMembershipTo')
			->will($this->returnValue(true));

		$boardApi = AssetManager::getInstance()->getBoardService();
		$boardApi->setUser($user);

		$expectedComment = new \MessageBoard\Model\Comment();
		$expectedComment->mDirname = 'messageboard';
		$expectedComment->setVars(array(
			'id' => 1,
			'user_id' => 123,
			'board_id' => 100,
			'body' => "comment comment comment",
			'created' => time(),
		));
		$actualComment = $boardApi->addComment('bulletin.story.234', 123, "comment comment comment");
		$this->assertEquals($expectedComment, $actualComment);

		$expect = array(
			array(
				'id'        => 1,
				'board_id' => 100,
				'user_id'   => 123,
				'body'      => "comment comment comment",
				'created'   => time(),
			),
		);
		$actual = $pdo->query("SELECT * FROM $commentTable")->fetchAll();
		$this->assertSame($expect, $actual);
	}

	public function testGetBoard()
	{
		$this->createBoardTable();
		$this->createCommentTable();

		$boardTable = $this->getBoardTableName();
		$commentTable = $this->getCommentTableName();

		$pdo = $this->getPDO();

		// Create board data
		$pdo->query("INSERT INTO $boardTable (id, client_key) VALUES(100, 'bulletin.story.234')");

		// Create comments data
		$pdo->query("
			INSERT INTO $commentTable
				(id, board_id, body, created)
			VALUES
				(9, 100, 'foo', 1),
				(8, 100, 'bar', 2),
				(7, 100, 'baz', 3)");

		$expectedComment1 = new \MessageBoard\Model\Comment();
		$expectedComment1->unsetNew();
		$expectedComment1->mDirname = 'messageboard';
		$expectedComment1->setVars(array(
			'id'        => 9,
			'user_id'   => 0,
			'board_id' => 100,
			'body'      => 'foo',
			'created'   => 1,
		));

		$expectedComment2 = new \MessageBoard\Model\Comment();
		$expectedComment2->unsetNew();
		$expectedComment2->mDirname = 'messageboard';
		$expectedComment2->setVars(array(
			'id'        => 8,
			'user_id'   => 0,
			'board_id' => 100,
			'body'      => 'bar',
			'created'   => 2,
		));

		$expectedComment3 = new \MessageBoard\Model\Comment();
		$expectedComment3->unsetNew();
		$expectedComment3->mDirname = 'messageboard';
		$expectedComment3->setVars(array(
			'id'        => 7,
			'user_id'   => 0,
			'board_id' => 100,
			'body'      => 'baz',
			'created'   => 3,
		));

		$expectedBoard = new \MessageBoard\Model\Board();
		$expectedBoard->unsetNew();
		$expectedBoard->mDirname = 'messageboard';
		$expectedBoard->setVars(array(
			'id'         => 100,
			'client_key' => 'bulletin.story.234',
			'created'    => 0,
		));
		$expectedBoard->setComments(array(
			9 => $expectedComment1,
			8 => $expectedComment2,
			7 => $expectedComment3,
		));

		$boardApi = AssetManager::getInstance()->getBoardService();
		$actual = $boardApi->getBoard('bulletin.story.234');

		$this->assertEquals($expectedBoard, $actual);
	}
}
