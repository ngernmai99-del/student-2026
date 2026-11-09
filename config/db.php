<?php
/**
 * ການເຊື່ອມຕໍ່ຖານຂໍ້ມູນ SQLite ຜ່ານ PDO (pdo_sqlite)
 * ແລະ ສ້າງຕາຕະລາງ + ຂໍ້ມູນເລີ່ມຕົ້ນແບບອັດຕະໂນມັດ
 */

if (session_status() === PHP_SESSION_NONE) {
    $localSessionDir = __DIR__ . '/../data/sessions';
    if (!is_dir($localSessionDir)) {
        @mkdir($localSessionDir, 0777, true);
    }
    if (is_dir($localSessionDir)) {
        session_save_path($localSessionDir);
    }
    if (PHP_SAPI !== 'cli') {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
    session_start();
}

if (PHP_SAPI !== 'cli' && !headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function getCsrfToken(): string
{
    return $_SESSION['csrf_token'] ?? '';
}

function verifyCsrfToken(?string $token): bool
{
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

date_default_timezone_set('Asia/Vientiane');

require_once __DIR__ . '/lao_data.php';

$dbDir = __DIR__ . '/../data';
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0777, true);
}
$htaccessPath = $dbDir . '/.htaccess';
if (!file_exists($htaccessPath)) {
    @file_put_contents($htaccessPath, "<IfModule mod_authz_core.c>\n    Require all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n    Order deny,allow\n    Deny from all\n</IfModule>\n");
}
$dataIndexPath = $dbDir . '/index.php';
if (!file_exists($dataIndexPath)) {
    @file_put_contents($dataIndexPath, "<?php http_response_code(403); exit('403 Forbidden');\n");
}
$sessIndexPath = $dbDir . '/sessions/index.php';
if (!file_exists($sessIndexPath)) {
    @file_put_contents($sessIndexPath, "<?php http_response_code(403); exit('403 Forbidden');\n");
}

$dbPath = $dbDir . '/student_2026.sqlite';

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON;');
    $pdo->exec('PRAGMA journal_mode = WAL;');
} catch (PDOException $e) {
    die('ບໍ່ສາມາດເຊື່ອມຕໍ່ຖານຂໍ້ມູນ SQLite ໄດ້: ' . htmlspecialchars($e->getMessage()));
}

/**
 * ສ້າງຕາຕະລາງຖ້າຍັງບໍ່ທັນມີ
 */
function initializeDatabase(PDO $pdo): void
{
    // 1. ຕາຕະລາງຜູ້ໃຊ້ລະບົບ (ສຳລັບ Login ເຂົ້າແກ້ໄຂຂໍ້ມູນ)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            full_name TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'admin',
            created_at TEXT NOT NULL DEFAULT (datetime('now', 'localtime'))
        );
    ");

    // 2. ຕາຕະລາງຂໍ້ມູນນັກສຶກສາ (ອີງຕາມແບບຟອມບັນທຶກ + ຂໍ້ມູນໃບຢັ້ງຢືນຈົບການສຶກສາ)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS students (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            cert_no TEXT NOT NULL UNIQUE,
            student_code TEXT DEFAULT '',
            prefix TEXT NOT NULL,
            first_name TEXT NOT NULL,
            last_name TEXT NOT NULL,
            dob_day TEXT NOT NULL,
            dob_month TEXT NOT NULL,
            dob_year TEXT NOT NULL,
            village TEXT NOT NULL,
            district TEXT NOT NULL,
            province TEXT NOT NULL,
            education_level TEXT NOT NULL DEFAULT 'ປະລິນຍາຕີ',
            major TEXT NOT NULL DEFAULT 'ເຕັກໂນໂລຊີຂໍ້ມູນຂ່າວສານ (IT)',
            academic_year TEXT NOT NULL DEFAULT '2025-2026',
            status TEXT NOT NULL DEFAULT 'ອະນຸມັດແລ້ວ (ລໍຖ້າຮັບໃບປະກາດ)',
            created_at TEXT NOT NULL DEFAULT (datetime('now', 'localtime')),
            updated_at TEXT NOT NULL DEFAULT (datetime('now', 'localtime'))
        );
    ");

    // 3. ຕາຕະລາງຕັ້ງຄ່າສະຖາບັນ (ສຳລັບຫົວໃບຢັ້ງຢືນຈົບການສຶກສາ)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS settings (
            setting_key TEXT PRIMARY KEY,
            setting_value TEXT NOT NULL
        );
    ");

    // ກວດສອບ ແລະ ເພີ່ມຜູ້ດູແລລະບົບເລີ່ມຕົ້ນ
    $userCount = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($userCount === 0) {
        $stmt = $pdo->prepare("
            INSERT INTO users (username, password_hash, full_name, role)
            VALUES (:username, :password_hash, :full_name, :role)
        ");
        $stmt->execute([
            ':username' => 'admin',
            ':password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
            ':full_name' => 'ຜູ້ດູແລລະບົບວິຊາການ (Admin)',
            ':role' => 'admin'
        ]);
    }

    // ຕັ້ງຄ່າເລີ່ມຕົ້ນສຳລັບໃບຢັ້ງຢືນ
    $defaultSettings = [
        'institution_name' => 'ວິທະຍາໄລ ເຕັກໂນໂລຊີ ແລະ ບໍລິຫານທຸລະກິດ',
        'ministry_name' => 'ກະຊວງສຶກສາທິການ ແລະ ກິລາ',
        'director_name' => 'ປອ. ຄຳພັນ ວົງສາວັດ',
        'director_title' => 'ຜູ້ອຳນວຍການ',
        'registrar_name' => 'ອຈ. ສົມພອນ ໄຊຍະວົງ',
        'issue_place' => 'ນະຄອນຫຼວງວຽງຈັນ'
    ];

    $stmtSetting = $pdo->prepare("INSERT OR IGNORE INTO settings (setting_key, setting_value) VALUES (:k, :v)");
    foreach ($defaultSettings as $k => $v) {
        $stmtSetting->execute([':k' => $k, ':v' => $v]);
    }

    // ຂໍ້ມູນຕົວຢ່າງເລີ່ມຕົ້ນ (ຖ້າຍັງບໍ່ມີຂໍ້ມູນເລີຍ)
    $studentCount = (int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
    if ($studentCount === 0) {
        $sampleStudents = [
            [
                'cert_no' => 'CERT-2026-0001',
                'student_code' => 'ST66001',
                'prefix' => 'ທ້າວ',
                'first_name' => 'ສົມຊາຍ',
                'last_name' => 'ພົມມະວົງ',
                'dob_day' => '05',
                'dob_month' => 'ມັງກອນ',
                'dob_year' => '2003',
                'village' => 'ດົງໂດກ',
                'district' => 'ໄຊທານີ',
                'province' => 'ນະຄອນຫຼວງວຽງຈັນ',
                'education_level' => 'ປະລິນຍາຕີ',
                'major' => 'ເຕັກໂນໂລຊີຂໍ້ມູນຂ່າວສານ (IT)',
                'academic_year' => '2025-2026',
                'status' => 'ອະນຸມັດແລ້ວ (ລໍຖ້າຮັບໃບປະກາດ)'
            ],
            [
                'cert_no' => 'CERT-2026-0002',
                'student_code' => 'ST66002',
                'prefix' => 'ນາງ',
                'first_name' => 'ມະນີຈັນ',
                'last_name' => 'ສີສຸລິດ',
                'dob_day' => '09',
                'dob_month' => 'ເມສາ',
                'dob_year' => '2004',
                'village' => 'ໂພນສະອາດ',
                'district' => 'ໄຊເສດຖາ',
                'province' => 'ນະຄອນຫຼວງວຽງຈັນ',
                'education_level' => 'ປະລິນຍາຕີ',
                'major' => 'ການບັນຊີ-ການເງິນ',
                'academic_year' => '2025-2026',
                'status' => 'ອະນຸມັດແລ້ວ (ລໍຖ້າຮັບໃບປະກາດ)'
            ],
            [
                'cert_no' => 'CERT-2026-0003',
                'student_code' => 'ST66003',
                'prefix' => 'ສນ',
                'first_name' => 'ບຸນມີ',
                'last_name' => 'ຈັນທະລັງສີ',
                'dob_day' => '02',
                'dob_month' => 'ຕຸລາ',
                'dob_year' => '2002',
                'village' => 'ຊຽງທອງ',
                'district' => 'ນະຄອນຫຼວງພະບາງ',
                'province' => 'ຫຼວງພະບາງ',
                'education_level' => 'ປະລິນຍາຕີ',
                'major' => 'ພາສາອັງກິດທຸລະກິດ',
                'academic_year' => '2025-2026',
                'status' => 'ອະນຸມັດແລ້ວ (ລໍຖ້າຮັບໃບປະກາດ)'
            ],
            [
                'cert_no' => 'CERT-2026-0004',
                'student_code' => 'ST66004',
                'prefix' => 'ນາງ',
                'first_name' => 'ແສງດາວ',
                'last_name' => 'ວົງພະຈັນ',
                'dob_day' => '18',
                'dob_month' => 'ກໍລະກົດ',
                'dob_year' => '2003',
                'village' => 'ທ່າແຂກກາງ',
                'district' => 'ທ່າແຂກ',
                'province' => 'ຄຳມ່ວນ',
                'education_level' => 'ຊັ້ນສູງ',
                'major' => 'ບໍລິຫານທຸລະກິດ',
                'academic_year' => '2025-2026',
                'status' => 'ອະນຸມັດແລ້ວ (ລໍຖ້າຮັບໃບປະກາດ)'
            ],
            [
                'cert_no' => 'CERT-2026-0005',
                'student_code' => 'ST66005',
                'prefix' => 'ທ້າວ',
                'first_name' => 'ທະນູສິນ',
                'last_name' => 'ແກ້ວມະນີ',
                'dob_day' => '07',
                'dob_month' => 'ທັນວາ',
                'dob_year' => '2003',
                'village' => 'ຫຼັກເມືອງ',
                'district' => 'ນະຄອນປາກເຊ',
                'province' => 'ຈຳປາສັກ',
                'education_level' => 'ປະລິນຍາຕີ',
                'major' => 'ວິທະຍາສາດຄອມພິວເຕີ',
                'academic_year' => '2025-2026',
                'status' => 'ອະນຸມັດແລ້ວ (ລໍຖ້າຮັບໃບປະກາດ)'
            ]
        ];

        $stmtInsert = $pdo->prepare("
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

        foreach ($sampleStudents as $st) {
            $stmtInsert->execute([
                ':cert_no' => $st['cert_no'],
                ':student_code' => $st['student_code'],
                ':prefix' => $st['prefix'],
                ':first_name' => $st['first_name'],
                ':last_name' => $st['last_name'],
                ':dob_day' => $st['dob_day'],
                ':dob_month' => $st['dob_month'],
                ':dob_year' => $st['dob_year'],
                ':village' => $st['village'],
                ':district' => $st['district'],
                ':province' => $st['province'],
                ':education_level' => $st['education_level'],
                ':major' => $st['major'],
                ':academic_year' => $st['academic_year'],
                ':status' => $st['status']
            ]);
        }
    }
}

initializeDatabase($pdo);

/**
 * ກວດສອບສະຖານະການເຂົ້າສູ່ລະບົບ (Login)
 */
function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

/**
 * ດຶງຂໍ້ມູນຜູ້ໃຊ້ປັດຈຸບັນ
 */
function currentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? 'admin',
        'full_name' => $_SESSION['full_name'] ?? 'ຜູ້ດູແລລະບົບ',
        'role' => $_SESSION['role'] ?? 'admin'
    ];
}

/**
 * ດຶງຄ່າຕັ້ງຄ່າຂອງລະບົບທັງໝົດ
 */
function getSystemSettings(PDO $pdo): array
{
    $rows = $pdo->query("SELECT setting_key, setting_value FROM settings")->fetchAll();
    $settings = [];
    foreach ($rows as $r) {
        $settings[$r['setting_key']] = $r['setting_value'];
    }
    return $settings;
}

/**
 * ສ້າງເລກທີໃບຢັ້ງຢືນອັດຕະໂນມັດ ເຊັ່ນ CERT-2026-0006
 */
function generateNextCertNo(PDO $pdo): string
{
    $year = date('Y');
    $maxId = (int)$pdo->query("SELECT COALESCE(MAX(id), 0) FROM students")->fetchColumn();
    $nextNum = $maxId + 1;
    do {
        $candidate = sprintf('CERT-%s-%04d', $year, $nextNum);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE cert_no = :c");
        $stmt->execute([':c' => $candidate]);
        $exists = (int)$stmt->fetchColumn() > 0;
        if ($exists) {
            $nextNum++;
        }
    } while ($exists);

    return $candidate;
}

/**
 * ສ້າງລະຫັດນັກສຶກສາອັດຕະໂນມັດ
 */
function generateNextStudentCode(PDO $pdo): string
{
    $maxId = (int)$pdo->query("SELECT COALESCE(MAX(id), 0) FROM students")->fetchColumn();
    return sprintf('ST66%03d', $maxId + 1);
}
