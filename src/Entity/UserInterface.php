<?php

namespace App\Entity;

interface UserInterface
{
    public function getFirstName(): ?string;
    public function getLastName(): ?string;
    public function getEmail(): ?string;
    public function getRoles(): array;
    public function getPassword(): ?string;
    public function getAddress(): ?string;
    public function getPhone(): ?string;
}