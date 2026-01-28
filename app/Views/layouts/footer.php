</main>
</div>

<!-- Footer -->
<footer class="bg-white border-t border-gray-200 mt-auto lg:ml-64">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="md:flex md:items-center md:justify-between">

            <!-- Copyright -->
            <div class="text-center md:text-left">
                <p class="text-sm text-gray-600">
                    © 2026 <span class="font-semibold text-gray-800">CAMELS Analysis System</span>.
                    All rights reserved.
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    ระบบวิเคราะห์ความพร้อมในการให้บริการของสหกรณ์และกลุ่มเกษตรกร
                </p>
            </div>

            <!-- Links -->
            <div class="mt-4 md:mt-0">
                <div class="flex justify-center md:justify-end space-x-6">
                    <a href="#" class="text-sm text-gray-600 hover:text-gray-900">
                        คู่มือการใช้งาน
                    </a>
                    <a href="#" class="text-sm text-gray-600 hover:text-gray-900">
                        เกี่ยวกับ
                    </a>
                    <a href="#" class="text-sm text-gray-600 hover:text-gray-900">
                        ติดต่อเรา
                    </a>
                </div>
                <div class="text-xs text-gray-500 text-center md:text-right mt-2">
                    <span>Version
                        <?= VERSION ?>
                    </span>
                    <?php if (DEBUG_MODE): ?>
                        <span class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 rounded">
                            Development Mode
                        </span>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</footer>

<!-- Alpine.js for dropdown functionality -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<?php if (isset($additionalScripts)): ?>
    <?= $additionalScripts ?>
<?php endif; ?>

<!-- Auto-hide flash message after 5 seconds -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.style.transition = 'opacity 0.5s ease';
                flashMessage.style.opacity = '0';
                setTimeout(() => {
                    flashMessage.remove();
                }, 500);
            }, 5000);
        }
    });
</script>

</body>

</html>