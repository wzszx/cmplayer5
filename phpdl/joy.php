<?php
header("Content-type: text/xml; charset=utf-8");
$thisurl = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
$query = $_SERVER[QUERY_STRING];
parse_str($query);
if($first){
first($first);
exit;	
}
if ($moive) {
	moive($moive);
	exit;
}
if ($joyid) {
	joyid($joyid);
	exit;
}
if ($playid) {
	playid($playid);
	exit;
}
if ($spage) {
	spage($spage);
	exit;
}
if ($page) {
	page($page);
	exit;
} else {
	all();
	exit;
}
function all() {
global $thisurl;
$url="http://chunwan.joy.cn/videolist/_0_1/1.htm";
$str=preg(curl($url,0),'/<h4 id="catebtn(.*)<\/ul>/imsU',1);
array_shift($str); 
$c=array();
foreach($str as $value){
	$m=preg($value,'/>([^<]+)<\/a><\/span>/',0);
	$l=preg($value,'/<li ><a href="([^<]+)<\/a><\/li>/',1);
	$c+=array($m=>$l);
}
$list="<list>\n";
foreach($c as $k=>$v){
	$list.="<m label=\"$k\">\n";
	foreach($v as $vs){
	$vs=explode('" >',$vs);
	if(preg_match('/teleplay/', $vs[0])||preg_match('/movie/', $vs[0])){
	$list.="<m  list_src=\"$thisurl?first=$vs[0]\" label=\"$vs[1]\" />\n";
	}else{
	$list.="<m  list_src=\"$thisurl?moive=$vs[0]\"  label=\"$vs[1]\" />\n";
	}
	
	}
	$list.="</m>\n";
}
	echo $list."</list>";
}
function page($url) {
	$joyid = preg(curl($url, 0), '/videoId:"([0-9]+)"/imsU', 0);
	joyid($joyid);
}
function spage($url) {
	$list = "<list>\n";
	global $thisurl;
	if (preg_match('/teleplay/', $url)) {
		$url = str_replace('middle', 'detail', $url);
		$arr = preg(curl($url, 0), '/<li vid="([0-9]+)">/imsU', 1);
		foreach ($arr as $key => $value) {
			$tvnum = $key +1;
			$list .= "<m type=\"2\" src=\"$thisurl?joyid=$value\" label=\"第" . $tvnum . "集\" />\n";
		}
	}
	elseif (preg_match('/movie/', $url)) {
		$tvstr = preg(curl($url, 0), '/<div class="vpagesr">(.*)<\/p>/imsU', 0);
		$tvarr = preg($tvstr, '/href="([^"]+)"/imsU', 1);
		foreach ($tvarr as $key => $value) {
			$movienum = $key +1;
			$list .= "<m type=\"2\" src=\"$thisurl?page=http://v.joy.cn$value\" label=\"第" . $movienum . "段\"/>\n";
		}
	} else {
		page($url);
	}
	echo $list . "</list>";
}

function moive($url) {
	$num = pagenum($url);
	$str = "";
	if ($num > 1) {
		$url = str_replace('1.htm', '', $url);
		for ($i = 1; $i <= $num; $i++) {
			$l_url = "$url$i.htm";
			$str .= curl($l_url, 0);
		}
	} else {
		$str = curl($url, 0);
	}
	if(preg_match('/teleplay/', $url)||preg_match('/movie/', $url)){
	$arr = preg($str, '/<h3><a href="([^>]+)">/imsU', 1);
	echo makexml($arr, '" target="_blank" title="', 'spage', 1);
	}else{
	$arr = preg($str, '/<h3><a href="([^>]+)" target="_blank">/imsU', 1);
	echo makexml($arr, '" title="', 'spage', 0);
	}
	
}

function pagenum($url) {
	$num = preg(curl($url, 0),'/htm".?>([0-9]+)<\/a><a style="/imsU',0);
	if ($num > 25) {
		return 25;
	} elseif($num<2) {
		return "1";
	}else{
	return $num;
	}

}

function first($url) {
	$first = preg(curl($url, 0), '/<p><em>类型(.*)<\/p>/msU', 0);
	$arr = preg($first, '/<a href="([^<]+)<\/a>/imsU', 1);
	echo  makexml($arr,'">','moive',1);
}
function joyid($id) {
	$xmlurl = "http://msx.app.joy.cn/service.php?action=msxv6&videoid=$id&playertype=joyplayer";
	$xmlstr = curl($xmlurl, 0);
	$xml = simplexml_load_string($xmlstr);
	$path = $xml->entry->item->HostPath[0];
	$file = $xml->entry->item->Url;
	$flvurl = $path . $file;
	header("location:$flvurl");
}
function preg($str, $preg, $bool) {
	if ($bool) {
		preg_match_all($preg, $str, $ar);
	} else {
		preg_match($preg, $str, $ar);
	}
	return $ar[1];
}
function makexml($arr, $cut, $get, $bool) {
	global $thisurl;
	$list = "<list>\n";
	foreach ($arr as $value) {
		$temp = explode("$cut", $value);
		$name = htmlspecialChars($temp[1]);
		if ($bool) {
			$list .= "<m list_src=\"$thisurl?$get=$temp[0]\" label=\"$name\" />\n";
		} else {
			$list .= "<m  type=\"2\" src=\"$thisurl?$get=$temp[0]\" label=\"$name\" />\n";
		}
	}
	$list .= "</list>";
	return $list;
}
function curl($url, $bool) {
	if (function_exists('file_get_contents')) {
		$data = file_get_contents($url);
	} else {
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$ch = curl_init();
		$timeout = 30;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
		$data = curl_exec($ch);
		curl_close($ch);
	}
	if ($bool) {
		return iconv('gbk', 'utf-8', $data);
	}
	return $data;
}
function iconvs($str, $bool) {
	if ($bool)
		return iconv('gbk', 'utf-8', $str);
	else
		return iconv('utf-8', 'gbk', $str);
}
?>
