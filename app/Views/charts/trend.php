<!-- Trend Analysis View -->
<div class="max-w-7xl mx-auto">

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">แนวโน้มการพัฒนา</h1>
                <p class="text-gray-600">
                    สหกรณ์: <span class="font-semibold">
                        <?= sanitize($coopInfo['coop_name'] ?? 'ไม่ระบุ') ?>
                    </span>
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="<?= url('dashboard') ?>"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    กลับหน้าแรก
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">จำนวนการประเมิน</p>
                    <p class="text-3xl font-bold text-gray-800">
                        <?= number_format($stats['total_assessments'] ?? 0) ?>
                    </p>
                </div>
                <div class="bg-blue-100 text-blue-600 w-12 h-12 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">คะแนนเฉลี่ย</p>
                    <p class="text-3xl font-bold text-gray-800">
                        <?= formatNumber($stats['avg_score'] ?? 0, 1) ?>
                    </p>
                </div>
                <div class="bg-green-100 text-green-600 w-12 h-12 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">คะแนนสูงสุด</p>
                    <p class="text-3xl font-bold text-green-600">
                        <?= formatNumber($stats['max_score'] ?? 0, 1) ?>
                    </p>
                </div>
                <div class="bg-green-100 text-green-600 w-12 h-12 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">คะแนนต่ำสุด</p>
                    <p class="text-3xl font-bold text-red-600">
                        <?= formatNumber($stats['min_score'] ?? 0, 1) ?>
                    </p>
                </div>
                <div class="bg-red-100 text-red-600 w-12 h-12 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                    </svg>
                </div>
            </div>
        </div>

    </div>

    <!-- Trend Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        <!-- Overall Score Trend -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">แนวโน้มคะแนนรวม</h3>
            <canvas id="overallTrendChart"></canvas>
        </div>

        <!-- Dimension Trends -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">แนวโน้มคะแนนแต่ละมิติ</h3>
            <canvas id="dimensionTrendChart"></canvas>
        </div>

    </div>

    <!-- Individual Dimension Trends -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">

        <?php
        $dimensions = [
            'capital' => ['name' => 'ทุน (C)', 'color' => 'blue'],
            'asset' => ['name' => 'สินทรัพย์ (A)', 'color' => 'green'],
            'management' => ['name' => 'การบริหาร (M)', 'color' => 'yellow'],
            'earning' => ['name' => 'กำไร (E)', 'color' => 'orange'],
            'liquidity' => ['name' => 'สภาพคล่อง (L)', 'color' => 'purple']
        ];

        foreach ($dimensions as $key => $dimension):
            ?>

            <div class="bg-white rounded-xl shadow-md p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-3">
                    <?= $dimension['name'] ?>
                </h4>
                <canvas id="<?= $key ?>Chart" height="150"></canvas>
            </div>

        <?php endforeach; ?>

    </div>

    <!-- History Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-800">ประวัติการประเมิน</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">วันที่</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">คะแนนรวม</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">ทุน</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">สินทรัพย์</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">การบริหาร</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">กำไร</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">สภาพคล่อง</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">ระดับ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($history as $record): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= formatDate($record['report_date']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-lg font-bold text-gray-800">
                                    <?= formatNumber($record['total_score'], 1) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                                <?= formatNumber($record['capital_score'], 1) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                                <?= formatNumber($record['asset_score'], 1) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                                <?= formatNumber($record['management_score'], 1) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                                <?= formatNumber($record['earning_score'], 1) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                                <?= formatNumber($record['liquidity_score'], 1) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?= sanitize($record['overall_rating'] ?? 'N/A') ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Chart.js Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // Prepare data from PHP
        const history = <?= json_encode(array_reverse($history)) ?>;

        const labels = history.map(h => new Date(h.report_date).toLocaleDateString('th-TH', { month: 'short', year: 'numeric' }));
        const overallScores = history.map(h => parseFloat(h.total_score));
        const capitalScores = history.map(h => parseFloat(h.capital_score));
        const assetScores = history.map(h => parseFloat(h.asset_score));
        const managementScores = history.map(h => parseFloat(h.management_score));
        const earningScores = history.map(h => parseFloat(h.earning_score));
        const liquidityScores = history.map(h => parseFloat(h.liquidity_score));

        // Overall Trend Chart
        new Chart(document.getElementById('overallTrendChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'คะแนนรวม',
                    data: overallScores,
                    borderColor: 'rgb(99, 102, 241)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return 'คะแนน: ' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, max: 100 }
                }
            }
        });

        // Multi-Dimension Trend Chart
        new Chart(document.getElementById('dimensionTrendChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    { label: 'ทุน', data: capitalScores, borderColor: 'rgb(59, 130, 246)', tension: 0.4 },
                    { label: 'สินทรัพย์', data: assetScores, borderColor: 'rgb(16, 185, 129)', tension: 0.4 },
                    { label: 'การบริหาร', data: managementScores, borderColor: 'rgb(245, 158, 11)', tension: 0.4 },
                    { label: 'กำไร', data: earningScores, borderColor: 'rgb(249, 115, 22)', tension: 0.4 },
                    { label: 'สภาพคล่อง', data: liquidityScores, borderColor: 'rgb(139, 92, 246)', tension: 0.4 }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true, max: 100 }
                }
            }
        });

        // Individual Dimension Charts
        const createMiniChart = (id, data, color) => {
            new Chart(document.getElementById(id), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        borderColor: color,
                        backgroundColor: color.replace('rgb', 'rgba').replace(')', ', 0.1)'),
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, max: 100, ticks: { display: false } },
                        x: { ticks: { display: false } }
                    }
                }
            });
        };

        createMiniChart('capitalChart', capitalScores, 'rgb(59, 130, 246)');
        createMiniChart('assetChart', assetScores, 'rgb(16, 185, 129)');
        createMiniChart('managementChart', managementScores, 'rgb(245, 158, 11)');
        createMiniChart('earningChart', earningScores, 'rgb(249, 115, 22)');
        createMiniChart('liquidityChart', liquidityScores, 'rgb(139, 92, 246)');
    });
</script>