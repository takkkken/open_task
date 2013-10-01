<?php

/**
 * download_patch.php
 *
 * @copyright  2013 Systemsoft ishibashi
 * @license    BSD
 */

$encodingList[] = "UTF-8";
$encodingList[] = "CP932";
$encodingList[] = "SJIS";

$q = $_GET["q"];

list($tmp,$revId,$repPath) = preg_split("/:|@/",$q);

$patch_data = `svnlook diff $repPath -r $revId`;


$patch_html = $patch_data;

//文字エンコード自動判定
if( mb_detect_encoding($patch_html,$encodingList)=="CP932" ||
	mb_detect_encoding($patch_html,$encodingList)=="SJIS" 
){
	//エンコード変更
	$patch_html = mb_convert_encoding($patch_html,"UTF-8","CP932");
}

//特殊文字（タグ等）を実態参照に変換
$patch_html = htmlspecialchars($patch_html);

$patch_html = preg_replace("/\r\n|\n|\r/","\n<br>\n",$patch_html);
$patch_html = preg_replace("/^(Index:.+)$/m"	,"<span style='color:blue;font-weight: bold;'>\\1</span>"	,$patch_html);
$patch_html = preg_replace("/^(Modified:.+)$/m"	,"<span style='color:blue;font-weight: bold;'>\\1</span>"	,$patch_html);
$patch_html = preg_replace("/^(===.+)$/m"		,"<span style='background-color:gray;'>\\1</span>"			,$patch_html);
$patch_html = preg_replace("/^(\-\-\-.+)$/m"	,"<span style='background-color:gray;'>\\1</span>"			,$patch_html);
$patch_html = preg_replace("/^(\+\+\+.+)$/m"	,"<span style='background-color:gray;'>\\1</span>"			,$patch_html);
$patch_html = preg_replace("/^(\-.*)$/m"		,"<span style='background-color:#db7093;'>\\1</span>"		,$patch_html);
$patch_html = preg_replace("/^(\+.*)$/m"		,"<span style='background-color:#3cb371;'>\\1</span>"		,$patch_html);
$patch_html = preg_replace("/^(@@.+)$/m"		,"<span style='color:red;'><br>\\1</span>"						,$patch_html);


/* Output Data */
echo $patch_html;

echo "<br><br>";
print("<a href=download_patch.php?q=".$q.">パッチファイルのダウンロード</a>");



exit();