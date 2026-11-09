<?php
$pageTitle = 'ເຂົ້າສູ່ລະບົບ (Login Form) | ລະບົບອອກໃບຢັ້ງຢືນຈົບການສຶກສາ';
require_once __DIR__ . '/includes/header.php';

$redirect = trim($_GET['redirect'] ?? 'report.php');
// ປ້ອງກັນ Open Redirect (ອະນຸຍາດສະເພາະໜ້າພາຍໃນລະບົບເທົ່ານັ້ນ)
if (preg_match('/^(https?:)?\/\//i', $redirect) || strpos($redirect, '..') !== false) {
    $redirect = 'report.php';
}
$editId = (int)($_GET['edit_id'] ?? 0);
if ($editId > 0) {
    $sep = (strpos($redirect, '?') !== false) ? '&' : '?';
    $redirect .= $sep . 'edit_id=' . $editId;
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card card-modern overflow-hidden">
                <div class="card-header-gov text-center py-4">
                    <div class="brand-emblem mx-auto mb-2" style="width: 60px; height: 60px; font-size: 1.8rem;">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <h4 class="fw-bold mb-1">ຟອມເຂົ້າສູ່ລະບົບ (Login Form)</h4>
                    <div class="small text-white-50">
                        ເຂົ້າສູ່ລະບົບເພື່ອຈັດການ, ແກ້ໄຂຂໍ້ມູນນັກສຶກສາ ແລະ ຕັ້ງຄ່າໃບຢັ້ງຢືນ
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    <?php if ($currentUser): ?>
                        <div class="alert alert-success rounded-3 text-center">
                            <i class="bi bi-check-circle-fill fs-3 d-block mb-2"></i>
                            <div>ທ່ານໄດ້ເຂົ້າສູ່ລະບົບແລ້ວໃນຊື່: <strong><?= htmlspecialchars($currentUser['full_name']) ?></strong></div>
                            <div class="mt-3 d-flex justify-content-center gap-2">
                                <a href="<?= htmlspecialchars($redirect) ?>" class="btn btn-primary btn-sm px-3 fw-bold">
                                    <i class="bi bi-pencil-square me-1"></i>ໄປໜ້າແກ້ໄຂຂໍ້ມູນ
                                </a>
                                <a href="logout.php" class="btn btn-outline-danger btn-sm px-3">
                                    ອອກຈາກລະບົບ
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form id="loginForm" autocomplete="off">
                        <input type="hidden" name="action" value="login">

                        <div class="mb-3">
                            <label class="form-label" for="username">ຊື່ຜູ້ໃຊ້ລະບົບ (Username) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-person-fill text-primary"></i></span>
                                <input type="text" class="form-control" name="username" id="username" placeholder="ປ້ອນຊື່ຜູ້ໃຊ້..." required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="password">ລະຫັດຜ່ານ (Password) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-key-fill text-primary"></i></span>
                                <input type="password" class="form-control" name="password" id="password" placeholder="ປ້ອນລະຫັດຜ່ານ..." required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePwdBtn" title="ສະແດງ/ເຊື່ອງລະຫັດຜ່ານ">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary py-2 fw-bold fs-6 rounded-3 shadow-sm" style="background-color: var(--secondary-blue);">
                                <i class="bi bi-box-arrow-in-right me-2"></i>ເຂົ້າສູ່ລະບົບ ເພື່ອແກ້ໄຂຂໍ້ມູນ
                            </button>
                        </div>
                    </form>

                    <!-- ກ່ອງແນະນຳຄວາມປອດໄພ -->
                    <div class="bg-light border rounded-3 p-3 mt-4 small text-muted">
                        <i class="bi bi-shield-lock-fill text-success me-1"></i>
                        <strong>ລະບົບຮັກສາຄວາມປອດໄພ:</strong> ສະເພາະຜູ້ດູແລລະບົບ (Admin) ທີ່ມີສິດເຂົ້າແກ້ໄຂຂໍ້ມູນເທົ່ານັ້ນ. (ລັອກອັດຕະໂນມັດເມື່ອປ້ອນລະຫັດຜິດເກີນ 5 ຄັ້ງ)
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePwdBtn = document.getElementById('togglePwdBtn');
    const pwdInput = document.getElementById('password');
    if (togglePwdBtn && pwdInput) {
        togglePwdBtn.addEventListener('click', function() {
            const isPwd = pwdInput.type === 'password';
            pwdInput.type = isPwd ? 'text' : 'password';
            this.innerHTML = isPwd ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
        });
    }

    const fillDemoBtn = document.getElementById('fillDemoBtn');
    if (fillDemoBtn) {
        fillDemoBtn.addEventListener('click', function() {
            document.getElementById('username').value = 'admin';
            document.getElementById('password').value = 'admin123';
        });
    }

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
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
                        title: 'ເຂົ້າສູ່ລະບົບສຳເລັດ!',
                        text: result.message,
                        timer: 1400,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = <?= json_encode($redirect) ?>;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'ເຂົ້າສູ່ລະບົບບໍ່ສຳເລັດ!',
                        text: result.message,
                        confirmButtonColor: '#1a4f8b',
                        confirmButtonText: 'ລອງໃໝ່'
                    });
                }
            } catch (err) {
                Swal.fire('ຜິດພາດ!', 'ບໍ່ສາມາດເຊື່ອມຕໍ່ກັບລະບົບໄດ້', 'error');
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

