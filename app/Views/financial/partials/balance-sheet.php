<!-- งบดุล (Balance Sheet) -->
<div class="space-y-8">

    <!-- สินทรัพย์ (Assets) -->
    <div>
        <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b-2 border-blue-500">สินทรัพย์ (Assets)</h3>

        <!-- สินทรัพย์หมุนเวียน -->
        <div class="mb-6">
            <h4 class="text-md font-semibold text-gray-700 mb-3">สินทรัพย์หมุนเวียน (Current Assets)</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm text-gray-700 mb-2">เงินสดและเงินฝากธนาคาร</label>
                    <input type="number" name="cash_and_cash_equivalent" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-2">เงินลงทุนระยะสั้น</label>
                    <input type="number" name="short_term_investments" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-2">ลูกหนี้ระยะสั้น</label>
                    <input type="number" name="account_receivable_short" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-2">เงินให้กู้ยืมระยะสั้น</label>
                    <input type="number" name="loan_receivable_short" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-2">สินค้าคงเหลือ</label>
                    <input type="number" name="inventory" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-2">สินทรัพย์หมุนเวียนอื่น</label>
                    <input type="number" name="other_current_assets" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>

            </div>
        </div>

        <!-- สินทรัพย์ไม่หมุนเวียน -->
        <div class="mb-6">
            <h4 class="text-md font-semibold text-gray-700 mb-3">สินทรัพย์ไม่หมุนเวียน (Non-Current Assets)</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm text-gray-700 mb-2">เงินลงทุนระยะยาว</label>
                    <input type="number" name="long_term_investments" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-2">เงินให้กู้ยืมระยะยาว</label>
                    <input type="number" name="loan_receivable_long" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-2">ลูกหนี้ระยะยาว</label>
                    <input type="number" name="account_receivable_long" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-2">ที่ดิน อาคาร และอุปกรณ์</label>
                    <input type="number" name="fixed_assets" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-2">สินทรัพย์ไม่มีตัวตน</label>
                    <input type="number" name="intangible_assets" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-2">สินทรัพย์ไม่หมุนเวียนอื่น</label>
                    <input type="number" name="other_non_current_assets" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>

            </div>
        </div>

        <!-- รวมสินทรัพย์ -->
        <div class="bg-blue-50 p-4 rounded-lg">
            <label class="block text-sm font-bold text-gray-800 mb-2">รวมสินทรัพย์ทั้งหมด <span
                    class="text-red-500">*</span></label>
            <input type="number" name="total_assets" step="0.01" min="0" required
                class="w-full px-4 py-2 border-2 border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-semibold"
                placeholder="0.00">
        </div>
    </div>

    <!-- หนี้สิน (Liabilities) -->
    <div>
        <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b-2 border-green-500">หนี้สิน (Liabilities)</h3>

        <!-- หนี้สินหมุนเวียน -->
        <div class="mb-6">
            <h4 class="text-md font-semibold text-gray-700 mb-3">หนี้สินหมุนเวียน (Current Liabilities)</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm text-gray-700 mb-2">เจ้าหนี้</label>
                    <input type="number" name="account_payable" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-2">เงินกู้ยืมระยะสั้น</label>
                    <input type="number" name="short_term_loan" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-2">เงินรับฝากจากสมาชิก</label>
                    <input type="number" name="member_deposits" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-2">หนี้สินหมุนเวียนอื่น</label>
                    <input type="number" name="other_current_liabilities" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>

            </div>
        </div>

        <!-- หนี้สินไม่หมุนเวียน -->
        <div class="mb-6">
            <h4 class="text-md font-semibold text-gray-700 mb-3">หนี้สินไม่หมุนเวียน (Non-Current Liabilities)</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm text-gray-700 mb-2">เงินกู้ยืมระยะยาว</label>
                    <input type="number" name="long_term_loan" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-2">หนี้สินไม่หมุนเวียนอื่น</label>
                    <input type="number" name="other_non_current_liabilities" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>

            </div>
        </div>

        <!-- รวมหนี้สิน -->
        <div class="bg-green-50 p-4 rounded-lg">
            <label class="block text-sm font-bold text-gray-800 mb-2">รวมหนี้สินทั้งหมด <span
                    class="text-red-500">*</span></label>
            <input type="number" name="total_liabilities" step="0.01" min="0" required
                class="w-full px-4 py-2 border-2 border-green-300 rounded-lg focus:ring-2 focus:ring-green-500 font-semibold"
                placeholder="0.00">
        </div>
    </div>

    <!-- ทุน (Equity) -->
    <div>
        <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b-2 border-purple-500">ทุน (Equity)</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

            <div>
                <label class="block text-sm text-gray-700 mb-2">ทุนเรือนหุ้น</label>
                <input type="number" name="share_capital" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-2">ทุนสำรองตามกฎหมาย</label>
                <input type="number" name="legal_reserve" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-2">ทุนสำรองอื่น</label>
                <input type="number" name="other_reserve" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-2">กำไร (ขาดทุน) สะสม</label>
                <input type="number" name="retained_earnings" step="0.01"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

        </div>

        <!-- รวมทุน -->
        <div class="bg-purple-50 p-4 rounded-lg">
            <label class="block text-sm font-bold text-gray-800 mb-2">รวมทุนทั้งหมด <span
                    class="text-red-500">*</span></label>
            <input type="number" name="total_equity" step="0.01" required
                class="w-full px-4 py-2 border-2 border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 font-semibold"
                placeholder="0.00">
        </div>
    </div>

    <!-- ข้อมูลเพิ่มเติม -->
    <div>
        <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b-2 border-orange-500">ข้อมูลเพิ่มเติม</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <label class="block text-sm text-gray-700 mb-2">จำนวนสมาชิกในงวด</label>
                <input type="number" name="total_members_period" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-2">ลูกหนี้ค้างชำระ</label>
                <input type="number" name="overdue_receivable" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-2">ลูกหนี้ครบกำหนด</label>
                <input type="number" name="due_receivable" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-2">ลูกหนี้ชำระตรงเวลา</label>
                <input type="number" name="paid_on_time_receivable" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

        </div>
    </div>

</div>