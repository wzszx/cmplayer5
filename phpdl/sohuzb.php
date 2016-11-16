<?php
function curl($url, $bool) {
                $user_agent = $_SERVER['HTTP_USER_AGENT'];
                $ch = curl_init();
                $timeout = 30;
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
                $data = curl_exec($ch);
                curl_close($ch);
        if ($bool) {
                return iconv('gbk', 'utf-8', $data);
        }
        return $data;
}
function tv_list() {
        global $thisurl;
        $url = "http://live.tv.sohu.com/";
        $url = curl($url,0);
        preg_match('|var data1 = \{\"data\":\[([^;]+)\]\};|',$url,$ar);
        //preg_match_all('/"tvId":(.*?),"tvPathName":/',$ar[1], $arr);
        //preg_match_all('/,"bigPic":"http:\/\/(.*?)","name":"(.*?)","url":/i',$ar[1], $name);
        preg_match_all('/"tvId":(.*?),"tvPathName":(.*?)","name":"(.*?)","url":/i',$ar[1], $arr);
        $ar=$arr[1];
        $name=$arr[3];
        $list = "<list>\n";
        $list .= '<m type="2" src="http://gslb.tv.sohu.com/live?cid=4" label="CNN新闻" />'."\n";
        $list .= '<m type="2" src="http://gslb.tv.sohu.com/live?cid=11" label="CCTV-4" />'."\n";
        $list .= '<m type="2" src="http://gslb.tv.sohu.com/live?cid=1" label="CCTV-13" />'."\n";
        $list .= '<m type="2" src="http://gslb.tv.sohu.com/live?cid=2" label="凤凰资讯" />'."\n";
        $list .= '<m type="2" src="http://gslb.tv.sohu.com/live?cid=46" label="CCTV-1HD" />'."\n";
        $list .= '<m type="2" src="http://gslb.tv.sohu.com/live?cid=47" label="东森亚洲" />'."\n";
        $list .= '<m type="2" src="http://gslb.tv.sohu.com/live?cid=54" label="香港卫视" />'."\n";
        $list .= '<m type="2" src="http://gslb.tv.sohu.com/live?cid=56" label="台湾超视" />'."\n";
        $list .= '<m type="2" src="http://gslb.tv.sohu.com/live?cid=70" label="大片影院" />'."\n";
        $list .= '<m type="2" src="http://gslb.tv.sohu.com/live?cid=71" label="未知电视台" />'."\n";
        foreach ($ar as $k => $v) {
                $list .= "<m type=\"2\" src=\"$thisurl?vid=$v\" label=\"$name[$k]\" />\n";
        }
        return "$list</list>";
}
function tv_vid($vid) {
        $url="http://live.tv.sohu.com/live/player_json.jhtml?lid=$vid&type=1";
        $url = curl($url,0);
        preg_match('|\["([^]]+)"\]|',$url,$ar);
        $ar = $ar[1];
        header("location:http://$ar");
}
$thisurl = "http://".$_SERVER ['HTTP_HOST'].$_SERVER['PHP_SELF'];
$xml="";
if(isset($_GET['vid'])){
$xml.=tv_vid($_GET['vid']);
}else{
$xml.=tv_list();
}
echo $xml;
?>