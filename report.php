<?php
$pageTitle = 'ຂໍ້ມູນລາຍງານ & ສົ່ງອອກ Excel | ລະບົບອອກໃບຢັ້ງຢືນຈົບການສຶກສາ';
require_once __DIR__ . '/includes/header.php';

// ຮັບຄ່າຕົວກອງຈາກ URL
$q        = trim($_GET['q'] ?? '');
$prefix   = trim($_GET['prefix'] ?? '');
$month    = trim($_GET['dob_month'] ?? '');
$province = trim($_GET['province'] ?? '');
$district = trim($_GET['district'] ?? '');

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

$stmt = $pdo->prepare("SELECT * FROM students {$whereSql} ORDER BY id DESC");
$stmt->execute($params);
$students = $stmt->fetchAll();

// ນັບສະຖິຕິຕາມຜົນການຄົ້ນຫາປັດຈຸບັນ
$filteredStats = [
    'total'  => count($students),
    'thao'   => 0,
    'nang'   => 0,
    'sn'     => 0,
    'khouba' => 0,
];

foreach ($students as $row) {
    if ($row['prefix'] === 'ທ້າວ') $filteredStats['thao']++;
    if ($row['prefix'] === 'ນາງ') $filteredStats['nang']++;
    if ($row['prefix'] === 'ສນ')  $filteredStats['sn']++;
    if ($row['prefix'] === 'ຄູບາ') $filteredStats['khouba']++;
}

// ສ້າງ Query String ສຳລັບປຸ່ມ Export Excel
$exportQuery = http_build_query([
    'q' => $q,
    'prefix' => $prefix,
    'dob_month' => $month,
    'province' => $province,
    'district' => $district
]);
?>

<style>
@media print {
    .no-print, .navbar-gov, .footer-gov {
        display: none !important;
    }
    body {
        background: #fff !important;
    }
    .card-modern {
        box-shadow: none !important;
        border: 1px solid #cbd5e1 !important;
    }
    .print-only-header {
        display: block !important;
    }
}
.print-only-header {
    display: none;
}
</style>

<div class="container py-4">
    <!-- ຫົວເອກະສານສຳລັບເວລາສັ່ງພິມລາຍງານ -->
    <div class="print-only-header text-center mb-4">
        <div class="fw-bold">ສາທາລະນະລັດ ປະຊາທິປະໄຕ ປະຊາຊົນລາວ</div>
        <div class="mb-2">ສັນຕິພາບ ເອກະລາດ ປະຊາທິປະໄຕ ເອກະພາບ ວັດທະນາຖາວອນ</div>
        <h4 class="fw-bold mt-3 mb-1">ບົດລາຍງານຂໍ້ມູນນັກສຶກສາ ຂໍອອກໃບຢັ້ງຢືນຈົບການສຶກສາ (ລໍຖ້າຮັບໃບປະກາດ)</h4>
        <div class="small text-muted">ວັນທີພິມລາຍງານ: <?= date('d/m/Y H:i') ?></div>
    </div>

    <!-- ຫົວຂໍ້ໜ້າລາຍງານ & ປຸ່ມສົ່ງອອກ Excel -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 no-print">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-file-earmark-spreadsheet-fill text-success me-2"></i>ຂໍ້ມູນລາຍງານນັກສຶກສາ (ອີງຕາມແບບຟອມບັນທຶກ)
            </h4>
            <div class="text-muted small">
                ສັງລວມລາຍຊື່ນັກສຶກສາ (ໜ້າຊື່, ຊື່, ນາມສະກຸນ), ວັນເດືອນປີເກີດ ແລະ ທີ່ຢູ່ປັດຈຸບັນ (ບ້ານ, ເມືອງ, ແຂວງ)
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="export_excel.php?<?= htmlspecialchars($exportQuery) ?>&format=xlsx" class="btn btn-success fw-bold px-3 py-2 rounded-3 shadow-sm">
                <i class="bi bi-file-earmark-excel-fill me-1"></i>ສົ່ງອອກເປັນຟາຍ Excel (.xlsx)
            </a>
            <a href="export_excel.php?<?= htmlspecialchars($exportQuery) ?>&format=xls" class="btn btn-outline-success fw-bold px-3 py-2 rounded-3">
                <i class="bi bi-download me-1"></i>Excel (.xls)
            </a>
            <button type="button" onclick="window.print()" class="btn btn-outline-dark fw-bold px-3 py-2 rounded-3">
                <i class="bi bi-printer-fill me-1"></i>ພິມລາຍງານ
            </button>
            <a href="certificate.php?all=1&<?= htmlspecialchars($exportQuery) ?>" class="btn btn-primary fw-bold px-3 py-2 rounded-3" style="background-color: var(--secondary-blue);">
                <i class="bi bi-award-fill me-1"></i>ພິມໃບຢັ້ງຢືນທັງໝົດ
            </a>
        </div>
    </div>

    <!-- ຟອມຄົ້ນຫາ ແລະ ກັ່ນຕອງຂໍ້ມູນລາຍງານ -->
    <div class="card card-modern mb-4 no-print">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="report.php" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small">ຄົ້ນຫາ (ຊື່, ນາມສະກຸນ, ບ້ານ)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="ພິມຄຳຄົ້ນຫາ...">
                    </div>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small">ໜ້າຊື່</label>
                    <select class="form-select" name="prefix">
                        <option value="">-- ທັງໝົດ --</option>
                        <?php foreach ($LAO_PREFIXES as $val => $label): ?>
                            <option value="<?= htmlspecialchars($val) ?>" <?= $prefix === $val ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small">ເດືອນເກີດ (ເດືອນລາວ)</label>
                    <select class="form-select" name="dob_month">
                        <option value="">-- ທຸກເດືອນ --</option>
                        <?php foreach ($LAO_MONTHS as $mVal => $mLabel): ?>
                            <option value="<?= htmlspecialchars($mVal) ?>" <?= $month === $mVal ? 'selected' : '' ?>><?= htmlspecialchars($mVal) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small">ເມືອງປັດຈຸບັນ</label>
                    <select class="form-select" name="district" id="filter_district" data-initial-value="<?= htmlspecialchars($district) ?>">
                        <option value="">-- ທຸກເມືອງ --</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small">ແຂວງປັດຈຸບັນ</label>
                    <select class="form-select" name="province" id="filter_province">
                        <option value="">-- ທຸກແຂວງ --</option>
                        <?php foreach (array_keys($LAO_PROVINCES_DISTRICTS) as $prov): ?>
                            <option value="<?= htmlspecialchars($prov) ?>" <?= $province === $prov ? 'selected' : '' ?>><?= htmlspecialchars($prov) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-1 d-grid gap-1">
                    <button type="submit" class="btn btn-primary fw-bold" title="ຄົ້ນຫາ">
                        <i class="bi bi-funnel-fill"></i>
                    </button>
                    <?php if ($q !== '' || $prefix !== '' || $month !== '' || $province !== '' || $district !== ''): ?>
                        <a href="report.php" class="btn btn-outline-secondary btn-sm" title="ລ້າງຕົວກອງ">ລ້າງ</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- ບັດສະຫຼຸບຈຳນວນຕາມຕົວກອງ -->
    <div class="row g-3 mb-4 row-cols-2 row-cols-md-3 row-cols-xl-5">
        <div class="col">
            <div class="card stat-card p-3 bg-white border-start border-4 border-primary">
                <div class="small text-muted fw-semibold">ລວມນັກສຶກສາໃນລາຍງານ</div>
                <div class="fs-4 fw-bold text-primary"><?= number_format($filteredStats['total']) ?> <span class="fs-6 fw-normal text-muted">ຄົນ</span></div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card p-3 bg-white border-start border-4 border-info">
                <div class="small text-muted fw-semibold">ໜ້າຊື່: ທ້າວ</div>
                <div class="fs-4 fw-bold text-info"><?= number_format($filteredStats['thao']) ?> <span class="fs-6 fw-normal text-muted">ຄົນ</span></div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card p-3 bg-white border-start border-4 border-danger">
                <div class="small text-muted fw-semibold">ໜ້າຊື່: ນາງ</div>
                <div class="fs-4 fw-bold text-danger"><?= number_format($filteredStats['nang']) ?> <span class="fs-6 fw-normal text-muted">ຄົນ</span></div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card p-3 bg-white border-start border-4 border-warning">
                <div class="small text-muted fw-semibold">ໜ້າຊື່: ສນ</div>
                <div class="fs-4 fw-bold text-warning"><?= number_format($filteredStats['sn']) ?> <span class="fs-6 fw-normal text-muted">ອົງ</span></div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card p-3 bg-white border-start border-4" style="border-color: #c2410c !important;">
                <div class="small text-muted fw-semibold">ໜ້າຊື່: ຄູບາ</div>
                <div class="fs-4 fw-bold" style="color: #c2410c;"><?= number_format($filteredStats['khouba']) ?> <span class="fs-6 fw-normal text-muted">ອົງ</span></div>
            </div>
        </div>
    </div>

    <!-- ຕາຕະລາງຂໍ້ມູນລາຍງານອີງຕາມແບບຟອມບັນທຶກ -->
    <div class="card card-modern">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="fw-bold text-dark">
                <i class="bi bi-list-columns-reverse text-primary me-2"></i>ຕາຕະລາງລາຍງານຂໍ້ມູນນັກສຶກສາ ອີງຕາມແບບຟອມບັນທຶກ (<?= number_format($filteredStats['total']) ?> ລາຍການ)
            </div>
            <div class="small no-print">
                <?php if ($currentUser): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                        <i class="bi bi-unlock-fill me-1"></i>ສະຖານະ Admin: ສາມາດກົດປຸ່ມ "ແກ້ໄຂ" ຫຼື "ລຶບ" ຂໍ້ມູນໄດ້
                    </span>
                <?php else: ?>
                    <a href="login.php?redirect=<?= urlencode('report.php?' . $exportQuery) ?>" class="btn btn-sm btn-warning fw-bold px-3">
                        <i class="bi bi-shield-lock-fill me-1"></i>Login Admin ເພື່ອແກ້ໄຂຂໍ້ມູນ
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-gov mb-0 align-middle">
                    <thead>
                        <tr class="text-center">
                            <th rowspan="2" style="width: 55px;">ລ/ດ</th>
                            <th colspan="3" class="bg-primary bg-opacity-10">ຂໍ້ມູນຊື່ ແລະ ນາມສະກຸນ</th>
                            <th colspan="3" class="bg-info bg-opacity-10">ວັນເດືອນປີເກີດ</th>
                            <th colspan="3" class="bg-success bg-opacity-10">ທີ່ຢູ່ປັດຈຸບັນ</th>
                            <th rowspan="2" class="no-print" style="width: 120px;">ໃບຢັ້ງຢືນ</th>
                            <?php if ($currentUser): ?>
                                <th rowspan="2" class="no-print" style="width: 150px;">ແກ້ໄຂຂໍ້ມູນ (Admin)</th>
                            <?php endif; ?>
                        </tr>
                        <tr class="text-center">
                            <th>ໜ້າຊື່</th>
                            <th>ຊື່</th>
                            <th>ນາມສະກຸນ</th>
                            <th>ວັນທີ (01-31)</th>
                            <th>ເດືອນລາວ</th>
                            <th>ປີ (ຄ.ສ)</th>
                            <th>ບ້ານ</th>
                            <th>ເມືອງ</th>
                            <th>ແຂວງ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="<?= $currentUser ? 12 : 11 ?>" class="text-center py-5 text-muted">
                                    <i class="bi bi-journal-x fs-1 d-block mb-2 opacity-50"></i>
                                    ບໍ່ພົບຂໍ້ມູນນັກສຶກສາຕາມເງື່ອນໄຂທີ່ຄົ້ນຫາ
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $index => $st): ?>
                                <?php
                                $badgeClass = 'badge-prefix-thao';
                                if ($st['prefix'] === 'ນາງ') $badgeClass = 'badge-prefix-nang';
                                if ($st['prefix'] === 'ສນ') $badgeClass = 'badge-prefix-sn';
                                if ($st['prefix'] === 'ຄູບາ') $badgeClass = 'badge-prefix-khouba';
                                $fullName = $st['prefix'] . ' ' . $st['first_name'] . ' ' . $st['last_name'];
                                $dayFormatted = str_pad((string)$st['dob_day'], 2, '0', STR_PAD_LEFT);
                                ?>
                                <tr>
                                    <td class="text-center fw-semibold"><?= $index + 1 ?></td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill px-3 py-1 <?= $badgeClass ?>">
                                            <?= htmlspecialchars($st['prefix']) ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold"><?= htmlspecialchars($st['first_name']) ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($st['last_name']) ?></td>
                                    <td class="text-center fw-bold text-primary"><?= htmlspecialchars($dayFormatted) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($st['dob_month']) ?></td>
                                    <td class="text-center fw-semibold"><?= htmlspecialchars($st['dob_year']) ?></td>
                                    <td><?= htmlspecialchars($st['village']) ?></td>
                                    <td><?= htmlspecialchars($st['district']) ?></td>
                                    <td class="fw-semibold text-primary"><?= htmlspecialchars($st['province']) ?></td>
                                    <td class="text-center no-print">
                                        <a href="certificate.php?id=<?= (int)$st['id'] ?>" class="btn btn-sm btn-outline-primary" title="ພິມໃບຢັ້ງຢືນຈົບການສຶກສາ">
                                            <i class="bi bi-printer-fill me-1"></i>ພິມໃບຢັ້ງຢືນ
                                        </a>
                                    </td>
                                    <?php if ($currentUser): ?>
                                        <td class="text-center no-print">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-warning fw-semibold text-dark" onclick="openEditModal(<?= (int)$st['id'] ?>)" title="ແກ້ໄຂຂໍ້ມູນ">
                                                    <i class="bi bi-pencil-square me-1"></i>ແກ້ໄຂ
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" onclick="deleteStudent(<?= (int)$st['id'] ?>, '<?= htmlspecialchars(addslashes($fullName)) ?>')" title="ລຶບຂໍ້ມູນ">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setupDistrictProvinceSync('filter_district', 'filter_province', '-- ທຸກເມືອງ --');
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
