<?php

/**
 * download_patch.php
 *
 * @copyright  2013 Systemsoft ishibashi
 * @license    BSD
 */


$q = $_GET["q"];

list($tmp,$revId,$repPath) = preg_split("/:|@/",$q);

$patch_data = `svnlook diff $repPath -r $revId`;

/* Output HTTP Header */
header('Content-Disposition: inline; filename="rev_'.$revId.'.patch"');
header('Content-Type: application/octet-stream');

/* Output Data */
echo $patch_data;


exit();