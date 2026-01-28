<!-- Sidebar -->
<aside id="sidebar"
    class="fixed left-0 top-16 h-[calc(100vh-4rem)] w-64 bg-white shadow-xl z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out overflow-y-auto">

    <div class="p-6">

        <!-- User Info Card (Mobile Only) -->
        <div class="lg:hidden mb-6 p-4 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg text-white">
            <div class="flex items-center space-x-3">
                <div
                    class="bg-white text-blue-600 w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg">
                    <?php
                    $fullName = getCurrentUser()['full_name'] ?? 'U';
                    $words = explode(' ', $fullName);
                    echo mb_substr($words[0], 0, 1, 'UTF-8');
                    ?>
                </div>
                <div>
                    <div class="font-semibold">
                        <?= sanitize(getCurrentUser()['full_name'] ?? 'ผู้ใช้') ?>
                    </div>
                    <div class="text-sm opacity-90">
                        <?php
                        $role = getCurrentUser()['role'] ?? 'user';
                        $roleText = match ($role) {
                            'admin' => 'ผู้ดูแลระบบ',
                            'manager' => 'ผู้จัดการ',
                            default => 'ผู้ใช้งาน'
                        };
                        echo $roleText;
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="space-y-2">

            <!-- หน้าแรก / Dashboard -->
            <a href="<?= url('dashboard') ?>"
                class="nav-link flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                <span class="font-medium">หน้าแรก</span>
            </a>

            <hr class="my-4 border-gray-200">

            <!-- Section: ขั้นตอนการวิเคราะห์ -->
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 px-3">
                ขั้นตอนการวิเคราะห์
            </div>

            <!-- Step 2: กรอกข้อมูลเพื่อวิเคราะห์ -->
            <a href="<?= url('financial/input') ?>"
                class="nav-link flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 <?= ($currentPage ?? '') === 'financial-input' ? 'active' : '' ?>">
                <div
                    class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-bold text-sm">
                    1
                </div>
                <div class="flex-1">
                    <div class="font-medium">กรอกข้อมูล</div>
                    <div class="text-xs text-gray-500">ข้อมูลทางการเงิน C-A-M-E-L</div>
                </div>
            </a>

            <!-- Step 3: อัตราส่วนทางการเงิน -->
            <a href="<?= url('ratio/view') ?>"
                class="nav-link flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 <?= ($currentPage ?? '') === 'ratio' ? 'active' : '' ?>">
                <div
                    class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-600 font-bold text-sm">
                    2
                </div>
                <div class="flex-1">
                    <div class="font-medium">อัตราส่วนทางการเงิน</div>
                    <div class="text-xs text-gray-500">ผลคำนวณและไฟจราจร</div>
                </div>
            </a>

            <!-- Step 4: ผลการวิเคราะห์ CAMELS -->
            <a href="<?= url('camels/result') ?>"
                class="nav-link flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 <?= ($currentPage ?? '') === 'camels-result' ? 'active' : '' ?>">
                <div
                    class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 text-purple-600 font-bold text-sm">
                    3
                </div>
                <div class="flex-1">
                    <div class="font-medium">ผลการวิเคราะห์</div>
                    <div class="text-xs text-gray-500">CAMELS Assessment</div>
                </div>
            </a>

            <hr class="my-4 border-gray-200">

            <!-- Section: รายงานและข้อมูล -->
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 px-3">
                รายงานและข้อมูล
            </div>

            <!-- รายงาน -->
            <a href="<?= url('report/pdf') ?>"
                class="nav-link flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 <?= ($currentPage ?? '') === 'report' ? 'active' : '' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span class="font-medium">รายงาน</span>
            </a>

            <!-- ข้อมูลสหกรณ์ (Admin Only) -->
            <?php if ((getCurrentUser()['role'] ?? '') === 'admin'): ?>
                <a href="#" class="nav-link flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    <span class="font-medium">ข้อมูลสหกรณ์</span>
                </a>

                <!-- จัดการผู้ใช้งาน (Admin Only) -->
                <a href="#" class="nav-link flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <span class="font-medium">จัดการผู้ใช้</span>
                </a>

                <hr class="my-4 border-gray-200">

                <!-- ตั้งค่าระบบ -->
                <a href="#" class="nav-link flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="font-medium">ตั้งค่าระบบ</span>
                </a>
            <?php endif; ?>

        </nav>

        <!-- Version Info -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="text-xs text-gray-500 text-center">
                <div class="font-semibold text-gray-700">CAMELS Analysis</div>
                <div class="mt-1">Version
                    <?= VERSION ?>
                </div>
                <div class="mt-2">© 2026 All rights reserved</div>
            </div>
        </div>

    </div>

</aside>

<!-- Overlay สำหรับ Mobile -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden"
    onclick="toggleMobileMenu()"></div>

<script>
    // Toggle Mobile Menu
    function toggleMobileMenu() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    // Close mobile menu when clicking on a link
    document.querySelectorAll('#sidebar a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 1024) {
                toggleMobileMenu();
            }
        });
    });

    // Close mobile menu on window resize
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            sidebar.classList.remove('-translate-x-full');
            overlay.classList.add('hidden');
        }
    });
</script>