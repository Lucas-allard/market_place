<?php

namespace App\Service\Pagination;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;

class PaginationService
{
    private int $currentPage;
    private int $limit;

    public function __construct(int $currentPage = 1, int $limit = 16)
    {
        $this->currentPage = $currentPage;
        $this->limit = $limit;
    }

    public function setCurrentPage(int $currentPage): void
    {
        $this->currentPage = $currentPage;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }


    /**
     * @throws Exception
     */
    public function getPaginatedResult(QueryBuilder $query, int $page, int $limit): array
    {
        $this->setCurrentPage($page);
        $this->setLimit($limit);
        $paginatedQuery = $this->paginateResults($query);
        $paginationData = $this->calculatePaginationData($paginatedQuery);

        return [
            'data' => $paginatedQuery->getIterator()->getArrayCopy(),
            'pagination' => $paginationData,
        ];
    }

    private function paginateResults(QueryBuilder $query): Paginator
    {
        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult($this->limit * ($this->currentPage - 1))
            ->setMaxResults($this->limit);

        return $paginator;
    }

    private function calculatePaginationData(Paginator $paginator): array
    {
        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / $this->limit);

        return [
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
            'currentPage' => $this->currentPage,
            'hasPreviousPage' => ($this->currentPage > 1),
            'hasNextPage' => ($this->currentPage < $totalPages),
            'previousPage' => ($this->currentPage > 1) ? $this->currentPage - 1 : null,
            'nextPage' => ($this->currentPage < $totalPages) ? $this->currentPage + 1 : null,
        ];
    }
}
