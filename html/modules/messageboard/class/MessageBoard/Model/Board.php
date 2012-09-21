<?php

namespace MessageBoard\Model;

class Board extends \MessageBoard\Model\Entity implements \MessageBoard\Model\BoardInterface
{
	const PRIMARY = 'id';
	const DATANAME = 'board';

	/** @var \MessageBoard\Model\Comment[] */
	protected $comments = array();

	/**
	 * @return mixed
	 */
	public function __construct()
	{
		$this->initVar('id', XOBJ_DTYPE_INT, '', false);
		$this->initVar('client_key', XOBJ_DTYPE_STRING, '', true);
		$this->initVar('created', XOBJ_DTYPE_INT, time(), false);
	}

	/**
	 * Set comment objects
	 * @param \MessageBoard\Model\Comment[] $comments
	 */
	public function setComments(array $comments)
	{
		$this->comments = $comments;
	}

	/**
	 * Return comment objects
	 * @return \MessageBoard\Model\Comment[]
	 */
	public function getComments()
	{
		return $this->comments;
	}
}
