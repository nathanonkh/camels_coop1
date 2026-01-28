<!-- งบกำไรขาดทุน (Income Statement) -->
<div class="space-y-8">

    <!-- รายได้ (Revenue) -->
    <div>
        <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b-2 border-blue-500">รายได้ (Revenue)</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

            <div>
                <label class="block text-sm text-gray-700 mb-2">รายได้จากธุรกิจสินเชื่อ</label>
                <input type="number" name="revenue_credit" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-2">รายได้จากการจำหน่ายสินค้า</label>
                <input type="number" name="revenue_sales" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-2">รายได้จากรวบรวมผลผลิต</label>
                <input type="number" name="revenue_procurement" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-2">รายได้จากแปรรูป</label>
                <input type="number" name="revenue_processing" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-2">รายได้จากบริการ</label>
                <input type="number" name="revenue_service" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-2">รายได้อื่น</label>
                <input type="number" name="revenue_other" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

        </div>

        <!-- รวมรายได้ -->
        <div class="bg-blue-50 p-4 rounded-lg">
            <label class="block text-sm font-bold text-gray-800 mb-2">รวมรายได้ทั้งหมด</label>
            <input type="number" name="total_revenue" step="0.01" min="0"
                class="w-full px-4 py-2 border-2 border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-semibold"
                placeholder="0.00">
        </div>
    </div>

    <!-- ต้นทุน (Cost) -->
    <div>
        <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b-2 border-red-500">ต้นทุน (Cost)</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

            <div>
                <label class="block text-sm text-gray-700 mb-2">ต้นทุนสินเชื่อ</label>
                <input type="number" name="cost_credit" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-2">ต้นทุนขาย</label>
                <input type="number" name="cost_sales" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-2">ต้นทุนรวบรวมผลผลิต</label>
                <input type="number" name="cost_procurement" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-2">ต้นทุนแปรรูป</label>
                <input type="number" name="cost_processing" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-2">ต้นทุนบริการ</label>
                <input type="number" name="cost_service" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

        </div>

        <!-- รวมต้นทุน -->
        <div class="bg-red-50 p-4 rounded-lg">
            <label class="block text-sm font-bold text-gray-800 mb-2">รวมต้นทุนทั้งหมด</label>
            <input type="number" name="total_cost" step="0.01" min="0"
                class="w-full px-4 py-2 border-2 border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 font-semibold"
                placeholder="0.00">
        </div>
    </div>

    <!-- กำไรขั้นต้น -->
    <div class="bg-green-50 p-4 rounded-lg">
        <label class="block text-sm font-bold text-gray-800 mb-2">กำไรขั้นต้น (Gross Profit)</label>
        <input type="number" name="gross_profit" step="0.01"
            class="w-full px-4 py-2 border-2 border-green-300 rounded-lg focus:ring-2 focus:ring-green-500 font-semibold"
            placeholder="0.00">
    </div>

    <!-- ค่าใช้จ่าย (Expenses) -->
    <div>
        <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b-2 border-yellow-500">ค่าใช้จ่าย (Expenses)</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <label class="block text-sm text-gray-700 mb-2">ค่าใช้จ่ายในการดำเนินงาน</label>
                <input type="number" name="operating_expenses" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-2">ค่าใช้จ่ายบุคลากร</label>
                <input type="number" name="personnel_expenses" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-2">ค่าใช้จ่ายบริหาร</label>
                <input type="number" name="administrative_expenses" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-2">ค่าเสื่อมราคาและค่าตัดจำหน่าย</label>
                <input type="number" name="depreciation" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

        </div>
    </div>

    <!-- กำไร/ขาดทุน -->
    <div>
        <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b-2 border-purple-500">กำไร/ขาดทุน (Profit/Loss)
        </h3>

        <div class="space-y-4">

            <div class="bg-purple-50 p-4 rounded-lg">
                <label class="block text-sm font-bold text-gray-800 mb-2">กำไร (ขาดทุน) จากการดำเนินงาน</label>
                <input type="number" name="operating_income" step="0.01"
                    class="w-full px-4 py-2 border-2 border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 font-semibold"
                    placeholder="0.00">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-700 mb-2">รายได้อื่น</label>
                    <input type="number" name="other_income" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-2">ค่าใช้จ่ายอื่น</label>
                    <input type="number" name="other_expenses" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="0.00">
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-50 to-blue-50 p-4 rounded-lg border-2 border-green-300">
                <label class="block text-sm font-bold text-gray-800 mb-2">กำไร (ขาดทุน) สุทธิ (Net Profit)</label>
                <input type="number" name="net_profit" step="0.01"
                    class="w-full px-4 py-2 border-2 border-green-400 rounded-lg focus:ring-2 focus:ring-green-500 font-bold text-lg"
                    placeholder="0.00">
            </div>

        </div>
    </div>

    <!-- การจัดสรรกำไร -->
    <div>
        <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b-2 border-indigo-500">การจัดสรรกำไร (Profit
            Allocation)</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div>
                <label class="block text-sm text-gray-700 mb-2">เงินปันผล</label>
                <input type="number" name="dividend" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-2">ทุนสำรองตามกฎหมาย</label>
                <input type="number" name="legal_reserve_allocation" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-2">ทุนสำรองอื่น</label>
                <input type="number" name="other_reserve_allocation" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    placeholder="0.00">
            </div>

        </div>
    </div>

</div>