<?php

namespace MessageBoard\Model;

use \Mockery as m;

class UserTest extends \PHPUnit_Framework_TestCase
{
	public function testSetUser()
	{
		$xoopsUser = $this->getMock('XoopsUser', null, array(), '', false);
		$user = new User();
		$this->assertNull($user->setUser($xoopsUser));
		$this->assertAttributeSame($xoopsUser, 'user', $user);
	}

	public function testSetPrincipal()
	{
		$principal = m::mock('XCube_Principal');
		$user = new User();
		$this->assertNull($user->setPrincipal($principal));
		$this->assertAttributeSame($principal, 'principal', $user);
	}

	public function testSetMemberRepository()
	{
		$memberRepository = m::mock('\MessageBoard\Model\MemberRepository');
		$user = new User();
		$this->assertNull($user->setMemberRepository($memberRepository));
		$this->assertAttributeSame($memberRepository, 'memberRepository', $user);
	}

	public function testHasMembershipTo()
	{
		$board = m::mock('\MessageBoard\Model\Board');

		$user = new User();
		$this->assertFalse($user->hasMembershipTo($board));
	}

	public function testHasMembershipTo_having_site_owner_principal()
	{
		$board = m::mock('\MessageBoard\Model\Board');
		$xoopsUser = $this->getMock('XoopsUser', null, array(), '', false);
		$principal = m::mock('XCube_Principal');
		$principal->shouldReceive('isInRole')->with('Site.Owner')->andReturn(true)->once();

		$user = new User();
		$user->setUser($xoopsUser);
		$user->setPrincipal($principal);
		$this->assertTrue($user->hasMembershipTo($board));
	}

	public function testHasMembershipTo_having_module_admin_principal()
	{
		$board = m::mock('\MessageBoard\Model\Board');
		$xoopsUser = $this->getMock('XoopsUser', null, array(), '', false);
		$principal = m::mock('XCube_Principal');
		$principal->shouldReceive('isInRole')->with('Site.Owner')->andReturn(false)->once();
		$principal->shouldReceive('isInRole')->with('Module.messageboard.Admin')->andReturn(true)->once();

		$user = new User();
		$user->setUser($xoopsUser);
		$user->setPrincipal($principal);
		$this->assertTrue($user->hasMembershipTo($board));
	}

	public function testHasMembershipTo_user_is_member_of_the_board()
	{
		$userId = 1234;
		$boardId = 9876;

		$board = m::mock('\MessageBoard\Model\Board');
		$board->shouldReceive('get')->with('id')->andReturn($boardId)->once();

		$xoopsUser = $this->getMock('XoopsUser', array('get'), array(), '', false);
		$xoopsUser->expects($this->once())->method('get')->with('uid')->will($this->returnValue($userId));

		$principal = m::mock('XCube_Principal');
		$principal->shouldReceive('isInRole')->with('Site.Owner')->andReturn(false)->once();
		$principal->shouldReceive('isInRole')->with('Module.messageboard.Admin')->andReturn(false)->once();

		$memberRepository = m::mock('\MessageBoard\Model\MemberRepository');
		$memberRepository->shouldReceive('hasMembership')->with($userId, $boardId)->andReturn(true)->once();

		$user = new User();
		$user->setUser($xoopsUser);
		$user->setPrincipal($principal);
		$user->setMemberRepository($memberRepository);
		$this->assertTrue($user->hasMembershipTo($board));
	}
}
