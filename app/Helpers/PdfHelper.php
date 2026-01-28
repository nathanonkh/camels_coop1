<?php
/**
 * PDF Helper
 * สร้างรายงาน PDF แบบง่าย (ไม่ต้องใช้ TCPDF)
 * ใช้ HTML + CSS แล้วแปลงเป็น PDF ด้วย Browser Print
 * 
 * PHP 8.0+
 */

class PdfHelper
{

    /**
     * สร้าง HTML สำหรับรายงาน CAMELS
     * 
     * @param array $data
     * @return string
     */
    public static function generateCamelsReportHtml(array $data): string
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="th">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>รายงาน CAMELS</title>
            <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&display=swap" rel="stylesheet">
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: 'Sarabun', sans-serif;
                    font-size: 14px;
                    line-height: 1.6;
                    color: #333;
                    padding: 20mm;
                }

                @media print {
                    body {
                        padding: 10mm;
                    }

                    .page-break {
                        page-break-after: always;
                    }

                    .no-print {
                        display: none;
                    }
                }

                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 3px solid #3B82F6;
                    padding-bottom: 20px;
                }

                .header h1 {
                    color: #3B82F6;
                    font-size: 28px;
                    font-weight: 700;
                    margin-bottom: 15px;
                }

                .info-table {
                    width: 100%;
                    margin-bottom: 30px;
                }

                .info-table td {
                    padding: 8px;
                }

                .info-table td:first-child {
                    font-weight: 600;
                    width: 30%;
                }

                .score-box {
                    background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%);
                    border: 2px solid #3B82F6;
                    border-radius: 10px;
                    padding: 30px;
                    text-align: center;
                    margin: 30px 0;
                }

                .score-box h2 {
                    font-size: 20px;
                    color: #1E40AF;
                    margin-bottom: 15px;
                }

                .overall-score {
                    font-size: 48px;
                    font-weight: 700;
                    margin: 10px 0;
                }

                .rating-text {
                    font-size: 18px;
                    font-weight: 600;
                    margin-top: 10px;
                }

                .dimension-scores {
                    margin: 30px 0;
                }

                .dimension-row {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 12px;
                    border-bottom: 1px solid #E5E7EB;
                }

                .dimension-name {
                    font-weight: 600;
                    flex: 1;
                }

                .dimension-score {
                    font-weight: 700;
                    font-size: 18px;
                    margin: 0 20px;
                }

                .dimension-rating {
                    padding: 4px 12px;
                    border-radius: 12px;
                    font-size: 12px;
                    font-weight: 600;
                }

                .section-title {
                    font-size: 22px;
                    font-weight: 700;
                    margin: 30px 0 15px 0;
                    padding-bottom: 10px;
                    border-bottom: 2px solid #E5E7EB;
                }

                .recommendations {
                    margin: 20px 0;
                }

                .recommendation-item {
                    margin: 10px 0;
                    padding-left: 20px;
                    line-height: 1.8;
                }

                .badge-green {
                    background: #D1FAE5;
                    color: #065F46;
                }

                .badge-yellow {
                    background: #FEF3C7;
                    color: #92400E;
                }

                .badge-red {
                    background: #FEE2E2;
                    color: #991B1B;
                }

                .score-green {
                    color: #10B981;
                }

                .score-yellow {
                    color: #F59E0B;
                }

                .score-red {
                    color: #EF4444;
                }

                .ratio-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 15px 0;
                }

                .ratio-table th {
                    background: #F3F4F6;
                    padding: 10px;
                    text-align: left;
                    font-weight: 600;
                    border-bottom: 2px solid #D1D5DB;
                }

                .ratio-table td {
                    padding: 8px 10px;
                    border-bottom: 1px solid #E5E7EB;
                }

                .footer {
                    margin-top: 50px;
                    text-align: center;
                    font-size: 12px;
                    color: #6B7280;
                    padding-top: 20px;
                    border-top: 1px solid #E5E7EB;
                }

                @media print {
                    .print-button {
                        display: none;
                    }
                }

                .print-button {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    padding: 12px 24px;
                    background: #3B82F6;
                    color: white;
                    border: none;
                    border-radius: 8px;
                    cursor: pointer;
                    font-size: 16px;
                    font-weight: 600;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                }

                .print-button:hover {
                    background: #2563EB;
                }
            </style>
        </head>

        <body>

            <button class="print-button no-print" onclick="window.print()">🖨️ พิมพ์รายงาน</button>

            <!-- Header -->
            <div class="header">
                <h1>รายงานผลการวิเคราะห์ CAMELS</h1>
            </div>

            <!-- Coop Info -->
            <table class="info-table">
                <tr>
                    <td><strong>ชื่อสหกรณ์:</strong></td>
                    <td>
                        <?= htmlspecialchars($data['coopInfo']['coop_name'] ?? '-') ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>รหัสสหกรณ์:</strong></td>
                    <td>
                        <?= htmlspecialchars($data['coopInfo']['coop_code'] ?? '-') ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>วันที่รายงาน:</strong></td>
                    <td>
                        <?= date('d/m/Y', strtotime($data['reportDate'] ?? 'now')) ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>วันที่สร้างรายงาน:</strong></td>
                    <td>
                        <?= date('d/m/Y H:i') ?> น.
                    </td>
                </tr>
            </table>

            <!-- Overall Score -->
            <div class="score-box">
                <h2>คะแนนรวม CAMELS</h2>
                <div class="overall-score <?= self::getScoreColorClass($data['camelsResult']['overall_score']) ?>">
                    <?= number_format($data['camelsResult']['overall_score'], 2) ?>
                </div>
                <div class="rating-text">
                    ระดับ:
                    <?= htmlspecialchars($data['camelsResult']['rating']['name']) ?>
                </div>
                <div style="margin-top: 15px; font-size: 14px; color: #6B7280;">
                    <?= htmlspecialchars($data['camelsResult']['rating']['description']) ?>
                </div>
            </div>

            <!-- Dimension Scores -->
            <div class="section-title">คะแนนแต่ละมิติ</div>

            <div class="dimension-scores">
                <?php
                $dimensions = [
                    'capital' => 'C - ความเพียงพอของทุน',
                    'asset' => 'A - คุณภาพสินทรัพย์',
                    'management' => 'M - การบริหารจัดการ',
                    'earning' => 'E - การทำกำไร',
                    'liquidity' => 'L - สภาพคล่อง'
                ];

                foreach ($dimensions as $key => $name):
                    $score = $data['camelsResult']['scores'][$key];
                    ?>
                    <div class="dimension-row">
                        <div class="dimension-name">
                            <?= $name ?>
                        </div>
                        <div class="dimension-score <?= self::getScoreColorClass($score['score']) ?>">
                            <?= number_format($score['score'], 2) ?>
                        </div>
                        <div class="dimension-rating <?= self::getBadgeClass($score['color']) ?>">
                            <?= htmlspecialchars($score['text']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="page-break"></div>

            <!-- Recommendations -->
            <div class="section-title" style="color: #10B981;">✓ จุดแข็ง</div>
            <div class="recommendations">
                <?php if (!empty($data['camelsResult']['recommendations']['strengths'])): ?>
                    <?php foreach ($data['camelsResult']['recommendations']['strengths'] as $i => $strength): ?>
                        <div class="recommendation-item">
                            <?= ($i + 1) ?>.
                            <?= htmlspecialchars($strength) ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="recommendation-item">ไม่มีข้อมูล</div>
                <?php endif; ?>
            </div>

            <div class="section-title" style="color: #F59E0B;">⚠ จุดที่ควรปรับปรุง</div>
            <div class="recommendations">
                <?php if (!empty($data['camelsResult']['recommendations']['improvements'])): ?>
                    <?php foreach ($data['camelsResult']['recommendations']['improvements'] as $i => $improvement): ?>
                        <div class="recommendation-item">
                            <?= ($i + 1) ?>.
                            <?= htmlspecialchars($improvement) ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="recommendation-item">ไม่มีข้อมูล</div>
                <?php endif; ?>
            </div>

            <?php if (!empty(array_filter($data['camelsResult']['recommendations']['priorities']))): ?>
                <div class="section-title" style="color: #EF4444;">⚡ ความสำคัญเร่งด่วน</div>
                <div class="recommendations">
                    <?php foreach ($data['camelsResult']['recommendations']['priorities'] as $i => $priority): ?>
                        <?php if (!empty($priority)): ?>
                            <div class="recommendation-item">
                                <?= ($i + 1) ?>.
                                <?= htmlspecialchars($priority) ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="page-break"></div>

            <!-- Financial Ratios -->
            <div class="section-title">อัตราส่วนทางการเงิน</div>

            <?php foreach ($dimensions as $key => $name): ?>
                <?php if (!empty($data['ratios'][$key])): ?>
                    <h3 style="margin: 20px 0 10px 0; font-size: 16px; font-weight: 600;">
                        <?= $name ?>
                    </h3>

                    <table class="ratio-table">
                        <thead>
                            <tr>
                                <th>อัตราส่วน</th>
                                <th style="text-align: right;">ค่า</th>
                                <th style="text-align: center;">หน่วย</th>
                                <th style="text-align: center;">ผลการประเมิน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['ratios'][$key] as $ratio): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($ratio['name']) ?>
                                    </td>
                                    <td style="text-align: right; font-weight: 600;">
                                        <?= number_format($ratio['value'], 2) ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <?= htmlspecialchars($ratio['unit'] ?? '') ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="dimension-rating <?= self::getBadgeClass($ratio['rating']['color'] ?? 'gray') ?>">
                                            <?= htmlspecialchars($ratio['rating']['text'] ?? '-') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endforeach; ?>

            <!-- Footer -->
            <div class="footer">
                <div><strong>CAMELS Analysis System</strong></div>
                <div>ระบบวิเคราะห์ความเพียงพอของทุน คุณภาพสินทรัพย์ การบริหารจัดการ การทำกำไร และสภาพคล่อง</div>
                <div style="margin-top: 10px;">©
                    <?= date('Y') ?> All rights reserved
                </div>
            </div>

        </body>

        </html>
        <?php
        return ob_get_clean();
    }

    /**
     * กำหนด CSS class สำหรับคะแนน
     * 
     * @param float $score
     * @return string
     */
    private static function getScoreColorClass(float $score): string
    {
        if ($score >= 70) {
            return 'score-green';
        } elseif ($score >= 40) {
            return 'score-yellow';
        } else {
            return 'score-red';
        }
    }

    /**
     * กำหนด CSS class สำหรับ badge
     * 
     * @param string $color
     * @return string
     */
    private static function getBadgeClass(string $color): string
    {
        return match ($color) {
            'green' => 'badge-green',
            'yellow' => 'badge-yellow',
            'red' => 'badge-red',
            default => 'badge-yellow'
        };
    }
}