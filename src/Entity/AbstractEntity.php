<?php

namespace App\Entity;


use DateTime;

abstract class AbstractEntity
{
    protected int $id;
    protected DateTime $createdAt;
    protected DateTime $updatedAt;

    public function getId() : int
    {
        return $this->id;
    }

    public function getCreatedAt() : DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt() : DateTime
    {
        return $this->updatedAt;
    }

}