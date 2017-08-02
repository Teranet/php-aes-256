<?php

require_once 'Aes256.php';
$key = "7snx886/cTK+6CcPBGt8uEKgu9ZG3rOPNNsjA6IjVGQ=";

$Lotto = new Aes256($key);
//test data here
$array = array(
	'id'=>543,
	'name'=>'KABURU',
	'desc'=>'Simple data encryption  code implemetned in php using aes-256'
);
$data = json_encode($array);

$res = $Lotto->request($data,'http://somedomain.com/kaburu/api/v1/endpoint');
print_r($res);

?>
