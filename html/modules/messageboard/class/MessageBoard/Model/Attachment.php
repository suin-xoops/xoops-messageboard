<?php

namespace MessageBoard\Model;

use \MessageBoard\Model\UploadingAttachment;

class Attachment extends \MessageBoard\Model\Entity
{
	const PRIMARY = 'id';
	const DATANAME = 'attachment';

	/** @var \MessageBoard\Model\UploadingAttachment */
	protected $uploadingAttachment;
	/** @var string */
	protected $filePath;

	/**
	 * Return new Attachment object
	 */
	public function __construct()
	{
		$this->initVar('id', XOBJ_DTYPE_INT, '', false);
		$this->initVar('board_id', XOBJ_DTYPE_INT, '', false);
		$this->initVar('comment_id', XOBJ_DTYPE_INT, '', false);
		$this->initVar('name', XOBJ_DTYPE_STRING, '', false);
		$this->initVar('size', XOBJ_DTYPE_INT, '', false);
		$this->initVar('created', XOBJ_DTYPE_INT, time(), false);
	}

	/**
	 * Set uploading attachment
	 * @param \MessageBoard\Model\UploadingAttachment $uploadingAttachment
	 */
	public function setUploadingAttachment(UploadingAttachment $uploadingAttachment)
	{
		$this->uploadingAttachment = $uploadingAttachment;
	}

	/**
	 * Determine if this attachment has uploading attachment
	 * @return bool
	 */
	public function hasUploadingAttachment()
	{
		if ( $this->uploadingAttachment === null )
		{
			return false;
		}

		return true;
	}

	/**
	 * Return uploading attachment object
	 * @return \MessageBoard\Model\UploadingAttachment
	 */
	public function getUploadingAttachment()
	{
		return $this->uploadingAttachment;
	}

	/**
	 * Unset uploading attachment
	 */
	public function unsetUploadingAttachment()
	{
		$this->uploadingAttachment = null;
	}

	/**
	 * Set file path
	 * @param string $filePath
	 */
	public function setFilePath($filePath)
	{
		$this->filePath = $filePath;
	}

	/**
	 * Return file path
	 * @return string
	 */
	public function getFilePath()
	{
		return $this->filePath;
	}
}
