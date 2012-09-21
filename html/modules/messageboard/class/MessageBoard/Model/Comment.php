<?php

namespace MessageBoard\Model;

use \MessageBoard\Model\Attachment;

class Comment extends \MessageBoard\Model\Entity
{
	const PRIMARY = 'id';
	const DATANAME = 'comment';

	/** @var \MessageBoard\Model\Attachment[] */
	protected $attachments = array();

	public function __construct()
	{
		$this->initVar('id', XOBJ_DTYPE_INT, '', false);
		$this->initVar('board_id', XOBJ_DTYPE_INT, '', false);
		$this->initVar('user_id', XOBJ_DTYPE_INT, '', false);
		$this->initVar('body', XOBJ_DTYPE_TEXT, '', false);
		$this->initVar('created', XOBJ_DTYPE_INT, time(), false);
	}

	/**
	 * Add attachment
	 * @param \MessageBoard\Model\Attachment $attachment
	 */
	public function addAttachment(Attachment $attachment)
	{
		$this->attachments[] = $attachment;
	}

	/**
	 * Return attachments
	 * @return \MessageBoard\Model\Attachment[]
	 */
	public function getAttachments()
	{
		return $this->attachments;
	}
}
