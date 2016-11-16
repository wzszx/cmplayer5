<?php
error_reporting(0);
header("Content-type: text/xml; charset=utf-8");
$singerid=$_REQUEST['singerid'];

if($singerid){
        singerid($singerid) ;

}

function singerid($sid) {
        $url = "http://www.xiami.com/artist/top/id/$sid";
        $str = curl($url);
        if (preg_match('/class="p_num">([0-9]+)<\/a> <a class="p_redirect_l"/ims', $str, $page)) {
                for ($i = 2; $i <= $page[1]; $i++) {
                        $surl = "$url/page/$i";
                        $str .= curl($surl);
                }
        }
        preg_match_all('/<td class="song_name"><a href="\/song\/([^"]+)" title="([^>]+)">/ims', $str, $arr);
        $arr = array_combine($arr[1], $arr[2]);
        $list = "<list>\n";
        foreach ($arr as $k => $v) {
          $list .= "<m src=\"proxy:xiami,$k\" label=\"$v\" />\n";
        }
        echo $list . "</list>";
}
function curl($url) {
        $ch = curl_init();
        $timeout = 30;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $date = curl_exec($ch);
        curl_close($ch);
        return $date;
}

?>