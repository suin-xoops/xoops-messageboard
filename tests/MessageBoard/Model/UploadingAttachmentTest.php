<?php

namespace MessageBoard\Model;

use \Expose\Expose as e;

class UploadingAttachmentTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @return \MessageBoard\Model\UploadingAttachment
	 */
	public function newUploadingAttachment()
	{
		return $this->getMock('\MessageBoard\Model\UploadingAttachment', null, array(), '', false);
	}

	public function test__construct()
	{
		$data = array(
			'name'     => 'FooBarBaz.png',
			'type'     => 'image/png',
			'tmp_name' => '/tmp/foo/bar/baz',
			'error'    => 1234,
			'size'     => 9876,
		);

		$uploadingAttachment = new UploadingAttachment($data);
		$this->assertAttributeSame($data['name'], 'name', $uploadingAttachment);
		$this->assertAttributeSame($data['type'], 'type', $uploadingAttachment);
		$this->assertAttributeSame($data['tmp_name'], 'temporaryName', $uploadingAttachment);
		$this->assertAttributeSame($data['error'], 'error', $uploadingAttachment);
		$this->assertAttributeSame($data['size'], 'size', $uploadingAttachment);
	}

	public function testCreateUploadingAttachments()
	{
		$file = array(
			'name'     => array('foo.png', 'bar.jpeg'),
			'type'     => array('image/png', 'image/jpg'),
			'tmp_name' => array('/tmp/foo', '/tmp/bar'),
			'error'    => array(1, 2),
			'size'     => array(1234, 9876),
		);

		$expected = array(
			new UploadingAttachment(array(
				'name'     => 'foo.png',
				'type'     => 'image/png',
				'tmp_name' => '/tmp/foo',
				'error'    => 1,
				'size'     => 1234,
			)),
			new UploadingAttachment(array(
				'name'     => 'bar.jpeg',
				'type'     => 'image/jpg',
				'tmp_name' => '/tmp/bar',
				'error'    => 2,
				'size'     => 9876,
			)),
		);

		$actual = UploadingAttachment::createUploadingAttachments($file);
		$this->assertEquals($expected, $actual);
	}

	public function testGetUploadingAttachments()
	{
		$file = array(
			'name'     => array('foo.png', 'bar.jpeg'),
			'type'     => array('image/png', 'image/jpg'),
			'tmp_name' => array('/tmp/foo', '/tmp/bar'),
			'error'    => array(UPLOAD_ERR_NO_FILE, UPLOAD_ERR_OK),
			'size'     => array(1234, 9876),
		);

		$expected = array(
			new UploadingAttachment(array(
				'name'     => 'bar.jpeg',
				'type'     => 'image/jpg',
				'tmp_name' => '/tmp/bar',
				'error'    => UPLOAD_ERR_OK,
				'size'     => 9876,
			)),
		);

		$actual = UploadingAttachment::getUploadingAttachments($file);
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @param $expect
	 * @param $error
	 * @dataProvider dataForTestHasBeenReceived
	 */
	public function testHasBeenReceived($expect, $error)
	{
		$uploadingAttachment = $this->newUploadingAttachment();
		e::expose($uploadingAttachment)->attr('error', $error);
		$this->assertSame($expect, $uploadingAttachment->hasBeenReceived());
	}

	public static function dataForTestHasBeenReceived()
	{
		return array(
			array(true, UPLOAD_ERR_OK),
			array(false, UPLOAD_ERR_NO_FILE),
		);
	}

	/**
	 * @param $expect
	 * @param $error
	 * @dataProvider dataForTestIsValid
	 */
	public function testIsValid($expect, $error)
	{
		$uploadingAttachment = $this->newUploadingAttachment();
		e::expose($uploadingAttachment)->attr('error', $error);
		$this->assertSame($expect, $uploadingAttachment->isValid());
	}

	public static function dataForTestIsValid()
	{
		return array(
			array(true, UPLOAD_ERR_OK),
			array(false, UPLOAD_ERR_NO_FILE),
			array(false, UPLOAD_ERR_INI_SIZE),
			array(false, UPLOAD_ERR_CANT_WRITE),
			array(false, UPLOAD_ERR_EXTENSION),
			array(false, UPLOAD_ERR_FORM_SIZE),
			array(false, UPLOAD_ERR_NO_TMP_DIR),
			array(false, UPLOAD_ERR_PARTIAL),
		);
	}

	public function testGetName()
	{
		$uploadingAttachment = $this->newUploadingAttachment();
		e::expose($uploadingAttachment)->attr('name', 'foobar.docx');
		$this->assertSame('foobar.docx', $uploadingAttachment->getName());
	}

	public function testGetSize()
	{
		$uploadingAttachment = $this->newUploadingAttachment();
		e::expose($uploadingAttachment)->attr('size', 12345);
		$this->assertSame(12345, $uploadingAttachment->getSize());
	}

	public function testGetTemporaryName()
	{
		$uploadingAttachment = $this->newUploadingAttachment();
		e::expose($uploadingAttachment)->attr('temporaryName', '/tmp/foo/bar/baz');
		$this->assertSame('/tmp/foo/bar/baz', $uploadingAttachment->getTemporaryName());
	}
}
