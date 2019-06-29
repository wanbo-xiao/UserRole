<?php

namespace Tests\AppBundle\Model;

use PHPUnit\Framework\TestCase;
use AppBundle\Service\UserRolesService;
use AppBundle\Model\Role;
use AppBundle\Model\User;


class UserRolesServiceTest extends TestCase
{

    protected $demoRoleData;
    protected $testRoleData;
    protected $demoUserData;
    protected $testUserData;

    public function setUp()
    {
        $this->demoRoleData = file_get_contents(__DIR__.'/../../../app/Resources/role.json');
        $this->demoUserData = file_get_contents(__DIR__.'/../../../app/Resources/user.json');
        $this->testRoleData = file_get_contents(__DIR__.'/../../../app/Resources/role_test.json');
        $this->testUserData = file_get_contents(__DIR__.'/../../../app/Resources/user_test.json');
    }

    public function testGetSubOrdinates()
    {
        $service = new UserRolesService(new Role(), new User());
        $service->setRoles($this->demoRoleData);
        $service->setUsers($this->demoUserData);

        // test example
        $this->assertJson($service->getSubOrdinates(3));
        $this->assertJsonStringEqualsJsonString(
            '[{"Id": 2,"Name": "Emily Employee","Role": 4}, {"Id": 5, "Name": "Steve Trainer","Role": 5}]',
            $service->getSubOrdinates(3)
        );
        $this->assertJson($service->getSubOrdinates(1));
        $this->assertJsonStringEqualsJsonString(
            '[{"Id":2,"Name":"Emily Employee","Role":4},{"Id":5,"Name":"Steve Trainer","Role":5},{"Id":3,"Name":"Sam Supervisor","Role":3},{"Id":4,"Name":"Mary Manager","Role":2}]',
            $service->getSubOrdinates(1)
        );

        // test user with 0 subordinate
        $this->assertJson($service->getSubOrdinates(2));
        $this->assertJsonStringEqualsJsonString('[]',$service->getSubOrdinates(2));

        // test user not existed
        $this->assertJson($service->getSubOrdinates(22));
        $this->assertJsonStringEqualsJsonString('[]',$service->getSubOrdinates(22));

        // test customize data
        $service_test = new UserRolesService(new Role(), new User());
        $service_test->setRoles($this->testRoleData);
        $service_test->setUsers($this->testUserData);
        $this->assertJson($service_test->getSubOrdinates(4));
        $this->assertJsonStringEqualsJsonString(
            '[{"Id":2,"Name":"Emily Employee","Role":4},{"Id":5,"Name":"Steve Trainer","Role":5},{"Id":3,"Name":"Sam Supervisor","Role":3},{"Id":10,"Name":"User Role11","Role":11},{"Id":7,"Name":"User Role10","Role":10},{"Id":9,"Name":"User Role8","Role":8},{"Id":6,"Name":"User Role7","Role":7},{"Id":8,"Name":"Another User Role7","Role":7}]',
            $service_test->getSubOrdinates(4)
        );
    }
}