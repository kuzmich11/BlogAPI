<?php

namespace src\Person;

use DateTimeImmutable;

class Person
{
    private int $id;
    private Name $name;
    private DateTimeImmutable $registeredOn;

    /**
     * @param int $id
     * @param Name $name
     * @param DateTimeImmutable $registeredOn
     */
    public function __construct(int $id, Name $name, DateTimeImmutable $registeredOn)
    {
        $this->id = $id;
        $this->name = $name;
        $this->registeredOn = $registeredOn;
    }

    public function __toString()
    {
        return $this->name .
            ' (на сайте с ' . $this->registeredOn->format('Y-m-d') . ')';
    }

}