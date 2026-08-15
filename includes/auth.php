<?php

define('ROOT', basename(dirname($_SERVER['SCRIPT_FILENAME'])) === 'staff' ? '../' : '');

require_once(__DIR__ . '/db.php');
require_once(__DIR__ . '/format.php');

if (session_status() === PHP_SESSION_NONE) {

    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

const PASSWORD_ALGO = PASSWORD_ARGON2ID;

const DUMMY_HASH = '$argon2id$v=19$m=65536,t=4,p=1$UEYySHRaNk1LQUF4R09Deg$uY16eZQnwxwxQyzIJzlF9K3BGjTbdY7G2//WJTEzJMo';

const PASSWORD_MIN = 8;

function authenticate(string $email, string $password): ?array
{
    $statement = db()->prepare(
        'SELECT id, name, email, phone, password_hash, role FROM users WHERE email = ? LIMIT 1'
    );
    $statement->execute([strtolower(trim($email))]);
    $user = $statement->fetch();

    if (!$user) {
        password_verify($password, DUMMY_HASH);

        return null;
    }

    if (!password_verify($password, $user['password_hash'])) {
        return null;
    }

    unset($user['password_hash']);

    return $user;
}

function email_taken(string $email, int $exceptId = 0): bool
{
    $statement = db()->prepare('SELECT 1 FROM users WHERE email = ? AND id <> ? LIMIT 1');
    $statement->execute([strtolower(trim($email)), $exceptId]);

    return (bool) $statement->fetchColumn();
}

function register_customer(string $name, string $email, string $password, string $phone = ''): ?array
{
    $statement = db()->prepare(
        "INSERT INTO users (name, email, phone, password_hash, role)
         VALUES (?, ?, ?, ?, 'customer')"
    );

    try {
        $statement->execute([
            trim($name),
            strtolower(trim($email)),
            trim($phone),

            password_hash($password, PASSWORD_ALGO),
        ]);
    } catch (PDOException $exception) {
        if ($exception->getCode() === '23000') {
            return null;
        }

        throw $exception;
    }

    return [
        'id'    => (int) db()->lastInsertId(),
        'name'  => trim($name),
        'email' => strtolower(trim($email)),
        'phone' => trim($phone),
        'role'  => 'customer',
    ];
}

function user_update(int $id, string $name, string $email, string $phone): bool
{
    $statement = db()->prepare('UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?');

    try {
        $statement->execute([trim($name), strtolower(trim($email)), trim($phone), $id]);
    } catch (PDOException $exception) {
        if ($exception->getCode() === '23000') {
            return false;
        }

        throw $exception;
    }

    return true;
}

function sign_in(array $user): void
{
    unset($user['password_hash']);

    session_regenerate_id(true);
    $_SESSION['user'] = $user;
}

function user_find(int $id): ?array
{
    if (!db_available()) {
        return null;
    }

    $statement = db()->prepare(
        'SELECT id, name, email, phone, role, created_at FROM users WHERE id = ? LIMIT 1'
    );
    $statement->execute([$id]);

    return $statement->fetch() ?: null;
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function home_for(array $user): string
{
    return ROOT . (($user['role'] ?? '') === 'staff' ? 'staff/offers.php' : 'account.php');
}

function require_staff(): array
{
    $user = current_user();

    if (!$user) {
        header('Location: ' . ROOT . 'login.php?staff=1');
        exit;
    }

    if ($user['role'] !== 'staff') {

        header('Location: ' . home_for($user) . '?staff=1');
        exit;
    }

    return $user;
}

function require_customer(): array
{
    $user = current_user();

    if (!$user) {
        header('Location: ' . ROOT . 'login.php?account=1');
        exit;
    }

    if ($user['role'] === 'staff') {

        header('Location: ' . home_for($user));
        exit;
    }

    return $user;
}

function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_take(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return $flash;
}
