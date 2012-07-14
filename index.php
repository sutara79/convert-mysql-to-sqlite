<!doctype html>
<html lang="ja">
	<head>
		<meta charset="UTF-8">
		<title>Convert MySQL to SQLite</title>
		<style type="text/css">
			form {
				border : 1px solid #aaa;
				border-radius : 4px;
			}
			address {
				margin-top:2em;
				border-top:2px solid #486;
				padding-top:8px;
			}
		</style>
	</head>
	<body>
		<h1>Convert MySQL to SQLite</h1>
		<p>Last update : 2012-07-14</p>
		<p>容量制限 : 10MB以内</p>
		<p>ローカルで実行する際の要件 : PHP5, JavaScript</p>
		<p>phpMyAdminや端末でダンプしたMySQLのSQLファイルを、SQLiteでインポートできるように加工します。</p>
		<p>
			重要なデータベースはWeb上で変換せず、ソースコードをダウンロードして、ローカル環境でPHPを動かして使ってください。<br>
			<a href="https://github.com/SutaraLumpur/convert_mysql_to_sqlite/zipball/master">GitHubからダウンロード</a>
		</p>
		<form action="generate.php" method="POST" enctype="multipart/form-data">
			<input type="file" name="upfile">
			<input type="submit" name="submit" value="Submit">
		</form>

		<!--**************************************************** -->

		<address>
			Author : sutara_lumpur /
			<a href="http://d.hatena.ne.jp/sutara_lumpur/20120714/1342269933">Blog</a> /
			<a href="http://twitter.com/sutara_lumpur">Twitter</a> /
			<img src="mail_image.png" alt="mail address">
		</address>
	</body>
</html>
