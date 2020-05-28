# リスク通知システム

店舗やイベント会場にて感染症などのリスクが発生した際に、QRコードとメールを利用して来店・来場された方に注意喚起するためのシステムとなります。  
本システムは誰でも無償で利用することのできるオープンソースとなっています。

# 推奨環境

* CentOS 7.8
* PHP 7.4.6
* MariaDB 10.4.13
* Apache/2.4.6
# Installation

本手順において、データベースやWebサーバの導入に関しては省略しています。

* PHP
```bash
$ yum install epel-release
$ rpm -Uvh http://rpms.famillecollet.com/enterprise/remi-release-7.rpm
$ yum install --enablerepo=remi,remi-php74 php php-pdo php-mbstring php-dom php-gd php-mysqlnd
```

* composer
```bash
$ php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
$ php -r "if (hash_file('sha384', 'composer-setup.php') === 'e0012edf3e80b6978849f5eff0d4b4e4c79ff1609dd1e613307e16318854d24ae64f26d17af3ef0bf7cfb710ca74755a') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
$ php composer-setup.php
$ php -r "unlink('composer-setup.php');"
# Globallyにcomposerを利用するために/usr/local/bin/に移動させる
$ mv composer.phar /usr/local/bin/composer 
```

* プロジェクト本体  
本システムは、PHPフレームワーク「Laravel」にて構築されています。  
Laravelの詳細につきましては、[Laravel公式サイト](http://laravel.jp/)をご確認ください。
```bash
$ git clone https://github.com/cfm-system-team/hazard_alert.git
$ cd tracking
$ composer install
$ cp .env.sample .env
$ php artisan key:generate
$ vi .env # データベースなどの環境情報を設定
$ php artisan migrate
```

# Usage

1. QRコードの発行

まず初めに、店舗・イベント等の責任者にメールアドレス登録用のQRコードを発行していただきます。  
QRコードは、下記URLにアクセスし必要事項を記入の上、QRコート生成ボタンをクリックすると発行されます。  
```
/group/register
```

2. 店舗・イベント等へ来店・来場された方のメールアドレス登録

発行されたQRコードを店舗やイベント会場に掲示し、来店・来場された方にスマートフォン等で読み取っていただきます。  
QRコードを読み取っていただくと、メールアドレス登録画面が表示されますので、来店・来場された方のメールアドレスを登録していただきます。
これにより、人、場所、来店・来場日時の情報を紐づけることができます。  
なお、本システムではメールアドレス以外の情報は登録されません。  

3. リスク発生時

本システムに登録した店舗やイベント等で感染症などのリスクが発生した場合、システム管理者などが下記URLにアクセスし、該当する店舗やイベント会場にて登録されたメールアドレスのリストをダウンロードし、メールにて該当者に注意喚起を促すことができます。  
なお、デフォルト設定では下記URLにベーシック認証がかけられています。  

```
/recipient/search
```

# Note
本システムを利用する上で、下記のファイルに関しては、ご自身で定義する必要があります。
* resources/views/terms_organization.blade.php  
店舗やイベント情報を登録する事業者用の利用規約を表示するためのファイルです。  
ご自身の利用シーンに合わせて、こちらのファイルに規約を定義してください。

* resources/views/terms_user.blade.php  
QRコードを読み取ってメールアドレスを登録する利用者用の利用規約を表示するためのファイルです。  
ご自身の利用シーンに合わせて、こちらのファイルに規約を定義してください。  

* resources/views/pop_pdf.blade.php  
生成したQRコードを三角ポップ用のレイアウトにはめ込んだPDFを表示するためのファイルです。  
ご自身の利用シーンに合わせて、こちらのファイルにデザインを組み込んでください。  

* resources/views/poster_pdf.blade.php  
生成したQRコードをA4サイズのレイアウトにはめ込んだPDFを表示するためのファイルです。  
ご自身の利用シーンに合わせて、こちらのファイルにデザインを組み込んでください。  

# Contribution
hazard_alertはOSSとして開発されており、IssueやPull requestの管理はすべてGitHub上で行われています。  
そのため、バグ報告や改善の提案・修正などは以下の方法で行うことができます。これらはいつでも歓迎しています。  
なお、開発スケジュールの都合上、リクエストの受付を停止させていただく場合がございますがご了承ください。  

### Issues
バグ報告や改善の提案、実装の方針の相談などは、GitHubの[Issues](https://github.com/cfm-system-team/hazard_alert/issues)より行うことができます。
* タイトルを見ただけで話題が分かるようにしてください
* ひとつのIssueの話題はひとつにしてください

[issueの作成はこちらから行えます。](https://github.com/cfm-system-team/hazard_alert/issues/new)

### Pull requests
バグ修正・改善の実装は、GitHubの[Pull requests](https://github.com/cfm-system-team/hazard_alert/pulls)より行うことができます。  

基本的にドキュメントやコードの変更は直接Pull requestで送っていただければと思います。  
しかし、新機能の追加や規格の制約の変更、実装の方針の相談があるものなどは、まずIssueの作成をしてください。  

また、複数の範囲にわたる問題を解決する場合は、1つのPull requestに対して1つのトピックになるように分割し、なるべく小さな変更のみを含むように努めてください。

詳細は以下のPull requestの作成方法を参考にしてください。

## Pull requestの作成方法
hazard_alertを変更しGitHubでPull requestを作成する方法を紹介します。以下の2通りがあります。

### GitHub上で編集する
ドキュメントの小さな変更や、コメント中のtypoの修正などは、GitHub上での編集が便利です。  
[Editing files in another user's repository](https://help.github.com/en/github/managing-files-in-a-repository/editing-files-in-another-users-repository) を参考にしてください。

### ローカルで編集する
1.　`cfm-system-team/hazard_alert` をforkし、手元にcloneする  
2.　ブランチを作る `git checkout -b hogehoge`  
3.　変更を加える  
4.　エラーが発生しないことを確認し、変更をコミットする  
5.　forkしたリポジトリにpushする  
6.　Pull Requestを送る  

# Author

* クリプトン・フューチャー・メディア株式会社  
https://www.crypton.co.jp/

# License

本システムは、[MITライセンス](https://opensource.org/licenses/mit-license.php)に準拠しています。
