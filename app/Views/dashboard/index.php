<!-- Dashboard Content -->
<div class="max-w-7xl mx-auto">

    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            ยินดีต้อนรับ,
            <?= sanitize($currentUser['full_name']) ?> 👋
        </h1>
        <p class="text-gray-600">
            ระบบวิเคราะห์ความพร้อมในการให้บริการของสหกรณ์ (CAMELS Analysis)
        </p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <!-- Card 1: จำนวนสหกรณ์ -->
        <div class="bg-white rounded-xl shadow-md p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">จำนวนสหกรณ์</p>
                    <p class="text-3xl font-bold text-gray-800">
                        <?= number_format($stats['total_coops']) ?>
                    </p>
                    <p class="text-xs text-green-600 mt-2">
                        <span class="inline-flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            ทั้งหมดในระบบ
                        </span>
                    </p>
                </div>
                <div
                    class="bg-gradient-to-br from-blue-500 to-blue-600 text-white w-14 h-14 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 2: จำนวนการวิเคราะห์ -->
        <div class="bg-white rounded-xl shadow-md p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">การวิเคราะห์ทั้งหมด</p>
                    <p class="text-3xl font-bold text-gray-800">
                        <?= number_format($stats['total_analysis']) ?>
                    </p>
                    <p class="text-xs text-blue-600 mt-2">
                        <span class="inline-flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z">
                                </path>
                            </svg>
                            รายงานทั้งหมด
                        </span>
                    </p>
                </div>
                <div
                    class="bg-gradient-to-br from-green-500 to-green-600 text-white w-14 h-14 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 3: รอดำเนินการ -->
        <div class="bg-white rounded-xl shadow-md p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">รอดำเนินการ</p>
                    <p class="text-3xl font-bold text-gray-800">
                        <?= number_format($stats['pending_analysis']) ?>
                    </p>
                    <p class="text-xs text-orange-600 mt-2">
                        <span class="inline-flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            ที่ต้องดำเนินการ
                        </span>
                    </p>
                </div>
                <div
                    class="bg-gradient-to-br from-yellow-500 to-orange-500 text-white w-14 h-14 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 4: จำนวนผู้ใช้ (Admin Only) -->
        <?php if ($currentUser['role'] === 'admin'): ?>
            <div class="bg-white rounded-xl shadow-md p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">ผู้ใช้งานทั้งหมด</p>
                        <p class="text-3xl font-bold text-gray-800">
                            <?= number_format($stats['total_users']) ?>
                        </p>
                        <p class="text-xs text-purple-600 mt-2">
                            <span class="inline-flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z">
                                    </path>
                                </svg>
                                ในระบบ
                            </span>
                        </p>
                    </div>
                    <div
                        class="bg-gradient-to-br from-purple-500 to-purple-600 text-white w-14 h-14 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Card 4: Quick Access (For non-admin users) -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl shadow-md p-6 card-hover cursor-pointer"
                onclick="window.location.href='<?= url('financial/input') ?>'">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-white opacity-90 mb-1">เริ่มต้นใช้งาน</p>
                        <p class="text-2xl font-bold">วิเคราะห์ใหม่</p>
                        <p class="text-xs opacity-90 mt-2">
                            คลิกเพื่อเริ่มวิเคราะห์ →
                        </p>
                    </div>
                    <div class="bg-white bg-opacity-20 w-14 h-14 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <!-- Quick Actions -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">การดำเนินการด่วน</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Action 1: เริ่มวิเคราะห์ใหม่ -->
            <a href="<?= url('financial/input') ?>"
                class="bg-white rounded-xl shadow-md p-6 card-hover border-2 border-transparent hover:border-blue-500">
                <div class="flex items-start">
                    <div class="bg-blue-100 text-blue-600 w-12 h-12 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">กรอกข้อมูลเพื่อวิเคราะห์</h3>
                        <p class="text-sm text-gray-600">เริ่มต้นกรอกข้อมูลทางการเงิน 5 มิติ (C-A-M-E-L)</p>
                        <div class="mt-3 text-blue-600 text-sm font-medium">
                            เริ่มต้น →
                        </div>
                    </div>
                </div>
            </a>

            <!-- Action 2: ดูอัตราส่วนทางการเงิน -->
            <a href="<?= url('ratio/view') ?>"
                class="bg-white rounded-xl shadow-md p-6 card-hover border-2 border-transparent hover:border-green-500">
                <div class="flex items-start">
                    <div class="bg-green-100 text-green-600 w-12 h-12 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">อัตราส่วนทางการเงิน</h3>
                        <p class="text-sm text-gray-600">ดูผลการคำนวณพร้อมระบบไฟจราจร</p>
                        <div class="mt-3 text-green-600 text-sm font-medium">
                            ดูเพิ่มเติม →
                        </div>
                    </div>
                </div>
            </a>

            <!-- Action 3: ดูผลการวิเคราะห์ -->
            <a href="<?= url('camels/result') ?>"
                class="bg-white rounded-xl shadow-md p-6 card-hover border-2 border-transparent hover:border-purple-500">
                <div class="flex items-start">
                    <div
                        class="bg-purple-100 text-purple-600 w-12 h-12 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">ผลการวิเคราะห์ CAMELS</h3>
                        <p class="text-sm text-gray-600">ดูคะแนนและผลการประเมินพร้อม Charts</p>
                        <div class="mt-3 text-purple-600 text-sm font-medium">
                            ดูผลลัพธ์ →
                        </div>
                    </div>
                </div>
            </a>

        </div>
    </div>

    <!-- Recent Activities (Optional) -->
    <?php if (!empty($recentActivities)): ?>
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">กิจกรรมล่าสุด</h2>

            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    กิจกรรม</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    รายละเอียด</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    เวลา</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($recentActivities as $activity): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?= sanitize($activity['action']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <?= sanitize($activity['description']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= formatDate($activity['created_date'], DATETIME_FORMAT) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        <!-- CAMELS Framework Info -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
            <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                เกี่ยวกับ CAMELS Framework
            </h3>
            <div class="space-y-2 text-sm text-gray-700">
                <p><strong class="text-blue-600">C</strong> - Capital Strength (ความเพียงพอของทุน)</p>
                <p><strong class="text-green-600">A</strong> - Asset Quality (คุณภาพของสินทรัพย์)</p>
                <p><strong class="text-yellow-600">M</strong> - Management Ability (ความสามารถในการบริหารจัดการ)</p>
                <p><strong class="text-orange-600">E</strong> - Earning Sufficiency (การทำกำไร)</p>
                <p><strong class="text-purple-600">L</strong> - Liquidity (สภาพคล่อง)</p>
            </div>
        </div>

        <!-- System Guide -->
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-200">
            <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                    </path>
                </svg>
                วิธีการใช้งานระบบ
            </h3>
            <ol class="space-y-2 text-sm text-gray-700 list-decimal list-inside">
                <li>เลือกสหกรณ์และงวดบัญชีที่ต้องการวิเคราะห์</li>
                <li>กรอกข้อมูลทางการเงิน 5 มิติ (C-A-M-E-L)</li>
                <li>ระบบจะคำนวณอัตราส่วนทางการเงินอัตโนมัติ</li>
                <li>ดูผลการวิเคราะห์พร้อม Gauge และ Radar Charts</li>
                <li>Export รายงาน PDF เพื่อนำเสนอ</li>
            </ol>
        </div>

    </div>

</div>