<!-- CAMELS Assessment Result View -->
<div class="max-w-7xl mx-auto">

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">ผลการวิเคราะห์ CAMELS</h1>
                <p class="text-gray-600">
                    สหกรณ์: <span class="font-semibold"><?= sanitize($coopInfo['coop_name'] ?? 'ไม่ระบุ') ?></span>
                    | วันที่รายงาน: <span class="font-semibold"><?= formatDate($reportDate) ?></span>
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="<?= url('ratio/view?coop_id=' . $coopId . '&report_date=' . $reportDate) ?>"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                    </svg>
                    ดูอัตราส่วนทางการเงิน
                </a>
                <a href="<?= url('camels/trend?coop_id=' . $coopId) ?>"
                    class="px-4 py-2 border border-blue-300 bg-blue-50 rounded-lg text-blue-700 hover:bg-blue-100 font-medium">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                    ดูแนวโน้ม
                </a>
                <a href="<?= url('report/pdf?coop_id=' . $coopId . '&report_date=' . $reportDate) ?>"
                    class="px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition">
                    Export PDF
                    <svg class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                        </path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Overall Score Card -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl shadow-xl p-8 mb-6 text-white">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            <!-- Left: Gauge Chart -->
            <div>
                <h2 class="text-2xl font-bold mb-4">คะแนนรวม CAMELS</h2>
                <div class="bg-white bg-opacity-20 rounded-lg p-6">
                    <canvas id="gaugeChart" height="200"></canvas>
                </div>
            </div>

            <!-- Right: Rating Details -->
            <div class="flex flex-col justify-center">
                <div class="space-y-4">
                    <div class="flex items-center justify-between bg-white bg-opacity-20 rounded-lg p-4">
                        <span class="text-lg">คะแนนรวม:</span>
                        <span class="text-4xl font-bold"><?= formatNumber($camelsResult['overall_score'], 2) ?></span>
                    </div>

                    <div class="flex items-center justify-between bg-white bg-opacity-20 rounded-lg p-4">
                        <span class="text-lg">ระดับ:</span>
                        <span class="text-2xl font-bold">
                            <?= $camelsResult['rating']['level'] ?> / 5
                        </span>
                    </div>

                    <div class="bg-white bg-opacity-20 rounded-lg p-4">
                        <div class="text-sm mb-2">ผลการประเมิน:</div>
                        <div class="text-2xl font-bold mb-2">
                            <?= sanitize($camelsResult['rating']['name']) ?>
                        </div>
                        <p class="text-sm opacity-90">
                            <?= sanitize($camelsResult['rating']['description']) ?>
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Dimension Scores -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">

        <?php
        $dimensions = [
            'capital' => ['name' => 'C - ทุน', 'color' => 'blue'],
            'asset' => ['name' => 'A - สินทรัพย์', 'color' => 'green'],
            'management' => ['name' => 'M - การบริหาร', 'color' => 'yellow'],
            'earning' => ['name' => 'E - กำไร', 'color' => 'orange'],
            'liquidity' => ['name' => 'L - สภาพคล่อง', 'color' => 'purple']
        ];

        foreach ($dimensions as $key => $dimension):
            $score = $camelsResult['scores'][$key];
            $colorClass = match ($score['color']) {
                'green' => 'from-green-500 to-green-600',
                'yellow' => 'from-yellow-500 to-orange-500',
                'red' => 'from-red-500 to-red-600',
                default => 'from-gray-500 to-gray-600'
            };
            ?>

            <div class="bg-white rounded-xl shadow-md p-6 text-center">
                <div class="text-sm text-gray-600 mb-2"><?= $dimension['name'] ?></div>
                <div class="text-4xl font-bold bg-gradient-to-r <?= $colorClass ?> bg-clip-text text-transparent mb-2">
                    <?= formatNumber($score['score'], 1) ?>
                </div>
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-<?= $dimension['color'] ?>-100 text-<?= $dimension['color'] ?>-800">
                    <?= sanitize($score['text']) ?>
                </span>
            </div>

        <?php endforeach; ?>

    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        <!-- Radar Chart -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">กราฟเปรียบเทียบ 5 มิติ (Radar Chart)</h3>
            <div class="flex justify-center">
                <canvas id="radarChart" width="400" height="400"></canvas>
            </div>
        </div>

        <!-- Bar Chart -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">กราฟคะแนนแต่ละมิติ (Bar Chart)</h3>
            <canvas id="barChart"></canvas>
        </div>

    </div>

    <!-- Recommendations -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        <!-- Strengths -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-xl font-bold text-green-600 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                จุดแข็ง (Strengths)
            </h3>

            <?php if (!empty($camelsResult['recommendations']['strengths'])): ?>
                <ul class="space-y-3">
                    <?php foreach ($camelsResult['recommendations']['strengths'] as $strength): ?>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700"><?= sanitize($strength) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-gray-500">ไม่มีข้อมูล</p>
            <?php endif; ?>
        </div>

        <!-- Improvements -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-xl font-bold text-orange-600 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
                จุดที่ควรปรับปรุง (Improvements)
            </h3>

            <?php if (!empty($camelsResult['recommendations']['improvements'])): ?>
                <ul class="space-y-3">
                    <?php foreach ($camelsResult['recommendations']['improvements'] as $improvement): ?>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-orange-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700"><?= sanitize($improvement) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-gray-500">ไม่มีข้อมูล</p>
            <?php endif; ?>
        </div>

    </div>

    <!-- Priorities -->
    <?php if (!empty($camelsResult['recommendations']['priorities'])): ?>
        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-6 mb-6">
            <h3 class="text-xl font-bold text-red-700 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                ความสำคัญเร่งด่วน (Priorities)
            </h3>

            <ul class="space-y-2">
                <?php foreach ($camelsResult['recommendations']['priorities'] as $priority): ?>
                    <?php if (!empty($priority)): ?>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-800 font-medium"><?= sanitize($priority) ?></span>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Action Buttons -->
    <div class="flex items-center justify-between mt-8">
        <a href="<?= url('ratio/view?coop_id=' . $coopId . '&report_date=' . $reportDate) ?>"
            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12">
                </path>
            </svg>
            กลับไปดูอัตราส่วน
        </a>

        <div class="flex space-x-3">
            <a href="<?= url('dashboard') ?>"
                class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                กลับหน้าแรก
            </a>
            <a href="<?= url('report/pdf?coop_id=' . $coopId . '&report_date=' . $reportDate) ?>"
                class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition">
                Export รายงาน PDF
                <svg class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
            </a>
        </div>
    </div>

</div>

<!-- Chart.js Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // Data from PHP
        const scores = {
            capital: <?= $camelsResult['scores']['capital']['score'] ?>,
            asset: <?= $camelsResult['scores']['asset']['score'] ?>,
            management: <?= $camelsResult['scores']['management']['score'] ?>,
            earning: <?= $camelsResult['scores']['earning']['score'] ?>,
            liquidity: <?= $camelsResult['scores']['liquidity']['score'] ?>
        };

        const overallScore = <?= $camelsResult['overall_score'] ?>;

        // Gauge Chart (using Doughnut)
        const gaugeCtx = document.getElementById('gaugeChart').getContext('2d');
        const gaugeChart = new Chart(gaugeCtx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [overallScore, 100 - overallScore],
                    backgroundColor: [
                        overallScore >= 70 ? '#10B981' : overallScore >= 40 ? '#F59E0B' : '#EF4444',
                        'rgba(255, 255, 255, 0.2)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                circumference: 180,
                rotation: 270,
                cutout: '75%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            },
            plugins: [{
                id: 'gaugeText',
                afterDraw: function (chart) {
                    const width = chart.width;
                    const height = chart.height;
                    const ctx = chart.ctx;

                    ctx.restore();
                    ctx.font = 'bold 48px Sarabun';
                    ctx.fillStyle = '#ffffff';
                    ctx.textBaseline = 'middle';
                    ctx.textAlign = 'center';

                    const text = overallScore.toFixed(1);
                    const textX = width / 2;
                    const textY = height / 2 + 20;

                    ctx.fillText(text, textX, textY);
                    ctx.save();
                }
            }]
        });

        // Radar Chart
        const radarCtx = document.getElementById('radarChart').getContext('2d');
        const radarChart = new Chart(radarCtx, {
            type: 'radar',
            data: {
                labels: [
                    'ทุน (C)',
                    'สินทรัพย์ (A)',
                    'การบริหาร (M)',
                    'กำไร (E)',
                    'สภาพคล่อง (L)'
                ],
                datasets: [{
                    label: 'คะแนน CAMELS',
                    data: [
                        scores.capital,
                        scores.asset,
                        scores.management,
                        scores.earning,
                        scores.liquidity
                    ],
                    backgroundColor: 'rgba(99, 102, 241, 0.2)',
                    borderColor: 'rgb(99, 102, 241)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgb(99, 102, 241)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(99, 102, 241)',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 20,
                            font: {
                                family: 'Sarabun'
                            }
                        },
                        pointLabels: {
                            font: {
                                size: 14,
                                family: 'Sarabun',
                                weight: 'bold'
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.label + ': ' + context.parsed.r.toFixed(2) + ' คะแนน';
                            }
                        },
                        titleFont: {
                            family: 'Sarabun'
                        },
                        bodyFont: {
                            family: 'Sarabun'
                        }
                    }
                }
            }
        });

        // Bar Chart
        const barCtx = document.getElementById('barChart').getContext('2d');
        const barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['ทุน', 'สินทรัพย์', 'การบริหาร', 'กำไร', 'สภาพคล่อง'],
                datasets: [{
                    label: 'คะแนน',
                    data: [
                        scores.capital,
                        scores.asset,
                        scores.management,
                        scores.earning,
                        scores.liquidity
                    ],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(139, 92, 246, 0.8)'
                    ],
                    borderColor: [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(249, 115, 22)',
                        'rgb(139, 92, 246)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            font: {
                                family: 'Sarabun'
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                family: 'Sarabun',
                                size: 12
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return 'คะแนน: ' + context.parsed.y.toFixed(2);
                            }
                        },
                        titleFont: {
                            family: 'Sarabun'
                        },
                        bodyFont: {
                            family: 'Sarabun'
                        }
                    }
                }
            }
        });
    });
</script>