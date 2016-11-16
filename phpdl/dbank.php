<?php
$uri = $_SERVER["REQUEST_URI"];
preg_match("/dbank.php\/(.+)\//",$uri,$code);
$code = $code[1];
$opts = array(
'http'=>array('method'=>"GET",'header'=>"User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.3)\r\n")
);//伪造User-Agent
$context = stream_context_create($opts);
$url = "http://dl.dbank.com/".$code;//原始下载页面
$data = file_get_contents($url,false,$context);
preg_match("/downloadUrl=.(.*?)..class=.gbtn.btn-xz./", $data, $data);
$myurl = $data[1];//获得下载地址
if($myurl){
header('Content-Type:application/force-download');//强制下载
header("Location:".$myurl);
die();
}
else echo "Error";
?>