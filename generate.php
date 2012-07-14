<!doctype html>
<html lang="ja">
	<head>
		<meta charset="UTF-8">
		<title>変換実行</title>
	</head>
	<body>
		<p><a href="./">戻る</a></p>

		<?php
			if (is_uploaded_file($_FILES['upfile']['tmp_name'])) {
				$date = date('YmdHis');
				$upfile = $date.'_mysql.sql';
				$dlfile = $date.'_sqlite3.sql';

				if (move_uploaded_file(
					$_FILES['upfile']['tmp_name'],
					$upfile
				)) {
					echo 'ファイルの変換を開始します。<br>';
					touch($dlfile);
					chmod($upfile, 0666);
					chmod($dlfile, 0666);

					$fp1 = fopen($upfile, 'r');
					$fp2 = fopen($dlfile, 'w');
					
					//********************************************
					//置換のコールバック関数
					//********************************************
					function my_callback($matches) {
						if (isset($matches[3]) && isset($matches[4])) {
							return $matches[4];
						} elseif (isset($matches[2]) && isset($matches[1])) {
							return $matches[1].'integer';
						}
					}
					//********************************************
					//ファイルへの書き込み
					//********************************************
					function my_fwrite($fp, $line) {
						//バッククオートをダブルクオートに
						$line = preg_replace('/(?<!\\\\)\`/', '"', $line);
						fwrite($fp, $line);
					}
					for ($i=1; $line=fgets($fp1); $i++) {
						//********************************************
						//変換処理
						//********************************************
						//--------------------------------------------
						//INSERT文の処理
						//--------------------------------------------
						//(phpMyAdmin)
						if (preg_match('/^(INSERT INTO \`([^\`]+)\` )\([^\)]+\) VALUES$/i', $line, $matches)) {
							echo "({$matches[2]}) INSERT文を変換します。<br>";
							$i++;
							$line = fgets($fp1);
							
							while (preg_match('/^(\([\s\S]+\))[,;]$/', $line, $matches2)) {
								$line = $matches[1].'VALUES'.$matches2[1].";\n";
								my_fwrite($fp2, $line);
								
								//次のループのための準備
								//※ ループの条件に適さないと、1行余分に読み込んだことになる。
								//このため、CREATE文よりも先にこの処理を記述しなければならない。
								$i++;
								$line = fgets($fp1);
							}
							
							echo "({$matches[2]}) INSERT文の変換を完了しました。<br>";
							//INSERT文が終わったら、INSERT文以外の変換作業へ移る。
						}
						//端末その他
						elseif (preg_match('/^(INSERT INTO \`([^\`]+)\` VALUES\s?)([\s\S]*\);)$/i', $line, $matches)) {
							echo "({$matches[2]}) INSERT文を変換します。<br>";
							while (preg_match('/^(\((?:(?:(?:\'[^\']*\')|[0-9]+),?)*\)),([\s\S]*)/', $matches[3], $matches2)) {
								$matches[3] = $matches2[2];
								$line = $matches[1].$matches2[1].";\n";
								my_fwrite($fp2, $line);
							}
							$line = $matches[1].$matches[3]."\n";
							my_fwrite($fp2, $line);

							echo "({$matches[2]}) INSERT文の変換を完了しました。<br>";
						}
						//--------------------------------------------
						//CREATE文の処理
						//--------------------------------------------
						if (preg_match('/^CREATE TABLE[\s\S]*\`([^\`]+)\`/i', $line, $matches)) {
							echo "({$matches[1]}) CREATE文を変換します。<br>";
							my_fwrite($fp2, $line);
							
							$i++;
							$line = fgets($fp1);
							

							while (!preg_match('/^\) ENGINE=/i', $line)) {
								//int系をintegerへ、AUTO_INCREMENTを削除する
								$line = preg_replace_callback('/^(\s*\`.+\`\s*)(int|tinyint|samllint|mediumint|bigint)(?:\(.+\))?|(AUTO_INCREMENT)(,?)$/i', 'my_callback', $line);
								my_fwrite($fp2, $line);

								$i++;
								$line = fgets($fp1);
							}
							//(phpMyAdmin, 端末) ") ENGINE=" は、ENGINE以降を削除
							fwrite($fp2, ");\n");
							$flag_create_table = false;
							echo "({$matches[1]}) CREATE文の変換を完了しました。<br>";
						}
					}
					
					echo 'ファイルの変換を完了しました。<br>';
					//変換前のファイルを削除
					unlink($upfile);
					echo 'ファイルを返信します。<br>';

					//JavaScriptでダウンロード用のページへジャンプする
					echo "<script type=\"text/javascript\">location.href = 'download.php?dlfile=$dlfile'</script>";
				} else {
					echo 'ファイルを送信できませんでした。';
				}
			} else {
				echo 'ファイルが選択されていません。';
			}
		?>
	</body>
</html>
