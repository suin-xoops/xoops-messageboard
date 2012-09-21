<?php

namespace MessageBoard\Model;

use \Mockery as m;
use \Expose\Expose as e;

class AttachmentTest extends \PHPUnit_Framework_TestCase
{
	public function testSetUploadingAttachment()
	{
		$uploadingAttachment = m::mock('\MessageBoard\Model\UploadingAttachment');

		$attachment = new Attachment();
		$this->assertNull($attachment->setUploadingAttachment($uploadingAttachment));
		$this->assertAttributeSame($uploadingAttachment, 'uploadingAttachment', $attachment);
	}

	/**
	 * @param $expect
	 * @param $uploadingAttachment
	 * @dataProvider dataForTestHasUploadingAttachment
	 */
	public function testHasUploadingAttachment($expect, $uploadingAttachment)
	{
		$attachment = new Attachment();
		e::expose($attachment)->attr('uploadingAttachment', $uploadingAttachment);
		$this->assertSame($expect, $attachment->hasUploadingAttachment());
	}

	public static function dataForTestHasUploadingAttachment()
	{
		return array(
			array(false, null),
			array(true, new \stdClass()),
		);
	}

	public function testGetUploadingAttachment()
	{
		$uploadingAttachment = m::mock();
		$attachment = new Attachment();
		e::expose($attachment)->attr('uploadingAttachment', $uploadingAttachment);
		$this->assertSame($uploadingAttachment, $attachment->getUploadingAttachment());
	}

	public function testUnsetUploadingAttachment()
	{
		$uploadingAttachment = m::mock();
		$attachment = new Attachment();
		e::expose($attachment)->attr('uploadingAttachment', $uploadingAttachment);
		$this->assertNull($attachment->unsetUploadingAttachment());
		$this->assertAttributeSame(null, 'uploadingAttachment', $attachment);
	}

	public function testSetFilePath()
	{
		$filePath = '/xoops_trust_path/uploads/messageboard/foo/bar';
		$attachment = new Attachment();
		$this->assertNull($attachment->setFilePath($filePath));
		$this->assertAttributeSame($filePath, 'filePath', $attachment);
	}

	public function testGetFilePath()
	{
		$filePath = '/xoops_trust_path/uploads/messageboard/foo/bar';
		$attachment = new Attachment();
		e::expose($attachment)->attr('filePath', $filePath);
		$this->assertSame($filePath, $attachment->getFilePath());
	}
}
