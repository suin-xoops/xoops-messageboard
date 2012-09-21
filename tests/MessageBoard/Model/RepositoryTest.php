<?php

namespace MessageBoard\Model;

use \Expose\Expose as e;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
	public function newRepository()
	{
		return $this->getMockForAbstractClass('\MessageBoard\Model\Repository', array(), '', false);
	}

	public function getDatabaseMock()
	{
		$database = $this->getMock('XoopsDatabase', null);
		$database->prefix = 'prefix';
		return $database;
	}

	public function test__construct()
	{
		$repository = $this->newRepository();
		e::expose($repository)->attr('mTable', '{dirname}_bar');
		$database = $this->getDatabaseMock();
		$dirname  = 'foo';
		$repository->__construct($database, $dirname);
		$this->assertAttributeSame('prefix_foo_bar', 'mTable', $repository);
	}

	/**
	 * @expectedException \RuntimeException
	 * @expectedExceptionMessage No such a class: NoSuchAClass
	 */
	public function testCreate()
	{
		/** @var $repository \MessageBoard\Model\Repository */
		$repository = $this->newRepository();
		$repository->mClass = 'NoSuchAClass';
		$repository->create();
	}

	public function testCreate_with_valid_class_name()
	{
		$entityClass = $this->getMockClass('stdClass', array('setNew'));

		/** @var $repository \MessageBoard\Model\Repository */
		$repository = $this->newRepository();
		$repository->mClass = $entityClass;
		$repository->mDirname = 'foobar';

		$expectedEntity = new $entityClass();
		$expectedEntity->mDirname = 'foobar';

		$actualEntity = $repository->create();
		$this->assertEquals($expectedEntity, $actualEntity);
	}
}
