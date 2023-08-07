<?php

namespace App\Service\Utils;

class SortHelper
{
    /**
     * @param array|null $criteria
     * @return array|string[]
     */
    public function getOrderBy(?array $criteria): array
    {
        if ($criteria) {
            $sort = array_key_first($criteria);
            $order = $criteria[$sort];

            return [$sort, $order];
        }

        return ['createdAt', 'DESC'];
    }

    /**
     * @param string|null $order
     * @param int|null $page
     * @param int|null $limit
     * @return array
     */
    public function setDefaultValues(?string &$order, ?int &$page, ?int &$limit): array
    {
        $order = $order ?? 'DESC';
        $page = $page ?? 1;
        $limit = $limit ?? 16;

        return [$order, $page, $limit];
    }
}