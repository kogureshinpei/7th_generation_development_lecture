<?php
// ① 会の一覧（担当：Aさん）
// 仕様：docs/requirements.md の「① 会の一覧」／見た目：http://localhost:8080/mockup/index.html
require_once __DIR__ . '/lib/current_user.php';

// この画面には自分で受け取る POST はない（操作ユーザーの切り替えは current_user.php が処理する）

// これから開かれる会を、参加人数つきで取ってくる
// - LEFT JOIN にするのは、参加者が 0 人の会も消さずに残すため（INNER JOIN だと消える）
// - COUNT(p.id) は参加者の行だけを数える。0 人の会は p.id が NULL なので 0 になる
// - 主催者名も一緒に出したいので users も JOIN する
$stmt = db()->prepare('
    SELECT
        e.id,
        e.title,
        e.place,
        e.starts_at,
        e.capacity,
        u.name AS organizer_name,
        COUNT(p.id) AS participant_count
    FROM events e
    JOIN users u ON u.id = e.organizer_id
    LEFT JOIN participants p ON p.event_id = e.id
    WHERE e.starts_at > NOW()
    GROUP BY e.id, e.title, e.place, e.starts_at, e.capacity, u.name
    ORDER BY e.starts_at ASC
');
$stmt->execute();
$events = $stmt->fetchAll();

$pageTitle = '会の一覧';
require __DIR__ . '/partials/header.php';
?>

<h1 class="page-title">これから開かれる会</h1>

<?php if (count($events) === 0): ?>
  <p class="empty">これから開かれる会はありません</p>
<?php else: ?>
  <ul class="event-list">
    <?php foreach ($events as $event): ?>
      <li>
        <a class="event-card" href="/detail.php?id=<?= h((string) $event['id']) ?>">
          <div class="event-card__head">
            <h2 class="event-card__title"><?= h($event['title']) ?></h2>
            <?php // 参加人数が定員に達していたら「満員」バッジを出す ?>
            <?php if ($event['participant_count'] >= $event['capacity']): ?>
              <span class="badge badge--full">満員</span>
            <?php endif; ?>
          </div>
          <dl class="meta-list">
            <div class="meta-row"><dt>日時</dt><dd><?= h(format_datetime($event['starts_at'])) ?></dd></div>
            <div class="meta-row"><dt>場所</dt><dd><?= h($event['place']) ?></dd></div>
            <div class="meta-row"><dt>主催</dt><dd><?= h($event['organizer_name']) ?></dd></div>
          </dl>
          <p class="event-card__count">参加 <strong><?= h((string) $event['participant_count']) ?> / <?= h((string) $event['capacity']) ?></strong> 人</p>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
