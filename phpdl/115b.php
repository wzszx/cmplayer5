<?php
/*
* 空间需要支持allow_url_fopen
* 外链形式：http://域名/115b.php/提取码/xxx.xxx
*PS：获取联通线路 $myurl = $json->data[0]->url;
*PS：获取电信线路 $myurl =  $json->data[1]->url;
*/
$uri = $_SERVER["REQUEST_URI"];
preg_match("/115b.php\/(.+)\//",$uri,$code);//自己修改
$code = $code[1];
$opts = array(
'http'=>array('method'=>"GET",'header'=>"User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.3)\r\n")
);//伪造User-Agent
$context = stream_context_create($opts);
$url = "http://115.com/?ct=pickcode&ac=guest_download&pickcode=".$code."&r=".strtotime("now")."&token=f2afd690dd1cdfe9677a6dc1c018812d92280e12";
$data = file_get_contents($url,false,$context);
$data = str_replace("//","",$data);
$data = json_decode($data);
//print_r($json);
$myurl = $data->data[0]->url;
//联通: $myurl = $data->data[0]->url;
//电信: $myurl = $json->data[1]->url;
if($myurl){
header('Content-Type:application/force-download');//强制下载
header("Location:".$myurl);
die();
}
else 
//echo "对不起，提取码不存在或已过期！";
header("Location:"."http://www.cenfun.com/");
die();
?>