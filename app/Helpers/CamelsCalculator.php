<?php
/**
 * CAMELS Calculator Helper
 * คำนวณคะแนน CAMELS จากอัตราส่วนทางการเงิน
 * 
 * PHP 8.0+
 */

class CamelsCalculator
{

    /**
     * คำนวณคะแนน CAMELS ทั้งหมด
     * 
     * @param array $ratios - อัตราส่วนทางการเงินทั้ง 5 มิติ
     * @return array
     */
    public static function calculateAll(array $ratios): array
    {
        $scores = [
            'capital' => self::calculateDimensionScore('capital', $ratios['capital'] ?? []),
            'asset' => self::calculateDimensionScore('asset', $ratios['asset'] ?? []),
            'management' => self::calculateDimensionScore('management', $ratios['management'] ?? []),
            'earning' => self::calculateDimensionScore('earning', $ratios['earning'] ?? []),
            'liquidity' => self::calculateDimensionScore('liquidity', $ratios['liquidity'] ?? []),
        ];

        // คำนวณคะแนนรวม
        $overallScore = self::calculateOverallScore($scores);

        // กำหนดระดับ
        $rating = self::getRating($overallScore);

        return [
            'scores' => $scores,
            'overall_score' => $overallScore,
            'rating' => $rating,
            'recommendations' => self::getRecommendations($scores, $ratios)
        ];
    }

    /**
     * คำนวณคะแนนแต่ละมิติ
     * 
     * @param string $dimension
     * @param array $dimensionRatios
     * @return array
     */
    public static function calculateDimensionScore(string $dimension, array $dimensionRatios): array
    {
        if (empty($dimensionRatios)) {
            return [
                'score' => 0,
                'rating' => 'unknown',
                'color' => 'gray',
                'text' => 'ไม่มีข้อมูล'
            ];
        }

        $totalScore = 0;
        $totalWeight = 0;

        foreach ($dimensionRatios as $code => $ratio) {
            if (!isset($ratio['rating']))
                continue;

            $rating = $ratio['rating'];
            $weight = self::getRatioWeight($dimension, $code);

            // แปลง rating เป็นคะแนน
            $score = match ($rating['level']) {
                'excellent' => 100,
                'good' => 80,
                'fair' => 60,
                'poor' => 40,
                default => 0
            };

            $totalScore += $score * $weight;
            $totalWeight += $weight;
        }

        $averageScore = $totalWeight > 0 ? $totalScore / $totalWeight : 0;

        return [
            'score' => round($averageScore, 2),
            'rating' => self::getScoreRating($averageScore)['level'],
            'color' => self::getScoreRating($averageScore)['color'],
            'text' => self::getScoreRating($averageScore)['text']
        ];
    }

    /**
     * คำนวณคะแนนรวม
     * 
     * @param array $scores
     * @return float
     */
    public static function calculateOverallScore(array $scores): float
    {
        // น้ำหนักของแต่ละมิติ (รวมเป็น 100%)
        $weights = [
            'capital' => 0.25,      // 25%
            'asset' => 0.20,        // 20%
            'management' => 0.15,   // 15%
            'earning' => 0.25,      // 25%
            'liquidity' => 0.15     // 15%
        ];

        $totalScore = 0;

        foreach ($scores as $dimension => $dimensionScore) {
            $weight = $weights[$dimension] ?? 0;
            $score = $dimensionScore['score'] ?? 0;
            $totalScore += $score * $weight;
        }

        return round($totalScore, 2);
    }

    /**
     * ดึงน้ำหนักของอัตราส่วน
     * 
     * @param string $dimension
     * @param string $ratioCode
     * @return float
     */
    private static function getRatioWeight(string $dimension, string $ratioCode): float
    {
        $weights = [
            'capital' => [
                'debt_to_equity' => 1.5,
                'reserve_to_assets' => 1.0,
                'return_on_equity' => 1.0,
            ],
            'asset' => [
                'overdue_ratio' => 1.5,
                'asset_turnover' => 1.0,
                'return_on_assets' => 1.5,
            ],
            'management' => [
                'deposit_to_loan' => 1.0,
                'business_growth_rate' => 1.0,
            ],
            'earning' => [
                'profit_per_member' => 1.0,
                'dividend_per_member' => 0.5,
                'debt_per_member' => 0.5,
                'operating_expense_ratio' => 1.5,
                'net_profit_margin' => 1.5,
            ],
            'liquidity' => [
                'current_ratio' => 1.5,
                'inventory_turnover' => 0.5,
                'average_inventory_days' => 0.5,
                'collection_ratio' => 1.5,
            ]
        ];

        return $weights[$dimension][$ratioCode] ?? 1.0;
    }

    /**
     * แปลงคะแนนเป็น Rating
     * 
     * @param float $score
     * @return array
     */
    private static function getScoreRating(float $score): array
    {
        if ($score >= 80) {
            return ['level' => 'excellent', 'color' => 'green', 'text' => 'ดีมาก'];
        } elseif ($score >= 60) {
            return ['level' => 'good', 'color' => 'green', 'text' => 'ดี'];
        } elseif ($score >= 40) {
            return ['level' => 'fair', 'color' => 'yellow', 'text' => 'พอใช้'];
        } else {
            return ['level' => 'poor', 'color' => 'red', 'text' => 'ควรปรับปรุง'];
        }
    }

    /**
     * กำหนดระดับความพร้อม
     * 
     * @param float $overallScore
     * @return array
     */
    public static function getRating(float $overallScore): array
    {
        if ($overallScore >= 85) {
            return [
                'level' => 5,
                'name' => 'เหมาะสมมากที่สุด',
                'description' => 'สหกรณ์มีความพร้อมในการให้บริการสูงมาก มีความมั่นคงทางการเงินและการบริหารจัดการที่ดีเยี่ยม',
                'color' => 'green',
                'badge_class' => 'bg-green-100 text-green-800'
            ];
        } elseif ($overallScore >= 70) {
            return [
                'level' => 4,
                'name' => 'เหมาะสม',
                'description' => 'สหกรณ์มีความพร้อมในการให้บริการในระดับดี มีความมั่นคงทางการเงินและสามารถบริหารจัดการได้อย่างมีประสิทธิภาพ',
                'color' => 'green',
                'badge_class' => 'bg-green-100 text-green-800'
            ];
        } elseif ($overallScore >= 55) {
            return [
                'level' => 3,
                'name' => 'ค่อนข้างเหมาะสม',
                'description' => 'สหกรณ์มีความพร้อมในระดับพอใช้ ควรพัฒนาในบางด้านเพื่อเพิ่มประสิทธิภาพ',
                'color' => 'yellow',
                'badge_class' => 'bg-yellow-100 text-yellow-800'
            ];
        } elseif ($overallScore >= 40) {
            return [
                'level' => 2,
                'name' => 'ควรปรับปรุง',
                'description' => 'สหกรณ์มีความพร้อมในระดับต่ำ ควรปรับปรุงและพัฒนาในหลายด้านอย่างเร่งด่วน',
                'color' => 'orange',
                'badge_class' => 'bg-orange-100 text-orange-800'
            ];
        } else {
            return [
                'level' => 1,
                'name' => 'ต้องปรับปรุงเร่งด่วน',
                'description' => 'สหกรณ์มีความเสี่ยงสูง จำเป็นต้องปรับปรุงและแก้ไขปัญหาอย่างเร่งด่วน',
                'color' => 'red',
                'badge_class' => 'bg-red-100 text-red-800'
            ];
        }
    }

    /**
     * สร้างคำแนะนำ
     * 
     * @param array $scores
     * @param array $ratios
     * @return array
     */
    public static function getRecommendations(array $scores, array $ratios): array
    {
        $recommendations = [
            'strengths' => [],
            'improvements' => [],
            'priorities' => []
        ];

        // วิเคราะห์จุดแข็ง
        foreach ($scores as $dimension => $score) {
            if ($score['score'] >= 70) {
                $recommendations['strengths'][] = self::getStrengthText($dimension, $score['score']);
            }
        }

        // วิเคราะห์จุดที่ควรปรับปรุง
        foreach ($scores as $dimension => $score) {
            if ($score['score'] < 60) {
                $recommendations['improvements'][] = self::getImprovementText($dimension, $score['score']);
                $recommendations['priorities'][] = self::getPriorityText($dimension, $ratios[$dimension] ?? []);
            }
        }

        // ถ้าไม่มีจุดที่ต้องปรับปรุง
        if (empty($recommendations['improvements'])) {
            $recommendations['improvements'][] = 'สหกรณ์มีผลการดำเนินงานที่ดีในทุกมิติ ควรรักษาระดับการดำเนินงานและพัฒนาต่อไป';
        }

        return $recommendations;
    }

    /**
     * ข้อความจุดแข็ง
     */
    private static function getStrengthText(string $dimension, float $score): string
    {
        $texts = [
            'capital' => sprintf('มีความเพียงพอของทุนในระดับดี (คะแนน %.2f) มีความมั่นคงทางการเงิน', $score),
            'asset' => sprintf('คุณภาพของสินทรัพย์อยู่ในเกณฑ์ดี (คะแนน %.2f) การจัดการสินทรัพย์มีประสิทธิภาพ', $score),
            'management' => sprintf('การบริหารจัดการมีประสิทธิภาพดี (คะแนน %.2f)', $score),
            'earning' => sprintf('มีความสามารถในการทำกำไรที่ดี (คะแนน %.2f) สร้างผลตอบแทนให้สมาชิกได้ดี', $score),
            'liquidity' => sprintf('มีสภาพคล่องที่ดี (คะแนน %.2f) สามารถชำระหนี้ได้ตามกำหนด', $score),
        ];

        return $texts[$dimension] ?? '';
    }

    /**
     * ข้อความจุดปรับปรุง
     */
    private static function getImprovementText(string $dimension, float $score): string
    {
        $texts = [
            'capital' => sprintf('ความเพียงพอของทุนต่ำ (คะแนน %.2f) ควรเพิ่มทุนหรือลดหนี้สิน', $score),
            'asset' => sprintf('คุณภาพสินทรัพย์ต่ำ (คะแนน %.2f) มีหนี้สงสัยจะสูญหรือสินทรัพย์ไม่ก่อให้เกิดรายได้', $score),
            'management' => sprintf('การบริหารจัดการยังไม่มีประสิทธิภาพ (คะแนน %.2f) ควรพัฒนาระบบบริหารจัดการ', $score),
            'earning' => sprintf('ความสามารถในการทำกำไรต่ำ (คะแนน %.2f) ควรเพิ่มรายได้หรือลดค่าใช้จ่าย', $score),
            'liquidity' => sprintf('สภาพคล่องต่ำ (คะแนน %.2f) อาจมีปัญหาในการชำระหนี้', $score),
        ];

        return $texts[$dimension] ?? '';
    }

    /**
     * ข้อความความสำคัญเร่งด่วน
     */
    private static function getPriorityText(string $dimension, array $dimensionRatios): string
    {
        $poorRatios = [];

        foreach ($dimensionRatios as $code => $ratio) {
            if (isset($ratio['rating']) && $ratio['rating']['level'] === 'poor') {
                $poorRatios[] = $ratio['name'];
            }
        }

        if (!empty($poorRatios)) {
            return sprintf('มิติ %s: ควรปรับปรุง %s', self::getDimensionName($dimension), implode(', ', $poorRatios));
        }

        return '';
    }

    /**
     * ชื่อมิติภาษาไทย
     */
    private static function getDimensionName(string $dimension): string
    {
        $names = [
            'capital' => 'ความเพียงพอของทุน',
            'asset' => 'คุณภาพสินทรัพย์',
            'management' => 'การบริหารจัดการ',
            'earning' => 'การทำกำไร',
            'liquidity' => 'สภาพคล่อง',
        ];

        return $names[$dimension] ?? $dimension;
    }
}