<?php
## Gladius Database Engine
# @author legolas558
# @version 0.6
# Licensed under GNU General Public License (GPL)
#
#
# Test example
#

$GLADIUS_DB_ROOT = './';

include '../gladius-testing.php';

$sql = file_get_contents('amy.sql');

gladius_test($sql);

?>