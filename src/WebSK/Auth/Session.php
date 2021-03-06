<?php

namespace WebSK\Auth;

use WebSK\Entity\Entity;

/**
 * Class Session
 * @package WebSK\Auth
 */
class Session extends Entity
{
    const ENTITY_SERVICE_CONTAINER_ID = 'auth.session_service';
    const ENTITY_REPOSITORY_CONTAINER_ID = 'auth.session_repository';
    const DB_TABLE_NAME = 'sessions';

    const AUTH_COOKIE_NAME = 'auth_session';

    const SESSION_LIFE_TIME = 31536000; // 1 год

    const _USER_ID = 'user_id';
    /** @var int */
    protected $user_id;

    const _SESSION = 'session';
    /** @var string */
    protected $session;

    const _HOSTNAME = 'hostname';
    /** @var string */
    protected $hostname;

    const _TIMESTAMP = 'timestamp';
    /** @var int */
    protected $timestamp;

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return string
     */
    public function getSession(): string
    {
        return $this->session;
    }

    /**
     * @param string $session
     */
    public function setSession(string $session): void
    {
        $this->session = $session;
    }

    /**
     * @return string
     */
    public function getHostname(): string
    {
        return $this->hostname;
    }

    /**
     * @param string $hostname
     */
    public function setHostname(string $hostname): void
    {
        $this->hostname = $hostname;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     */
    public function setTimestamp(int $timestamp): void
    {
        $this->timestamp = $timestamp;
    }
}
