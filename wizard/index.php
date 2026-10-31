<?php
declare(strict_types=1);
session_start();

$configFile = __DIR__ . '/config.php';
if (!is_file($configFile)) {
    http_response_code(500);
    exit('Missing config.php. Copy config.example.php to config.php.');
}
require $configFile;

function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function valid_identifier(string $value): bool {
    return (bool)preg_match('/^[A-Za-z0-9_]{1,48}$/', $value);
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function verify_csrf(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        throw new RuntimeException('Invalid form token. Refresh and try again.');
    }
}

$error = '';
$message = '';

try {
    $db = new mysqli($db_host, $db_admin_user, $db_admin_password, '', (int)$db_port);
    $db->set_charset('utf8mb4');
} catch (Throwable $e) {
    http_response_code(500);
    exit('Cannot connect to MariaDB. Check ~/dbwizard/config.php and make sure MariaDB is running.');
}

if (isset($_POST['login'])) {
    if (hash_equals((string)$admin_password, (string)($_POST['password'] ?? ''))) {
        session_regenerate_id(true);
        $_SESSION['authenticated'] = true;
        csrf_token();
    } else {
        $error = 'Incorrect wizard password.';
    }
}
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: /');
    exit;
}

if (!($_SESSION['authenticated'] ?? false)):
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Database Wizard Login</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#101827;color:#e5e7eb;font:16px Arial;min-height:100vh;display:grid;place-items:center;padding:20px}.card{width:100%;max-width:390px;background:#1d293b;padding:25px;border-radius:16px}input,button{width:100%;padding:14px;border:1px solid #40506a;border-radius:9px;margin-top:12px;background:#111c2d;color:white}button{background:#16a34a;border:0;font-weight:bold}.error{background:#7f1d1d;padding:12px;border-radius:8px}
</style></head><body><div class="card"><h2>Database Wizard</h2><p>Sign in to manage local databases.</p>
<?php if ($error): ?><p class="error"><?=h($error)?></p><?php endif; ?>
<form method="post"><input type="password" name="password" placeholder="Wizard admin password" required><button name="login" value="1">Login</button></form>
</div></body></html>
<?php exit; endif;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['login']) && !isset($_POST['logout'])) {
    try {
        verify_csrf();
        $action = (string)($_POST['action'] ?? '');

        if ($action === 'create') {
            $database = trim((string)($_POST['database'] ?? ''));
            $username = trim((string)($_POST['username'] ?? ''));
            $password = (string)($_POST['password'] ?? '');
            $confirm = (string)($_POST['confirm'] ?? '');

            if (!valid_identifier($database) || !valid_identifier($username)) {
                throw new RuntimeException('Database and username may contain only letters, numbers and underscores (max 48 characters).');
            }
            if (strlen($password) < 10 || $password !== $confirm) {
                throw new RuntimeException('Passwords must match and be at least 10 characters.');
            }

            $db->query("CREATE DATABASE `" . $db->real_escape_string($database) . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // User and host are safely quoted as SQL string values.
            $userSql = "'" . $db->real_escape_string($username) . "'@'localhost'";
            $passSql = "'" . $db->real_escape_string($password) . "'";
            $db->query("CREATE USER {$userSql} IDENTIFIED BY {$passSql}");
            $db->query("GRANT ALL PRIVILEGES ON `" . $db->real_escape_string($database) . "`.* TO {$userSql}");
            $db->query("FLUSH PRIVILEGES");

            $message = 'Database and user created; privileges granted.';
        } elseif ($action === 'drop_database') {
            $database = trim((string)($_POST['database'] ?? ''));
            $protected = ['mysql', 'sys', 'information_schema', 'performance_schema'];
            if (!valid_identifier($database) || in_array(strtolower($database), $protected, true)) {
                throw new RuntimeException('Invalid or protected database name.');
            }
            $db->query("DROP DATABASE `" . $db->real_escape_string($database) . "`");
            $message = 'Database deleted. This cannot be undone without a backup.';
        } elseif ($action === 'drop_user') {
            $username = trim((string)($_POST['username'] ?? ''));
            if (!valid_identifier($username) || strtolower($username) === 'root') {
                throw new RuntimeException('Invalid username or protected root account.');
            }
            $userSql = "'" . $db->real_escape_string($username) . "'@'localhost'";
            $db->query("DROP USER {$userSql}");
            $message = 'Local database user deleted.';
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$databases = [];
$result = $db->query('SHOW DATABASES');
while ($row = $result->fetch_row()) {
    $databases[] = (string)$row[0];
}
$users = [];
try {
    $result = $db->query("SELECT User FROM mysql.user WHERE Host='localhost' ORDER BY User");
    while ($row = $result->fetch_assoc()) {
        $users[] = (string)$row['User'];
    }
} catch (Throwable $e) {
    // Some MariaDB configurations restrict reading mysql.user.
}
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Database Wizard</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#101827;color:#e5e7eb;font:15px Arial,sans-serif}header{background:#182438;padding:17px 20px;display:flex;justify-content:space-between;align-items:center}main{max-width:850px;margin:auto;padding:18px}.card{background:#1d293b;padding:20px;border-radius:14px;margin-bottom:18px}h2{font-size:19px;margin:0 0 14px}input,button{width:100%;padding:13px;border:1px solid #40506a;border-radius:9px;margin:7px 0;background:#111c2d;color:#fff}button{background:#16a34a;border:0;font-weight:bold;cursor:pointer}.danger{background:#b91c1c}.small{width:auto;padding:9px 13px}.notice,.error{padding:12px;border-radius:9px;margin-bottom:15px}.notice{background:#14532d}.error{background:#7f1d1d}.item{padding:12px 0;border-bottom:1px solid #35445b;overflow-wrap:anywhere}.muted{color:#aab5c5;font-size:13px}.inline{display:inline}
</style></head><body>
<header><strong>cPanel-style Database Wizard</strong><form method="post"><button class="small danger" name="logout" value="1">Logout</button></form></header>
<main>
<?php if ($message): ?><div class="notice"><?=h($message)?></div><?php endif; ?>
<?php if ($error): ?><div class="error"><?=h($error)?></div><?php endif; ?>

<section class="card"><h2>Create a Database</h2>
<form method="post">
<input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="action" value="create">
<input name="database" placeholder="New Database Name" maxlength="48" required>
<input name="username" placeholder="New Database Username" maxlength="48" required>
<input type="password" name="password" placeholder="Password (minimum 10 characters)" required>
<input type="password" name="confirm" placeholder="Confirm Password" required>
<button>Create Database</button>
</form><p class="muted">Creates a database, a localhost-only user, and grants that user all privileges on this database.</p>
</section>

<section class="card"><h2>Current Databases</h2>
<?php foreach ($databases as $name): ?><div class="item"><strong><?=h($name)?></strong>
<?php if (!in_array(strtolower($name), ['mysql','sys','information_schema','performance_schema'], true)): ?>
<form method="post" onsubmit="return confirm('Permanently delete <?=h($name)?> and all its tables? Make a backup first.')">
<input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="action" value="drop_database"><input type="hidden" name="database" value="<?=h($name)?>">
<button class="danger">Delete Database</button></form><?php else: ?><p class="muted">Protected system database</p><?php endif; ?></div><?php endforeach; ?>
</section>

<section class="card"><h2>Local Database Users</h2>
<?php if (!$users): ?><p class="muted">Could not read user list, or no local users were returned.</p><?php endif; ?>
<?php foreach ($users as $name): ?><div class="item"><strong><?=h($name)?>@localhost</strong>
<?php if (strtolower($name) !== 'root'): ?><form method="post" onsubmit="return confirm('Delete user <?=h($name)?>? This may affect applications using it.')">
<input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="action" value="drop_user"><input type="hidden" name="username" value="<?=h($name)?>">
<button class="danger">Delete User</button></form><?php else: ?><p class="muted">Protected root account</p><?php endif; ?></div><?php endforeach; ?>
</section>

<section class="card"><h2>Connection Settings</h2><p>Host: <strong><?=h((string)$db_host)?></strong></p><p>Port: <strong><?=h((string)$db_port)?></strong></p><p>User host: <strong>localhost</strong></p><p class="muted">Use the database name, username and password you created in your PHP application.</p></section>
</main></body></html>
