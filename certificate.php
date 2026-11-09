<?php
$pageTitle = 'ພິມໃບຢັ້ງຢືນຈົບການສຶກສາ (ລໍຖ້າຮັບໃບປະກາດ)';
require_once __DIR__ . '/includes/header.php';

// ດຶງລາຍຊື່ນັກສຶກສາທັງໝົດສຳລັບ Dropdown ເລືອກພິມ
$allStudents = $pdo->query("SELECT * FROM students ORDER BY id DESC")->fetchAll();

$selectedId = (int)($_GET['id'] ?? 0);
$printAll   = isset($_GET['all']) && $_GET['all'] == '1';

$certificatesToRender = [];

if ($printAll) {
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
    $stmt = $pdo->prepare("SELECT * FROM students {$whereSql} ORDER BY id ASC");
    $stmt->execute($params);
    $certificatesToRender = $stmt->fetchAll();
} else {
    if ($selectedId <= 0 && !empty($allStudents)) {
        $selectedId = (int)$allStudents[0]['id'];
    }
    if ($selectedId > 0) {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
        $stmt->execute([':id' => $selectedId]);
        $found = $stmt->fetch();
        if ($found) {
            $certificatesToRender[] = $found;
        }
    }
}

// ວັນທີອອກໃບຢັ້ງຢືນປັດຈຸບັນເປັນເດືອນລາວ
$monthNamesByIndex = array_keys($LAO_MONTHS);
$currentDayPadded = date('d');
$currentLaoMonth = $monthNamesByIndex[(int)date('n') - 1] ?? 'ມັງກອນ';
$currentYear = date('Y');
?>

<style>
.cert-page {
    width: 210mm;
    min-height: 285mm;
    margin: 0 auto 2rem auto;
    background: #ffffff;
    padding: 18mm 20mm;
    box-shadow: 0 10px 35px rgba(15, 41, 66, 0.14);
    border: 1px solid #cbd5e1;
    position: relative;
    color: #000;
    font-family: 'Phetsarath OT', 'Noto Sans Lao', serif;
    line-height: 1.9;
}

.cert-border-inner {
    border: 3px double #1a4f8b;
    padding: 12mm 14mm;
    min-height: 250mm;
    position: relative;
}

.cert-emblem {
    width: 68px;
    height: 68px;
    margin: 0 auto 6px auto;
    border-radius: 50%;
    border: 2px solid #b45309;
    background: radial-gradient(circle, #fef9c3 0%, #fde047 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #92400e;
    font-size: 2rem;
}

.photo-box {
    width: 32mm;
    height: 42mm;
    border: 1.5px dashed #475569;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: 0.78rem;
    color: #475569;
    background-color: #f8fafc;
}

.dotted-val {
    border-bottom: 1px dotted #1e293b;
    padding: 0 10px;
    font-weight: 700;
    color: #0f2942;
    display: inline-block;
}

@media (max-width: 991px) {
    .cert-page {
        width: 100%;
        padding: 15px;
        min-height: auto;
    }
    .cert-border-inner {
        padding: 15px;
        min-height: auto;
    }
}

@media print {
    @page {
        size: A4 portrait;
        margin: 8mm;
    }
    body {
        background: #fff !important;
    }
    .no-print, .navbar-gov, .footer-gov {
        display: none !important;
    }
    .cert-page {
        width: 100% !important;
        min-height: 270mm !important;
        margin: 0 !important;
        padding: 5mm !important;
        box-shadow: none !important;
        border: none !important;
        page-break-after: always;
    }
    .cert-page:last-child {
        page-break-after: auto;
    }
}
</style>

<div class="container py-4">
    <!-- ແຖບເຄື່ອງມືເລືອກນັກສຶກສາ ແລະ ສັ່ງພິມ -->
    <div class="card card-modern mb-4 no-print">
        <div class="card-body p-3 p-md-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <form method="GET" action="certificate.php" class="d-flex flex-wrap align-items-center gap-2 flex-grow-1">
                <label class="fw-bold text-dark me-1">
                    <i class="bi bi-person-badge-fill text-primary me-1"></i>ເລືອກນັກສຶກສາເພື່ອອອກໃບຢັ້ງຢືນ:
                </label>
                <select name="id" class="form-select" style="max-width: 420px;" onchange="this.form.submit()">
                    <?php foreach ($allStudents as $st): ?>
                        <?php
                        $label = sprintf(
                            '%s %s %s (ບ້ານ%s, ເມືອງ%s, ແຂວງ%s)',
                            $st['prefix'],
                            $st['first_name'],
                            $st['last_name'],
                            $st['village'],
                            $st['district'],
                            $st['province']
                        );
                        ?>
                        <option value="<?= (int)$st['id'] ?>" <?= (!$printAll && (int)$st['id'] === $selectedId) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <div class="d-flex flex-wrap gap-2">
                <?php if ($currentUser && !$printAll && $selectedId > 0): ?>
                    <button type="button" class="btn btn-warning fw-bold px-3" onclick="openEditModal(<?= $selectedId ?>)">
                        <i class="bi bi-pencil-square me-1"></i>ແກ້ໄຂຂໍ້ມູນນັກສຶກສານີ້
                    </button>
                <?php endif; ?>

                <?php if ($currentUser): ?>
                    <button type="button" class="btn btn-outline-secondary fw-semibold" data-bs-toggle="modal" data-bs-target="#settingsModal">
                        <i class="bi bi-gear-fill me-1"></i>ຕັ້ງຄ່າຊື່ສະຖາບັນ
                    </button>
                <?php endif; ?>

                <button type="button" onclick="window.print()" class="btn btn-primary fw-bold px-4 shadow-sm" style="background-color: var(--secondary-blue);">
                    <i class="bi bi-printer-fill me-2"></i>ສັ່ງພິມໃບຢັ້ງຢືນ (Print A4)
                </button>
            </div>
        </div>
    </div>

    <?php if (empty($certificatesToRender)): ?>
        <div class="alert alert-warning text-center p-5 rounded-4">
            <i class="bi bi-exclamation-circle fs-1 d-block mb-2"></i>
            ບໍ່ພົບຂໍ້ມູນນັກສຶກສາສຳລັບອອກໃບຢັ້ງຢືນ. <a href="index.php" class="fw-bold">ກັບໄປໜ້າບັນທຶກຂໍ້ມູນ</a>
        </div>
    <?php else: ?>
        <?php foreach ($certificatesToRender as $cert): ?>
            <?php
            $dayPadded = str_pad((string)$cert['dob_day'], 2, '0', STR_PAD_LEFT);
            ?>
            <div class="cert-page">
                <div class="cert-border-inner">
                    <!-- ກາໝາຍຊາດ / ຄຳຂວັນ -->
                    <div class="text-center mb-3">
                        <div class="cert-emblem">
                            <i class="bi bi-award-fill"></i>
                        </div>
                        <div class="fw-bold" style="font-size: 1.15rem;">ສາທາລະນະລັດ ປະຊາທິປະໄຕ ປະຊາຊົນລາວ</div>
                        <div class="fw-semibold" style="font-size: 1rem;">ສັນຕິພາບ ເອກະລາດ ປະຊາທິປະໄຕ ເອກະພາບ ວັດທະນາຖາວອນ</div>
                        <div style="width: 120px; height: 2px; background: #1e293b; margin: 6px auto;"></div>
                    </div>

                    <!-- ຊື່ກະຊວງ / ສະຖາບັນ ແລະ ເລກທີ -->
                    <div class="d-flex justify-content-between align-items-start mb-3" style="font-size: 0.98rem;">
                        <div>
                            <div class="fw-bold"><?= htmlspecialchars($sysSettings['ministry_name'] ?? 'ກະຊວງສຶກສາທິການ ແລະ ກິລາ') ?></div>
                            <div class="fw-bold text-primary"><?= htmlspecialchars($sysSettings['institution_name'] ?? 'ວິທະຍາໄລ ເຕັກໂນໂລຊີ ແລະ ບໍລິຫານທຸລະກິດ') ?></div>
                            <div>ຫ້ອງການວິຊາການ ແລະ ກິດຈະການນັກສຶກສາ</div>
                        </div>
                        <div class="text-end">
                            <div>ເລກທີ: <span class="fw-bold text-danger font-monospace"><?= htmlspecialchars($cert['cert_no']) ?></span>/ວຊກ</div>
                        </div>
                    </div>

                    <!-- ຫົວຂໍ້ໃບຢັ້ງຢືນ + ບ່ອນຕິດຮູບ 3x4 -->
                    <div class="row align-items-center mb-4">
                        <div class="col-3 d-flex justify-content-start">
                            <div class="photo-box">
                                <i class="bi bi-person-bounding-box fs-3 mb-1"></i>
                                <span>ຮູບຂະໜາດ 3x4</span>
                                <span style="font-size: 0.68rem;">(ປະທັບຕາ)</span>
                            </div>
                        </div>
                        <div class="col-6 text-center">
                            <h3 class="fw-bold mb-1" style="color: #0f2942; font-size: 1.65rem; letter-spacing: 0.5px;">
                                ໃບຢັ້ງຢືນຈົບການສຶກສາ
                            </h3>
                            <div class="fw-bold text-secondary" style="font-size: 1.02rem;">
                                (ສະບັບຊົ່ວຄາວ - ລໍຖ້າຮັບໃບປະກາດສະນີຍະບັດ)
                            </div>
                        </div>
                        <div class="col-3"></div>
                    </div>

                    <!-- ເນື້ອໃນການຢັ້ງຢືນ (ອີງຕາມແບບຟອມບັນທຶກ) -->
                    <div class="px-2" style="font-size: 1.08rem; text-align: justify;">
                        <p class="mb-3" style="text-indent: 2.2rem;">
                            <?= htmlspecialchars($sysSettings['director_title'] ?? 'ຜູ້ອຳນວຍການ') ?>
                            <strong><?= htmlspecialchars($sysSettings['institution_name'] ?? '') ?></strong>
                            ຂໍຢັ້ງຢືນຢ່າງເປັນທາງການວ່າ:
                        </p>

                        <div class="ps-3 pe-2 py-3 mb-3 rounded-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="row g-2 mb-2">
                                <div class="col-md-12">
                                    <span>ຊື່ ແລະ ນາມສະກຸນນັກສຶກສາ:</span>
                                    <span class="dotted-val fs-5 px-3">
                                        <?= htmlspecialchars($cert['prefix']) ?> <?= htmlspecialchars($cert['first_name']) ?> <?= htmlspecialchars($cert['last_name']) ?>
                                    </span>
                                </div>
                            </div>

                            <div class="row g-2 mb-2">
                                <div class="col-md-12">
                                    <span>ເກີດວັນທີ:</span>
                                    <span class="dotted-val px-3"><?= htmlspecialchars($dayPadded) ?></span>
                                    <span>ເດືອນ:</span>
                                    <span class="dotted-val px-3"><?= htmlspecialchars($cert['dob_month']) ?></span>
                                    <span>ປີ:</span>
                                    <span class="dotted-val px-3"><?= htmlspecialchars($cert['dob_year']) ?></span>
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-md-12">
                                    <span>ທີ່ຢູ່ປັດຈຸບັນ ບ້ານ:</span>
                                    <span class="dotted-val px-3"><?= htmlspecialchars($cert['village']) ?></span>
                                    <span>ເມືອງ:</span>
                                    <span class="dotted-val px-3"><?= htmlspecialchars($cert['district']) ?></span>
                                    <span>ແຂວງ:</span>
                                    <span class="dotted-val px-3"><?= htmlspecialchars($cert['province']) ?></span>
                                </div>
                            </div>
                        </div>

                        <p class="mb-3" style="text-indent: 2.2rem;">
                            ຜູ້ກ່ຽວໄດ້ສຶກສາຄົ້ນຄວ້າຮ່ຳຮຽນ, ເສັງຜ່ານທຸກລາຍວິຊາ ແລະ ສຳເລັດການສຶກສາຄົບຖ້ວນຕາມຫຼັກສູດທີ່
                            <?= htmlspecialchars($sysSettings['ministry_name'] ?? 'ກະຊວງສຶກສາທິການ ແລະ ກິລາ') ?> ວາງອອກທຸກປະການ ແລະ
                            <strong>ຢູ່ໃນໄລຍະລໍຖ້າຮັບໃບປະກາດສະນີຍະບັດສະບັບຈິງ</strong>.
                        </p>

                        <p class="mb-4" style="text-indent: 2.2rem;">
                            ດັ່ງນັ້ນ, ຈຶ່ງໄດ້ອອກໃບຢັ້ງຢືນຈົບການສຶກສາສະບັບນີ້ໃຫ້ຜູ້ກ່ຽວໄວ້ເປັນຫຼັກຖານ ເພື່ອນຳໃຊ້ເຂົ້າໃນການປະກອບເອກະສານສະໝັກວຽກງານ ຫຼື ສຶກສາຕໍ່ຕາມຄວາມເໝາະສົມ. ໃບຢັ້ງຢືນສະບັບນີ້ມີຄຸນຄ່າທາງລັດຖະການຈົນກວ່າຈະໄດ້ຮັບໃບປະກາດສະນີຍະບັດຕົ້ນສະບັບ.
                        </p>
                    </div>

                    <!-- ວັນທີ ແລະ ລາຍເຊັນ -->
                    <div class="text-end pe-3 mb-2" style="font-size: 1rem;">
                        ທີ່ <?= htmlspecialchars($sysSettings['issue_place'] ?? 'ນະຄອນຫຼວງວຽງຈັນ') ?>,
                        ວັນທີ <?= htmlspecialchars($currentDayPadded) ?>
                        ເດືອນ <?= htmlspecialchars($currentLaoMonth) ?>
                        ປີ <?= htmlspecialchars($currentYear) ?>
                    </div>

                    <div class="row text-center mt-4" style="font-size: 1rem;">
                        <div class="col-6">
                            <div class="fw-bold">ຫົວໜ້າຫ້ອງການວິຊາການ</div>
                            <div style="height: 75px;"></div>
                            <div class="fw-bold"><?= htmlspecialchars($sysSettings['registrar_name'] ?? 'ອຈ. ສົມພອນ ໄຊຍະວົງ') ?></div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold"><?= htmlspecialchars($sysSettings['director_title'] ?? 'ຜູ້ອຳນວຍການ') ?></div>
                            <div style="height: 75px;"></div>
                            <div class="fw-bold"><?= htmlspecialchars($sysSettings['director_name'] ?? 'ປອ. ຄຳພັນ ວົງສາວັດ') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
