<?php

namespace App\Entity;

interface SellerInterface
{
    /**
     * @return string|null
     */
    public function getCompany(): ?string;

    /**
     * @return string|null
     */
    public function getSiret(): ?string;

    /**
     * @return string|null
     */
    public function getVat(): ?string;

    /**
     * @todo Réfléchir à la création d'une entité Rating
     */
    public function getSellerRating(): ?float;

    /**
     * @return array|null
     */
    public function getProducts(): ?array;

    /**
     * @return array|null
     */
    public function getSales(): ?array;
}