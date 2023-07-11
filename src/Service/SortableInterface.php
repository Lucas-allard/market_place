<?php

namespace App\Service;


interface SortableInterface
{
    /**
     * @param array|null $criteria
     * @return array
     */
    public function getOrderBy(?array $criteria): array;

    /**
     * @param string|null $order
     * @param int|null $page
     * @param int|null $limit
     * @return array
     */
    public function setDefaultValues(?string &$order, ?int &$page, ?int &$limit): array;
}