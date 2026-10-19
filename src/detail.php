<?php
// ③ 会の詳細・参加（担当：Cさん）
// 仕様：docs/requirements.md の「③ 会の詳細・参加」／見た目：http://localhost:8080/mockup/detail.html
require_once __DIR__ . '/lib/current_user.php';

// TODO: POST で「参加する」「キャンセルする」が送られてきたら participants を更新し、同じページへリダイレクトする（PHP・SQL）

// TODO: id の会と、その参加者を取ってくる（SQL）

$pageTitle = '会の詳細';
$narrow = true;
require __DIR__ . '/partials/header.php';
?>

<!-- TODO: 会の情報・参加者一覧・参加ボタン（HTML） -->

<?php require __DIR__ . '/partials/footer.php'; ?>
