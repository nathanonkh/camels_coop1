<!-- Ratio Display View -->
<div class="max-w-7xl mx-auto">

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">อัตราส่วนทางการเงิน</h1>
                <p class="text-gray-600">
                    สหกรณ์: <span class="font-semibold">
                        <?= sanitize($coopInfo['coop_name'] ?? 'ไม่ระบุ') ?>
                    </span>
                    | วันที่รายงาน: <span class="font-semibold">
                        <?= formatDate($reportDate) ?>
                    </span>
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="<?= url('financial/input?coop_id=' . $coopId . '&report_date=' . $reportDate) ?>"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                    </svg>
                    แก้ไขข้อมูล
                </a>
                <a href="<?= url('camels/result?coop_id=' . $coopId . '&report_date=' . $reportDate) ?>"
                    class="px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition">
                    ถัดไป: ผลการวิเคราะห์ CAMELS
                    <svg class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-center justify-center space-x-8">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-500 rounded-full mr-2"></div>
                <span class="text-sm font-medium text-gray-700">🟢 ดี / ผ่านเกณฑ์</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-yellow-500 rounded-full mr-2"></div>
                <span class="text-sm font-medium text-gray-700">🟡 พอใช้ / ควรระวัง</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-red-500 rounded-full mr-2"></div>
                <span class="text-sm font-medium text-gray-700">🔴 ควรปรับปรุง / ไม่ผ่านเกณฑ์</span>
            </div>
        </div>
    </div>

    <!-- Dimension Tabs -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-4 overflow-x-auto" aria-label="Tabs">
                <button type="button" onclick="showDimension('capital')"
                    class="dimension-tab active whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm"
                    data-dimension="capital">
                    C - ความเพียงพอของทุน
                </button>
                <button type="button" onclick="showDimension('asset')"
                    class="dimension-tab whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm"
                    data-dimension="asset">
                    A - คุณภาพสินทรัพย์
                </button>
                <button type="button" onclick="showDimension('management')"
                    class="dimension-tab whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm"
                    data-dimension="management">
                    M - การบริหารจัดการ
                </button>
                <button type="button" onclick="showDimension('earning')"
                    class="dimension-tab whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm"
                    data-dimension="earning">
                    E - การทำกำไร
                </button>
                <button type="button" onclick="showDimension('liquidity')"
                    class="dimension-tab whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm"
                    data-dimension="liquidity">
                    L - สภาพคล่อง
                </button>
            </nav>
        </div>
    </div>

    <!-- C - Capital Strength -->
    <div id="dimension-capital" class="dimension-content">
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-blue-600 mb-6 flex items-center">
                <span
                    class="bg-blue-100 text-blue-600 w-10 h-10 rounded-full flex items-center justify-center mr-3 font-bold">C</span>
                ความเพียงพอของทุน (Capital Strength)
            </h2>

            <div class="space-y-4">
                <?php if (isset($ratios['capital']) && !empty($ratios['capital'])): ?>
                    <?php foreach ($ratios['capital'] as $code => $ratio): ?>
                        <?php
                        $rating = $ratio['rating'];
                        $bgColor = match ($rating['color']) {
                            'green' => 'bg-green-50 border-green-200',
                            'yellow' => 'bg-yellow-50 border-yellow-200',
                            'red' => 'bg-red-50 border-red-200',
                            default => 'bg-gray-50 border-gray-200'
                        };
                        ?>
                        <div class="<?= $bgColor ?> border-l-4 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 mb-1">
                                        <?= sanitize($ratio['name']) ?>
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        <?= sanitize($ratio['formula'] ?? '') ?>
                                    </p>
                                </div>
                                <div class="text-right ml-4">
                                    <div class="text-2xl font-bold text-gray-800">
                                        <?= formatNumber($ratio['value'], 2) ?> <span class="text-lg">
                                            <?= sanitize($ratio['unit']) ?>
                                        </span>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $rating['badge_class'] ?> mt-2">
                                        <?= sanitize($rating['text']) ?>
                                    </span>
                                </div>
                            </div>

                            <?php if (isset($ratio['benchmark'])): ?>
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <p class="text-xs text-gray-600">
                                        เกณฑ์มาตรฐาน:
                                        <?= sanitize($ratio['benchmark']['description'] ?? 'ไม่มีข้อมูล') ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500">ไม่มีข้อมูลอัตราส่วน</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- A - Asset Quality -->
    <div id="dimension-asset" class="dimension-content hidden">
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-green-600 mb-6 flex items-center">
                <span
                    class="bg-green-100 text-green-600 w-10 h-10 rounded-full flex items-center justify-center mr-3 font-bold">A</span>
                คุณภาพของสินทรัพย์ (Asset Quality)
            </h2>

            <div class="space-y-4">
                <?php if (isset($ratios['asset']) && !empty($ratios['asset'])): ?>
                    <?php foreach ($ratios['asset'] as $code => $ratio): ?>
                        <?php
                        $rating = $ratio['rating'];
                        $bgColor = match ($rating['color']) {
                            'green' => 'bg-green-50 border-green-200',
                            'yellow' => 'bg-yellow-50 border-yellow-200',
                            'red' => 'bg-red-50 border-red-200',
                            default => 'bg-gray-50 border-gray-200'
                        };
                        ?>
                        <div class="<?= $bgColor ?> border-l-4 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 mb-1">
                                        <?= sanitize($ratio['name']) ?>
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        <?= sanitize($ratio['formula'] ?? '') ?>
                                    </p>
                                </div>
                                <div class="text-right ml-4">
                                    <div class="text-2xl font-bold text-gray-800">
                                        <?= formatNumber($ratio['value'], 2) ?> <span class="text-lg">
                                            <?= sanitize($ratio['unit']) ?>
                                        </span>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $rating['badge_class'] ?> mt-2">
                                        <?= sanitize($rating['text']) ?>
                                    </span>
                                </div>
                            </div>

                            <?php if (isset($ratio['benchmark'])): ?>
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <p class="text-xs text-gray-600">
                                        เกณฑ์มาตรฐาน:
                                        <?= sanitize($ratio['benchmark']['description'] ?? 'ไม่มีข้อมูล') ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500">ไม่มีข้อมูลอัตราส่วน</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- M - Management Ability -->
    <div id="dimension-management" class="dimension-content hidden">
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-yellow-600 mb-6 flex items-center">
                <span
                    class="bg-yellow-100 text-yellow-600 w-10 h-10 rounded-full flex items-center justify-center mr-3 font-bold">M</span>
                ความสามารถในการบริหารจัดการ (Management Ability)
            </h2>

            <div class="space-y-4">
                <?php if (isset($ratios['management']) && !empty($ratios['management'])): ?>
                    <?php foreach ($ratios['management'] as $code => $ratio): ?>
                        <?php
                        $rating = $ratio['rating'];
                        $bgColor = match ($rating['color']) {
                            'green' => 'bg-green-50 border-green-200',
                            'yellow' => 'bg-yellow-50 border-yellow-200',
                            'red' => 'bg-red-50 border-red-200',
                            default => 'bg-gray-50 border-gray-200'
                        };
                        ?>
                        <div class="<?= $bgColor ?> border-l-4 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 mb-1">
                                        <?= sanitize($ratio['name']) ?>
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        <?= sanitize($ratio['formula'] ?? '') ?>
                                    </p>
                                </div>
                                <div class="text-right ml-4">
                                    <div class="text-2xl font-bold text-gray-800">
                                        <?= formatNumber($ratio['value'], 2) ?> <span class="text-lg">
                                            <?= sanitize($ratio['unit']) ?>
                                        </span>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $rating['badge_class'] ?> mt-2">
                                        <?= sanitize($rating['text']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500">ไม่มีข้อมูลอัตราส่วน</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- E - Earning Sufficiency -->
    <div id="dimension-earning" class="dimension-content hidden">
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-orange-600 mb-6 flex items-center">
                <span
                    class="bg-orange-100 text-orange-600 w-10 h-10 rounded-full flex items-center justify-center mr-3 font-bold">E</span>
                การทำกำไร (Earning Sufficiency)
            </h2>

            <div class="space-y-4">
                <?php if (isset($ratios['earning']) && !empty($ratios['earning'])): ?>
                    <?php foreach ($ratios['earning'] as $code => $ratio): ?>
                        <?php
                        $rating = $ratio['rating'];
                        $bgColor = match ($rating['color']) {
                            'green' => 'bg-green-50 border-green-200',
                            'yellow' => 'bg-yellow-50 border-yellow-200',
                            'red' => 'bg-red-50 border-red-200',
                            default => 'bg-gray-50 border-gray-200'
                        };
                        ?>
                        <div class="<?= $bgColor ?> border-l-4 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 mb-1">
                                        <?= sanitize($ratio['name']) ?>
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        <?= sanitize($ratio['formula'] ?? '') ?>
                                    </p>
                                </div>
                                <div class="text-right ml-4">
                                    <div class="text-2xl font-bold text-gray-800">
                                        <?= formatNumber($ratio['value'], 2) ?> <span class="text-lg">
                                            <?= sanitize($ratio['unit']) ?>
                                        </span>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $rating['badge_class'] ?> mt-2">
                                        <?= sanitize($rating['text']) ?>
                                    </span>
                                </div>
                            </div>

                            <?php if (isset($ratio['benchmark'])): ?>
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <p class="text-xs text-gray-600">
                                        เกณฑ์มาตรฐาน:
                                        <?= sanitize($ratio['benchmark']['description'] ?? 'ไม่มีข้อมูล') ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500">ไม่มีข้อมูลอัตราส่วน</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- L - Liquidity -->
    <div id="dimension-liquidity" class="dimension-content hidden">
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-purple-600 mb-6 flex items-center">
                <span
                    class="bg-purple-100 text-purple-600 w-10 h-10 rounded-full flex items-center justify-center mr-3 font-bold">L</span>
                สภาพคล่อง (Liquidity)
            </h2>

            <div class="space-y-4">
                <?php if (isset($ratios['liquidity']) && !empty($ratios['liquidity'])): ?>
                    <?php foreach ($ratios['liquidity'] as $code => $ratio): ?>
                        <?php
                        $rating = $ratio['rating'];
                        $bgColor = match ($rating['color']) {
                            'green' => 'bg-green-50 border-green-200',
                            'yellow' => 'bg-yellow-50 border-yellow-200',
                            'red' => 'bg-red-50 border-red-200',
                            default => 'bg-gray-50 border-gray-200'
                        };
                        ?>
                        <div class="<?= $bgColor ?> border-l-4 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 mb-1">
                                        <?= sanitize($ratio['name']) ?>
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        <?= sanitize($ratio['formula'] ?? '') ?>
                                    </p>
                                </div>
                                <div class="text-right ml-4">
                                    <div class="text-2xl font-bold text-gray-800">
                                        <?= formatNumber($ratio['value'], 2) ?> <span class="text-lg">
                                            <?= sanitize($ratio['unit']) ?>
                                        </span>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $rating['badge_class'] ?> mt-2">
                                        <?= sanitize($rating['text']) ?>
                                    </span>
                                </div>
                            </div>

                            <?php if (isset($ratio['benchmark'])): ?>
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <p class="text-xs text-gray-600">
                                        เกณฑ์มาตรฐาน:
                                        <?= sanitize($ratio['benchmark']['description'] ?? 'ไม่มีข้อมูล') ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500">ไม่มีข้อมูลอัตราส่วน</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-between mt-8">
        <a href="<?= url('financial/input?coop_id=' . $coopId . '&report_date=' . $reportDate) ?>"
            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12">
                </path>
            </svg>
            กลับไปแก้ไขข้อมูล
        </a>

        <a href="<?= url('camels/result?coop_id=' . $coopId . '&report_date=' . $reportDate) ?>"
            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition">
            ถัดไป: ดูผลการวิเคราะห์ CAMELS
            <svg class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6">
                </path>
            </svg>
        </a>
    </div>

</div>

<script>
    // Dimension Tab Switching
    function showDimension(dimension) {
        // Hide all dimensions
        document.querySelectorAll('.dimension-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Remove active class from all tabs
        document.querySelectorAll('.dimension-tab').forEach(tab => {
            tab.classList.remove('active', 'border-blue-600', 'text-blue-600');
            tab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        });

        // Show selected dimension
        document.getElementById('dimension-' + dimension).classList.remove('hidden');

        // Add active class to clicked tab
        const activeTab = document.querySelector('[data-dimension="' + dimension + '"]');
        activeTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        activeTab.classList.add('active', 'border-blue-600', 'text-blue-600');
    }

    // Initialize first dimension
    document.addEventListener('DOMContentLoaded', function () {
        showDimension('capital');
    });
</script>

<style>
    .dimension-tab.active {
        border-color: #3B82F6 !important;
        color: #3B82F6 !important;
    }
</style>