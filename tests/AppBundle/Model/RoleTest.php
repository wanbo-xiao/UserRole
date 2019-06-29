<?php

namespace Tests\AppBundle\Model;

use PHPUnit\Framework\TestCase;
use AppBundle\Model\Role;

class RoleTest extends TestCase
{
    protected $demoRoleData;
    protected $testRoleData;

    public function setUp()
    {
        $this->demoRoleData = file_get_contents(__DIR__.'/../../../app/Resources/role.json');
        $this->testRoleData = file_get_contents(__DIR__.'/../../../app/Resources/role_test.json');
    }

    public function testSetRoles()
    {
        $role = new Role();
        $role->setRoles($this->demoRoleData);
        $rolesResult = $role->getRoles();
        $this->assertInternalType('array', $rolesResult);
        $this->assertEquals(5, count($rolesResult));
        foreach ($rolesResult as $singleRole) {
            $this->checkSingleRoleStructure($singleRole);
        }
        $this->assertEquals('System Administrator', $rolesResult[1]['Name']);
        $this->assertEquals(0, $rolesResult[1]['Parent']);
        $this->assertEquals([2], $rolesResult[1]['Children']);
        $this->assertEquals([4, 5], $rolesResult[3]['Children']);

        //test with 1. child data before it's parent data in json data  2. more than 1 root role
        $role_test = new Role();
        $role_test->setRoles($this->testRoleData);
        $rolesTestResult = $role_test->getRoles();
        $this->assertEquals(16, count($rolesTestResult));
        $this->assertEquals('Role7', $rolesTestResult[7]['Name']);
        $this->assertEquals(6, $rolesTestResult[7]['Parent']);
        $this->assertEquals([9, 10, 8], $rolesTestResult[7]['Children']);
        $this->assertEquals('Role13 in another tree', $rolesTestResult[13]['Name']);
        $this->assertEquals(0, $rolesTestResult[13]['Parent']);
        $this->assertEquals([14], $rolesTestResult[13]['Children']);
    }

    public function testGetRoleById()
    {
        $role = new Role();
        $role->setRoles($this->demoRoleData);
        $currentRole = $role->getRoleById(2);
        $this->checkSingleRoleStructure($currentRole);
        $this->assertEquals('Location Manager', $currentRole['Name']);
        $this->assertEquals(1, $currentRole['Parent']);
        $this->assertEquals([3], $currentRole['Children']);

        // test role with wrong id
        $roleByWrongId = $role->getRoleById(22);
        $this->assertInternalType('array', $roleByWrongId);
        $this->assertEmpty($roleByWrongId);
    }


    public function testSearchChildrenById()
    {
        $role = new Role();
        $role->setRoles($this->demoRoleData);
        $this->assertEquals([3], $role->searchChildrenById(2));

        $role_test = new Role();
        $role_test->setRoles($this->testRoleData);
        $this->assertEquals([9, 10, 8], $role_test->searchChildrenById(7));
    }

    public function testInsertNewRole()
    {
        $role = new Role();
        $role->setRoles($this->demoRoleData);
        $previousRoles    = $role->getRoles();
        $previousChildren = $role->searchChildrenById(2);
        $role->insertNewRole(
            ['Name' => 'Role10', 'Parent' => 2, 'Id' => 10]
        );
        $currentRoles    = $role->getRoles();
        $currentChildren = $role->searchChildrenById(2);
        $diffRoles       = array_diff_key($currentRoles, $previousRoles);
        $this->assertEquals([10], array_keys($diffRoles));
        $this->checkSingleRoleStructure($diffRoles[10]);
        $this->assertEquals([10], array_values(array_diff($currentChildren, $previousChildren)));
    }

    public function testAddChildToParentById()
    {
        $role = new Role();
        $role->setRoles($this->demoRoleData);
        $previousRole = $role->getRoleById(2);
        $role->addChildToParentById(10, 2);
        $currentRole = $role->getRoleById(2);
        $this->assertEquals([10], array_values(array_diff($currentRole['Children'], $previousRole['Children'])));
    }

    public function testGetSubRoles()
    {
        $role = new Role();
        $role->setRoles($this->demoRoleData);
        $this->assertEquals([4, 5, 3, 2], $role->getSubRoles(1));
        $this->assertEquals([], $role->getSubRoles(4));
        $this->assertEquals([4, 5, 3, 2, 1], $role->getSubRoles(1, true));

        $role_test = new Role();
        $role_test->setRoles($this->testRoleData);
        $this->assertEquals([12, 11, 9, 10, 8, 7], $role_test->getSubRoles(6));
    }

    // test single role structure
    private function checkSingleRoleStructure($role)
    {
        $this->assertInternalType('array', $role);
        $this->assertArrayHasKey('Name', $role);
        $this->assertArrayHasKey('Parent', $role);
        $this->assertInternalType('int', $role['Parent']);
        $this->assertArrayHasKey('Children', $role);
        $this->assertInternalType('array', $role['Children']);
    }
}