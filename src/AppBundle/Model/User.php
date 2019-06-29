<?php

namespace AppBundle\Model;

class User
{
    protected $usersWithKeyId;
    protected $usersWithKeyRole;

    public function __construct()
    {
        $this->usersWithKeyId   = [];
        $this->usersWithKeyRole = [];
    }

    // reformat the json to 2 arrays  1. key is userID  2. key is role
    public function setUsers($users)
    {
        $usersArray = json_decode($users, true);
        if (is_array($usersArray) && count($usersArray) > 0) {
            foreach ($usersArray as $user) {
                $this->insertNewUser($user);
            }
        }
    }

    public function getUsersWithKeyId()
    {
        return $this->usersWithKeyId;
    }

    public function getUsersWithKeyRole()
    {
        return $this->usersWithKeyRole;
    }

    public function getUserById($userId)
    {
        if (array_key_exists($userId, $this->usersWithKeyId)) {
            return $this->usersWithKeyId[$userId];
        }

        return [];
    }

    public function getUsersByRole($roleId)
    {
        if (array_key_exists($roleId, $this->usersWithKeyRole)) {
            return $this->usersWithKeyRole[$roleId];
        }

        return [];
    }

    public function insertNewUser($user)
    {
        if (array_key_exists('Id', $user) && array_key_exists('Name', $user) && array_key_exists('Role', $user)) {
            if (array_key_exists($user['Id'], $this->usersWithKeyId)) {
                // TODO: if found duplicate, should I ignore it or update it ?
            } else {
                $this->usersWithKeyId[$user['Id']]       = [
                    'Id'   => $user['Id'],
                    'Name' => $user['Name'],
                    'Role' => $user['Role'],
                ];
                $this->usersWithKeyRole[$user['Role']][] = [
                    'Id'   => $user['Id'],
                    'Name' => $user['Name'],
                    'Role' => $user['Role'],
                ];
            }
        }
    }

}