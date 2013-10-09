
open task と wwh と websvn をマッシュアップした、少人数チームで活用できそうな BTS です。



＜注意＞
SVNコミット時にチケットコメントを追記するようにするには、svn_util\readme.txt を参照ください。


インストール方法
	１．DB作成
		CreateDataBase権限のあるユーザを用意
		install\tables.sql を MySQLにて実行
	２．設置
		適当なパスに全ソースを設置
		パーミッションはApacheユーザが参照、書き込み可能に。
	３．設定
		@config.ini を 変更します。


環境
	CentOs6.4
		MySQL
		PHP
		Apache


クライアント
	Chrome 30.0
	FireFox 23.0
	IE 10.0



