<?php
function curl_get_contents($url)
{
if(!function_exists('curl_init')) {
return false;
} else {
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
$content = curl_exec($ch);
$errormsg = curl_error($ch);
curl_close($ch);
if($errormsg != '') {
echo $errormsg;
return false;
}
return $content;
}
}
function fsock_get_contents($url)
{
if(!function_exists('fsockopen')) {
return false;
} else {
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$url = eregi_replace('^http://', '', $url);
$temp = explode('/', $url);
$host = array_shift($temp);
$path = '/'.implode('/', $temp);
$temp = explode(':', $host);
$host = $temp[0];
$port = isset($temp[1]) ? $temp[1] : 80;
$fp = @fsockopen($host, $port, $errno, $errstr, 30);
if ($fp){
@fputs($fp, "GET $path HTTP/1.1\r\nHost: $host\r\nAccept: */*\r\nReferer:$url\r\nUser-Agent: $user_agent\r\nConnection: Close\r\n\r\n");
}
$content = '';
while ($str = @fread($fp, 4096)){
$content .= $str;
}
@fclose($fp);
//Redirect
if(preg_match("/^HTTP\/\d.\d 301 Moved Permanently/is", $content)){
if(preg_match("/Location:(.*?)\r\n/is", $content, $murl)){
return fsock_get_contents($murl[1]);
}
}
//Read contents
if(preg_match("/^HTTP\/\d.\d 200 OK/is", $content)){
preg_match("/Content-Type:(.*?)\r\n/is", $content, $murl);
$contentType = trim($murl[1]);
$content = explode("\r\n\r\n", $content, 2);
$content = $content[1];
}
return $content;
}
}
function get_page_contents($url)
{
$content = '';
if(function_exists('curl_init')) {
$content = curl_get_contents($url);
} elseif (function_exists('fsockopen')) {
$content = curl_get_contents($url);
} else echo "函数 curl_init 和 fsockopen 都为关闭,请至少打开一个.";
return $content;
}
function td_id($url)
{
if(preg_match('/http:\/\/hd.tudou.com\/program\/([A-Za-z0-9-_]+)/', $url, $hd)) {
$target_url = "http://hd.tudou.com/program/".$hd[1];
} elseif (preg_match('/http:\/\/www.tudou.com\/programs\/view\/([A-Za-z0-9-_]+)/', $url, $td)) {
$target_url = "http://www.tudou.com/programs/view/".$td[1];
}
$content = get_page_contents($target_url);
preg_match('/iid.*?([0-9]+)/', $content, $tudou);
$tudou_id = $tudou[1];
return $tudou_id;
}
function get_flv($video_id)
{
$flv_link = "";
$target_url = 'http://v2.tudou.com/v2/cdn?id='.$video_id;
$video_data = get_page_contents($target_url);
preg_match('/<f w=\"([0-9]+)\"/', $video_data, $match_num);
preg_match('/http:\/\/(.*?)\?/', $video_data, $match_url);
preg_match('/key=([\w]+)/', $video_data, $match_key);
$flv_link = 'http://'.$match_url[1].'?'.$match_num[1].'&key='.$match_key[1].'&id=tudou';
return $flv_link;
}
$url=get_flv(td_id($_GET['url']));
Header("HTTP/1.1 303 See Other");
header("location: $url");
?>