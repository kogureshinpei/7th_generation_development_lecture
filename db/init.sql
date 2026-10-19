-- テーブル作成と初期データ。DB を初めて作るときだけ実行される
-- 書き換えたら docker compose down -v → docker compose up -d で作り直す
SET NAMES utf8mb4;
SET time_zone = '+09:00';

-- 操作ユーザー（ログイン機能の代わりに、ヘッダーで切り替える）
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL
);

-- もくもく会（勉強会）
CREATE TABLE events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  organizer_id INT NOT NULL,               -- 主催者（users.id）
  title VARCHAR(100) NOT NULL,
  description TEXT NOT NULL,
  place VARCHAR(100) NOT NULL,
  starts_at DATETIME NOT NULL,             -- 開催日時
  capacity INT NOT NULL,                   -- 定員
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (organizer_id) REFERENCES users(id)
);

-- 参加登録（どのユーザーが、どの会に参加するか）
CREATE TABLE participants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  event_id INT NOT NULL,
  user_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (event_id, user_id),              -- 同じ人が同じ会に 2 回登録できない
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO users (name) VALUES
  ('あおい'), ('けんた'), ('さくら'), ('はると'), ('みお');

-- 日付は「今日」からの相対で入れる（いつ起動しても未来の会がある）
INSERT INTO events (organizer_id, title, description, place, starts_at, capacity) VALUES
  (1, 'PHP もくもく会', 'カリキュラムの PHP 課題を各自進めます。詰まったら声をかけ合いましょう。', 'ラウンジ A',
     TIMESTAMP(CURDATE() + INTERVAL 2 DAY, '19:00:00'), 6),
  (2, 'SQL 演習会', 'JOIN と GROUP BY の練習問題を一緒に解きます。', 'オンライン（Discord）',
     TIMESTAMP(CURDATE() + INTERVAL 4 DAY, '20:00:00'), 3),
  (3, '秋ハ アイデア出し', 'ハッカソンで作りたいものを持ち寄って話します。手ぶらで OK。', '会議室 3',
     TIMESTAMP(CURDATE() + INTERVAL 7 DAY, '18:30:00'), 10),
  (4, 'Git 入門ハンズオン', 'ブランチ・コミット・プルリクエストを手を動かして覚えます。', 'ラウンジ B',
     TIMESTAMP(CURDATE() - INTERVAL 3 DAY, '19:00:00'), 8);

INSERT INTO participants (event_id, user_id) VALUES
  (1, 1), (1, 3),
  (2, 2), (2, 3), (2, 5),   -- SQL 演習会は定員 3 で満員
  (3, 3),
  (4, 1), (4, 2), (4, 4);
