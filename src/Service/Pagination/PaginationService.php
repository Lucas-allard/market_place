<?php
namespace App\Service\Pagination;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PaginationService
{
    private QueryBuilder $queryBuilder;
    private int $currentPage;
    private int $limit;

    public function setQueryBuilder(QueryBuilder $queryBuilder): void
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function setCurrentPage(int $currentPage): void
    {
        $this->currentPage = $currentPage;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function getPaginatedResult(): array
    {
        $paginator = new Paginator($this->queryBuilder);
        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / $this->limit);

        $this->currentPage = max(1, min($this->currentPage, $totalPages));

        $this->queryBuilder
            ->setFirstResult(($this->currentPage - 1) * $this->limit)
            ->setMaxResults($this->limit);

        $results = $this->queryBuilder->getQuery()->getResult();

        return [
            'data' => $results,
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
