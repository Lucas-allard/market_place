<?php

namespace App\Service\Caracteristic;

use App\Entity\Caracteristic;
use App\Factory\CaracteristicFactory;
use App\Repository\CaracteristicRepository;
use App\Service\Pagination\PaginationService;
use App\Service\Utils\SortHelper;

class CaracteristicService
{
    /**
     * @var CaracteristicRepository
     */
    private CaracteristicRepository $caracteristicRepository;
    /**
     * @var SortHelper
     */
    private SortHelper $sortHelper;
    /**
     * @var PaginationService
     */
    private PaginationService $paginationService;
    /**
     * @var CaracteristicFactory
     */
    private CaracteristicFactory $caracteristicFactory;

    /**
     * @param CaracteristicRepository $caracteristicRepository
     * @param SortHelper $sortHelper
     * @param PaginationService $paginationService
     * @param CaracteristicFactory $caracteristicFactory
     */
    public function __construct(
        CaracteristicRepository $caracteristicRepository,
        SortHelper $sortHelper,
        PaginationService $paginationService,
        CaracteristicFactory $caracteristicFactory
    )
    {
        $this->caracteristicRepository = $caracteristicRepository;
        $this->sortHelper = $sortHelper;
        $this->paginationService = $paginationService;
        $this->caracteristicFactory = $caracteristicFactory;
    }

    /**
     * @param array|null $sort
     * @param int|null $page
     * @param int|null $limit
     * @return array
     */
    public function getPaginatedCaracteristics(?array $sort = null, ?int $page = null, ?int $limit = null): array
    {
        [$sort, $order] = $this->sortHelper->getOrderBy($sort);

        [$order, $page, $limit] = $this->sortHelper->setDefaultValues($order, $page, $limit);

        $query = $this->caracteristicRepository->findAllQueryBuilder($sort, $order);

        return $this->paginationService->getPaginatedResult($query, $page, $limit);
    }

    /**
     * @return CaracteristicFactory
     */
    public function getFactory(): CaracteristicFactory
    {
        return $this->caracteristicFactory;
    }

    /**
     * @param Caracteristic $caracteristic
     * @return void
     */
    public function deleteCaracteristic(Caracteristic $caracteristic): void
    {
        $this->caracteristicRepository->remove($caracteristic, true);
    }
}