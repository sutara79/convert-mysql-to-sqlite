<?php
	//********************************************
	//置換のコールバック関数
	//********************************************
	function callback_create($matches) {
		if (isset($matches[4])) {
			return '';
		} elseif (isset($matches[3])) {
			return '';
		} elseif (isset($matches[2]) && isset($matches[1])) {
			return $matches[1].'integer';
		}
	}
	function callback_backquote($matches) {
		if (isset($matches[2])) {
			//SQLite用に、バッククオートをダブルクオートに変換する。
			return '"';
		} elseif (isset($matches[1])) {
			//クオートで囲まれた中にあるバッククオートは変換しない。
			return $matches[1];
		}
	}
	//********************************************
	//ファイルへの書き込み
	//********************************************
	function my_fwrite($fp, $line) {
		//バッククオートをダブルクオートに
		$line = preg_replace_callback('/(\'(?:(?:(?!\\\\).)?(?:(?:\\\\\\\\)*\\\\)\'|[^\'])*\')|(`)/us', 'callback_backquote', $line);

		//'\n'を改行に変換する
		$line = preg_replace('/((?:(?!\\\\).)?(?:\\\\\\\\)*)(\\\\n)/us', '\1'."\n", $line);

		fwrite($fp, $line);
	}
	//********************************************
	//変換処理
	//********************************************
	//ファイルを開く
	$fp1 = fopen($_GET['upfile'], 'r');
	$fp2 = fopen($_GET['dlfile'], 'a');
	//JavaScriptへ返す配列を準備
	$return = array(
		'feof' => false,
		'pos'  => $_GET['pos']
	);
	fseek($fp1, $return['pos']);
	for ($i=0; ($line = fgets($fp1)) && ($i < 100); $i++) {
		//--------------------------------------------
		//INSERT文の処理
		//--------------------------------------------
		//(phpMyAdmin)
		if (preg_match('/^(INSERT INTO \`[^\`]+\` )[\s\S]*VALUES$/ui', $line, $matches)) {
			$i++;
			$line = fgets($fp1);
			
			while (preg_match('/^([\s\S]+),$/u', $line, $matches2)) {
				$line = $matches[1].'VALUES'.$matches2[1].";\n";
				my_fwrite($fp2, $line);
				
				//次のループのための準備
				//※ ループの条件に適さないと、1行余分に読み込んだことになる。
				//このため、CREATE文よりも先にこの処理を記述しなければならない。
				$i++;
				$line = fgets($fp1);
			}
			if (preg_match('/;$/u', $line)) {
				$line = $matches[1].'VALUES'.$line."\n";
				my_fwrite($fp2, $line);
			}
			//INSERT文が終わったら、INSERT文以外の変換作業へ移る。
		}
		//端末その他
		elseif (preg_match('/^(INSERT INTO \`[^\`]+\` VALUES\s?)([\s\S]*\);)$/ui', $line, $matches)) {
			while (preg_match_all('/^(\((?:(?:\'(?:(?:(?!\\\\).)?(?:(?:\\\\\\\\)*\\\\)\'|[^\'])*\'|[0-9]+|NULL),? ?)+\)), ?([\s\S]*)/ui', $matches[2], $matches2)) {
				$matches[2] = $matches2[2];
				$line = $matches[1].$matches2[1].";\n";
				my_fwrite($fp2, $line);
			}
			$line = $matches[1].$matches[2]."\n";
			my_fwrite($fp2, $line);
		}
		//--------------------------------------------
		//CREATE文の処理
		//--------------------------------------------
		if (preg_match('/^CREATE TABLE[\s\S]*(\`[^\`]+\`)/ui', $line, $matches)) {
			my_fwrite($fp2, "CREATE TABLE {$matches[1]} (\n");
			
			$i++;
			$line = fgets($fp1);
			
			$j = 0;
			while (!preg_match('/^\) ENGINE=/i', $line)) {

				//int系をintegerへ、AUTO_INCREMENTを削除する
				if (!preg_match('/^\s*UNIQUE KEY/ui', $line)) {
					//以下の置換で行末のカンマを削除している。CREATE文が続くなら、カンマを復活させる。
					if ($j > 0) fwrite($fp2, ",\n");
					$j++;

					$line = preg_replace_callback('/^(\s*`[^`]+`\s*)(int|tinyint|samllint|mediumint|bigint)(?:\([^\)]+\))?|(AUTO_INCREMENT),?\\n$|(,\\n)$/ui', 'callback_create', $line);
					my_fwrite($fp2, $line);
				}
				$i++;
				$line = fgets($fp1);
			}
			//(phpMyAdmin, 端末) ") ENGINE=" は、ENGINE以降を削除
			fwrite($fp2, "\n);\n");
			$flag_create_table = false;
		}
		$return['pos']  = ftell($fp1);
	}
	$return['feof'] = feof($fp1);
	fclose($fp1);
	fclose($fp2);
	echo json_encode($return);
?>
