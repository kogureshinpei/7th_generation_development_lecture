<?php
// ② 会をつくる（担当：Bさん）
// 仕様：docs/requirements.md の「② 会をつくる」／見た目：http://localhost:8080/mockup/new.html
require_once __DIR__ . '/lib/current_user.php';

// フォームに入っている値。エラーで戻ってきたとき value に戻して、入力を消さないために使う
$values = [
    'title' => '',
    'starts_at' => '',
    'place' => '',
    'capacity' => '',
    'description' => '',
];
// 項目ごとのエラーメッセージ（キーは $values と同じ）
$errors = [];

// POST で送られてきたら、入力をチェックして events に保存し、detail.php へリダイレクトする
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $key => $value) {
        $input = $_POST[$key] ?? '';
        // 文字列以外（title[]=… のような配列）は空として扱う。前後の空白は取り除く
        $values[$key] = is_string($input) ? trim($input) : '';
    }

    // タイトル：必須・100 文字以内（日本語も 1 文字と数えるので mb_strlen）
    if ($values['title'] === '') {
        $errors['title'] = 'タイトルを入力してください';
    } elseif (mb_strlen($values['title']) > 100) {
        $errors['title'] = 'タイトルは 100 文字以内で入力してください';
    }

    // 開催日時：必須・今より後。datetime-local は「2026-10-29T19:00」の形で送られてくる
    if ($values['starts_at'] === '') {
        $errors['starts_at'] = '開催日時を入力してください';
    } else {
        $startsAt = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $values['starts_at']);
        // 2/30 のような日付は 3/2 に繰り上がってしまうので、元の文字列に戻るかでも確かめる
        if ($startsAt === false || $startsAt->format('Y-m-d\TH:i') !== $values['starts_at']) {
            $errors['starts_at'] = '開催日時の形が正しくありません';
        } elseif ($startsAt <= new DateTimeImmutable()) {
            $errors['starts_at'] = '開催日時は今より後の日時にしてください';
        }
    }

    // 場所：必須・100 文字以内
    if ($values['place'] === '') {
        $errors['place'] = '場所を入力してください';
    } elseif (mb_strlen($values['place']) > 100) {
        $errors['place'] = '場所は 100 文字以内で入力してください';
    }

    // 定員：必須・1〜50 の整数。HTML の min/max は書き換えられるので PHP でも確かめる
    if ($values['capacity'] === '') {
        $errors['capacity'] = '定員を入力してください';
    } elseif (filter_var($values['capacity'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 50]]) === false) {
        $errors['capacity'] = '定員は 1〜50 の整数で入力してください';
    }

    // 説明：必須
    if ($values['description'] === '') {
        $errors['description'] = '説明を入力してください';
    }

    // エラーが 1 つもなければ保存する。主催者は、いま操作しているユーザー
    if (count($errors) === 0) {
        $stmt = db()->prepare(
            'INSERT INTO events (organizer_id, title, description, place, starts_at, capacity)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            current_user()['id'],
            $values['title'],
            $values['description'],
            $values['place'],
            $startsAt->format('Y-m-d H:i:00'),
            (int) $values['capacity'],
        ]);

        // 保存後はリダイレクトする（再読み込みで同じ会が 2 件できないように）
        $eventId = (int) db()->lastInsertId();
        header('Location: /detail.php?id=' . $eventId);
        exit;
    }
}

$pageTitle = '会をつくる';
$narrow = true;
require __DIR__ . '/partials/header.php';
?>

<a class="back-link" href="/">← 一覧へ戻る</a>
<h1 class="page-title">会をつくる</h1>

<!-- 主催者は、いま操作しているユーザー（フォームには入れない） -->
<form class="form" method="post">
  <div class="form-field<?= isset($errors['title']) ? ' form-field--error' : '' ?>">
    <label class="label" for="title">タイトル<span class="label__required">必須</span></label>
    <input class="input" type="text" id="title" name="title" maxlength="100" placeholder="例：PHP もくもく会" value="<?= h($values['title']) ?>">
    <?php if (isset($errors['title'])): ?>
      <p class="field-error"><?= h($errors['title']) ?></p>
    <?php else: ?>
      <p class="field-hint">100 文字以内</p>
    <?php endif; ?>
  </div>

  <div class="form-field<?= isset($errors['starts_at']) ? ' form-field--error' : '' ?>">
    <label class="label" for="starts_at">開催日時<span class="label__required">必須</span></label>
    <input class="input" type="datetime-local" id="starts_at" name="starts_at" value="<?= h($values['starts_at']) ?>">
    <?php if (isset($errors['starts_at'])): ?>
      <p class="field-error"><?= h($errors['starts_at']) ?></p>
    <?php else: ?>
      <p class="field-hint">今より後の日時</p>
    <?php endif; ?>
  </div>

  <div class="form-field<?= isset($errors['place']) ? ' form-field--error' : '' ?>">
    <label class="label" for="place">場所<span class="label__required">必須</span></label>
    <input class="input" type="text" id="place" name="place" maxlength="100" placeholder="例：ラウンジ A／オンライン（Discord）" value="<?= h($values['place']) ?>">
    <?php if (isset($errors['place'])): ?>
      <p class="field-error"><?= h($errors['place']) ?></p>
    <?php else: ?>
      <p class="field-hint">100 文字以内</p>
    <?php endif; ?>
  </div>

  <div class="form-field<?= isset($errors['capacity']) ? ' form-field--error' : '' ?>">
    <label class="label" for="capacity">定員<span class="label__required">必須</span></label>
    <input class="input input--short" type="number" id="capacity" name="capacity" min="1" max="50" placeholder="6" value="<?= h($values['capacity']) ?>">
    <?php if (isset($errors['capacity'])): ?>
      <p class="field-error"><?= h($errors['capacity']) ?></p>
    <?php else: ?>
      <p class="field-hint">1〜50 人</p>
    <?php endif; ?>
  </div>

  <div class="form-field<?= isset($errors['description']) ? ' form-field--error' : '' ?>">
    <label class="label" for="description">説明<span class="label__required">必須</span></label>
    <textarea class="textarea" id="description" name="description" placeholder="何をする会か、持ち物など"><?= h($values['description']) ?></textarea>
    <?php if (isset($errors['description'])): ?>
      <p class="field-error"><?= h($errors['description']) ?></p>
    <?php endif; ?>
  </div>

  <div class="form-actions">
    <a class="btn" href="/">やめる</a>
    <button class="btn btn--primary" type="submit">会をつくる</button>
  </div>
</form>

<?php require __DIR__ . '/partials/footer.php'; ?>
