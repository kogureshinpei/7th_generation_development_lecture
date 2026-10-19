<?php
// ① 会の一覧（担当：Aさん）
// 仕様：docs/requirements.md の「① 会の一覧」／見た目：http://localhost:8080/mockup/index.html
require_once __DIR__ . '/lib/current_user.php';

// TODO: これから開かれる会を、参加人数つきで取ってくる（SQL）

$pageTitle = '会の一覧';
require __DIR__ . '/partials/header.php';
?>

<h1 class="page-title">これから開かれる会</h1>

<!-- TODO: 会をカードで並べる（HTML） -->

<?php require __DIR__ . '/partials/footer.php'; ?>
