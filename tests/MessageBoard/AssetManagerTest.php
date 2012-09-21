<?php

namespace MessageBoard;

use \Mockery as m;
use \Expose\Expose as e;

class AssetManagerTest extends \PHPUnit_Framework_TestCase
{
	public function newAssetManager($methods = null)
	{
		return $this->getMock('\MessageBoard\AssetManager', $methods, array(), '', false);
	}

	public function newDatabaseMock()
	{
		return $this->getMock('XoopsMySQLDatabase', null, array(), '', false);
	}

	public function testGetInstance()
	{
		$instance1 = AssetManager::getInstance();
		$instance2 = AssetManager::getInstance();
		$this->assertSame($instance1, $instance2);
	}

	public function testSetDatabase()
	{
		$assetManager = $this->newAssetManager();
		$this->assertAttributeSame(null, 'database', $assetManager);

		$database = $this->newDatabaseMock();
		$this->assertSame($assetManager, $assetManager->setDatabase($database));
		$this->assertAttributeSame($database, 'database', $assetManager);
	}

	public function testSetDirname()
	{
		$dirname = 'foo';
		$assetManager = $this->newAssetManager();
		$this->assertAttributeSame(null, 'dirname', $assetManager);
		$this->assertSame($assetManager, $assetManager->setDirname($dirname));
		$this->assertAttributeSame($dirname, 'dirname', $assetManager);
	}

	public function testGetBoardRepository()
	{
		$boardRepositoryMock = $this->getMock('stdClass');

		$assetManager = $this->newAssetManager(array('_getRepository'));
		$assetManager
			->expects($this->once())
			->method('_getRepository')
			->with('Board')
			->will($this->returnValue($boardRepositoryMock));

		$this->assertSame($boardRepositoryMock, $assetManager->getBoardRepository());
	}

	public function testGetBoardService()
	{
		$user = $this->getMock('stdClass');

		$boardService = $this->getMock('stdClass', array('setUser'));
		$boardService
			->expects($this->once())
			->method('setUser')
			->with($user);

		$assetManager = $this->newAssetManager(array('_getService', 'getUser'));
		$assetManager
			->expects($this->once())
			->method('_getService')
			->with('Board')
			->will($this->returnValue($boardService));
		$assetManager
			->expects($this->once())
			->method('getUser')
			->will($this->returnValue($user));

		$this->assertSame($boardService, $assetManager->getBoardService());
	}

	public function test_getRepository()
	{
		eval('
		namespace MessageBoard\Model;
		class FooBarRepository {
			public static $constructor;
			public function __construct($database, $dirname) {
				$c = self::$constructor;
				$c($database, $dirname);
			}
		}');

		$that = $this;
		$database = $this->newDatabaseMock();
		$dirname  = 'foo';

		\MessageBoard\Model\FooBarRepository::$constructor = function($_database, $_dirname) use ($that, $database, $dirname) {
			$that->assertSame($database, $_database);
			$that->assertSame($dirname, $_dirname);
		};

		$assetManager = $this->newAssetManager(array('injectDependingRepositories'));
		$assetManager
			->expects($this->once())
			->method('injectDependingRepositories')
			->with($this->isInstanceOf('\MessageBoard\Model\FooBarRepository'));
		e::expose($assetManager)->attr('database', $database)->attr('dirname', $dirname);
		$actual = e::expose($assetManager)->call('_getRepository', 'FooBar');
		$this->assertInstanceOf('\MessageBoard\Model\FooBarRepository', $actual);
	}

	public function test_getService()
	{
		$serviceClass = '\MessageBoard\Model\Service\FooBarApi';
		eval('
		namespace MessageBoard\Model\Service;
		class FooBarApi {}
		');

		$assetManager = $this->newAssetManager(array('injectDependingRepositories'));
		$assetManager
			->expects($this->once())
			->method('injectDependingRepositories')
			->with($this->isInstanceOf($serviceClass));
		$actual = e::expose($assetManager)->call('_getService', 'FooBar');
		$this->assertInstanceOf($serviceClass, $actual);
	}

	public function testInjectDependingRepositories()
	{
		// depending repositories
		$fooRepository = 'foo rep';
		$barRepository = 'bar rep';

		$assetManager = $this->newAssetManager(array('_getRepository'));
		$assetManager
			->expects($this->at(0))
			->method('_getRepository')
			->with('Foo')
			->will($this->returnValue($fooRepository));
		$assetManager
			->expects($this->at(1))
			->method('_getRepository')
			->with('Bar')
			->will($this->returnValue($barRepository));

		$dependingObject = $this
			->getMockBuilder('stdClass')
			->disableOriginalConstructor()
			->setMethods(array('setFooRepository', 'setBarRepository'))
			->getMock();
		$dependingObject
			->expects($this->once())
			->method('setFooRepository')
			->with($fooRepository);
		$dependingObject
			->expects($this->once())
			->method('setBarRepository')
			->with($barRepository);

		$assetManager->injectDependingRepositories($dependingObject);
	}
}
