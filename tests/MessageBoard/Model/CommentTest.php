<?php

namespace MessageBoard\Model;

use \Mockery as m;
use \Expose\Expose as e;

class CommentTest extends \PHPUnit_Framework_TestCase
{
	public function testAddAttachment()
	{
		$attachment1 = m::mock('\MessageBoard\Model\Attachment');
		$attachment2 = m::mock('\MessageBoard\Model\Attachment');
		$attachment3 = m::mock('\MessageBoard\Model\Attachment');

		$expect = array($attachment1, $attachment2, $attachment3);

		$comment = new Comment();
		$this->assertNull($comment->addAttachment($attachment1));
		$this->assertNull($comment->addAttachment($attachment2));
		$this->assertNull($comment->addAttachment($attachment3));
		$this->assertAttributeSame($expect, 'attachments', $comment);
	}

	public function testGetAttachments()
	{
		$attachment1 = m::mock('\MessageBoard\Model\Attachment');
		$attachment2 = m::mock('\MessageBoard\Model\Attachment');
		$attachment3 = m::mock('\MessageBoard\Model\Attachment');

		$attachments = array($attachment1, $attachment2, $attachment3);

		$comment = new Comment();
		e::expose($comment)->attr('attachments', $attachments);
		$this->assertSame($attachments, $comment->getAttachments());
	}
}
