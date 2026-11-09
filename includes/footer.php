<!-- Modal ແກ້ໄຂຂໍ້ມູນນັກສຶກສາ (Edit Student Modal - ສະເພາະ Admin) -->
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header card-header-gov">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2 text-warning"></i>ແກ້ໄຂຂໍ້ມູນນັກສຶກສາ (Admin)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editStudentForm">
                <input type="hidden" name="action" value="update_student">
                <input type="hidden" name="id" id="edit_id">

                <div class="modal-body p-4">
                    <!-- ໝວດທີ 1: ຂໍ້ມູນຊື່ ແລະ ນາມສະກຸນ -->
                    <div class="section-badge">
                        <i class="bi bi-person-vcard-fill"></i>
                        <span>1. ຂໍ້ມູນຊື່ ແລະ ນາມສະກຸນ (ເລືອກໜ້າຊື່: ທ້າວ, ນາງ, ສນ ແລະ ຄູບາ)</span>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">ໜ້າຊື່ <span class="text-danger">*</span></label>
                            <select class="form-select" name="prefix" id="edit_prefix" required>
                                <?php foreach ($LAO_PREFIXES as $val => $label): ?>
                                    <option value="<?= htmlspecialchars($val) ?>"><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ຊື່ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="first_name" id="edit_first_name" placeholder="ປ້ອນຊື່..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ນາມສະກຸນ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="last_name" id="edit_last_name" placeholder="ປ້ອນນາມສະກຸນ..." required>
                        </div>
                    </div>

                    <!-- ໝວດທີ 2: ວັນເດືອນປີເກີດ -->
                    <div class="section-badge">
                        <i class="bi bi-calendar-event-fill"></i>
                        <span>2. ວັນເດືອນປີເກີດ (ວັນທີ 01-09 ມີເລກ 0, ເດືອນລາວ, ປີຕົວເລກ)</span>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">ວັນທີເກີດ (01 - 31) <span class="text-danger">*</span></label>
                            <select class="form-select" name="dob_day" id="edit_dob_day" required>
                                <?php foreach ($LAO_DAYS as $dayVal => $dayLabel): ?>
                                    <option value="<?= htmlspecialchars($dayVal) ?>">ວັນທີ <?= htmlspecialchars($dayLabel) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ເດືອນເກີດ (ເດືອນລາວ) <span class="text-danger">*</span></label>
                            <select class="form-select" name="dob_month" id="edit_dob_month" required>
                                <?php foreach ($LAO_MONTHS as $mVal => $mLabel): ?>
                                    <option value="<?= htmlspecialchars($mVal) ?>"><?= htmlspecialchars($mLabel) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ປີເກີດ (ຕົວເລກ) <span class="text-danger">*</span></label>
                            <select class="form-select" name="dob_year" id="edit_dob_year" required>
                                <?php foreach ($LAO_YEARS as $yVal => $yLabel): ?>
                                    <option value="<?= htmlspecialchars($yVal) ?>"><?= htmlspecialchars($yLabel) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- ໝວດທີ 3: ທີ່ຢູ່ປັດຈຸບັນ -->
                    <div class="section-badge">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>3. ທີ່ຢູ່ປັດຈຸບັນ (ປ້ອນບ້ານ, ເລືອກເມືອງ, ເລືອກແຂວງ)</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">ບ້ານປັດຈຸບັນ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="village" id="edit_village" placeholder="ປ້ອນຊື່ບ້ານ..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ເລືອກເມືອງ <span class="text-danger">*</span></label>
                            <select class="form-select" name="district" id="edit_district" required>
                                <option value="">-- ເລືອກເມືອງ --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ເລືອກແຂວງ <span class="text-danger">*</span></label>
                            <select class="form-select" name="province" id="edit_province" required>
                                <option value="">-- ເລືອກແຂວງ --</option>
                                <?php foreach (array_keys($LAO_PROVINCES_DISTRICTS) as $prov): ?>
                                    <option value="<?= htmlspecialchars($prov) ?>"><?= htmlspecialchars($prov) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0 rounded-bottom-4 px-4 py-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>ຍົກເລີກ
                    </button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="bi bi-check2-circle me-1"></i>ບັນທຶກການແກ້ໄຂ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal ຕັ້ງຄ່າຫົວໃບຢັ້ງຢືນຈົບການສຶກສາ -->
<div class="modal fade" id="settingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header card-header-gov">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-gear-fill me-2 text-warning"></i>ຕັ້ງຄ່າຂໍ້ມູນສະຖາບັນ (ໃບຢັ້ງຢືນ)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="settingsForm">
                <input type="hidden" name="action" value="update_settings">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">ກະຊວງ / ອົງການຂັ້ນເທິງ</label>
                        <input type="text" class="form-control" name="ministry_name" value="<?= htmlspecialchars($sysSettings['ministry_name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ຊື່ສະຖາບັນການສຶກສາ / ວິທະຍາໄລ / ມະຫາວິທະຍາໄລ</label>
                        <input type="text" class="form-control" name="institution_name" value="<?= htmlspecialchars($sysSettings['institution_name'] ?? '') ?>" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">ຕຳແໜ່ງຜູ້ລົງນາມ</label>
                            <input type="text" class="form-control" name="director_title" value="<?= htmlspecialchars($sysSettings['director_title'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ຊື່ຜູ້ອຳນວຍການ / ອະທິການບໍດີ</label>
                            <input type="text" class="form-control" name="director_name" value="<?= htmlspecialchars($sysSettings['director_name'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">ຫົວໜ້າຫ້ອງການວິຊາການ</label>
                            <input type="text" class="form-control" name="registrar_name" value="<?= htmlspecialchars($sysSettings['registrar_name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ສະຖານທີ່ອອກໃບຢັ້ງຢືນ</label>
                            <input type="text" class="form-control" name="issue_place" value="<?= htmlspecialchars($sysSettings['issue_place'] ?? '') ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 rounded-bottom-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ປິດ</button>
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="bi bi-save me-1"></i>ບັນທຶກການຕັ້ງຄ່າ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal ປ່ຽນລະຫັດຜ່ານ Admin -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header card-header-gov">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-key-fill me-2 text-warning"></i>ປ່ຽນລະຫັດຜ່ານ Admin
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="changePasswordForm">
                <input type="hidden" name="action" value="change_password">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">ລະຫັດຜ່ານປັດຈຸບັນ <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">ລະຫັດຜ່ານໃໝ່ (ຢ່າງໜ້ອຍ 6 ຕົວອັກສອນ) <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="new_password" minlength="6" required>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 rounded-bottom-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ຍົກເລີກ</button>
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="bi bi-shield-check me-1"></i>ບັນທຶກລະຫັດຜ່ານໃໝ່
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer-gov py-3 mt-5 no-print">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center small gap-2">
        <div>
            <i class="bi bi-mortarboard-fill text-warning me-1"></i>
            <strong>ລະບົບຊອບແວອອກໃບຢັ້ງຢືນຈົບການສຶກສາ ລໍຖ້າຮັບໃບປະກາດ (Online System)</strong>
        </div>
        <div>
            ພັດທະນາດ້ວຍ PHP + SQLite + Bootstrap 5 + SweetAlert2 | &copy; <?= date('Y') ?>
        </div>
    </div>
</footer>

<!-- Bootstrap 5.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // ຂໍ້ມູນແຂວງ ແລະ ເມືອງທັງໝົດ ໃນ ສປປ ລາວ
    const LAO_PROVINCES_DISTRICTS = <?= json_encode($LAO_PROVINCES_DISTRICTS, JSON_UNESCAPED_UNICODE) ?>;
    const IS_LOGGED_IN = <?= isLoggedIn() ? 'true' : 'false' ?>;

    /**
     * ຟັງຊັນເຊື່ອມໂຍງ Dropdown ເລືອກເມືອງ ແລະ ເລືອກແຂວງ ແບບ 2 ທາງ (Two-way Sync)
     */
    function setupDistrictProvinceSync(districtSelectId, provinceSelectId, allowAllText = '-- ເລືອກເມືອງ --') {
        const districtEl = document.getElementById(districtSelectId);
        const provinceEl = document.getElementById(provinceSelectId);
        if (!districtEl || !provinceEl) return;

        function populateDistricts(selectedProvince, selectedDistrict = '') {
            districtEl.innerHTML = '';
            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = allowAllText;
            districtEl.appendChild(defaultOpt);

            if (selectedProvince && LAO_PROVINCES_DISTRICTS[selectedProvince]) {
                LAO_PROVINCES_DISTRICTS[selectedProvince].forEach(dist => {
                    const opt = document.createElement('option');
                    opt.value = dist;
                    opt.dataset.province = selectedProvince;
                    opt.textContent = dist;
                    if (dist === selectedDistrict) opt.selected = true;
                    districtEl.appendChild(opt);
                });
            } else {
                Object.keys(LAO_PROVINCES_DISTRICTS).forEach(prov => {
                    const group = document.createElement('optgroup');
                    group.label = 'ແຂວງ/ນະຄອນ: ' + prov;
                    LAO_PROVINCES_DISTRICTS[prov].forEach(dist => {
                        const opt = document.createElement('option');
                        opt.value = dist;
                        opt.dataset.province = prov;
                        opt.textContent = dist + ' (' + prov + ')';
                        if (dist === selectedDistrict) opt.selected = true;
                        group.appendChild(opt);
                    });
                    districtEl.appendChild(group);
                });
            }
        }

        provinceEl.addEventListener('change', function() {
            const currentDist = districtEl.value;
            const prov = this.value;
            const keepDist = (prov && LAO_PROVINCES_DISTRICTS[prov] && LAO_PROVINCES_DISTRICTS[prov].includes(currentDist))
                ? currentDist
                : '';
            populateDistricts(prov, keepDist);
        });

        districtEl.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const distVal = this.value;
            if (!distVal) return;

            let matchedProv = selectedOption ? selectedOption.dataset.province : '';
            if (!matchedProv) {
                for (const [prov, dists] of Object.entries(LAO_PROVINCES_DISTRICTS)) {
                    if (dists.includes(distVal)) {
                        matchedProv = prov;
                        break;
                    }
                }
            }
            if (matchedProv && provinceEl.value !== matchedProv) {
                provinceEl.value = matchedProv;
                populateDistricts(matchedProv, distVal);
            }
        });

        populateDistricts(provinceEl.value, districtEl.dataset.initialValue || '');

        return { populateDistricts };
    }

    const editLocationSync = setupDistrictProvinceSync('edit_district', 'edit_province');

    /**
     * ເປີດຟອມແກ້ໄຂຂໍ້ມູນນັກສຶກສາ (ສະເພາະ Admin ທີ່ເຂົ້າສູ່ລະບົບແລ້ວ)
     */
    async function openEditModal(studentId) {
        if (!IS_LOGGED_IN) {
            const currentUrl = encodeURIComponent(window.location.pathname + window.location.search);
            Swal.fire({
                icon: 'warning',
                title: 'ສະເພາະຜູ້ດູແລລະບົບ (Admin)!',
                html: 'ຜູ້ໃຊ້ທົ່ວໄປສາມາດບັນທຶກຂໍ້ມູນໄດ້ຢ່າງດຽວ.<br>ກະລຸນາ <b>Login Admin</b> ເພື່ອແກ້ໄຂຂໍ້ມູນ.',
                showCancelButton: true,
                confirmButtonColor: '#1a4f8b',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="bi bi-box-arrow-in-right me-1"></i>ໄປໜ້າ Login Admin',
                cancelButtonText: 'ຍົກເລີກ'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `login.php?redirect=${currentUrl}&edit_id=${studentId}`;
                }
            });
            return;
        }

        try {
            const resp = await fetch(`api.php?action=get_student&id=${studentId}`);
            const data = await resp.json();
            if (!data.success) {
                Swal.fire('ຜິດພາດ!', data.message || 'ບໍ່ສາມາດດຶງຂໍ້ມູນໄດ້', 'error');
                return;
            }

            const st = data.student;
            document.getElementById('edit_id').value = st.id;
            document.getElementById('edit_prefix').value = st.prefix;
            document.getElementById('edit_first_name').value = st.first_name;
            document.getElementById('edit_last_name').value = st.last_name;
            document.getElementById('edit_dob_day').value = String(st.dob_day).padStart(2, '0');
            document.getElementById('edit_dob_month').value = st.dob_month;
            document.getElementById('edit_dob_year').value = st.dob_year;
            document.getElementById('edit_village').value = st.village;
            document.getElementById('edit_province').value = st.province;

            if (editLocationSync) {
                editLocationSync.populateDistricts(st.province, st.district);
            }

            const modal = new bootstrap.Modal(document.getElementById('editStudentModal'));
            modal.show();
        } catch (err) {
            Swal.fire('ຜິດພາດ!', 'ບໍ່ສາມາດເຊື່ອມຕໍ່ກັບເຊີບເວີໄດ້', 'error');
        }
    }

    /**
     * ບັນທຶກການແກ້ໄຂຂໍ້ມູນນັກສຶກສາ
     */
    const editStudentForm = document.getElementById('editStudentForm');
    if (editStudentForm) {
        editStudentForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            try {
                const resp = await fetch('api.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await resp.json();

                if (result.success) {
                    const modalEl = document.getElementById('editStudentModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'ແກ້ໄຂຂໍ້ມູນສຳເລັດ!',
                        text: result.message,
                        confirmButtonColor: '#1a4f8b',
                        confirmButtonText: 'ຕົກລົງ'
                    }).then(() => {
                        const url = new URL(window.location.href);
                        url.searchParams.delete('edit_id');
                        window.location.href = url.toString();
                    });
                } else {
                    Swal.fire('ແຈ້ງເຕືອນ!', result.message, 'warning');
                }
            } catch (err) {
                Swal.fire('ຜິດພາດ!', 'ເກີດຂໍ້ຜິດພາດໃນການບັນທຶກຂໍ້ມູນ', 'error');
            }
        });
    }

    /**
     * ລຶບຂໍ້ມູນນັກສຶກສາ (ສະເພາະ Admin)
     */
    function deleteStudent(studentId, fullName) {
        if (!IS_LOGGED_IN) return;

        Swal.fire({
            icon: 'warning',
            title: 'ຢືນຢັນການລຶບຂໍ້ມູນ?',
            html: `ທ່ານຕ້ອງການລຶບຂໍ້ມູນຂອງ <b>${fullName}</b> ແທ້ບໍ່?`,
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-trash3-fill me-1"></i>ຢືນຢັນລຶບຂໍ້ມູນ',
            cancelButtonText: 'ຍົກເລີກ'
        }).then(async (result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('action', 'delete_student');
                formData.append('id', studentId);

                const resp = await fetch('api.php', { method: 'POST', body: formData });
                const data = await resp.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'ລຶບຂໍ້ມູນສຳເລັດ!',
                        text: data.message,
                        timer: 1400,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('ຜິດພາດ!', data.message, 'error');
                }
            }
        });
    }

    const settingsForm = document.getElementById('settingsForm');
    if (settingsForm) {
        settingsForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const resp = await fetch('api.php', { method: 'POST', body: formData });
            const data = await resp.json();
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'ບັນທຶກສຳເລັດ!',
                    text: data.message,
                    timer: 1400,
                    showConfirmButton: false
                }).then(() => window.location.reload());
            } else {
                Swal.fire('ແຈ້ງເຕືອນ', data.message, 'warning');
            }
        });
    }

    const changePasswordForm = document.getElementById('changePasswordForm');
    if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const resp = await fetch('api.php', { method: 'POST', body: formData });
            const data = await resp.json();
            if (data.success) {
                const modalEl = document.getElementById('changePasswordModal');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();
                this.reset();
                Swal.fire({
                    icon: 'success',
                    title: 'ປ່ຽນລະຫັດຜ່ານສຳເລັດ!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                Swal.fire('ແຈ້ງເຕືອນ', data.message, 'warning');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const params = new URLSearchParams(window.location.search);
        const autoEditId = params.get('edit_id');
        if (autoEditId && IS_LOGGED_IN) {
            openEditModal(autoEditId);
        }
    });
</script>
</body>
</html>
