<?php

namespace App\Entity\Interface;

interface UserInterface
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

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
    public function getCity(): ?string;

    /**
     * @return string|null
     */
    public function getStreet(): ?string;

    /**
     * @return string|null
     */
    public function getStreetNumber(): ?string;

    /**
     * @return string|null
     */
    public function getPostalCode(): ?string;

    /**
     * @return string|null
     */
    public function getPhone(): ?string;

    /**
     * @return bool|null
     */
    public function isVerified(): ?bool;

    public function setIsVerified(bool $isVerified): self;
}