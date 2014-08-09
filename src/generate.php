<?php
/**
 * 非同期でファイルを少しずつ変換する。
 */
$date = date('YmdHis');
$upfile = $date.'_mysql.sql';
$dlfile = $date.'_sqlite3.sql';
?>
<!doctype html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title></title>
		<script type="text/javascript" src="//code.jquery.com/jquery.min.js"></script>
		<script type="text/javascript">
			var filesize;
			var upfile = '<?php echo $upfile ?>';
			var dlfile = '<?php echo $dlfile ?>';
			function getFileBitByBit(pos) {
				$.getJSON(
					'transfer.php',
					{
						pos: pos,
						upfile: upfile,
						dlfile: dlfile
					},
					function(json) {
						var per = Math.floor(json.pos / filesize * 100);
						$('#msg').val(per + '% complete.\n');

						// 最後まで読み込んでいなければ、再び実行
						if (json.feof) {
							$('#msg').val('100% complete.\nA file is send back.');
							location.href = 'download.php?upfile=' + upfile + '&dlfile=' + dlfile;
						} else {
							getFileBitByBit(json.pos);
						}
					}
				);
			}
			jQuery(document).ready(function($) {
				getFileBitByBit(0);
			});
		</script>
		<style type="text/css">
		</style>
	</head>
	<body>
		<p><a href="../">Back</a></p>
		<?php
			if (is_uploaded_file($_FILES['upfile']['tmp_name'])) {
				if (move_uploaded_file(
					$_FILES['upfile']['tmp_name'],
					$upfile
				)) {
					touch($dlfile);
					chmod($upfile, 0666);
					chmod($dlfile, 0666);
					echo '<script>filesize = '.filesize($upfile).'</script>';
				} else {
					echo 'Failed to send a file.';
				}
			} else {
				echo 'File is not selected.';
			}
		?>
		<textarea id="msg" rows="4" cols="40"></textarea>
	</body>
</html>