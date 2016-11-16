<?php
$url="http://www.youku.com/star/~ajax/opus.html?__rt=1&__ro=opus_list&uid=UMTQ2NzAzMg%3D%3D&u=Cascada&t=%E5%87%A4%E5%87%B0%E4%BC%A0%E5%A5%87&t=all&p=1&y=0&n=0&q=0";
$url=g_contents($url);
preg_match_all('/v_show\/id_(.*).html" title="(.*)"/imsU',$url,$arr);
$ids=$arr[1];
$xml="<list>\n";
$titles=$arr[2];
foreach($ids as $key=>$id ){
    if($key%2==0){  
     $xml .= '<m type ="youku"  streamtype="flv"  src="' . $id. '" label="' . $titles[$key] . '"/>' . "\n";
    }
}
$xml .="</list>\n";
echo $xml;
function g_contents($url) {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $ch = curl_init();
        $timeout = 30;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        @ $c = curl_exec($ch);
        curl_close($ch);
        return $c;
}
?>