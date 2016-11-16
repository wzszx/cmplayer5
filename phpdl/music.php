<?php
ERROR_REPORTING(0);
header("Content-type: text/html; charset=utf-8");
$fname = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
$query = $_SERVER[QUERY_STRING];
parse_str($query);
if ($yahoo) {
        yahoo(urlencode($yahoo));
        exit;
}
if ($sougou) {
        sougou(urlencode($sougou));
        exit;
}
if ($baidu) {
        baidu(urlencode($baidu));
        exit;
}
if ($sina) {
        sina(urlencode($sina));
        exit;
}else{
echo "支持四种格式，?sina=歌曲名称, ?yahoo=歌曲名称 ?baidu=歌曲名称 ?sougou=歌曲名称";
}
function sina($name){
	$url="http://music.sina.com.cn/yueku/search/s.php?e=utf-8&t=all&k=$name";
	preg_match('/mpwPlay\(([0-9]+)\)/imsU',curl($url),$id);
	sid($id[1]);
	exit;
}
function sid($id) {
	$url = "http://m.v.iask.com/ask_n.php?oid=$id&pid=707";
	preg_match('|"([^"]+)"|U',curl($url),$mp3);
	if($mp3[1]){
	header("location:$mp3[1]");
	}else{
	$urls="http://music.sina.com.cn/yueku/m.php?id=$id&FLAG_ADDLIST=0&coFlag=100013";
	preg_match('/"url":"([^"]+)","/imsU',curl($urls),$murl);
	$mp3url=urldecode(base64_decode($murl[1]));
	preg_match('|iask_music_song_url="([^"]+)";|',curl($mp3url),$mp3);
	header("location:$mp3[1]");
	}

}
function sougou($name) {
        $url = "http://mp3.sogou.com/api/links2?query=$name&id=".rand();
        $str = iconv('GBK', 'UTF-8', curl($url));
        $obj = json_decode($str);
        $list = $obj->list;
        foreach ($list as $value) {
                $mp3 = $value->postfix;
                $mp3url = $value->url;
                if ($mp3 == 'mp3') {
                        header("location:$mp3url");
                }
                exit;
        }


}
function yahoo_gl($url){
        $domain=parse_url($url,PHP_URL_HOST);
        $domainend=end(explode('.',$domain));
        $domainarr=array('biz','info');
        if(in_array($domainend,$domainarr)){
        return false;
        }else{
        return true;
        }
}
function yahoo($str) {
        $url = "http://music.yahoo.cn/s?q=$str&m=5";
        preg_match_all('/&url=([^"]+)"/imsU', curl($url), $mp3);
        array_unique($mp3[1]);
        foreach($mp3[1] as $value){
        $mp3=urldecode($value);
          if(yahoo_gl($mp3)==true){
          header("location:$mp3");
          exit;
          }
        }

}

function baidu($str) {
        $url = 'http://box.zhangmen.baidu.com/x?op=12&count=1&mtype=2&title=' . $str .  '$$';
        $xml = simplexml_load_string(curl($url));
        $arr = $xml->url;
        foreach ($arr as $value) {
                $encode = $value->encode;
                $decode = $value->decode;
                $encode = str_replace(end(explode('/', $encode)), '', $encode);
                $domain = parse_url($encode, PHP_URL_HOST);
                $type = explode('?', $decode);
                $type = end(explode('.', $type[0]));
                if ($domain != "zhangmenshiting.baidu.com" && $type == "mp3") {
                        $src = $encode . $decode;
                        if($src){
                        header("location:$src");
                        }else{
                        return;
                        }
                        //echo $src;
                        break;
                }
        }
}


function curl($url) {
        $ch = curl_init();
        $timeout = 3;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
}
?>
