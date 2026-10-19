<?php
// 各ページの先頭で読み込む。読み込む前に $pageTitle を決めておく
$me = current_user();
?>
<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($pageTitle ?? '') ?> | もくもく会ボード</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Zen+Kaku+Gothic+New:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<header class="site-header">
  <div class="site-header__inner">
    <a href="/" class="site-title">もくもく会ボード</a>
    <div class="site-header__actions">
      <form method="post" class="user-switch">
        <label>操作ユーザー
          <select name="switch_user_id" onchange="this.form.submit()">
            <?php foreach (all_users() as $user): ?>
              <option value="<?= h((string) $user['id']) ?>" <?= $user['id'] === $me['id'] ? 'selected' : '' ?>><?= h($user['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <noscript><button type="submit" class="btn">切替</button></noscript>
      </form>
      <a href="/new.php" class="btn btn--primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"></path></svg>会をつくる
      </a>
    </div>
  </div>
</header>
<main class="container<?= !empty($narrow) ? " container--narrow" : "" ?>">
