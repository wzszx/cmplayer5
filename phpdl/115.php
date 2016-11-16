<?
if(empty($_SERVER['HTTPS'])){
   $NEWURL='https://'.$_SERVER["HTTP_APPNAME"].'.sinaapp.com'.$_SERVER["SCRIPT_URL"];
   header("location:$NEWURL");
}
error_reporting(0);
header("Content-type:text/html; charset=utf-8");
$query=$_SERVER['QUERY_STRING'];
parse_str($query);
if($url&&$email&&$password){
u115($url,$email,$password);
exit;
}elseif($url){
u115($url);
exit;
}else{
echo "url不可为空";
}
function u115($url,$email='henszx@126.com',$password='henszx888'){
$len=strlen($url);
if($len==8||$len==9){
$url="http://115.com/file/".$url;
}
if($len>29||$len<8){
echo "地址不合法";
exit;
}

$cookie=tempnam('./temp','cookie');//COOKIES缓存目录 请直接手动创建一个
$post="login[account]=$email&login[passwd]=$password";
$curl=curl_init('http://passport.115.com/?ac=login&tpl=pc');
curl_setopt($curl,CURLOPT_HEADER,0);
curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
curl_setopt($curl,CURLOPT_POST,0);
curl_setopt($curl,CURLOPT_COOKIEJAR,$cookie);
curl_setopt($curl,CURLOPT_POSTFIELDS,$post);
curl_exec($curl);
curl_close($curl);

$ch=curl_init($url);
curl_setopt($ch,CURLOPT_HEADER,0);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie);
$data=curl_exec($ch);
curl_close($ch);

preg_match("/GetMyDownloadAddress\('([^']+)',/imsU",$data,$ajax);
$ajax='http://115.com/?ct=download&ac=get&'.$ajax[1];
$cf=curl_init($ajax);
curl_setopt($cf,CURLOPT_HEADER,0);
curl_setopt($cf,CURLOPT_RETURNTRANSFER,1);
curl_setopt($cf,CURLOPT_COOKIEFILE,$cookie);
$cq=curl_exec($cf);
curl_close($cf);
$isp=get_user_isp();
if(!$isp){
    $isp="电信";
}
$jsons=json_decode($cq)->urlssss;
foreach($jsons as $json){
    if($json->isp==$isp){
        $fileUrl=$json->url;
        header("location:$fileUrl");
        exit;
    }

}

}
echo get_user_isp();
function  get_user_isp(){
$ip = $_SERVER["REMOTE_ADDR"];
$url='http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=$ip';
$ch=curl_init($url);
curl_setopt($ch,CURLOPT_HEADER,0);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$data=curl_exec($ch);
curl_close($ch);
preg_match('|var remote_ip_info =(.*);$|',$data, $ar);
return  json_decode($ar[1])->isp;

}


?>