<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

abstract class AbstractEntity
{
    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id = null;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'datetime', nullable: true, options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected ?DateTime $createdAt;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'datetime', nullable: true, options: ['default' => null, 'on update' => 'CURRENT_TIMESTAMP'])]
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