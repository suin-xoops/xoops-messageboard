<?php

namespace MessageBoard\Model\Service;

use \MessageBoard\Model\User;
use \MessageBoard\Model\BoardRepository;
use \MessageBoard\Model\MemberRepository;
use \MessageBoard\Model\CommentRepository;
use \MessageBoard\Model\AttachmentRepository;
use \MessageBoard\Model\DomainException;

class BoardApi implements \MessageBoard\Model\Service\BoardApiInterface
{
	/** @var \MessageBoard\Model\User */
	protected $user;
	/** @var \MessageBoard\Model\BoardRepository */
	protected $boardRepository;
	/** @var \MessageBoard\Model\MemberRepository */
	protected $memberRepository;
	/** @var \MessageBoard\Model\CommentRepository */
	protected $commentRepository;
	/** @var \MessageBoard\Model\AttachmentRepository */
	protected $attachmentRepository;

	/**
	 * Dependency injection of user object
	 * @param \MessageBoard\Model\User $user
	 */
	public function setUser(User $user)
	{
		$this->user = $user;
	}

	/**
	 * Dependency injection of BoardRepository object
	 * @param \MessageBoard\Model\BoardRepository $boardRepository
	 */
	public function setBoardRepository(BoardRepository $boardRepository)
	{
		$this->boardRepository = $boardRepository;
	}

	/**
	 * Dependency injection of MemberRepository object
	 * @param \MessageBoard\Model\MemberRepository $memberRepository
	 */
	public function setMemberRepository(MemberRepository $memberRepository)
	{
		$this->memberRepository = $memberRepository;
	}

	/**
	 * Dependency injection of CommentRepository object
	 * @param \MessageBoard\Model\CommentRepository $commentRepository
	 */
	public function setCommentRepository(CommentRepository $commentRepository)
	{
		$this->commentRepository = $commentRepository;
	}

	/**
	 * Dependency injection of AttachmentRepository object
	 * @param \MessageBoard\Model\AttachmentRepository $attachmentRepository
	 */
	public function setAttachmentRepository(AttachmentRepository $attachmentRepository)
	{
		$this->attachmentRepository = $attachmentRepository;
	}

	/**
	 * Create new board
	 * @param string $clientKey This key MUST be unique globally and MUST identify the client contents
	 * @return \MessageBoard\Model\Board
	 * @throws \MessageBoard\Model\DomainException When failed to save board
	 */
	public function create($clientKey)
	{
		/** @var $board \MessageBoard\Model\Board */
		$board = $this->boardRepository->create();
		$board->set('client_key', $clientKey);

		if ( $this->boardRepository->insert($board) === false )
		{
			throw DomainException::failedToCreateBoardClientKeyOf($clientKey);
		}

		return $board;
	}

	/**
	 * Add a user to the board as a member
	 * @param int    $userId
	 * @param string $clientKey
	 * @return void
	 * @throws \MessageBoard\Model\DomainException When try to add a member to non-exsting board,
	 *                                          When failed to save add a member
	 *
	 * Even a user has already joined the board
	 * this API does not throws any exceptions.
	 */
	public function addMember($userId, $clientKey)
	{
		$board = $this->boardRepository->getByClientKey($clientKey);

		if ( is_object($board) === false )
		{
			throw DomainException::boardNotFoundForClientKey($clientKey);
		}

		$boardId = $board->get('id');

		if ( $this->memberRepository->hasMembership($userId, $boardId) )
		{
			return;
		}

		if ( $this->memberRepository->addMembership($userId, $boardId) === false )
		{
			throw DomainException::failedToAddMemberToBoardClientKeyOf($clientKey);
		}
	}

	/**
	 * Add a new comment to the board
	 * @param string $clientKey
	 * @param int    $userId
	 * @param string $body
	 * @param \MessageBoard\Model\UploadingAttachment[]  $uploadingAttachments
	 * @throws \MessageBoard\Model\DomainException When failed to save comment
	 *                                          When the user has no membership to the board
	 * @return \MessageBoard\Model\Comment
	 */
	public function addComment($clientKey, $userId, $body, array $uploadingAttachments = array())
	{
		$board = $this->boardRepository->getByClientKey($clientKey);

		if ( is_object($board) === false )
		{
			throw DomainException::boardNotFoundForClientKey($clientKey);
		}

		if ( $this->user->hasMembershipTo($board) === false )
		{
			throw DomainException::userHasNoMembershipToBoard($userId, $clientKey);
		}

		/** @var $comment \MessageBoard\Model\Comment */
		$comment = $this->commentRepository->create();
		$comment->setVars(array(
			'board_id' => $board->get('id'),
			'user_id'   => $userId,
			'body'      => $body,
		));

		if ( $this->commentRepository->insert($comment) === false )
		{
			throw DomainException::failedToAddCommentToBoardClientKeyOf($clientKey);
		}

		foreach ( $uploadingAttachments as $uploadingAttachment )
		{
			$this->attachmentRepository->createWithUploadingAttachment($comment, $uploadingAttachment);
		}

		return $comment;
	}

	/**
	 * Return board
	 * @param string $clientKey
	 * @return \MessageBoard\Model\Board|bool
	 */
	public function getBoard($clientKey)
	{
		$board = $this->boardRepository->getByClientKey($clientKey);

		if ( $board === false )
		{
			return false;
		}

		$boardId = $board->get('id');
		$comments    = $this->commentRepository->findByBoardId($boardId);
		$attachments = $this->attachmentRepository->findByBoardId($boardId);

		foreach ( $attachments as $attachment )
		{
			$commentId = $attachment->get('comment_id');
			$comments[$commentId]->addAttachment($attachment);
		}

		$board->setComments($comments);
		return $board;
	}
}
