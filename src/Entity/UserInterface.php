<?php

namespace App\Entity;

interface UserInterface
{
    /**
     * @return string|null
     */
    public function getFirstName(): ?string;

    /**
     * @return string|null
     */
    public function getLastName(): ?string;

    /**
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * @return string|null
     */
    public function getAddress(): ?string;

    /**
     * @return string|null
     */
    public function getPhone(): ?string;
}