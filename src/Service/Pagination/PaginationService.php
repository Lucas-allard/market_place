<?php

namespace App\Service\Pagination;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;

class PaginationService
{
    /**
     * @var int
     */
    private int $currentPage;
    /**
     * @var int
     */
    private int $limit;

    /**
     * @param int $currentPage
     * @param int $limit
     */
    public function __construct(int $currentPage = 1, int $limit = 16)
    {
        $this->currentPage = $currentPage;
        $this->limit = $limit;
    }

    /**
     * @param int $currentPage
     * @return void
     */
    public function setCurrentPage(int $currentPage): void
    {
        $this->currentPage = $currentPage;
    }

    /**
     * @param int $limit
     * @return void
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }


    /**
     * @param QueryBuilder $query
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getPaginatedResult(QueryBuilder $query, int $page, int $limit): array
    {
        $this->setCurrentPage($page);
        $this->setLimit($limit);
        $paginatedQuery = $this->paginateResults($query);
        $paginationData = $this->calculatePaginationData($paginatedQuery);

        $data = [];


        try {
            $data = $paginatedQuery->getIterator()->getArrayCopy();
        } catch (Exception $e) {
        }


        return [
            'data' => $data,
            'pagination' => $paginationData,
        ];
    }

    /**
     * @param QueryBuilder $query
     * @return Paginator
     */
    private function paginateResults(QueryBuilder $query): Paginator
    {
        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult($this->limit * ($this->currentPage - 1))
            ->setMaxResults($this->limit);

        return $paginator;
    }

    /**
     * @param Paginator $paginator
     * @return array
     */
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
