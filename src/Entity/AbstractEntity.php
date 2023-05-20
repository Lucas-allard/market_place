<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

#[ORM\MappedSuperclass]
abstract class AbstractEntity
{
    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    protected ?int $id = null;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'datetime')]
    protected DateTime $createdAt;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'datetime', nullable: true, options: ['default' => null, 'on update' => 'CURRENT_TIMESTAMP'])]
    protected DateTime $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

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
     * @param DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt() : ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

}