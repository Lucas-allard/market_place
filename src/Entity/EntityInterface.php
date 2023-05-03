<?php

namespace App\Entity;

interface EntityInterface
{
    public function getId(): ?int;

    public function getCreatedAt(): ?\DateTimeInterface;

    public function getUpdatedAt(): ?\DateTimeInterface;
}