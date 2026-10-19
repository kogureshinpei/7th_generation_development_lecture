<?php
// 環境確認ページ。PHP・DB・初期データがそろっていれば「準備OK」と表示する
require_once __DIR__ . '/lib/db.php';

$checks = [];

// 1. PHP のバージョン
$checks[] = [
    'label' => 'PHP バージョン',
    'ok' => version_compare(PHP_VERSION, '8.0.0', '>='),
    'detail' => PHP_VERSION,
];

// 2. DB 接続と 3. 初期データの件数
try {
    $pdo = db();
    $checks[] = ['label' => 'DB 接続', 'ok' => true, 'detail' => 'MySQL ' . $pdo->query('SELECT VERSION()')->fetchColumn()];

    foreach (['users', 'events', 'participants'] as $table) {
        $count = (int) $pdo->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
        $checks[] = ['label' => "{$table} テーブル", 'ok' => $count > 0, 'detail' => $count . '件'];
    }
} catch (PDOException $e) {
    $checks[] = ['label' => 'DB 接続', 'ok' => false, 'detail' => $e->getMessage()];
}

$allOk = !in_array(false, array_column($checks, 'ok'), true);
?>
<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>環境チェック | もくもく会ボード</title>
<link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<main class="container">
  <h1>環境チェック</h1>
  <table class="check-table">
    <?php foreach ($checks as $check): ?>
      <tr>
        <th><?= h($check['label']) ?></th>
        <td class="<?= $check['ok'] ? 'ok' : 'ng' ?>"><?= $check['ok'] ? 'OK' : 'NG' ?></td>
        <td><?= h($check['detail']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <?php if ($allOk): ?>
    <p class="check-result check-result--ok">準備OK</p>
    <p><a href="/">もくもく会ボードを開く →</a></p>
  <?php else: ?>
    <p class="check-result check-result--ng">まだ準備できていません</p>
    <p>README の「うまく動かないとき」を見てください。DB の起動直後なら、数十秒待ってから再読み込みしてください。</p>
  <?php endif; ?>
</main>
</body>
</html>
