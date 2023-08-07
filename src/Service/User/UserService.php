<?php

namespace App\Service\User;

use App\Entity\Interface\UserInterface;
use App\Repository\UserRepository;
use App\Service\Chart\ChartService;
use App\Service\Pagination\PaginationService;
use App\Service\Utils\SortHelper;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Model\Chart;

class UserService
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var ChartService
     */
    private ChartService $chartService;

    /**
     * @var PaginationService
     */
    private PaginationService $paginationService;

    /**
     * @var SortHelper
     */
    private SortHelper $sortHelper;
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @param UserRepository $userRepository
     * @param ChartService $chartService
     * @param PaginationService $paginationService
     * @param SortHelper $sortHelper
     * @param TranslatorInterface $translator
     */
    public function __construct(
        UserRepository $userRepository,
        ChartService $chartService,
        PaginationService $paginationService,
        SortHelper $sortHelper,
        TranslatorInterface $translator,
    )
    {
        $this->userRepository = $userRepository;
        $this->chartService = $chartService;
        $this->paginationService = $paginationService;
        $this->sortHelper = $sortHelper;
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    public function getUsers(): array
    {

        return $this->userRepository->findAll();
    }


    /**
     * @param array|null $sort
     * @param int|null $page
     * @param int|null $limit
     * @return array
     */
    public function getPaginatedUsers(?array $sort = null, ?int $page = null, ?int $limit = null): array
    {
        [$sort, $order] = $this->sortHelper->getOrderBy($sort);

        [$order, $page, $limit] = $this->sortHelper->setDefaultValues($order, $page, $limit);

        $queryBuilder = $this->userRepository->findAllQueryBuilder($sort, $order);

        return  $this->paginationService->getPaginatedResult($queryBuilder, $page, $limit);
    }

    /**
     * @param array $users
     * @return Chart
     */
    public function getRegistrationsPerMonthChart(array $users): Chart
    {
        $total = [];

        foreach ($users as $user) {
            $month = $user->getCreatedAt()->format('F');
            $monthName = $this->translator->trans($month);
            if (!isset($total[$monthName])) {
                $total[$monthName] = 0;
            }
            $total[$monthName]++;
        }


        return $this->chartService->buildChart($total, Chart::TYPE_DOUGHNUT, true);
    }

    /**
     * @param array $users
     * @param string $role
     * @return Chart
     */
    public function getRegistrationByRolePerMonthChart(array $users, string $role): Chart
    {
        $total = [];

        foreach ($users as $user) {
            if (!in_array($role, $user->getRoles())) {
                continue;
            }

            $month = $user->getCreatedAt()->format('F');
            $monthName = $this->translator->trans($month);
            if (!isset($total[$monthName])) {
                $total[$monthName] = 0;
            }
            $total[$monthName]++;
        }

        return $this->chartService->buildChart($total, Chart::TYPE_DOUGHNUT, true);
    }

    /**
     * @param UserInterface $user
     * @return void
     */
    public function deleteUser(UserInterface $user): void
    {
        $this->userRepository->remove($user);

    }
}