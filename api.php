<?php
/**
 * API Endpoint ສຳລັບຈັດການຂໍ້ມູນນັກສຶກສາ, ເຂົ້າສູ່ລະບົບ, ແລະ ຕັ້ງຄ່າໃບຢັ້ງຢືນ (JSON + SweetAlert2)
 */
require_once __DIR__ . '/config/db.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        // ==========================================
        // 1. ບັນທຶກຂໍ້ມູນນັກສຶກສາໃໝ່ (Online Form)
        // ==========================================
        case 'create_student':
            $prefix = trim($_POST['prefix'] ?? '');
            $first_name = trim($_POST['first_name'] ?? '');
            $last_name = trim($_POST['last_name'] ?? '');
            $dob_day = trim($_POST['dob_day'] ?? '');
            $dob_month = trim($_POST['dob_month'] ?? '');
            $dob_year = trim($_POST['dob_year'] ?? '');
            $village = trim($_POST['village'] ?? '');
            $district = trim($_POST['district'] ?? '');
            $province = trim($_POST['province'] ?? '');

            $student_code = trim($_POST['student_code'] ?? '');
            $education_level = trim($_POST['education_level'] ?? 'ປະລິນຍາຕີ');
            $major = trim($_POST['major'] ?? 'ເຕັກໂນໂລຊີຂໍ້ມູນຂ່າວສານ (IT)');
            $academic_year = trim($_POST['academic_year'] ?? '2025-2026');
            $status = trim($_POST['status'] ?? 'ອະນຸມັດແລ້ວ (ລໍຖ້າຮັບໃບປະກາດ)');

            // ກວດສອບຄວາມຖືກຕ້ອງຂອງຂໍ້ມູນ
            if (!array_key_exists($prefix, $LAO_PREFIXES)) {
                echo json_encode(['success' => false, 'message' => 'ກະລຸນາເລືອກໜ້າຊື່ (ທ້າວ, ນາງ, ສນ ຫຼື ຄູບາ)']);
                exit;
            }
            if ($first_name === '' || $last_name === '') {
                echo json_encode(['success' => false, 'message' => 'ກະລຸນາປ້ອນຊື່ ແລະ ນາມສະກຸນ ໃຫ້ຄົບຖ້ວນ']);
                exit;
            }
            if (mb_strlen($first_name) > 100 || mb_strlen($last_name) > 100 || mb_strlen($village) > 120) {
                echo json_encode(['success' => false, 'message' => 'ຂໍ້ມູນທີ່ປ້ອນຍາວເກີນໄປ (ສູງສຸດ 100 ຕົວອັກສອນ)']);
                exit;
            }
            // ຮັບປະກັນວ່າ ວັນທີ 01-09 ມີເລກ 0 ນຳໜ້າສະເໝີ
            if (is_numeric($dob_day)) {
                $dayInt = (int)$dob_day;
                if ($dayInt >= 1 && $dayInt <= 31) {
                    $dob_day = str_pad((string)$dayInt, 2, '0', STR_PAD_LEFT);
                }
            }
            if (!array_key_exists($dob_day, $LAO_DAYS)) {
                echo json_encode(['success' => false, 'message' => 'ກະລຸນາເລືອກວັນທີເກີດ (01 - 31)']);
                exit;
            }
            if (!array_key_exists($dob_month, $LAO_MONTHS)) {
                echo json_encode(['success' => false, 'message' => 'ກະລຸນາເລືອກເດືອນເກີດ (ເດືອນລາວ)']);
                exit;
            }
            if (!preg_match('/^\d{4}$/', $dob_year)) {
                echo json_encode(['success' => false, 'message' => 'ກະລຸນາເລືອກປີເກີດເປັນຕົວເລກ 4 ຕົວ']);
                exit;
            }
            if ($village === '' || $district === '' || $province === '') {
                echo json_encode(['success' => false, 'message' => 'ກະລຸນາປ້ອນບ້ານ, ເລືອກເມືອງ ແລະ ເລືອກແຂວງ ໃຫ້ຄົບຖ້ວນ']);
                exit;
            }

            $cert_no = generateNextCertNo($pdo);
            if ($student_code === '') {
                $student_code = generateNextStudentCode($pdo);
            }

            $stmt = $pdo->prepare("
                INSERT INTO students (
                    cert_no, student_code, prefix, first_name, last_name,
                    dob_day, dob_month, dob_year,
                    village, district, province,
                    education_level, major, academic_year, status
                ) VALUES (
                    :cert_no, :student_code, :prefix, :first_name, :last_name,
                    :dob_day, :dob_month, :dob_year,
                    :village, :district, :province,
                    :education_level, :major, :academic_year, :status
                )
            ");

            $stmt->execute([
                ':cert_no' => $cert_no,
                ':student_code' => $student_code,
                ':prefix' => $prefix,
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':dob_day' => $dob_day,
                ':dob_month' => $dob_month,
                ':dob_year' => $dob_year,
                ':village' => $village,
                ':district' => $district,
                ':province' => $province,
                ':education_level' => $education_level !== '' ? $education_level : 'ປະລິນຍາຕີ',
                ':major' => $major !== '' ? $major : 'ເຕັກໂນໂລຊີຂໍ້ມູນຂ່າວສານ (IT)',
                ':academic_year' => $academic_year !== '' ? $academic_year : '2025-2026',
                ':status' => $status !== '' ? $status : 'ອະນຸມັດແລ້ວ (ລໍຖ້າຮັບໃບປະກາດ)'
            ]);

            $newId = (int)$pdo->lastInsertId();

            echo json_encode([
                'success' => true,
                'message' => "ບັນທຶກຂໍ້ມູນນັກສຶກສາ {$prefix} {$first_name} {$last_name} ສຳເລັດແລ້ວ!",
                'student_id' => $newId,
                'cert_no' => $cert_no
            ]);
            break;

        // ==========================================
        // 2. ດຶງຂໍ້ມູນນັກສຶກສາຕາມ ID
        // ==========================================
        case 'get_student':
            $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
            $stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $student = $stmt->fetch();

            if (!$student) {
                echo json_encode(['success' => false, 'message' => 'ບໍ່ພົບຂໍ້ມູນນັກສຶກສາ']);
                exit;
            }

            echo json_encode([
                'success' => true,
                'student' => $student
            ]);
            break;

        // ==========================================
        // 3. ແກ້ໄຂຂໍ້ມູນນັກສຶກສາ (ສະເພາະ Admin ທີ່ເຂົ້າສູ່ລະບົບແລ້ວ)
        // ==========================================
        case 'update_student':
            if (!isLoggedIn()) {
                echo json_encode([
                    'success' => false,
                    'require_login' => true,
                    'message' => 'ຜູ້ໃຊ້ທົ່ວໄປສາມາດບັນທຶກຂໍ້ມູນໄດ້ຢ່າງດຽວ! ກະລຸນາ Login Admin ເພື່ອແກ້ໄຂຂໍ້ມູນ.'
                ]);
                exit;
            }

            $id = (int)($_POST['id'] ?? 0);
            $prefix = trim($_POST['prefix'] ?? '');
            $first_name = trim($_POST['first_name'] ?? '');
            $last_name = trim($_POST['last_name'] ?? '');
            $dob_day = trim($_POST['dob_day'] ?? '');
            $dob_month = trim($_POST['dob_month'] ?? '');
            $dob_year = trim($_POST['dob_year'] ?? '');
            $village = trim($_POST['village'] ?? '');
            $district = trim($_POST['district'] ?? '');
            $province = trim($_POST['province'] ?? '');

            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ລະຫັດອ້າງອີງບໍ່ຖືກຕ້ອງ']);
                exit;
            }
            if (!array_key_exists($prefix, $LAO_PREFIXES)) {
                echo json_encode(['success' => false, 'message' => 'ກະລຸນາເລືອກໜ້າຊື່ (ທ້າວ, ນາງ, ສນ ຫຼື ຄູບາ)']);
                exit;
            }
            if ($first_name === '' || $last_name === '') {
                echo json_encode(['success' => false, 'message' => 'ກະລຸນາປ້ອນຊື່ ແລະ ນາມສະກຸນ ໃຫ້ຄົບຖ້ວນ']);
                exit;
            }
            if (mb_strlen($first_name) > 100 || mb_strlen($last_name) > 100 || mb_strlen($village) > 120) {
                echo json_encode(['success' => false, 'message' => 'ຂໍ້ມູນທີ່ປ້ອນຍາວເກີນໄປ (ສູງສຸດ 100 ຕົວອັກສອນ)']);
                exit;
            }
            if (is_numeric($dob_day)) {
                $dayInt = (int)$dob_day;
                if ($dayInt >= 1 && $dayInt <= 31) {
                    $dob_day = str_pad((string)$dayInt, 2, '0', STR_PAD_LEFT);
                }
            }
            if (!array_key_exists($dob_day, $LAO_DAYS)) {
                echo json_encode(['success' => false, 'message' => 'ກະລຸນາເລືອກວັນທີເກີດ (01 - 31)']);
                exit;
            }
            if (!array_key_exists($dob_month, $LAO_MONTHS)) {
                echo json_encode(['success' => false, 'message' => 'ກະລຸນາເລືອກເດືອນເກີດ (ເດືອນລາວ)']);
                exit;
            }
            if (!preg_match('/^\d{4}$/', $dob_year)) {
                echo json_encode(['success' => false, 'message' => 'ກະລຸນາເລືອກປີເກີດເປັນຕົວເລກ']);
                exit;
            }
            if ($village === '' || $district === '' || $province === '') {
                echo json_encode(['success' => false, 'message' => 'ກະລຸນາປ້ອນບ້ານ, ເລືອກເມືອງ ແລະ ເລືອກແຂວງ ໃຫ້ຄົບຖ້ວນ']);
                exit;
            }

            $stmt = $pdo->prepare("
                UPDATE students SET
                    prefix = :prefix,
                    first_name = :first_name,
                    last_name = :last_name,
                    dob_day = :dob_day,
                    dob_month = :dob_month,
                    dob_year = :dob_year,
                    village = :village,
                    district = :district,
                    province = :province,
                    updated_at = datetime('now', 'localtime')
                WHERE id = :id
            ");

            $stmt->execute([
                ':prefix' => $prefix,
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':dob_day' => $dob_day,
                ':dob_month' => $dob_month,
                ':dob_year' => $dob_year,
                ':village' => $village,
                ':district' => $district,
                ':province' => $province,
                ':id' => $id
            ]);

            echo json_encode([
                'success' => true,
                'message' => "ອັບເດດຂໍ້ມູນຂອງ {$prefix} {$first_name} {$last_name} ສຳເລັດແລ້ວ!"
            ]);
            break;

        // ==========================================
        // 4. ລຶບຂໍ້ມູນນັກສຶກສາ (ຕ້ອງເຂົ້າສູ່ລະບົບກ່ອນ)
        // ==========================================
        case 'delete_student':
            if (!isLoggedIn()) {
                echo json_encode([
                    'success' => false,
                    'require_login' => true,
                    'message' => 'ກະລຸນາເຂົ້າສູ່ລະບົບ (Login) ກ່ອນຈຶ່ງສາມາດລຶບຂໍ້ມູນໄດ້!'
                ]);
                exit;
            }

            $id = (int)($_POST['id'] ?? 0);
            $stmt = $pdo->prepare("DELETE FROM students WHERE id = :id");
            $stmt->execute([':id' => $id]);

            echo json_encode([
                'success' => true,
                'message' => 'ລຶບຂໍ້ມູນນັກສຶກສາອອກຈາກລະບົບສຳເລັດແລ້ວ!'
            ]);
            break;

        // ==========================================
        // 5. ເຂົ້າສູ່ລະບົບ (Login Form ພ້ອມລະບົບປ້ອງກັນ Brute-Force)
        // ==========================================
        case 'login':
            $now = time();
            $lockUntil = (int)($_SESSION['login_lock_until'] ?? 0);
            if ($lockUntil > $now) {
                $waitSec = $lockUntil - $now;
                echo json_encode([
                    'success' => false,
                    'message' => "ທ່ານປ້ອນລະຫັດຜິດຫຼາຍຄັ້ງເກີນໄປ! ກະລຸນາລໍຖ້າອີກ {$waitSec} ວິນາທີ."
                ]);
                exit;
            }

            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($username === '' || $password === '') {
                echo json_encode([
                    'success' => false,
                    'message' => 'ກະລຸນາປ້ອນຊື່ຜູ້ໃຊ້ (Username) ແລະ ລະຫັດຜ່ານ (Password)'
                ]);
                exit;
            }

            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $_SESSION['login_attempts'] = (int)($_SESSION['login_attempts'] ?? 0) + 1;
                if ($_SESSION['login_attempts'] >= 5) {
                    $_SESSION['login_lock_until'] = $now + 180; // ລັອກ 3 ນາທີ
                    $_SESSION['login_attempts'] = 0;
                }
                echo json_encode([
                    'success' => false,
                    'message' => 'ຊື່ຜູ້ໃຊ້ ຫຼື ລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ!'
                ]);
                exit;
            }

            $_SESSION['login_attempts'] = 0;
            $_SESSION['login_lock_until'] = 0;
            @session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            echo json_encode([
                'success' => true,
                'message' => 'ເຂົ້າສູ່ລະບົບສຳເລັດ! ຍິນດີຕ້ອນຮັບ ' . $user['full_name']
            ]);
            break;

        // ==========================================
        // 6. ປ່ຽນລະຫັດຜ່ານ Admin
        // ==========================================
        case 'change_password':
            if (!isLoggedIn()) {
                echo json_encode(['success' => false, 'message' => 'ກະລຸນາເຂົ້າສູ່ລະບົບກ່ອນ']);
                exit;
            }
            $currentPwd = $_POST['current_password'] ?? '';
            $newPwd = $_POST['new_password'] ?? '';
            if (mb_strlen($newPwd) < 6) {
                echo json_encode(['success' => false, 'message' => 'ລະຫັດຜ່ານໃໝ່ຕ້ອງມີຢ່າງໜ້ອຍ 6 ຕົວອັກສອນ']);
                exit;
            }
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => (int)$_SESSION['user_id']]);
            $user = $stmt->fetch();
            if (!$user || !password_verify($currentPwd, $user['password_hash'])) {
                echo json_encode(['success' => false, 'message' => 'ລະຫັດຜ່ານປັດຈຸບັນບໍ່ຖືກຕ້ອງ!']);
                exit;
            }
            $stmtUpd = $pdo->prepare("UPDATE users SET password_hash = :h WHERE id = :id");
            $stmtUpd->execute([
                ':h' => password_hash($newPwd, PASSWORD_DEFAULT),
                ':id' => (int)$_SESSION['user_id']
            ]);
            echo json_encode(['success' => true, 'message' => 'ປ່ຽນລະຫັດຜ່ານ Admin ສຳເລັດແລ້ວ!']);
            break;

        // ==========================================
        // 7. ບັນທຶກການຕັ້ງຄ່າຫົວໃບຢັ້ງຢືນ
        // ==========================================
        case 'update_settings':
            if (!isLoggedIn()) {
                echo json_encode([
                    'success' => false,
                    'require_login' => true,
                    'message' => 'ກະລຸນາເຂົ້າສູ່ລະບົບກ່ອນແກ້ໄຂການຕັ້ງຄ່າ'
                ]);
                exit;
            }

            $keys = ['institution_name', 'ministry_name', 'director_name', 'director_title', 'registrar_name', 'issue_place'];
            $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES (:k, :v)");
            foreach ($keys as $k) {
                if (isset($_POST[$k])) {
                    $stmt->execute([':k' => $k, ':v' => mb_substr(trim($_POST[$k]), 0, 150)]);
                }
            }

            echo json_encode([
                'success' => true,
                'message' => 'ບັນທຶກການຕັ້ງຄ່າໃບຢັ້ງຢືນສຳເລັດແລ້ວ!'
            ]);
            break;

        default:
            echo json_encode([
                'success' => false,
                'message' => 'ບໍ່ພົບຄຳສັ່ງທີ່ຮ້ອງຂໍ (Invalid action)'
            ]);
            break;
    }
} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => 'ເກີດຂໍ້ຜິດພາດໃນການປະມວນຜົນ ກະລຸນາລອງໃໝ່ອີກຄັ້ງ'
    ]);
}

