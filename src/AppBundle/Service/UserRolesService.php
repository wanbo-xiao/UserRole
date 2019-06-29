<?php

namespace AppBundle\Service;

use AppBundle\Model\User;
use AppBundle\Model\Role;

class UserRolesService
{
    private $user;
    private $role;

    public function __construct(Role $role, User $user)
    {
        $this->role = $role;
        $this->user = $user;
    }

    public function setRoles($roles)
    {
        $this->role->setRoles($roles);
    }

    public function setUsers($users)
    {
        $this->user->setUsers($users);
    }

    // 1. find user's role  2. find all sub roles 3.List all users in those sub roles
    public function getSubOrdinates($userId)
    {
        $subUsers = [];
        $user     = $this->user->getUserById($userId);
        if ($user) {
            $subRoles = $this->role->getSubRoles($user['Role']);
            foreach ($subRoles as $subRole) {
                $subUsers = array_merge($subUsers, $this->user->getUsersByRole($subRole));
            }
        }

        return json_encode($subUsers);
    }
}