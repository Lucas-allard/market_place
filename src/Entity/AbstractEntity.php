<?php

namespace App\Entity;


use DateTime;

abstract class AbstractEntity
{
    /**
     * @var int|null
     */
    protected ?int $id = null;
    /**
     * @var DateTime|null
     */
    protected ?DateTime $createdAt;
    /**
     * @var DateTime|null
     */
    protected ?DateTime $updatedAt;

    /**
     * @return int|null
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @return DateTime|null
     */
    public function getCreatedAt() : ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt() : ?DateTime
    {
        return $this->updatedAt;
    }

}