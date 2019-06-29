<?php

namespace AppBundle\Model;


class Role
{
    private $role;

    public function __construct()
    {
        $this->role = [];
    }

    // reformat the json code to array
    public function setRoles($roles)
    {
        $rolesArray = json_decode($roles, true);
        if (is_array($rolesArray) && count($rolesArray) > 0) {
            foreach ($rolesArray as $role) {
                $this->insertNewRole($role);
            }
        }
    }

    public function getRoles()
    {
        return $this->role;
    }

    public function getRoleById($id)
    {
        if (array_key_exists($id, $this->role)) {
            return $this->role[$id];
        }

        return [];
    }

    // once a new role inserted 1. loop to find all its children  2. add as a child to its parent
    public function insertNewRole(Array $role)
    {
        if (array_key_exists('Id', $role) && array_key_exists('Name', $role) && array_key_exists('Parent', $role)) {
            if (array_key_exists($role['Id'], $this->role)) {
                //TODO: if found duplicate, should I ignore it or update it ?
            } else {
                $this->role[$role['Id']] = [
                    'Name'     => $role['Name'],
                    'Parent'   => $role['Parent'],
                    'Children' => $this->searchChildrenById($role['Id']),
                ];

                $this->addChildToParentById($role['Id'], $role['Parent']);
            }
        }
    }

    public function searchChildrenById($id)
    {
        $children = [];
        foreach ($this->role as $roleId => $roleData) {
            if ($roleData['Parent'] == $id) {
                $children[] = $roleId;
            }
        }

        return $children;
    }

    public function addChildToParentById($childId, $parentId)
    {
        if (array_key_exists($parentId, $this->role)) {
            $this->role[$parentId]['Children'][] = $childId;
        }
    }

    // recursive get all sub roles, exclude itself
    public function getSubRoles($roleId, $includeThisId = false)
    {
        $returnIds = array();
        if (array_key_exists($roleId, $this->role)) {
            foreach ($this->role[$roleId]['Children'] as $subRoleId) {
                $returnIds = array_merge($returnIds, $this->getSubRoles($subRoleId, true));
            }
            if ($includeThisId) {
                $returnIds[] = $roleId;
            }
        }

        return $returnIds;
    }
}