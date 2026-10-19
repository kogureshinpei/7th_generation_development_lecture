# もくもく会ボード

秋ハweek「開発講座」（7期）の題材です。
勉強会（もくもく会）を開いて参加者を集める小さな Web アプリを、**3人チームで 1人 1画面ずつ**、**素の PHP + MySQL + JavaScript** で作ります。

- 要件定義書：[docs/requirements.md](docs/requirements.md)
- モックアップ：起動後に http://localhost:8080/mockup/index.html

## 起動手順

Docker Desktop が起動している状態で、ターミナルで次を実行します。

```bash
git clone <チームのリポジトリの URL>
cd <リポジトリ名>
docker compose up -d
```

ブラウザで **http://localhost:8080/check.php** を開き、大きく「**準備OK**」と表示されれば完了です。

| URL | 中身 |
|---|---|
| http://localhost:8080/ | アプリ本体 |
| http://localhost:8080/mockup/index.html | モックアップ |
| http://localhost:8081/ | phpMyAdmin（DB の中身を見る。**見るだけ**） |

> phpMyAdmin は SELECT しかできないユーザーでログインしています。画面に「Edit」「Delete」ボタンは出ますが、押してもエラーになります。
> データを変えるのはアプリ（PHP）から。DB を直接いじると「アプリでは起きないはずのデータ」ができてしまうためです。

> 初回は Docker イメージのダウンロードとデータベースの準備で 1〜3 分かかります。
> 「まだ準備できていません」と出たら、少し待って再読み込みしてください。

止めるとき・データを初期状態に戻すとき：

```bash
docker compose down      # 止める（データは残る）
docker compose down -v   # 止めてデータも消す（次の up で初期データに戻る）
```

## チームの進め方

| 担当 | 画面 | ファイル | ブランチ名 |
|---|---|---|---|
| Aさん | ① 会の一覧 | `src/index.php` | `feature/index` |
| Bさん | ② 会をつくる | `src/new.php` | `feature/new` |
| Cさん | ③ 会の詳細・参加 | `src/detail.php` | `feature/detail` |

1. **全員で**：モックアップと要件定義書を読み、各画面で「画面・PHP・SQL のそれぞれで何が必要か」を書き出す
2. **各自**：自分のブランチを切る（`git switch -c feature/index` など）
3. **各自**：AI に要件定義書の該当部分とモックアップの HTML を渡して実装する。受け入れ条件を 1 つずつ自分で確かめる
4. **各自**：コミット → `git push -u origin feature/index` → GitHub で `main` 向けのプルリクエストを作る
5. **チーム**：他の人の PR を開き、受け入れ条件を手元で確かめてからマージする
6. **全員で**：`main` を pull して、① → ② → ③ と通しで動かす

> 自分の担当ファイル以外（`partials/`・`lib/`・`style.css`・`init.sql`）を変えたいときは、先にチームに相談します。
> 同じファイルを 2 人が変えると、マージのときにぶつかります（コンフリクト）。

## うまく動かないとき

| 症状 | 対処 |
|---|---|
| `port is already allocated`（8080 や 3307） | 他のアプリがそのポートを使っています。そのアプリを止めるか、`docker-compose.yml` の `"8080:80"` を `"8090:80"` のように左側だけ変えて、http://localhost:8090/check.php を開く |
| `Cannot connect to the Docker daemon` | Docker Desktop が起動していません。起動してからもう一度 `docker compose up -d` |
| check.php で「DB 接続」が NG | DB の起動中です。30 秒ほど待って再読み込み。直らなければ `docker compose logs db` でエラーを確認 |
| `db/init.sql` を書き換えたのに反映されない | init.sql は **DB を初めて作るときだけ** 実行されます。`docker compose down -v` → `docker compose up -d` で作り直す |
| 画面が真っ白・エラーが出る | `docker compose logs web` で PHP のエラーを確認 |
| DB の中身を見たい | http://localhost:8081/ の phpMyAdmin で、左の `mokumoku` → テーブル名を押す。SQL を打って確かめるなら上の「SQL」タブ（SELECT だけ実行できる） |

## フォルダ構成

```
.
├── docker-compose.yml   … Web サーバ・DB・phpMyAdmin をまとめて起動する設定
├── Dockerfile           … PHP + Apache の環境（PDO MySQL を有効化）
├── db/
│   ├── init.sql         … テーブル作成と初期データ
│   └── viewer.sql       … phpMyAdmin 用の「見るだけ」ユーザー
├── docs/
│   ├── requirements.md  … 要件定義書
│   └── mockup/          … モックアップ（http://localhost:8080/mockup/）
└── src/                 … ここがそのまま http://localhost:8080/ になる
    ├── check.php        … 環境確認ページ
    ├── index.php        … ① 会の一覧        ← Aさん
    ├── new.php          … ② 会をつくる      ← Bさん
    ├── detail.php       … ③ 会の詳細・参加  ← Cさん
    ├── lib/
    │   ├── db.php           … DB 接続 db()、エスケープ h()、日時の表示 format_datetime()
    │   └── current_user.php … 操作ユーザーの切替・取得 current_user()（ログインの代わり）
    ├── partials/
    │   ├── header.php   … 全ページ共通のヘッダー
    │   └── footer.php   … 全ページ共通のフッター
    └── assets/
        ├── style.css    … 見た目（モックアップと共通）
        └── main.js      … JavaScript を書く場所
```

## 1つの画面 ＝ 画面・PHP・SQL のつながり

どの画面も、上から順にこの流れで動きます。

```
ブラウザ ──(GET / POST)──▶ src/○○.php
                              │ ① require で共通部品を読む（lib/, partials/）
                              │ ② POST なら：入力チェック → SQL で保存 → リダイレクト
                              │ ③ SQL で表示に必要なデータを取る（db()->prepare → execute）
                              ▼
                           HTML を組み立てて返す（値は h() を通す）
```
