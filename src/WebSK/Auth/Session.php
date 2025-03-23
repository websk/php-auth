<?php

namespace WebSK\Auth;

use WebSK\Entity\Entity;

/**
 * Class Session
 * @package WebSK\Auth
 */
class Session extends Entity
{
    const string DB_TABLE_NAME = 'sessions';

    const string AUTH_COOKIE_NAME = 'auth_session';

    const int SESSION_LIFE_TIME = 31536000; // 1 год

    const string _USER_ID = 'user_id';
    protected int $user_id;

    const string _SESSION = 'session';
    protected string $session;

    const string _HOSTNAME = 'hostname';
    protected string $hostname;

    const string _TIMESTAMP = 'timestamp';
    protected int $timestamp;

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
