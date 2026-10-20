<?php
// ② 会をつくる（担当：Bさん）
// 仕様：docs/requirements.md の「② 会をつくる」／見た目：http://localhost:8080/mockup/new.html
require_once __DIR__ . '/lib/current_user.php';

// TODO: POST で送られてきたら、入力をチェックして events に保存し、リダイレクトする（PHP・SQL）

$pageTitle = '会をつくる';
$narrow = true;
require __DIR__ . '/partials/header.php';
?>

<h1 class="page-title">会をつくる</h1>

<!-- TODO: 入力フォーム（HTML） -->

<?php require __DIR__ . '/partials/footer.php'; ?>
