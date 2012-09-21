<?php

namespace MessageBoard\Model;

use \XoopsUser;
use \XCube_Principal;
use \MessageBoard\Model\MemberRepository;
use \MessageBoard\Model\Board;

class User
{
	/** @var \XoopsUser */
	protected $user;
	/** @var \XCube_Principal */
	protected $principal;
	/** @var \MessageBoard\Model\MemberRepository */
	protected $memberRepository;

	/**
	 * @param \XoopsUser $user
	 */
	public function setUser(XoopsUser $user)
	{
		$this->user = $user;
	}

	/**
	 * @param \XCube_Principal $principal
	 */
	public function setPrincipal(XCube_Principal $principal)
	{
		$this->principal = $principal;
	}

	/**
	 * @param \MessageBoard\Model\MemberRepository $memberRepository
	 */
	public function setMemberRepository(MemberRepository $memberRepository)
	{
		$this->memberRepository = $memberRepository;
	}

	/**
	 * @param \MessageBoard\Model\Board $board
	 * @return bool
	 */
	public function hasMembershipTo(Board $board)
	{
		if ( is_object($this->user) === false )
		{
			return false;
		}

		if ( $this->principal->isInRole('Site.Owner') )
		{
			return true;
		}

		if ( $this->principal->isInRole('Module.messageboard.Admin') )
		{
			return true;
		}

		return $this->memberRepository->hasMembership($this->user->get('uid'), $board->get('id'));
	}
}
