<?php
// データベース（MySQL）に接続して PDO を返す
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $host = getenv('DB_HOST') ?: 'db';
        $name = getenv('DB_NAME') ?: 'mokumoku';
        $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";
        $pdo = new PDO($dsn, getenv('DB_USER') ?: 'mokumoku', getenv('DB_PASSWORD') ?: 'mokumoku', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,       // エラー時は例外を投げる
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // 結果を連想配列で受け取る
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    return $pdo;
}

// HTML に出力する文字列をエスケープする（XSS 対策）
function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
