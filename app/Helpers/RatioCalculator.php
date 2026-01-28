<?php
/**
 * Ratio Calculator Helper
 * คำนวณอัตราส่วนทางการเงินทั้ง 5 มิติ (C-A-M-E-L)
 * 
 * PHP 8.0+
 */

class RatioCalculator
{

    /**
     * คำนวณอัตราส่วนทั้งหมด
     * 
     * @param array $balanceSheet - ข้อมูลงบดุล
     * @param array $incomeStatement - ข้อมูลงบกำไรขาดทุน
     * @return array
     */
    public static function calculateAll(array $balanceSheet, array $incomeStatement): array
    {
        $ratios = [];

        // C - Capital Strength
        $ratios['capital'] = self::calculateCapitalRatios($balanceSheet, $incomeStatement);

        // A - Asset Quality
        $ratios['asset'] = self::calculateAssetRatios($balanceSheet, $incomeStatement);

        // M - Management Ability
        $ratios['management'] = self::calculateManagementRatios($balanceSheet, $incomeStatement);

        // E - Earning Sufficiency
        $ratios['earning'] = self::calculateEarningRatios($balanceSheet, $incomeStatement);

        // L - Liquidity
        $ratios['liquidity'] = self::calculateLiquidityRatios($balanceSheet, $incomeStatement);

        return $ratios;
    }

    /**
     * C - Capital Strength (ความเพียงพอของทุน)
     */
    public static function calculateCapitalRatios(array $bs, array $is): array
    {
        $ratios = [];

        // 1. อัตราส่วนหนี้สินต่อทุน (Debt to Equity Ratio)
        if ($bs['total_equity'] > 0) {
            $ratios['debt_to_equity'] = [
                'value' => $bs['total_liabilities'] / $bs['total_equity'],
                'name' => 'อัตราส่วนหนี้สินต่อทุน',
                'unit' => 'เท่า',
                'formula' => 'หนี้สินรวม / ทุนรวม'
            ];
        }

        // 2. อัตราส่วนทุนสำรองต่อสินทรัพย์ (Reserve to Assets Ratio)
        if ($bs['total_assets'] > 0) {
            $total_reserves = ($bs['legal_reserve'] ?? 0) + ($bs['other_reserve'] ?? 0);
            $ratios['reserve_to_assets'] = [
                'value' => $total_reserves / $bs['total_assets'],
                'name' => 'อัตราส่วนทุนสำรองต่อสินทรัพย์',
                'unit' => 'เท่า',
                'formula' => '(ทุนสำรองตามกฎหมาย + ทุนสำรองอื่น) / สินทรัพย์รวม'
            ];
        }

        // 3. อัตราผลตอบแทนต่อส่วนของทุน (Return on Equity - ROE)
        if ($bs['total_equity'] > 0) {
            $ratios['return_on_equity'] = [
                'value' => (($is['net_profit'] ?? 0) / $bs['total_equity']) * 100,
                'name' => 'อัตราผลตอบแทนต่อส่วนของทุน (ROE)',
                'unit' => '%',
                'formula' => '(กำไรสุทธิ / ทุนรวม) × 100'
            ];
        }

        return $ratios;
    }

    /**
     * A - Asset Quality (คุณภาพของสินทรัพย์)
     */
    public static function calculateAssetRatios(array $bs, array $is): array
    {
        $ratios = [];

        // 1. อัตราการค้างชำระของลูกหนี้ (Overdue Ratio)
        $total_receivable = ($bs['loan_receivable_short'] ?? 0) + ($bs['loan_receivable_long'] ?? 0);
        if ($total_receivable > 0) {
            $ratios['overdue_ratio'] = [
                'value' => (($bs['overdue_receivable'] ?? 0) / $total_receivable) * 100,
                'name' => 'อัตราการค้างชำระของลูกหนี้',
                'unit' => '%',
                'formula' => '(ลูกหนี้ค้างชำระ / ลูกหนี้รวม) × 100'
            ];
        }

        // 2. อัตราหมุนของสินทรัพย์ (Asset Turnover)
        if ($bs['total_assets'] > 0) {
            $ratios['asset_turnover'] = [
                'value' => ($is['total_revenue'] ?? 0) / $bs['total_assets'],
                'name' => 'อัตราหมุนของสินทรัพย์',
                'unit' => 'เท่า',
                'formula' => 'รายได้รวม / สินทรัพย์รวม'
            ];
        }

        // 3. อัตราผลตอบแทนต่อสินทรัพย์ (Return on Assets - ROA)
        if ($bs['total_assets'] > 0) {
            $ratios['return_on_assets'] = [
                'value' => (($is['net_profit'] ?? 0) / $bs['total_assets']) * 100,
                'name' => 'อัตราผลตอบแทนต่อสินทรัพย์ (ROA)',
                'unit' => '%',
                'formula' => '(กำไรสุทธิ / สินทรัพย์รวม) × 100'
            ];
        }

        return $ratios;
    }

    /**
     * M - Management Ability (ความสามารถในการบริหารจัดการ)
     */
    public static function calculateManagementRatios(array $bs, array $is): array
    {
        $ratios = [];

        // 1. อัตราการเติบโตของธุรกิจ (Business Growth Rate)
        // ใช้รายได้ต่อสินทรัพย์เป็นตัวชี้วัด
        if ($bs['total_assets'] > 0) {
            $ratios['business_growth_rate'] = [
                'value' => (($is['total_revenue'] ?? 0) / $bs['total_assets']) * 100,
                'name' => 'อัตราการใช้ประโยชน์จากสินทรัพย์',
                'unit' => '%',
                'formula' => '(รายได้รวม / สินทรัพย์รวม) × 100'
            ];
        }

        return $ratios;
    }

    /**
     * E - Earning Sufficiency (การทำกำไร)
     */
    public static function calculateEarningRatios(array $bs, array $is): array
    {
        $ratios = [];

        $total_members = $bs['total_members_period'] ?? 1;
        if ($total_members == 0)
            $total_members = 1;

        // 1. กำไร(ขาดทุน)ต่อสมาชิก (Profit per Member)
        $ratios['profit_per_member'] = [
            'value' => ($is['net_profit'] ?? 0) / $total_members,
            'name' => 'กำไร(ขาดทุน)ต่อสมาชิก',
            'unit' => 'บาท',
            'formula' => 'กำไรสุทธิ / จำนวนสมาชิก'
        ];

        // 2. เงินออมต่อสมาชิก (Savings per Member)
        $savings = ($bs['member_deposits'] ?? 0) + ($bs['share_capital'] ?? 0);
        $ratios['savings_per_member'] = [
            'value' => $savings / $total_members,
            'name' => 'เงินออมต่อสมาชิก',
            'unit' => 'บาท',
            'formula' => '(เงินรับฝาก + ทุนเรือนหุ้น) / จำนวนสมาชิก'
        ];

        // 3. หนี้สินต่อสมาชิก (Debt per Member)
        $ratios['debt_per_member'] = [
            'value' => ($bs['total_liabilities'] ?? 0) / $total_members,
            'name' => 'หนี้สินต่อสมาชิก',
            'unit' => 'บาท',
            'formula' => 'หนี้สินรวม / จำนวนสมาชิก'
        ];

        // 4. อัตราค่าใช้จ่ายดำเนินงานต่อรายได้ (Operating Expense Ratio)
        if (($is['total_revenue'] ?? 0) > 0) {
            $ratios['operating_expense_ratio'] = [
                'value' => (($is['operating_expenses'] ?? 0) / $is['total_revenue']) * 100,
                'name' => 'อัตราค่าใช้จ่ายดำเนินงานต่อรายได้',
                'unit' => '%',
                'formula' => '(ค่าใช้จ่ายดำเนินงาน / รายได้รวม) × 100'
            ];
        }

        // 5. อัตรากำไร(ขาดทุน)สุทธิ (Net Profit Margin)
        if (($is['total_revenue'] ?? 0) > 0) {
            $ratios['net_profit_margin'] = [
                'value' => (($is['net_profit'] ?? 0) / $is['total_revenue']) * 100,
                'name' => 'อัตรากำไร(ขาดทุน)สุทธิ',
                'unit' => '%',
                'formula' => '(กำไรสุทธิ / รายได้รวม) × 100'
            ];
        }

        return $ratios;
    }

    /**
     * L - Liquidity (สภาพคล่อง)
     */
    public static function calculateLiquidityRatios(array $bs, array $is): array
    {
        $ratios = [];

        // 1. อัตราส่วนทุนหมุนเวียน (Current Ratio)
        if (($bs['current_liabilities'] ?? 0) > 0) {
            $ratios['current_ratio'] = [
                'value' => ($bs['current_assets'] ?? 0) / $bs['current_liabilities'],
                'name' => 'อัตราส่วนทุนหมุนเวียน',
                'unit' => 'เท่า',
                'formula' => 'สินทรัพย์หมุนเวียน / หนี้สินหมุนเวียน'
            ];
        }

        // 2. อัตราหมุนของสินค้า (Inventory Turnover)
        if (($bs['inventory'] ?? 0) > 0) {
            $cost_of_goods_sold = ($is['cost_sales'] ?? 0);
            $inventory_turnover = $cost_of_goods_sold / $bs['inventory'];

            $ratios['inventory_turnover'] = [
                'value' => $inventory_turnover,
                'name' => 'อัตราหมุนของสินค้า',
                'unit' => 'เท่า',
                'formula' => 'ต้นทุนขาย / สินค้าคงเหลือเฉลี่ย'
            ];

            // 2.1 อายุเฉลี่ยของสินค้า (Average Inventory Days)
            if ($inventory_turnover > 0) {
                $ratios['average_inventory_days'] = [
                    'value' => 365 / $inventory_turnover,
                    'name' => 'อายุเฉลี่ยของสินค้า',
                    'unit' => 'วัน',
                    'formula' => '365 / อัตราหมุนของสินค้า'
                ];
            }
        }

        // 3. อัตราลูกหนี้ที่ชำระได้ตามกำหนด (Collection Ratio)
        $due_receivable = ($bs['due_receivable'] ?? 0);
        if ($due_receivable > 0) {
            $ratios['collection_ratio'] = [
                'value' => (($bs['paid_on_time_receivable'] ?? 0) / $due_receivable) * 100,
                'name' => 'อัตราลูกหนี้ที่ชำระได้ตามกำหนด',
                'unit' => '%',
                'formula' => '(ลูกหนี้ชำระตรงเวลา / ลูกหนี้ครบกำหนด) × 100'
            ];
        }

        return $ratios;
    }

    /**
     * ประเมินผลอัตราส่วน (Rating)
     * 
     * @param string $ratioCode
     * @param float $value
     * @param array $benchmark
     * @return array
     */
    public static function getRating(string $ratioCode, float $value, ?array $benchmark = null): array
    {
        // ถ้าไม่มี benchmark ให้ใช้ค่า default
        if (!$benchmark) {
            $benchmark = self::getDefaultBenchmark($ratioCode);
        }

        $rating = [
            'level' => 'unknown',
            'color' => 'gray',
            'text' => 'ไม่มีข้อมูล',
            'badge_class' => 'bg-gray-100 text-gray-800'
        ];

        $direction = $benchmark['direction'] ?? 1; // 1 = ยิ่งมากยิ่งดี, 0 = ยิ่งน้อยยิ่งดี

        if ($direction == 1) {
            // ยิ่งมากยิ่งดี (เช่น ROA, ROE)
            if ($value >= ($benchmark['excellent_min'] ?? PHP_FLOAT_MAX)) {
                $rating = ['level' => 'excellent', 'color' => 'green', 'text' => 'ดีมาก', 'badge_class' => 'bg-green-100 text-green-800'];
            } elseif ($value >= ($benchmark['good_min'] ?? 0)) {
                $rating = ['level' => 'good', 'color' => 'green', 'text' => 'ดี', 'badge_class' => 'bg-green-100 text-green-800'];
            } elseif ($value >= ($benchmark['fair_min'] ?? 0)) {
                $rating = ['level' => 'fair', 'color' => 'yellow', 'text' => 'พอใช้', 'badge_class' => 'bg-yellow-100 text-yellow-800'];
            } else {
                $rating = ['level' => 'poor', 'color' => 'red', 'text' => 'ควรปรับปรุง', 'badge_class' => 'bg-red-100 text-red-800'];
            }
        } else {
            // ยิ่งน้อยยิ่งดี (เช่น Debt to Equity, Operating Expense Ratio)
            if ($value <= ($benchmark['excellent_max'] ?? 0)) {
                $rating = ['level' => 'excellent', 'color' => 'green', 'text' => 'ดีมาก', 'badge_class' => 'bg-green-100 text-green-800'];
            } elseif ($value <= ($benchmark['good_max'] ?? PHP_FLOAT_MAX)) {
                $rating = ['level' => 'good', 'color' => 'green', 'text' => 'ดี', 'badge_class' => 'bg-green-100 text-green-800'];
            } elseif ($value <= ($benchmark['fair_max'] ?? PHP_FLOAT_MAX)) {
                $rating = ['level' => 'fair', 'color' => 'yellow', 'text' => 'พอใช้', 'badge_class' => 'bg-yellow-100 text-yellow-800'];
            } else {
                $rating = ['level' => 'poor', 'color' => 'red', 'text' => 'ควรปรับปรุง', 'badge_class' => 'bg-red-100 text-red-800'];
            }
        }

        return $rating;
    }

    /**
     * ดึง Benchmark เริ่มต้น
     */
    private static function getDefaultBenchmark(string $ratioCode): array
    {
        $benchmarks = [
            'debt_to_equity' => ['good_max' => 0.75, 'fair_max' => 1.75, 'direction' => 0],
            'reserve_to_assets' => ['good_min' => 0.20, 'fair_min' => 0.10, 'direction' => 1],
            'return_on_equity' => ['good_min' => 5.0, 'fair_min' => 2.0, 'direction' => 1],
            'return_on_assets' => ['good_min' => 3.0, 'fair_min' => 1.5, 'direction' => 1],
            'overdue_ratio' => ['good_max' => 5.0, 'fair_max' => 10.0, 'direction' => 0],
            'operating_expense_ratio' => ['good_max' => 45, 'fair_max' => 65, 'direction' => 0],
            'collection_ratio' => ['good_min' => 90, 'fair_min' => 60, 'direction' => 1],
            'current_ratio' => ['good_min' => 1.5, 'fair_min' => 1.0, 'direction' => 1],
            'net_profit_margin' => ['good_min' => 5.0, 'fair_min' => 2.0, 'direction' => 1],
            'inventory_turnover' => ['good_min' => 33.50, 'fair_min' => 12.25, 'direction' => 1],
        ];

        return $benchmarks[$ratioCode] ?? ['direction' => 1];
    }
}