<?php

namespace MessageBoard\Model\Service;

interface BoardApiInterface
{
	/**
	 * Create new board
	 * @param string $clientKey This key MUST be unique globally and MUST identify the client contents
	 * @return \MessageBoard\Model\Board
	 * @throws \MessageBoard\Model\DomainException When failed to save board
	 */
	public function create($clientKey);

	/**
	 * Add a user to the board as a member
	 * @param int $userId
	 * @param string $clientKey
	 * @return void
	 * @throws \MessageBoard\Model\DomainException When try to add a member to non-exsting board,
	 *                                          When failed to save add a member
	 *
	 * Even a user has already joined the board
	 * this API does not throws any exceptions.
	 */
	public function addMember($userId, $clientKey);

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
	public function addComment($clientKey, $userId, $body, array $uploadingAttachments = array());

	/**
	 * Return board
	 * @param string $clientKey
	 * @return \MessageBoard\Model\Board|bool Returns FALSE if board not exists
	 */
	public function getBoard($clientKey);
}
