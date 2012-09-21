<?php

namespace MessageBoard\Model;

class Member extends \MessageBoard\Model\Entity implements \MessageBoard\Model\MemberInterface
{
	public function __construct()
	{
		$this->initVar('id', XOBJ_DTYPE_INT, '', false);
		$this->initVar('user_id', XOBJ_DTYPE_INT, '', false);
		$this->initVar('board_id', XOBJ_DTYPE_INT, '', false);
		$this->initVar('created', XOBJ_DTYPE_INT, time(), false);
	}
}
