<?php
$pageTitle = 'ຟອມບັນທຶກຂໍ້ມູນນັກສຶກສາ | ລະບົບອອກໃບຢັ້ງຢືນຈົບການສຶກສາ ລໍຖ້າຮັບໃບປະກາດ';
require_once __DIR__ . '/includes/header.php';

// ດຶງສະຖິຕິພາບລວມ
$stats = [
    'total'  => (int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn(),
    'thao'   => (int)$pdo->query("SELECT COUNT(*) FROM students WHERE prefix = 'ທ້າວ'")->fetchColumn(),
    'nang'   => (int)$pdo->query("SELECT COUNT(*) FROM students WHERE prefix = 'ນາງ'")->fetchColumn(),
    'sn'     => (int)$pdo->query("SELECT COUNT(*) FROM students WHERE prefix = 'ສນ'")->fetchColumn(),
    'khouba' => (int)$pdo->query("SELECT COUNT(*) FROM students WHERE prefix = 'ຄູບາ'")->fetchColumn(),
];

// ດຶງຂໍ້ມູນນັກສຶກສາທັງໝົດມາສະແດງໃນຕາຕະລາງ
$search = trim($_GET['q'] ?? '');
$whereSql = '';
$params = [];
if ($search !== '') {
    $whereSql = "WHERE first_name LIKE :q OR last_name LIKE :q OR village LIKE :q OR district LIKE :q OR province LIKE :q";
    $params[':q'] = '%' . $search . '%';
}

$stmtStudents = $pdo->prepare("SELECT * FROM students {$whereSql} ORDER BY id DESC");
$stmtStudents->execute($params);
$students = $stmtStudents->fetchAll();
?>

<div class="container py-4">
    <!-- ແຖບສະແດງສິດຜູ້ໃຊ້ປັດຈຸບັນ -->
    <?php if ($currentUser): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex flex-wrap justify-content-between align-items-center mb-4 py-2 px-3">
            <div>
                <i class="bi bi-shield-check-fill me-2 fs-5 align-middle"></i>
                <strong>ສະຖານະ Admin:</strong> ທ່ານເຂົ້າສູ່ລະບົບໃນຊື່ <b><?= htmlspecialchars($currentUser['full_name']) ?></b> — ສາມາດບັນທຶກ, <strong>ແກ້ໄຂຂໍ້ມູນ</strong> ແລະ ລຶບຂໍ້ມູນນັກສຶກສາໄດ້.
            </div>
            <a href="logout.php" class="btn btn-sm btn-outline-danger fw-semibold my-1">
                <i class="bi bi-box-arrow-right me-1"></i>ອອກຈາກລະບົບ
            </a>
        </div>
    <?php else: ?>
        <div class="alert alert-primary border-0 shadow-sm rounded-3 d-flex flex-wrap justify-content-between align-items-center mb-4 py-2 px-3">
            <div>
                <i class="bi bi-info-circle-fill me-2 fs-5 align-middle"></i>
                <strong>ສະຖານະຜູ້ໃຊ້ທົ່ວໄປ (User):</strong> ສາມາດ<strong>ບັນທຶກຂໍ້ມູນນັກສຶກສາໄດ້ຢ່າງດຽວ</strong>. ຖ້າຕ້ອງການແກ້ໄຂຂໍ້ມູນ ກະລຸນາເຂົ້າສູ່ລະບົບ Admin.
            </div>
            <a href="login.php?redirect=index.php" class="btn btn-sm btn-warning fw-bold my-1">
                <i class="bi bi-shield-lock-fill me-1"></i>Login Admin ເພື່ອແກ້ໄຂຂໍ້ມູນ
            </a>
        </div>
    <?php endif; ?>

    <!-- ບັດສະຫຼຸບຂໍ້ມູນ -->
    <div class="row g-3 mb-4 row-cols-2 row-cols-md-3 row-cols-xl-5">
        <div class="col">
            <div class="card stat-card h-100 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">ນັກສຶກສາທັງໝົດ</div>
                        <div class="fs-4 fw-bold text-dark"><?= number_format($stats['total']) ?> <span class="fs-6 fw-normal text-muted">ຄົນ</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#e0f2fe; color:#0369a1;">
                        <i class="bi bi-person-standing"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">ໜ້າຊື່: ທ້າວ</div>
                        <div class="fs-4 fw-bold" style="color:#0369a1;"><?= number_format($stats['thao']) ?> <span class="fs-6 fw-normal text-muted">ຄົນ</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#fce7f3; color:#be185d;">
                        <i class="bi bi-person-standing-dress"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">ໜ້າຊື່: ນາງ</div>
                        <div class="fs-4 fw-bold" style="color:#be185d;"><?= number_format($stats['nang']) ?> <span class="fs-6 fw-normal text-muted">ຄົນ</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#fef3c7; color:#b45309;">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">ໜ້າຊື່: ສນ</div>
                        <div class="fs-4 fw-bold" style="color:#b45309;"><?= number_format($stats['sn']) ?> <span class="fs-6 fw-normal text-muted">ອົງ</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#ffedd5; color:#c2410c;">
                        <i class="bi bi-person-vcard-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">ໜ້າຊື່: ຄູບາ</div>
                        <div class="fs-4 fw-bold" style="color:#c2410c;"><?= number_format($stats['khouba']) ?> <span class="fs-6 fw-normal text-muted">ອົງ</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ຟອມບັນທຶກຂໍ້ມູນນັກສຶກສາອອນລາຍ -->
    <div class="card card-modern mb-4">
        <div class="card-header-gov d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="mb-1 fw-bold">
                    <i class="bi bi-file-earmark-plus-fill text-warning me-2"></i>ຟອມບັນທຶກຂໍ້ມູນນັກສຶກສາ (ອອກໃບຢັ້ງຢືນຈົບການສຶກສາ ລໍຖ້າຮັບໃບປະກາດ)
                </h5>
                <div class="small text-white-50">
                    ກະລຸນາປ້ອນຂໍ້ມູນໃຫ້ຄົບຖ້ວນຕາມແບບຟອມລຸ່ມນີ້
                </div>
            </div>
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-wifi me-1"></i>ລະບົບອອນລາຍ (Online Form)
            </span>
        </div>

        <div class="card-body p-4">
            <form id="createStudentForm" autocomplete="off">
                <input type="hidden" name="action" value="create_student">

                <!-- ໝວດທີ 1: ໜ້າຊື່, ຊື່ ແລະ ນາມສະກຸນ -->
                <div class="section-badge">
                    <i class="bi bi-person-vcard-fill"></i>
                    <span>1. ຂໍ້ມູນຊື່ ແລະ ນາມສະກຸນ (ເລືອກໜ້າຊື່: ທ້າວ, ນາງ, ສນ ແລະ ຄູບາ)</span>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label" for="prefix">ໜ້າຊື່ <span class="text-danger">*</span></label>
                        <select class="form-select" name="prefix" id="prefix" required>
                            <option value="">-- ເລືອກໜ້າຊື່ --</option>
                            <?php foreach ($LAO_PREFIXES as $val => $label): ?>
                                <option value="<?= htmlspecialchars($val) ?>"><?= htmlspecialchars($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <label class="form-label" for="first_name">ຊື່ <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person-fill text-primary"></i></span>
                            <input type="text" class="form-control" name="first_name" id="first_name" placeholder="ປ້ອນຊື່ນັກສຶກສາ..." required>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <label class="form-label" for="last_name">ນາມສະກຸນ <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person-lines-fill text-primary"></i></span>
                            <input type="text" class="form-control" name="last_name" id="last_name" placeholder="ປ້ອນນາມສະກຸນ..." required>
                        </div>
                    </div>
                </div>

                <!-- ໝວດທີ 2: ວັນເດືອນປີເກີດ -->
                <div class="section-badge">
                    <i class="bi bi-calendar3"></i>
                    <span>2. ວັນເດືອນປີເກີດ (ວັນທີ 01-09 ມີເລກ 0, ເດືອນລາວ, ເລືອກປີເປັນຕົວເລກ)</span>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label" for="dob_day">ວັນທີເກີດ (ຕົວເລກ 01 - 31) <span class="text-danger">*</span></label>
                        <select class="form-select" name="dob_day" id="dob_day" required>
                            <option value="">-- ເລືອກວັນທີ (01 - 31) --</option>
                            <?php foreach ($LAO_DAYS as $dayVal => $dayLabel): ?>
                                <option value="<?= htmlspecialchars($dayVal) ?>">ວັນທີ <?= htmlspecialchars($dayLabel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="dob_month">ເດືອນເກີດ (ເດືອນລາວ) <span class="text-danger">*</span></label>
                        <select class="form-select" name="dob_month" id="dob_month" required>
                            <option value="">-- ເລືອກເດືອນລາວ --</option>
                            <?php foreach ($LAO_MONTHS as $mVal => $mLabel): ?>
                                <option value="<?= htmlspecialchars($mVal) ?>"><?= htmlspecialchars($mLabel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="dob_year">ປີເກີດ (ຕົວເລກ) <span class="text-danger">*</span></label>
                        <select class="form-select" name="dob_year" id="dob_year" required>
                            <option value="">-- ເລືອກປີເກີດ (ຄ.ສ) --</option>
                            <?php foreach ($LAO_YEARS as $yVal => $yLabel): ?>
                                <option value="<?= htmlspecialchars($yVal) ?>" <?= $yVal === '2003' ? 'selected' : '' ?>><?= htmlspecialchars($yLabel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- ໝວດທີ 3: ທີ່ຢູ່ປັດຈຸບັນ -->
                <div class="section-badge">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>3. ທີ່ຢູ່ປັດຈຸບັນ (ປ້ອນບ້ານ, ເລືອກເມືອງ, ເລືອກແຂວງ)</span>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label" for="village">ບ້ານປັດຈຸບັນ (ປ້ອນບ້ານ) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-house-door-fill text-primary"></i></span>
                            <input type="text" class="form-control" name="village" id="village" placeholder="ປ້ອນຊື່ບ້ານປັດຈຸບັນ..." required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="district">ເມືອງປັດຈຸບັນ (ເລືອກເມືອງ) <span class="text-danger">*</span></label>
                        <select class="form-select" name="district" id="district" required>
                            <option value="">-- ເລືອກເມືອງ --</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="province">ແຂວງປັດຈຸບັນ (ເລືອກແຂວງ) <span class="text-danger">*</span></label>
                        <select class="form-select" name="province" id="province" required>
                            <option value="">-- ເລືອກແຂວງ --</option>
                            <?php foreach (array_keys($LAO_PROVINCES_DISTRICTS) as $prov): ?>
                                <option value="<?= htmlspecialchars($prov) ?>"><?= htmlspecialchars($prov) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex flex-wrap justify-content-end gap-2">
                    <button type="reset" class="btn btn-outline-secondary px-4 py-2 rounded-3" id="btnResetForm">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>ລ້າງຂໍ້ມູນຟອມ
                    </button>
                    <button type="submit" class="btn btn-primary px-5 py-2 rounded-3 fw-bold shadow-sm" style="background-color: var(--secondary-blue);">
                        <i class="bi bi-save2-fill me-2"></i>ບັນທຶກຂໍ້ມູນນັກສຶກສາ
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ຕາຕະລາງສະແດງຂໍ້ມູນລາຍງານອີງຕາມແບບຟອມບັນທຶກ -->
    <div class="card card-modern">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-table text-primary me-2"></i>ລາຍການຂໍ້ມູນນັກສຶກສາທີ່ບັນທຶກແລ້ວ (<?= count($students) ?> ລາຍການ)
                </h5>
                <div class="small text-muted">
                    <?php if ($currentUser): ?>
                        ທ່ານເຂົ້າສູ່ລະບົບ Admin ແລ້ວ: ສາມາດກົດປຸ່ມ <strong>ແກ້ໄຂ</strong> ຫຼື <strong>ລຶບ</strong> ຂໍ້ມູນໄດ້ທັນທີ
                    <?php else: ?>
                        ຜູ້ໃຊ້ທົ່ວໄປສາມາດບັນທຶກຂໍ້ມູນໄດ້ຢ່າງດຽວ (ຖ້າຕ້ອງການແກ້ໄຂຂໍ້ມູນ ກະລຸນາ <a href="login.php?redirect=index.php" class="fw-bold">Login Admin</a>)
                    <?php endif; ?>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <form class="d-flex gap-2" method="GET" action="index.php">
                    <div class="input-group input-group-sm" style="min-width: 230px;">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="ຄົ້ນຫາຊື່, ບ້ານ, ເມືອງ, ແຂວງ...">
                        <?php if ($search !== ''): ?>
                            <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
                        <?php endif; ?>
                    </div>
                </form>

                <a href="export_excel.php?q=<?= urlencode($search) ?>" class="btn btn-success btn-sm fw-bold px-3 py-2 rounded-3 shadow-sm">
                    <i class="bi bi-file-earmark-excel-fill me-1"></i>ສົ່ງອອກ Excel (.xlsx)
                </a>

                <a href="report.php" class="btn btn-outline-primary btn-sm fw-bold px-3 py-2 rounded-3">
                    <i class="bi bi-bar-chart-line-fill me-1"></i>ໜ້າລາຍງານລະອຽດ
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-gov mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 55px;">ລ/ດ</th>
                            <th class="text-center">ໜ້າຊື່</th>
                            <th>ຊື່</th>
                            <th>ນາມສະກຸນ</th>
                            <th>ວັນເດືອນປີເກີດ</th>
                            <th>ບ້ານປັດຈຸບັນ</th>
                            <th>ເມືອງ</th>
                            <th>ແຂວງ</th>
                            <th class="text-center">ໃບຢັ້ງຢືນ</th>
                            <?php if ($currentUser): ?>
                                <th class="text-center" style="width: 150px;">ແກ້ໄຂຂໍ້ມູນ (Admin)</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="<?= $currentUser ? 10 : 9 ?>" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                    ຍັງບໍ່ມີຂໍ້ມູນນັກສຶກສາທີ່ຄົ້ນຫາ
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $idx => $st): ?>
                                <?php
                                $badgeClass = 'badge-prefix-thao';
                                if ($st['prefix'] === 'ນາງ') $badgeClass = 'badge-prefix-nang';
                                if ($st['prefix'] === 'ສນ') $badgeClass = 'badge-prefix-sn';
                                if ($st['prefix'] === 'ຄູບາ') $badgeClass = 'badge-prefix-khouba';
                                $fullName = $st['prefix'] . ' ' . $st['first_name'] . ' ' . $st['last_name'];
                                $dobFormatted = str_pad((string)$st['dob_day'], 2, '0', STR_PAD_LEFT) . ' ' . $st['dob_month'] . ' ' . $st['dob_year'];
                                ?>
                                <tr>
                                    <td class="text-center fw-semibold text-muted"><?= $idx + 1 ?></td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill px-3 py-1 <?= $badgeClass ?>">
                                            <?= htmlspecialchars($st['prefix']) ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($st['first_name']) ?></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($st['last_name']) ?></td>
                                    <td>
                                        <i class="bi bi-calendar2-week text-primary me-1"></i>
                                        <?= htmlspecialchars($dobFormatted) ?>
                                    </td>
                                    <td><?= htmlspecialchars($st['village']) ?></td>
                                    <td><?= htmlspecialchars($st['district']) ?></td>
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold">
                                            <?= htmlspecialchars($st['province']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="certificate.php?id=<?= (int)$st['id'] ?>" class="btn btn-sm btn-outline-primary" title="ພິມໃບຢັ້ງຢືນຈົບການສຶກສາ">
                                            <i class="bi bi-printer-fill me-1"></i>ພິມໃບຢັ້ງຢືນ
                                        </a>
                                    </td>
                                    <?php if ($currentUser): ?>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
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
    // ເຊື່ອມໂຍງ Dropdown ເລືອກເມືອງ ແລະ ເລືອກແຂວງ ໃນຟອມບັນທຶກຫຼັກ
    const createLocationSync = setupDistrictProvinceSync('district', 'province');

    const btnResetForm = document.getElementById('btnResetForm');
    if (btnResetForm && createLocationSync) {
        btnResetForm.addEventListener('click', () => {
            setTimeout(() => createLocationSync.populateDistricts('', ''), 10);
        });
    }

    // ຈັດການການສົ່ງຟອມບັນທຶກຂໍ້ມູນນັກສຶກສາດ້ວຍ AJAX + SweetAlert2
    const createForm = document.getElementById('createStudentForm');
    if (createForm) {
        createForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            try {
                const resp = await fetch('api.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await resp.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'ບັນທຶກຂໍ້ມູນສຳເລັດ!',
                        text: result.message,
                        confirmButtonColor: '#1a4f8b',
                        confirmButtonText: 'ຕົກລົງ'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'ກະລຸນາກວດສອບຂໍ້ມູນ!',
                        text: result.message,
                        confirmButtonColor: '#1a4f8b',
                        confirmButtonText: 'ຕົກລົງ'
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'ເກີດຂໍ້ຜິດພາດ!',
                    text: 'ບໍ່ສາມາດເຊື່ອມຕໍ່ກັບລະບົບຖານຂໍ້ມູນໄດ້',
                    confirmButtonText: 'ຕົກລົງ'
                });
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
