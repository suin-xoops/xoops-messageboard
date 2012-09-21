<?php

namespace MessageBoard\Model;

use \CriteriaCompo;
use \Criteria;
use \MessageBoard\Model\UploadingAttachment;
use \MessageBoard\Model\Comment;
use \MessageBoard\Model\DomainException;

class AttachmentRepository extends \MessageBoard\Model\Repository
{
	public $mTable = '{dirname}_attachment';
	public $mPrimary = 'id';
	public $mClass = '\MessageBoard\Model\Attachment';

	/** @var string */
	protected $attachmentDir;

	/**
	 * Create attachment with uploading attachment data
	 * @param \MessageBoard\Model\Comment             $comment
	 * @param \MessageBoard\Model\UploadingAttachment $uploadingAttachment
	 * @throws \MessageBoard\Model\DomainException
	 * @return \MessageBoard\Model\Attachment
	 */
	public function createWithUploadingAttachment(Comment $comment, UploadingAttachment $uploadingAttachment)
	{
		/** @var $attachment \MessageBoard\Model\Attachment */
		$attachment = $this->create();
		$attachment->setVars(array(
			'board_id'  => $comment->get('board_id'),
			'comment_id' => $comment->get('id'),
			'name'       => $uploadingAttachment->getName(),
			'size'       => $uploadingAttachment->getSize(),
		));
		$attachment->setUploadingAttachment($uploadingAttachment);

		if ( $this->insert($attachment) === false )
		{
			throw DomainException::failedToCreateAttachment();
		}

		return $attachment;
	}

	/**
	 * Set attachment directory
	 * @param string $attachmentDir
	 */
	public function setAttachmentDir($attachmentDir)
	{
		$this->attachmentDir = $attachmentDir;
	}


	/**
	 * @param \MessageBoard\Model\Attachment $attachment
	 * @param bool   $force
	 * @return bool
	 */
	public function insert(&$attachment, $force = true)
	{
		if ( parent::insert($attachment, $force) === false )
		{
			return false;
		}

		if ( $attachment->hasUploadingAttachment() )
		{
			$this->_upload($attachment);
		}

		return true;
	}

	/**
	 * @param null $criteria
	 * @param null $limit
	 * @param null $start
	 * @param bool $id_as_key
	 * @return array
	 */
	public function &getObjects($criteria = null, $limit = null, $start = null, $id_as_key = false)
	{
		/** @var $objects \MessageBoard\Model\Attachment[] */
		$objects = parent::getObjects($criteria, $limit, $start, $id_as_key);

		foreach ( $objects as $object )
		{
			$object->setFilePath(sprintf('%s/%s/%s', $this->attachmentDir, $object->get('board_id'), $object->get('id')));
		}

		return $objects;
	}

	/**
	 * @param \MessageBoard\Model\Attachment $attachment
	 * @return bool
	 */
	protected function _upload(Attachment $attachment)
	{
		$uploadDirectory = $this->attachmentDir.'/'.$attachment->get('board_id').'/';

		if ( is_dir($uploadDirectory) === false )
		{
			if ( mkdir($uploadDirectory, 0777, true) === false )
			{
				return false;
			}
		}

		$uploadFrom = $attachment->getUploadingAttachment()->getTemporaryName();
		$uploadTo   = $uploadDirectory.'/'.$attachment->get('id');

		if ( move_uploaded_file($uploadFrom, $uploadTo) === false )
		{
			return false;
		}

		$attachment->unsetUploadingAttachment();

		return true;
	}

	/**
	 * Find and return comments with board ID
	 * @param int $boardId
	 * @return \MessageBoard\Model\Comment[]
	 */
	public function findByBoardId($boardId)
	{
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('board_id', $boardId));
		return $this->getObjects($criteria);
	}
}
