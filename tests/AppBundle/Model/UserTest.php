<?php

namespace Tests\AppBundle\Model;

use AppBundle\Model\User;
use PHPUnit\Framework\TestCase;
use AppBundle\Model\Role;

class UserTest extends TestCase
{
    protected $demoUserData;
    protected $testUserData;

    public function setUp()
    {
        $this->demoUserData = file_get_contents(__DIR__.'/../../../app/Resources/user.json');
        $this->testUserData = file_get_contents(__DIR__.'/../../../app/Resources/user_test.json');
    }

    public function testSetUsers()
    {
        $user = new User();
        $user->setUsers($this->demoUserData);
        $usersById = $user->getUsersWithKeyId();
        $this->assertInternalType('array', $usersById);
        $this->assertEquals(5, count($usersById));
        foreach ($usersById as $singleUsersById) {
            $this->checkSingleUserStructure($singleUsersById);
        }
        $usersByRole = $user->getUsersWithKeyRole();
        $this->assertInternalType('array', $usersByRole);
        $this->assertEquals(5, count($usersByRole));
        foreach ($usersByRole as $usersBySingleRole) {
            $this->assertInternalType('array', $usersBySingleRole);
            foreach ($usersBySingleRole as $singleUserBySingleRole) {
                $this->checkSingleUserStructure($singleUserBySingleRole);
            }
        }

        $this->assertEquals('Emily Employee', $usersById[2]['Name']);
        $this->assertEquals(4, $usersById[2]['Role']);
        $this->assertEquals(1, count($usersByRole[2]));
        $this->assertEquals('Mary Manager', $usersByRole[2][0]['Name']);
        $this->assertEquals(4, $usersByRole[2][0]['Id']);

        //test multi users in the same role
        $user_test = new User();
        $user_test->setUsers($this->testUserData);
        $userTestByRole = $user_test->getUsersWithKeyRole();

        $this->assertEquals(2, count($userTestByRole[7]));
        $this->assertEquals(6, $userTestByRole[7][0]['Id']);
        $this->assertEquals(8, $userTestByRole[7][1]['Id']);
        //test 0 user in a role
        $this->assertArrayNotHasKey(6, $userTestByRole);
        $this->assertArrayNotHasKey(9, $userTestByRole);
    }

    public function testGetUserById()
    {
        $user = new User();
        $user->setUsers($this->demoUserData);
        $userById = $user->getUserById(3);
        $this->checkSingleUserStructure($userById);
        $this->assertEquals('Sam Supervisor', $userById['Name']);
        $this->assertEquals(3, $userById['Role']);

        //test with wrong id
        $userByWrongId = $user->getUserById(33);
        $this->assertInternalType('array', $userByWrongId);
        $this->assertEmpty($userByWrongId);
    }

    public function testGetUsersByRole()
    {
        $user = new User();
        $user->setUsers($this->demoUserData);
        $usersByRole = $user->getUsersByRole(5);
        $this->assertInternalType('array', $usersByRole);
        $this->assertEquals(1, count($usersByRole));
        foreach ($usersByRole as $singleUserByRole) {
            $this->checkSingleUserStructure($singleUserByRole);
        }
        $this->assertEquals('Steve Trainer', $usersByRole[0]['Name']);
        $this->assertEquals(5, $usersByRole[0]['Id']);

        //test with wrong id
        $userByWrongRole = $user->getUsersByRole(55);
        $this->assertInternalType('array', $userByWrongRole);
        $this->assertEmpty($userByWrongRole);
    }

    public function testInsertNewUser()
    {
        $user = new User();
        $user->setUsers($this->demoUserData);
        $previousUsersById   = $user->getUsersWithKeyId();
        $previousUsersByRole = $user->getUsersWithKeyRole();

        $user->insertNewUser(['Id' => 6, 'Name' => 'User6', 'Role' => 7]);
        $currentUsersById   = $user->getUsersWithKeyId();
        $currentUsersByRole = $user->getUsersWithKeyRole();
        $diffUsersById      = array_diff_key($currentUsersById, $previousUsersById);
        $this->assertEquals([6], array_keys($diffUsersById));
        $this->checkSingleUserStructure($diffUsersById[6]);
        $this->assertEquals('User6', $diffUsersById[6]['Name']);
        $this->assertEquals(7, $diffUsersById[6]['Role']);

        // test new user with new role
        $diffUsersByRole = array_diff_key($currentUsersByRole, $previousUsersByRole);
        $this->assertEquals([7], array_keys($diffUsersByRole));
        $this->assertInternalType('array', $diffUsersByRole[7]);
        $this->assertEquals(1, count($diffUsersByRole[7]));
        $this->checkSingleUserStructure($diffUsersByRole[7][0]);
        $this->assertEquals('User6', $diffUsersByRole[7][0]['Name']);
        $this->assertEquals(6, $diffUsersByRole[7][0]['Id']);

        // test new user with existing role
        $previousUsersByRole   = $user->getUsersWithKeyRole();
        $previousUsersByRoleId = $user->getUsersByRole(2);
        $user->insertNewUser(['Id' => 7, 'Name' => 'User7', 'Role' => 2]);
        $currentUsersByRole   = $user->getUsersWithKeyRole();
        $currentUsersByRoleId = $user->getUsersByRole(2);
        $diffUsersByRole      = array_diff_key($currentUsersByRole, $previousUsersByRole);
        $this->assertEquals(0, count($diffUsersByRole));
        $diffUsersByRoleId = array_diff_key($currentUsersByRoleId, $previousUsersByRoleId);
        $this->assertEquals(1, count($diffUsersByRoleId));
        $this->assertEquals(7, $diffUsersByRoleId[1]['Id']);
        $this->assertEquals('User7', $diffUsersByRoleId[1]['Name']);
    }

    // test single role structure
    private function checkSingleUserStructure($user)
    {
        $this->assertInternalType('array', $user);
        $this->assertArrayHasKey('Name', $user);
        $this->assertArrayHasKey('Id', $user);
        $this->assertInternalType('int', $user['Id']);
        $this->assertArrayHasKey('Role', $user);
        $this->assertInternalType('int', $user['Role']);
    }
}