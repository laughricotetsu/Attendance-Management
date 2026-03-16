# Attendance Management（勤怠管理アプリ）  
  
勤怠の打刻（出勤・退勤・休憩）を管理し、勤怠修正申請と管理者による承認フローを提供するWebアプリケーションです。  
ユーザーは日々の勤怠を記録し、誤った打刻があった場合には修正申請を行うことができます。  
管理者はその申請内容を確認し、承認または却下することができます。  
  
## 主な機能  
  
### ユーザー機能  
ユーザー登録  
ログイン / ログアウト  
出勤打刻  
退勤打刻  
休憩開始 / 終了  
勤怠修正申請  
  
### 管理者機能  
勤怠修正申請の承認 / 却下  
ユーザー管理  
  
# 開発環境構築  
  
## Docker起動  
docker-compose up -d  
## Laravelセットアップ  
composer install  
cp .env.example .env  
php artisan key:generate  
## マイグレーション  
php artisan migrate  
  
## メール認証
mailhogというツールを使用しています。<br>
  
  
## テーブル仕様  
### テーブル一覧  
  
### users  
id	ユーザーID  
name	ユーザー名  
email	メールアドレス  
email_verified_at	メール認証日時  
password	パスワード  
role	権限（user / admin）  
created_at	作成日時  
updated_at	更新日時  
  
### attendances  
id	勤怠ID  
user_id	ユーザーID  
work_date	勤務日  
clock_in	出勤時間  
clock_out	退勤時間  
status	勤怠状態  
remarks	備考  
created_at	作成日時  
updated_at	更新日時  
  
### breaks  
id	休憩ID  
attendance_id	勤怠ID  
break_start	休憩開始時間  
break_end	休憩終了時間  
created_at	作成日時  
updated_at	更新日時  
  
### attendance_correction_requests  
id	修正申請ID  
attendance_id	勤怠ID  
user_id	申請ユーザー  
status	申請状態  
reason	申請理由  
approved_by	承認者  
approved_at	承認日時  
created_at	作成日時  
updated_at	更新日時  
  
### attendance_correction_request_details  
id	詳細ID  
request_id	修正申請ID  
target_type	修正対象  
target_id	修正対象ID  
before_value	修正前  
after_value	修正後  
created_at	作成日時  
updated_at	更新日時  
  
## リレーション  
  
users	1 : N attendances  
attendances	1 : N breaks  
attendances	1 : N attendance_correction_requests  
users	1 : N attendance_correction_requests  
attendance_correction_requests	1 : N attendance_correction_request_details  
  
## ER図
![alt](attendance_erd.drawio.png)
  
  
  
  
## PHPUnitを利用したテストに関して
以下のコマンド:  
```
//テスト用データベースの作成
docker-compose exec mysql bash
mysql -u root -p
//パスワードはrootと入力
create database test_database;

docker-compose exec php bash
php artisan migrate:fresh --env=testing
./vendor/bin/phpunit
```

