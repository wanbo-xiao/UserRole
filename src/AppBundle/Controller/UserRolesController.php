<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Service\UserRolesService;

class UserRolesController extends Controller
{
    private $service;

    public function __construct(UserRolesService $service)
    {
        $this->service = $service;
        $inputUsers = file_get_contents(__DIR__.'/../../../app/Resources/user.json');
        $inputRoles = file_get_contents(__DIR__.'/../../../app/Resources/role.json');
        $this->service->setRoles($inputRoles);
        $this->service->setUsers($inputUsers);
    }

    public function demoAction(){
        echo $this->service->getSubOrdinates(3)."\n";
        echo $this->service->getSubOrdinates(1)."\n";
    }

    public function getSubOrdinates($userId)
    {
        echo $this->service->getSubOrdinates($userId)."\n";
    }
}
