<?php

namespace WebSK\Auth\User;

use WebSK\Entity\Entity;

/**
 * Class Role
 * @package WebSK\Auth\User
 */
class Role extends Entity
{
    const string DB_TABLE_NAME = 'roles';

    const int ADMIN_ROLE_ID = 1;

    const string _NAME = 'name';
    protected string $name;

    const string _DESIGNATION = 'designation';
    protected string $designation;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDesignation(): string
    {
        return $this->designation;
    }

    /**
     * @param string $designation
     */
    public function setDesignation(string $designation): void
    {
        $this->designation = $designation;
    }
}
