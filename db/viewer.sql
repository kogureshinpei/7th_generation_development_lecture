-- phpMyAdmin 用の「見るだけ」ユーザー。SELECT しかできないので、GUI から書き換えようとしてもエラーになる
-- init.sql の後に実行される（ファイル名の順）
CREATE USER 'viewer'@'%' IDENTIFIED BY 'viewer';
GRANT SELECT ON mokumoku.* TO 'viewer'@'%';
