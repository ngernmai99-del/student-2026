<?php
/**
 * ສົ່ງອອກຂໍ້ມູນລາຍງານນັກສຶກສາເປັນຟາຍ Microsoft Excel (.xlsx ແລະ .xls)
 * ອີງຕາມແບບຟອມບັນທຶກ: ໜ້າຊື່, ຊື່, ນາມສະກຸນ, ວັນເດືອນປີເກີດ (ວັນທີ 01-09 ມີເລກ 0, ເດືອນລາວ, ປີ), ບ້ານ, ເມືອງ, ແຂວງ
 */
require_once __DIR__ . '/config/db.php';

$q        = trim($_GET['q'] ?? '');
$prefix   = trim($_GET['prefix'] ?? '');
$month    = trim($_GET['dob_month'] ?? '');
$province = trim($_GET['province'] ?? '');
$district = trim($_GET['district'] ?? '');
$format   = strtolower(trim($_GET['format'] ?? 'xlsx'));

$whereClauses = [];
$params = [];

if ($q !== '') {
    $whereClauses[] = "(first_name LIKE :q OR last_name LIKE :q OR village LIKE :q)";
    $params[':q'] = '%' . $q . '%';
}
if ($prefix !== '') {
    $whereClauses[] = "prefix = :prefix";
    $params[':prefix'] = $prefix;
}
if ($month !== '') {
    $whereClauses[] = "dob_month = :dob_month";
    $params[':dob_month'] = $month;
}
if ($province !== '') {
    $whereClauses[] = "province = :province";
    $params[':province'] = $province;
}
if ($district !== '') {
    $whereClauses[] = "district = :district";
    $params[':district'] = $district;
}

$whereSql = !empty($whereClauses) ? ('WHERE ' . implode(' AND ', $whereClauses)) : '';
$stmt = $pdo->prepare("SELECT * FROM students {$whereSql} ORDER BY id ASC");
$stmt->execute($params);
$students = $stmt->fetchAll();

$sysSettings = getSystemSettings($pdo);
$timestamp = date('Ymd_His');

/**
 * ຟັງຊັນສ້າງຟາຍ .xlsx ແທ້ (OpenXML Spreadsheet) ດ້ວຍ ZipArchive
 */
function exportAsXlsx(array $students, array $sysSettings, string $filename): void
{
    $tmpDir = __DIR__ . '/data';
    if (!is_dir($tmpDir)) {
        @mkdir($tmpDir, 0777, true);
    }
    $tmpFile = $tmpDir . '/xlsx_' . uniqid('', true) . '.xlsx';
    $zip = new ZipArchive();
    if ($zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        exportAsXlsHtml($students, $sysSettings, str_replace('.xlsx', '.xls', $filename));
        return;
    }

    // 1. [Content_Types].xml
    $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
        . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
        . '<Default Extension="xml" ContentType="application/xml"/>'
        . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
        . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
        . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
        . '</Types>';
    $zip->addFromString('[Content_Types].xml', $contentTypes);

    // 2. _rels/.rels
    $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
        . '</Relationships>';
    $zip->addFromString('_rels/.rels', $rels);

    // 3. xl/workbook.xml
    $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        . '<sheets><sheet name="ລາຍງານຂໍ້ມູນນັກສຶກສາ" sheetId="1" r:id="rId1"/></sheets>'
        . '</workbook>';
    $zip->addFromString('xl/workbook.xml', $workbook);

    // 4. xl/_rels/workbook.xml.rels
    $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
        . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
        . '</Relationships>';
    $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

    // 5. xl/styles.xml
    $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        . '<fonts count="4">'
        . '<font><sz val="11"/><name val="Phetsarath OT"/></font>'
        . '<font><b/><sz val="14"/><color rgb="FF0F2942"/><name val="Phetsarath OT"/></font>'
        . '<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Phetsarath OT"/></font>'
        . '<font><b/><sz val="11"/><color rgb="FF0F2942"/><name val="Phetsarath OT"/></font>'
        . '</fonts>'
        . '<fills count="3">'
        . '<fill><patternFill patternType="none"/></fill>'
        . '<fill><patternFill patternType="gray125"/></fill>'
        . '<fill><patternFill patternType="solid"><fgColor rgb="FF1A4F8B"/><bgColor indexed="64"/></patternFill></fill>'
        . '</fills>'
        . '<borders count="2">'
        . '<border><left/><right/><top/><bottom/><diagonal/></border>'
        . '<border>'
        . '<left style="thin"><color rgb="FFCBD5E1"/></left>'
        . '<right style="thin"><color rgb="FFCBD5E1"/></right>'
        . '<top style="thin"><color rgb="FFCBD5E1"/></top>'
        . '<bottom style="thin"><color rgb="FFCBD5E1"/></bottom>'
        . '<diagonal/>'
        . '</border>'
        . '</borders>'
        . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
        . '<cellXfs count="5">'
        . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
        . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
        . '<xf numFmtId="0" fontId="2" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
        . '<xf numFmtId="49" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
        . '<xf numFmtId="49" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>'
        . '</cellXfs>'
        . '</styleSheet>';
    $zip->addFromString('xl/styles.xml', $styles);

    $makeCell = function (string $colLetter, int $rowNum, string $val, int $styleIdx): string {
        $escaped = htmlspecialchars($val, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        return '<c r="' . $colLetter . $rowNum . '" s="' . $styleIdx . '" t="inlineStr"><is><t>' . $escaped . '</t></is></c>';
    };

    $rowsXml = '';

    // ແຖວທີ 1: ຊື່ບົດລາຍງານ
    $titleText = 'ລາຍງານຂໍ້ມູນນັກສຶກສາ ຂໍອອກໃບຢັ້ງຢືນຈົບການສຶກສາ ລໍຖ້າຮັບໃບປະກາດ';
    $rowsXml .= '<row r="1" ht="28" customHeight="1">' . $makeCell('A', 1, $titleText, 1) . '</row>';

    // ແຖວທີ 2: ວັນທີສົ່ງອອກ
    $subTitle = 'ວັນທີສົ່ງອອກຂໍ້ມູນ: ' . date('d/m/Y H:i') . ' | ຈຳນວນທັງໝົດ: ' . count($students) . ' ຄົນ';
    $rowsXml .= '<row r="2" ht="20" customHeight="1">' . $makeCell('A', 2, $subTitle, 0) . '</row>';

    // ແຖວທີ 3: ຫົວຕາຕະລາງ (ອີງຕາມແບບຟອມບັນທຶກ)
    $headers = [
        'A' => 'ລ/ດ',
        'B' => 'ໜ້າຊື່',
        'C' => 'ຊື່',
        'D' => 'ນາມສະກຸນ',
        'E' => 'ວັນທີເກີດ',
        'F' => 'ເດືອນເກີດ (ເດືອນລາວ)',
        'G' => 'ປີເກີດ',
        'H' => 'ວັນເດືອນປີເກີດ (ລວມ)',
        'I' => 'ບ້ານປັດຈຸບັນ',
        'J' => 'ເມືອງປັດຈຸບັນ',
        'K' => 'ແຂວງປັດຈຸບັນ'
    ];

    $rowsXml .= '<row r="3" ht="26" customHeight="1">';
    foreach ($headers as $col => $text) {
        $rowsXml .= $makeCell($col, 3, $text, 2);
    }
    $rowsXml .= '</row>';

    // ແຖວທີ 4 ຂຶ້ນໄປ: ຂໍ້ມູນນັກສຶກສາ
    $rowNum = 4;
    foreach ($students as $idx => $st) {
        $dayPadded = str_pad((string)$st['dob_day'], 2, '0', STR_PAD_LEFT);
        $dobFull = $dayPadded . ' ' . $st['dob_month'] . ' ' . $st['dob_year'];

        $rowsXml .= '<row r="' . $rowNum . '" ht="22" customHeight="1">';
        $rowsXml .= $makeCell('A', $rowNum, (string)($idx + 1), 3);
        $rowsXml .= $makeCell('B', $rowNum, (string)$st['prefix'], 3);
        $rowsXml .= $makeCell('C', $rowNum, (string)$st['first_name'], 4);
        $rowsXml .= $makeCell('D', $rowNum, (string)$st['last_name'], 4);
        $rowsXml .= $makeCell('E', $rowNum, $dayPadded, 3);
        $rowsXml .= $makeCell('F', $rowNum, (string)$st['dob_month'], 3);
        $rowsXml .= $makeCell('G', $rowNum, (string)$st['dob_year'], 3);
        $rowsXml .= $makeCell('H', $rowNum, $dobFull, 3);
        $rowsXml .= $makeCell('I', $rowNum, (string)$st['village'], 4);
        $rowsXml .= $makeCell('J', $rowNum, (string)$st['district'], 4);
        $rowsXml .= $makeCell('K', $rowNum, (string)$st['province'], 4);
        $rowsXml .= '</row>';
        $rowNum++;
    }

    // 6. xl/worksheets/sheet1.xml
    $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        . '<cols>'
        . '<col min="1" max="1" width="7" customWidth="1"/>'
        . '<col min="2" max="2" width="12" customWidth="1"/>'
        . '<col min="3" max="4" width="20" customWidth="1"/>'
        . '<col min="5" max="7" width="16" customWidth="1"/>'
        . '<col min="8" max="8" width="22" customWidth="1"/>'
        . '<col min="9" max="11" width="22" customWidth="1"/>'
        . '</cols>'
        . '<sheetData>' . $rowsXml . '</sheetData>'
        . '<mergeCells count="2">'
        . '<mergeCell ref="A1:K1"/>'
        . '<mergeCell ref="A2:K2"/>'
        . '</mergeCells>'
        . '</worksheet>';

    $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
    $zip->close();

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($tmpFile));
    header('Cache-Control: max-age=0');

    readfile($tmpFile);
    @unlink($tmpFile);
    exit;
}

/**
 * ຟັງຊັນສ້າງຟາຍ .xls ແບບ HTML/Excel Table
 */
function exportAsXlsHtml(array $students, array $sysSettings, string $filename): void
{
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    echo "\xEF\xBB\xBF";
    ?>
    <html xmlns:o="urn:schemas-microsoft-com:office:office"
          xmlns:x="urn:schemas-microsoft-com:office:excel"
          xmlns="http://www.w3.org/TR/REC-html40">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <style>
            body, table, td, th {
                font-family: 'Phetsarath OT', 'Noto Sans Lao', sans-serif;
                font-size: 11pt;
            }
            .title {
                font-size: 14pt;
                font-weight: bold;
                text-align: center;
                color: #0f2942;
            }
            th {
                background-color: #1a4f8b;
                color: #ffffff;
                font-weight: bold;
                border: 1px solid #94a3b8;
                padding: 6px;
                text-align: center;
            }
            td {
                border: 1px solid #cbd5e1;
                padding: 5px;
            }
            .text-cell {
                mso-number-format: "\@";
            }
            .center {
                text-align: center;
            }
        </style>
    </head>
    <body>
        <table>
            <tr>
                <td colspan="11" class="title">
                    ລາຍງານຂໍ້ມູນນັກສຶກສາ ຂໍອອກໃບຢັ້ງຢືນຈົບການສຶກສາ ລໍຖ້າຮັບໃບປະກາດ
                </td>
            </tr>
            <tr>
                <td colspan="11" class="center">
                    ວັນທີສົ່ງອອກ: <?= date('d/m/Y H:i') ?> | ລວມທັງໝົດ: <?= count($students) ?> ຄົນ
                </td>
            </tr>
            <thead>
                <tr>
                    <th>ລ/ດ</th>
                    <th>ໜ້າຊື່</th>
                    <th>ຊື່</th>
                    <th>ນາມສະກຸນ</th>
                    <th>ວັນທີເກີດ</th>
                    <th>ເດືອນເກີດ (ເດືອນລາວ)</th>
                    <th>ປີເກີດ</th>
                    <th>ວັນເດືອນປີເກີດ (ລວມ)</th>
                    <th>ບ້ານປັດຈຸບັນ</th>
                    <th>ເມືອງປັດຈຸບັນ</th>
                    <th>ແຂວງປັດຈຸບັນ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $i => $st): ?>
                    <?php
                    $dayPadded = str_pad((string)$st['dob_day'], 2, '0', STR_PAD_LEFT);
                    $dobFull = $dayPadded . ' ' . $st['dob_month'] . ' ' . $st['dob_year'];
                    ?>
                    <tr>
                        <td class="center"><?= $i + 1 ?></td>
                        <td class="center"><?= htmlspecialchars($st['prefix']) ?></td>
                        <td><?= htmlspecialchars($st['first_name']) ?></td>
                        <td><?= htmlspecialchars($st['last_name']) ?></td>
                        <td class="center text-cell"><?= htmlspecialchars($dayPadded) ?></td>
                        <td class="center"><?= htmlspecialchars($st['dob_month']) ?></td>
                        <td class="center text-cell"><?= htmlspecialchars($st['dob_year']) ?></td>
                        <td class="center text-cell"><?= htmlspecialchars($dobFull) ?></td>
                        <td><?= htmlspecialchars($st['village']) ?></td>
                        <td><?= htmlspecialchars($st['district']) ?></td>
                        <td><?= htmlspecialchars($st['province']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    exit;
}

if ($format === 'xls') {
    exportAsXlsHtml($students, $sysSettings, "student_report_{$timestamp}.xls");
} else {
    exportAsXlsx($students, $sysSettings, "student_report_{$timestamp}.xlsx");
}
