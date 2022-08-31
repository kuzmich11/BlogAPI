<?php

namespace KuznetsovVladimir\BlogApi\User;


use KuznetsovVladimir\BlogApi\Blog\UUID;

class User
{
    private UUID $uuid;
    private string $username;
    private Name $name;

    /**
     * @param UUID $uuid
     * @param Name $name
     * @param string $username
     */
    public function __construct(UUID $uuid, string $username, Name $name)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->username = $username;
    }


    /**
     * @return UUID $uuid
     */
    public function uuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @param UUID $uuid
     */
    public function setUuid(UUID $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return Name
     */
    public function name(): Name
    {
        return $this->name;
    }

    /**
     * @param Name $name
     */
    public function setName(Name $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function username(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function __toString()
    {
        return $this->name . ' c логином ' . $this->username;
    }

}