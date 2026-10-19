<?php
// ③ 会の詳細・参加（担当：Cさん）
// 仕様：docs/requirements.md の「③ 会の詳細・参加」／見た目：http://localhost:8080/mockup/detail.html
require_once __DIR__ . '/lib/current_user.php';

$id = (int) ($_GET['id'] ?? 0);
$me = current_user();

// POST で「参加する」「キャンセルする」が送られてきたら participants を更新し、同じページへリダイレクトする
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'join') {
        // 2 人が同時に押しても定員を超えないよう、トランザクションの中で会の行をロックしてから人数を数える
        db()->beginTransaction();

        $stmt = db()->prepare('SELECT organizer_id, capacity FROM events WHERE id = ? FOR UPDATE');
        $stmt->execute([$id]);
        $event = $stmt->fetch();

        $stmt = db()->prepare('SELECT COUNT(*) FROM participants WHERE event_id = ?');
        $stmt->execute([$id]);
        $count = (int) $stmt->fetchColumn();

        // ボタンを隠すだけでは、別タブや古い画面から送られると防げない。PHP 側でも確かめる
        if ($event && $event['organizer_id'] !== $me['id'] && $count < $event['capacity']) {
            try {
                $stmt = db()->prepare('INSERT INTO participants (event_id, user_id) VALUES (?, ?)');
                $stmt->execute([$id, $me['id']]);
            } catch (PDOException $e) {
                // 参加済みの人がもう一度送ると UNIQUE 制約で弾かれる（1062）。その場合は何もしない
                if ($e->errorInfo[1] !== 1062) {
                    throw $e;
                }
            }
        }

        db()->commit();
    }

    if ($action === 'cancel') {
        // 自分の行だけを消す（user_id も条件に入れる）
        $stmt = db()->prepare('DELETE FROM participants WHERE event_id = ? AND user_id = ?');
        $stmt->execute([$id, $me['id']]);
    }

    // リダイレクトしておくと、再読み込みしても POST がもう一度送られない
    header('Location: /detail.php?id=' . $id);
    exit;
}

// id の会と、その参加者を取ってくる
$stmt = db()->prepare(
    'SELECT events.*, users.name AS organizer_name
     FROM events
     JOIN users ON users.id = events.organizer_id
     WHERE events.id = ?'
);
$stmt->execute([$id]);
$event = $stmt->fetch();  // 見つからなければ false

$participants = [];
if ($event) {
    // 申し込みが早い順。同じ秒に登録されたときのために id でも並べる
    $stmt = db()->prepare(
        'SELECT users.id, users.name
         FROM participants
         JOIN users ON users.id = participants.user_id
         WHERE participants.event_id = ?
         ORDER BY participants.created_at, participants.id'
    );
    $stmt->execute([$id]);
    $participants = $stmt->fetchAll();
}

// ボタンの出し分けに使う
$isFull = $event && count($participants) >= $event['capacity'];
$isOrganizer = $event && $event['organizer_id'] === $me['id'];
$isJoined = in_array($me['id'], array_column($participants, 'id'), true);

$pageTitle = '会の詳細';
$narrow = true;
require __DIR__ . '/partials/header.php';
?>

<a class="back-link" href="/">← 一覧へ戻る</a>

<?php if (!$event): ?>
  <p class="empty">会が見つかりません</p>
<?php else: ?>
  <article class="event-detail">
    <div class="event-detail__head">
      <h1 class="event-detail__title"><?= h($event['title']) ?></h1>
      <?php if ($isFull): ?>
        <span class="badge badge--full">満員</span>
      <?php endif; ?>
    </div>

    <dl class="meta-list">
      <div class="meta-row"><dt>日時</dt><dd><?= h(format_datetime($event['starts_at'])) ?></dd></div>
      <div class="meta-row"><dt>場所</dt><dd><?= h($event['place']) ?></dd></div>
      <div class="meta-row"><dt>主催</dt><dd><?= h($event['organizer_name']) ?></dd></div>
      <div class="meta-row"><dt>参加</dt><dd><?= count($participants) ?> / <?= h((string) $event['capacity']) ?> 人</dd></div>
    </dl>

    <section>
      <h2 class="section-title">説明</h2>
      <p class="event-detail__description"><?= h($event['description']) ?></p>
    </section>

    <section>
      <h2 class="section-title">参加者（申し込み順）</h2>
      <?php if (count($participants) === 0): ?>
        <p class="participant-list__empty">まだ参加者はいません</p>
      <?php else: ?>
        <ul class="participant-list">
          <?php foreach ($participants as $participant): ?>
            <li class="participant-list__item<?= $participant['id'] === $me['id'] ? ' participant-list__item--me' : '' ?>"><?= h($participant['name']) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </section>

    <div class="join-area">
      <?php if ($isOrganizer): ?>
        <p class="status-message">あなたが主催する会です</p>
      <?php elseif ($isJoined): ?>
        <p class="join-area__note">参加登録済みです</p>
        <form method="post">
          <input type="hidden" name="action" value="cancel">
          <button class="btn btn--danger btn--large" type="submit">キャンセルする</button>
        </form>
      <?php elseif ($isFull): ?>
        <button class="btn btn--large" type="button" disabled>満員です</button>
      <?php else: ?>
        <form method="post">
          <input type="hidden" name="action" value="join">
          <button class="btn btn--primary btn--large" type="submit">参加する</button>
        </form>
      <?php endif; ?>
    </div>
  </article>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
