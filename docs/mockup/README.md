# モックアップ

画面の見た目の正解。`docker compose up -d` のあと http://localhost:8080/mockup/ で見られる。
PHP で画面を作るときは、ここの HTML をコピーして、見本の文字を `<?= h(...) ?>` に置き換える。

| ファイル | 画面 |
|---|---|
| `index.html` | ① 会の一覧 |
| `new.html` | ② 会をつくる（下にエラー時の見本） |
| `detail.html` | ③ 会の詳細・参加（下にボタン 4 状態・見つからないときの見本） |

黄色い点線の枠（`mock-note`）は説明用の見本。アプリ本体には入れない。

## クラス一覧（`src/assets/style.css`）

### 全画面共通

| クラス | 使う場所 |
|---|---|
| `site-header` / `site-header__inner` / `site-header__actions` | ヘッダー（header.php） |
| `site-title` | 左上のアプリ名 |
| `user-switch` | 操作ユーザーの切り替え |
| `container` / `container--narrow` | `<main>`。narrow は 1 列の画面（②③） |
| `page-title` | ページの見出し `<h1>` |
| `back-link` | 「← 一覧へ戻る」 |
| `btn` | ボタン（`<a>` でも `<button>` でも） |
| `btn--primary` | メインの操作（会をつくる・参加する） |
| `btn--danger` | 取り消し（キャンセルする） |
| `btn--large` | 大きいボタン（詳細の参加ボタン） |
| `badge` / `badge--full` | 小さな札。`--full` は「満員」 |
| `meta-list` / `meta-row` | 日時・場所・主催の行（`<dl>` と `<div><dt><dd>`）。一覧と詳細で共通 |
| `empty` | 0 件・見つからないときのメッセージ |

### ① 一覧

| クラス | 使う場所 |
|---|---|
| `event-list` | カードを並べる `<ul>` |
| `event-card` | カード 1 枚（`<a>`。全体がリンク） |
| `event-card__head` / `event-card__title` | タイトルとバッジの行 |
| `event-card__count` | 「参加 2 / 6 人」（数字は `<strong>`） |

### ② 会をつくる

| クラス | 使う場所 |
|---|---|
| `form` | フォーム全体の白い枠 |
| `form-field` | 1 項目（ラベル＋入力＋メッセージ） |
| `form-field--error` | エラーがある項目。入力欄が赤くなる |
| `label` / `label__required` | 項目名と「必須」 |
| `input` / `input--short` / `textarea` | 入力欄。`--short` は定員など短いもの |
| `field-hint` | 「100 文字以内」などのヒント |
| `field-error` | エラーメッセージ |
| `form-actions` | 送信ボタンの並び |

### ③ 詳細・参加

| クラス | 使う場所 |
|---|---|
| `event-detail` | 会の情報の白い枠（`<article>`） |
| `event-detail__head` / `event-detail__title` | タイトルとバッジ |
| `event-detail__description` | 説明（改行をそのまま出す） |
| `section-title` | 「説明」「参加者」の小見出し |
| `participant-list` / `participant-list__item` | 参加者の名前 |
| `participant-list__item--me` | 操作ユーザー本人（任意） |
| `participant-list__empty` | 参加者 0 人のとき |
| `join-area` | 参加ボタンを置く場所 |
| `join-area__note` | ボタンの上の補足（参加登録済みです） |
| `status-message` | ボタンの代わりの文（あなたが主催する会です） |

満員は `<button class="btn btn--large" disabled>満員です</button>`（`disabled` で灰色になる）。

### その他

| クラス | 使う場所 |
|---|---|
| `check-table` / `ok` / `ng` / `check-result` / `check-result--ok` / `check-result--ng` | 環境チェック（check.php） |
| `mock-note` / `mock-note__title` / `mock-note__text` / `mock-note__grid` / `mock-note__label` | モックアップの注記だけで使う |
