<?php

$q = $_SERVER['PATH_INFO'];

$p = array();

if($q){

$arr = explode('/',substr($q,1));

$c = count($arr);

if($c>0 && $c%2==0){

for($i=0;$i<$c;$i+=2){

$p[$arr[$i]] = $arr[$i+1];

}

}

}



$str = file_get_contents ("http://pan.baidu.com/share/link?shareid=$arr[1]&uk=$arr[3]");

        preg_match('/www.baidupcs.com(.*)\",\"/iU', $str, $res);

$md = $res[1];

Header("Location:http://www.baidupcs.com$md");

?>
