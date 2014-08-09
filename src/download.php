<?php
/**
 * できあがったファイルをダウンロードさせる
 */
header("Content-type: text/plain");
header("Content-Disposition: attachment; filename={$_GET['dlfile']}");
readfile($_GET['dlfile']);

// 変換後のファイルを削除
if (file_exists($_GET['dlfile'])) unlink($_GET['dlfile']);
if (file_exists($_GET['upfile'])) unlink($_GET['upfile']);