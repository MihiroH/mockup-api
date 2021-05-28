# API

## How to setup
- `$ docker-compose up -d`
- `$ docker-compose exec app /bin/sh`
- `$ php api/setup/export_prefs_regions_to_csv.php api/setup/list.csv`  
警告が表示されたら各csvに問題がないか確認。
```
例）
1. Warning: exists incorrect prefectures that is 大阪市 line on 1610.
=> api/setup/prefs.csvの1610行目の都道府県が間違っていることを確認。
   この場合は"大阪市"なので、prefs.csvに大阪府があるか確認。
   ない場合：最後の行に大阪府を追加（pref_codeは最後のものに1足したものにすること）
   ある場合：問題なし

2. Warning: exists incorrect region that is 取手市中央町2-5 line on 401.
=> おそらくapi/setup/regions.csvの401行目の住所に都道府県の記載がないので、必要であれば先方に確認する。

3. Warning: not exists pref_code key in this array on line 1610.
=> おそらくapi/setup/new_stores.csvの1610行目にpref_codeがないので、他のpref_code(都道府県コード)を参照して追加。
   もし他にない場合は、橋本に要相談。もしくは先方にマスターデータを直してもらう。

4. Warning: not exists region_code key in this array on line 1610.
=> おそらくapi/setup/new_stores.csvの1610行目にregion_codeがないので、他のregion_code(地域コード)を参照して追加。
   もし他にない場合は、橋本に要相談。もしくは先方にマスターデータを直してもらう。
```

- `$ exit`
- `$ docker-compose exec db /bin/sh`
- `$ mysql -u {{ ユーザー名 }} -p < /home/docker/data/setup/setup.sql`  
`例） $ mysql -u example_user -p < /home/docker/data/setup/setup.sql`  
=> パスワードを入力

- `$ exit`  
