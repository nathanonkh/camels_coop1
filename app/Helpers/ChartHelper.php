<?php
/**
 * Chart Helper
 * สร้าง Configuration สำหรับ Charts ต่างๆ
 * 
 * PHP 8.0+
 */

class ChartHelper
{

    /**
     * สร้าง Configuration สำหรับ Gauge Chart
     * 
     * @param float $score
     * @param string $label
     * @return array
     */
    public static function getGaugeConfig(float $score, string $label = 'คะแนน'): array
    {
        $color = self::getColorByScore($score);

        return [
            'type' => 'doughnut',
            'data' => [
                'datasets' => [
                    [
                        'data' => [$score, 100 - $score],
                        'backgroundColor' => [$color, 'rgba(229, 231, 235, 0.3)'],
                        'borderWidth' => 0
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'circumference' => 180,
                'rotation' => 270,
                'cutout' => '75%',
                'plugins' => [
                    'legend' => ['display' => false],
                    'tooltip' => ['enabled' => false]
                ]
            ]
        ];
    }

    /**
     * สร้าง Configuration สำหรับ Radar Chart
     * 
     * @param array $scores
     * @return array
     */
    public static function getRadarConfig(array $scores): array
    {
        return [
            'type' => 'radar',
            'data' => [
                'labels' => ['ทุน (C)', 'สินทรัพย์ (A)', 'การบริหาร (M)', 'กำไร (E)', 'สภาพคล่อง (L)'],
                'datasets' => [
                    [
                        'label' => 'คะแนน CAMELS',
                        'data' => [
                            $scores['capital'] ?? 0,
                            $scores['asset'] ?? 0,
                            $scores['management'] ?? 0,
                            $scores['earning'] ?? 0,
                            $scores['liquidity'] ?? 0
                        ],
                        'backgroundColor' => 'rgba(99, 102, 241, 0.2)',
                        'borderColor' => 'rgb(99, 102, 241)',
                        'borderWidth' => 2,
                        'pointBackgroundColor' => 'rgb(99, 102, 241)',
                        'pointBorderColor' => '#fff',
                        'pointHoverBackgroundColor' => '#fff',
                        'pointHoverBorderColor' => 'rgb(99, 102, 241)',
                        'pointRadius' => 5,
                        'pointHoverRadius' => 7
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => true,
                'scales' => [
                    'r' => [
                        'beginAtZero' => true,
                        'max' => 100,
                        'ticks' => [
                            'stepSize' => 20,
                            'font' => ['family' => 'Sarabun']
                        ],
                        'pointLabels' => [
                            'font' => [
                                'size' => 14,
                                'family' => 'Sarabun',
                                'weight' => 'bold'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * สร้าง Configuration สำหรับ Line Chart (Trend)
     * 
     * @param array $history
     * @param string $dimension
     * @return array
     */
    public static function getTrendLineConfig(array $history, string $dimension = 'overall'): array
    {
        $labels = [];
        $data = [];

        foreach (array_reverse($history) as $record) {
            $labels[] = date('M Y', strtotime($record['report_date']));

            if ($dimension === 'overall') {
                $data[] = $record['overall_score'];
            } else {
                $data[] = $record[$dimension . '_score'] ?? 0;
            }
        }

        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => self::getDimensionLabel($dimension),
                        'data' => $data,
                        'borderColor' => 'rgb(99, 102, 241)',
                        'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                        'pointRadius' => 4,
                        'pointHoverRadius' => 6
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => ['display' => false]
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'max' => 100,
                        'ticks' => ['font' => ['family' => 'Sarabun']]
                    ],
                    'x' => [
                        'ticks' => ['font' => ['family' => 'Sarabun']]
                    ]
                ]
            ]
        ];
    }

    /**
     * สร้าง Configuration สำหรับ Multi-Line Chart
     * 
     * @param array $history
     * @return array
     */
    public static function getMultiLineConfig(array $history): array
    {
        $labels = [];
        $capitalData = [];
        $assetData = [];
        $managementData = [];
        $earningData = [];
        $liquidityData = [];

        foreach (array_reverse($history) as $record) {
            $labels[] = date('M Y', strtotime($record['report_date']));
            $capitalData[] = $record['capital_score'] ?? 0;
            $assetData[] = $record['asset_score'] ?? 0;
            $managementData[] = $record['management_score'] ?? 0;
            $earningData[] = $record['earning_score'] ?? 0;
            $liquidityData[] = $record['liquidity_score'] ?? 0;
        }

        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'ทุน (C)',
                        'data' => $capitalData,
                        'borderColor' => 'rgb(59, 130, 246)',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'tension' => 0.4
                    ],
                    [
                        'label' => 'สินทรัพย์ (A)',
                        'data' => $assetData,
                        'borderColor' => 'rgb(16, 185, 129)',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                        'tension' => 0.4
                    ],
                    [
                        'label' => 'การบริหาร (M)',
                        'data' => $managementData,
                        'borderColor' => 'rgb(245, 158, 11)',
                        'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                        'tension' => 0.4
                    ],
                    [
                        'label' => 'กำไร (E)',
                        'data' => $earningData,
                        'borderColor' => 'rgb(249, 115, 22)',
                        'backgroundColor' => 'rgba(249, 115, 22, 0.1)',
                        'tension' => 0.4
                    ],
                    [
                        'label' => 'สภาพคล่อง (L)',
                        'data' => $liquidityData,
                        'borderColor' => 'rgb(139, 92, 246)',
                        'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                        'tension' => 0.4
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => [
                        'display' => true,
                        'position' => 'top',
                        'labels' => ['font' => ['family' => 'Sarabun']]
                    ]
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'max' => 100,
                        'ticks' => ['font' => ['family' => 'Sarabun']]
                    ]
                ]
            ]
        ];
    }

    /**
     * สร้าง Configuration สำหรับ Stacked Bar Chart
     * 
     * @param array $ratios
     * @return array
     */
    public static function getStackedBarConfig(array $ratios): array
    {
        $dimensions = ['capital', 'asset', 'management', 'earning', 'liquidity'];
        $levels = ['excellent' => [], 'good' => [], 'fair' => [], 'poor' => []];

        foreach ($dimensions as $dimension) {
            $counts = ['excellent' => 0, 'good' => 0, 'fair' => 0, 'poor' => 0];

            if (isset($ratios[$dimension])) {
                foreach ($ratios[$dimension] as $ratio) {
                    if (isset($ratio['rating']['level'])) {
                        $level = $ratio['rating']['level'];
                        if (isset($counts[$level])) {
                            $counts[$level]++;
                        }
                    }
                }
            }

            foreach ($levels as $level => &$data) {
                $data[] = $counts[$level];
            }
        }

        return [
            'type' => 'bar',
            'data' => [
                'labels' => ['ทุน', 'สินทรัพย์', 'การบริหาร', 'กำไร', 'สภาพคล่อง'],
                'datasets' => [
                    [
                        'label' => 'ดีมาก',
                        'data' => $levels['excellent'],
                        'backgroundColor' => 'rgba(16, 185, 129, 0.8)'
                    ],
                    [
                        'label' => 'ดี',
                        'data' => $levels['good'],
                        'backgroundColor' => 'rgba(34, 197, 94, 0.6)'
                    ],
                    [
                        'label' => 'พอใช้',
                        'data' => $levels['fair'],
                        'backgroundColor' => 'rgba(245, 158, 11, 0.8)'
                    ],
                    [
                        'label' => 'ควรปรับปรุง',
                        'data' => $levels['poor'],
                        'backgroundColor' => 'rgba(239, 68, 68, 0.8)'
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'scales' => [
                    'x' => ['stacked' => true],
                    'y' => ['stacked' => true]
                ]
            ]
        ];
    }

    /**
     * กำหนดสีตามคะแนน
     * 
     * @param float $score
     * @return string
     */
    private static function getColorByScore(float $score): string
    {
        if ($score >= 70) {
            return 'rgb(16, 185, 129)'; // Green
        } elseif ($score >= 40) {
            return 'rgb(245, 158, 11)'; // Yellow/Orange
        } else {
            return 'rgb(239, 68, 68)'; // Red
        }
    }

    /**
     * ดึงชื่อมิติภาษาไทย
     * 
     * @param string $dimension
     * @return string
     */
    private static function getDimensionLabel(string $dimension): string
    {
        $labels = [
            'overall' => 'คะแนนรวม',
            'capital' => 'ทุน (C)',
            'asset' => 'สินทรัพย์ (A)',
            'management' => 'การบริหาร (M)',
            'earning' => 'กำไร (E)',
            'liquidity' => 'สภาพคล่อง (L)'
        ];

        return $labels[$dimension] ?? $dimension;
    }

    /**
     * Export Chart เป็น JSON
     * 
     * @param array $config
     * @return string
     */
    public static function exportJson(array $config): string
    {
        return json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}