<!-- Financial Input Form -->
<div class="max-w-7xl mx-auto">

    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">กรอกข้อมูลเพื่อวิเคราะห์</h1>
        <p class="text-gray-600">ข้อมูลทางการเงิน 5 มิติ (C-A-M-E-L) สำหรับการวิเคราะห์ CAMELS</p>
    </div>

    <?php
    // แสดง Validation Errors
    if (isset($_SESSION['financial_errors'])):
        $errors = $_SESSION['financial_errors'];
        unset($_SESSION['financial_errors']);
        echo ValidationHelper::formatErrors($errors);
    endif;
    ?>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">

        <form method="POST" action="<?= url('financial/save') ?>" id="financial-form" class="p-6">

            <?= csrfField() ?>

            <!-- Section 1: ข้อมูลพื้นฐาน -->
            <div class="mb-8 pb-8 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800 mb-4">ข้อมูลพื้นฐาน</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                    <!-- เลือกสหกรณ์ -->
                    <div class="lg:col-span-2">
                        <label for="coop_id" class="block text-sm font-medium text-gray-700 mb-2">
                            สหกรณ์ / กลุ่มเกษตรกร <span class="text-red-500">*</span>
                        </label>
                        <select id="coop_id" name="coop_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                            <option value="">-- เลือกสหกรณ์ --</option>
                            <?php foreach ($cooperatives as $coop): ?>
                                <option value="<?= $coop['coop_id'] ?>">
                                    <?= sanitize($coop['coop_name']) ?> (
                                    <?= sanitize($coop['coop_code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- วันที่รายงาน -->
                    <div>
                        <label for="report_date" class="block text-sm font-medium text-gray-700 mb-2">
                            วันที่รายงาน <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="report_date" name="report_date" value="<?= date('Y-m-d') ?>"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                    </div>

                    <!-- ปีงบประมาณ -->
                    <div>
                        <label for="fiscal_year" class="block text-sm font-medium text-gray-700 mb-2">
                            ปีงบประมาณ <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="fiscal_year" name="fiscal_year" value="<?= date('Y') ?>" min="2020"
                            max="<?= date('Y') + 1 ?>"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                    </div>

                    <!-- งวดบัญชี -->
                    <div class="hidden">
                        <label for="report_period" class="block text-sm font-medium text-gray-700 mb-2">
                            งวดบัญชี
                        </label>
                        <select id="report_period" name="report_period"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?= $i ?>">เดือนที่
                                    <?= $i ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-4 overflow-x-auto" aria-label="Tabs">
                        <button type="button" onclick="switchTab('balance-sheet')"
                            class="tab-button active whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm"
                            data-tab="balance-sheet">
                            งบดุล (Balance Sheet)
                        </button>
                        <button type="button" onclick="switchTab('income-statement')"
                            class="tab-button whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm"
                            data-tab="income-statement">
                            งบกำไรขาดทุน (Income Statement)
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Tab Content: งบดุล -->
            <div id="tab-balance-sheet" class="tab-content">
                <?php require_once APP_PATH . '/Views/financial/partials/balance-sheet.php'; ?>
            </div>

            <!-- Tab Content: งบกำไรขาดทุน -->
            <div id="tab-income-statement" class="tab-content hidden">
                <?php require_once APP_PATH . '/Views/financial/partials/income-statement.php'; ?>
            </div>

            <!-- Submit Buttons -->
            <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-between">
                <a href="<?= url('dashboard') ?>"
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                    ยกเลิก
                </a>

                <button type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition duration-150">
                    <span class="inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        บันทึกและคำนวณอัตราส่วน
                    </span>
                </button>
            </div>

        </form>

    </div>

    <!-- Help Section -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            คำแนะนำ
        </h3>
        <ul class="space-y-2 text-sm text-gray-700">
            <li class="flex items-start">
                <svg class="w-5 h-5 mr-2 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                กรอกข้อมูลให้ครบถ้วนในทั้งสองแท็บ (งบดุลและงบกำไรขาดทุน)
            </li>
            <li class="flex items-start">
                <svg class="w-5 h-5 mr-2 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                ระบบจะตรวจสอบสมการบัญชี: สินทรัพย์รวม = หนี้สิน + ทุน
            </li>
            <li class="flex items-start">
                <svg class="w-5 h-5 mr-2 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                สามารถกรอกตัวเลขโดยใช้คอมมา (,) หรือไม่ใช้ก็ได้ (เช่น 1,000,000 หรือ 1000000)
            </li>
            <li class="flex items-start">
                <svg class="w-5 h-5 mr-2 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                หลังจากบันทึกแล้ว ระบบจะคำนวณอัตราส่วนทางการเงินอัตโนมัติ
            </li>
        </ul>
    </div>

</div>

<script>
    // Tab Switching
    function switchTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });

        // Remove active class from all buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active', 'border-blue-600', 'text-blue-600');
            button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        });

        // Show selected tab
        document.getElementById('tab-' + tabName).classList.remove('hidden');

        // Add active class to clicked button
        const activeButton = document.querySelector('[data-tab="' + tabName + '"]');
        activeButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        activeButton.classList.add('active', 'border-blue-600', 'text-blue-600');
    }

    // Initialize first tab as active
    document.addEventListener('DOMContentLoaded', function () {
        switchTab('balance-sheet');
    });

    // Number Formatting (add commas)
    function formatNumber(input) {
        let value = input.value.replace(/,/g, '');
        if (!isNaN(value) && value !== '') {
            input.value = parseFloat(value).toLocaleString('en-US', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });
        }
    }

    // Auto-calculate totals (optional)
    function calculateAssetTotal() {
        // ตัวอย่างการคำนวณ Total Assets อัตโนมัติ
        // คุณสามารถเพิ่มฟังก์ชันนี้ได้ตามต้องการ
    }
</script>

<style>
    .tab-button.active {
        border-color: #3B82F6 !important;
        color: #3B82F6 !important;
    }
</style>