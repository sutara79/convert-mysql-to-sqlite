<?php
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
		if (preg_match('/^(INSERT INTO \`([^\`]+)\` )[\s\S]*VALUES$/i', $line, $matches)) {
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
			//INSERT文が終わったら、INSERT文以外の変換作業へ移る。
		}
		//端末その他
		elseif (preg_match('/^(INSERT INTO \`([^\`]+)\` VALUES\s?)([\s\S]*\);)$/i', $line, $matches)) {
			while (preg_match('/^(\((?:(?:(?:\'[^\']*\')|[0-9]+),?)*\)),([\s\S]*)/', $matches[3], $matches2)) {
				$matches[3] = $matches2[2];
				$line = $matches[1].$matches2[1].";\n";
				my_fwrite($fp2, $line);
			}
			$line = $matches[1].$matches[3]."\n";
			my_fwrite($fp2, $line);
		}
		//--------------------------------------------
		//CREATE文の処理
		//--------------------------------------------
		if (preg_match('/^CREATE TABLE[\s\S]*\`([^\`]+)\`/i', $line, $matches)) {
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
		}
		$return['pos']  = ftell($fp1);
	}
	$return['feof'] = feof($fp1);
	fclose($fp1);
	fclose($fp2);
	echo json_encode($return);
?>
