<?php

namespace MessageBoard\Model;

class DomainException extends \RuntimeException
{
	public static function failedToCreateBoardClientKeyOf($clientKey)
	{
		return new self(sprintf('Failed to create board client key of %s', $clientKey));
	}

	public static function boardNotFoundForClientKey($clientKey)
	{
		return new self(sprintf('Board not found for client key: %s', $clientKey));
	}

	public static function failedToAddMemberToBoardClientKeyOf($clientKey)
	{
		return new self(sprintf('Failed to add member to board client key of %s', $clientKey));
	}

	public static function userHasNoMembershipToBoard($userId, $clientKey)
	{
		return new self(sprintf('User (user_id: %s) has no membership to the board client key of %s', $userId, $clientKey));
	}

	public static function failedToAddCommentToBoardClientKeyOf($clientKey)
	{
		return new self(sprintf('Failed to add comment to board client key of %s', $clientKey));
	}

	public static function failedToCreateAttachment()
	{
		return new self(sprintf('Failed to create attachment'));
	}

	public static function failedToMoveUploadingAttachment($from, $to)
	{
		return new self(sprintf('Failed to move uploading attachment from %s to %s', $from, $to));
	}
}
