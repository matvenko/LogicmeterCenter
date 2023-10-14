<?php
include('init.php');
$get = unserialize(read_file('test.html'));

$get['result_code'] = 1;
$get['o_order_n'] = "73_3660581";
$get['amount'] = 1499;
$get['trx_id'] = time();

$url = "http://localhost/logicmeter/op/bog.php?";
foreach($get as $key => $value){
	$url .= $key."=".$value."&";
}

echo "<a href=\"".$url."\">Pay</a>";

echo "<pre>";
print_r($get);
echo "</pre>";