<?php

namespace App\Service\Chart;

use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ChartService
{

    /**
     * @var ChartBuilderInterface
     */
    private ChartBuilderInterface $chartBuilder;

    /**
     * @param ChartBuilderInterface $chartBuilder
     */
    public function __construct(ChartBuilderInterface $chartBuilder)
    {
        $this->chartBuilder = $chartBuilder;
    }

    /**
     * @param array $data
     * @param string $type
     * @param bool $uniqueColors
     * @return Chart
     */
    public function buildChart(array $data, string $type, bool $uniqueColors = false): Chart
    {
        $chart = $this->chartBuilder->createChart($type);

        $backgroundColor = [];
        $labels = array_keys($data);
        $values = array_values($data);

        foreach ($labels as $label) {
            $backgroundColor[] = $uniqueColors ? 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ',0.5)' : 'rgba(0, 123, 255, 0.5)';
        }

        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total',
                    'backgroundColor' => $backgroundColor,
                    'data' => $values,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'yAxes' => [
                    [
                        'ticks' => [
                            'beginAtZero' => true,
                        ],
                    ],
                ],
            ],
        ]);

        return $chart;
    }
}