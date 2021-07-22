-- ユーザーを作成
CREATE USER 'xxxxx'@'localhost' IDENTIFIED BY 'xxxxx';

-- ユーザー一覧を表示
SELECT user, host FROM mysql.user;

-- データベースを作成
CREATE DATABASE xxxxx;

-- ユーザーにデータベースの権限を付与
GRANT CREATE, SELECT, INSERT, UPDATE, DROP, DELETE ON xxxxx.* TO 'xxxxx'@'localhost';

-- ユーザーのデ－タベースの権限を表示
SHOW GRANTS FOR 'xxxxx'@'localhost';

-- prefecturesテーブルを作成
CREATE TABLE xxxxx.prefectures (
  pref_code INT NOT NULL PRIMARY KEY,
  pref_name VARCHAR(255) NOT NULL
);

-- regionsテーブルを作成
CREATE TABLE xxxxx.regions (
  region_code INT NOT NULL PRIMARY KEY,
  pref_code INT NOT NULL,
  region_name VARCHAR(255) NOT NULL,

  FOREIGN KEY (pref_code)
    REFERENCES prefectures(pref_code)
);

-- storesテーブルを作成
CREATE TABLE xxxxx.stores (
  id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
  category VARCHAR(255) NOT NULL,
  company_name VARCHAR(255) NOT NULL,
  store_name VARCHAR(255) NOT NULL,
  postal_code VARCHAR(8) NOT NULL,
  prefectures VARCHAR(64) NOT NULL,
  address VARCHAR(255) NOT NULL,
  tel VARCHAR(13),
  pref_code INT NOT NULL,
  region_code INT NOT NULL,

  FOREIGN KEY (pref_code)
    REFERENCES prefectures(pref_code),

  FOREIGN KEY (region_code)
    REFERENCES regions(region_code)
);

-- DBを選択
USE xxxxx;

-- CSVデータをDBにインポート
LOAD DATA INFILE '/home/docker/data/setup/prefs.csv' INTO TABLE prefectures FIELDS TERMINATED BY ',' IGNORE 1 LINES (pref_code, pref_name);
LOAD DATA INFILE '/home/docker/data/setup/regions.csv' INTO TABLE regions FIELDS TERMINATED BY ',' IGNORE 1 LINES (region_code, pref_code, region_name);
LOAD DATA INFILE '/home/docker/data/setup/new_stores.csv' INTO TABLE stores FIELDS TERMINATED BY ',' IGNORE 1 LINES (category, company_name, store_name, postal_code, prefectures, address, tel, pref_code, region_code);

SELECT * FROM stores ORDER BY id LIMIT 5;
SELECT * FROM prefectures ORDER BY pref_code LIMIT 5;
SELECT * FROM regions ORDER BY region_code LIMIT 5;
