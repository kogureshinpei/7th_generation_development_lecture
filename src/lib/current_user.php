<?php
// 「操作ユーザー」を扱う。ログイン機能の代わりに、ヘッダーで選んだユーザーをセッションに保存する
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ヘッダーのセレクトから送られてきたら切り替える
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['switch_user_id'])) {
    $_SESSION['user_id'] = (int) $_POST['switch_user_id'];
    header('Location: ' . ($_SERVER['REQUEST_URI'] ?? '/'));
    exit;
}

function all_users(): array
{
    return db()->query('SELECT id, name FROM users ORDER BY id')->fetchAll();
}

// いま操作しているユーザー（未選択なら最初のユーザー）
function current_user(): array
{
    $users = all_users();
    foreach ($users as $user) {
        if ($user['id'] === ($_SESSION['user_id'] ?? null)) {
            return $user;
        }
    }
    $_SESSION['user_id'] = $users[0]['id'];
    return $users[0];
}
