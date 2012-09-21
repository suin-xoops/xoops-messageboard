<?php

namespace MessageBoard\Model;

use \MessageBoard\Model\DomainException;

class UploadingAttachment
{
	/** @var string */
	protected $name;
	/** @var string */
	protected $type;
	/** @var string */
	protected $temporaryName;
	/** @var int */
	protected $error;
	/** @var int */
	protected $size;

	/**
	 * Create uploading attachments with $_FILES
	 * @param array $file
	 * @return \MessageBoard\Model\UploadingAttachment[]
	 */
	public static function createUploadingAttachments(array $file)
	{
		$uploadingAttachments = array();

		foreach ( $file['name'] as $index => $name )
		{
			$uploadingAttachments[] = new static(array(
				'name'     => $file['name'][$index],
				'type'     => $file['type'][$index],
				'tmp_name' => $file['tmp_name'][$index],
				'error'    => $file['error'][$index],
				'size'     => $file['size'][$index],
			));
		}

		return $uploadingAttachments;
	}

	/**
	 * Return uploading attachments with $_FILES filtering received files
	 * @param array $file
	 * @return \MessageBoard\Model\UploadingAttachment[]
	 */
	public static function getUploadingAttachments(array $file)
	{
		$uploadingAttachments = static::createUploadingAttachments($file);
		return array_values(array_filter($uploadingAttachments, function(UploadingAttachment $uploadingAttachment){
			return $uploadingAttachment->hasBeenReceived();
		}));
	}

	/**
	 * Return new object
	 * @param array $data
	 */
	public function __construct(array $data)
	{
		$this->name = $data['name'];
		$this->type = $data['type'];
		$this->temporaryName = $data['tmp_name'];
		$this->error = $data['error'];
		$this->size = $data['size'];
	}

	/**
	 * Determine if this attachment has been received by server
	 * @return bool
	 */
	public function hasBeenReceived()
	{
		if ( $this->error === UPLOAD_ERR_NO_FILE )
		{
			return false;
		}

		return true;
	}

	/**
	 * Determine if this attachment is valid
	 * @return bool
	 */
	public function isValid()
	{
		if ( $this->error === UPLOAD_ERR_OK )
		{
			return true;
		}

		return false;
	}

	/**
	 * Return this attachment file name
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Return this attachment file size
	 * @return int Bytes
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * Return this attachment temporary name
	 * @return string
	 */
	public function getTemporaryName()
	{
		return $this->temporaryName;
	}
}
