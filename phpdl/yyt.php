<?php
//注，这是我修改自颓废兄的批量采集音悦台的代码http://bbs.cenfun.com/thread-15368-1-1.html
//这里我修改成只直接跳转地址，不再是下级目录列表了，可以连续播放。
error_reporting(0);
$id = $_GET[id];
if ($id) {
        makeFlv($id);
        } else {
        makeXml();        
}
function makeXml() {
$port = $_SERVER['SERVER_PORT']; //取得端口号。
if ($port==80)
$name = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["SCRIPT_NAME"];
else $name = 'http://'.$_SERVER['SERVER_NAME'].':'.$port.$_SERVER["SCRIPT_NAME"]; //非80默认端口则绝对地址加入端口号。
$xml="<list>";
$xml.="\n";
if(isset($_GET['y'])){
        $yy=urlencode($_GET['y']);
        $f='';
        if(isset($_GET['p'])){
                $p=$_GET['p'];
                for($k=$p;$k<=$p+3;$k++){
                $up="http://www.yinyuetai.com/search/index?page=".$k."&orderType=totalViews&keyword=".$yy."&videoSourceType=music_video";
                $f.=get_contents($up);
                }
        }else{
        for($p=1;$p<=4;$p++){
                $up="http://www.yinyuetai.com/search/index?page=".$p."&orderType=totalViews&keyword=".$yy."&videoSourceType=music_video";
                $f.=get_contents($up);
                }
        }
        //$f=mb_convert_encoding($f, 'GBK', 'UTF-8');
        $st=get_array($f);
        $sn=get_name($f);
        $list='';
        for($i=0;$i<79;$i++){
                @$list.='<m src="'.$name.'?id='.$st[$i].'" label="'.preg_replace("/[\<]*[\>]*/","",$sn[$i]).'" />';
                $list.="\n";
        }
        $xml.=$list;
        }else{
                $xml.="Please input your want to find singer";
        }
        $xml.="</list>";
        $nullurl='<m src="'.$name.'?id=" label="" />';
        $xml=str_replace($nullurl,"",$xml);           //去掉空ID的行。
        $xml=preg_replace('/[\n][\n][\n]/',"",$xml);  //去掉空行。
        header("Content-Type: text/xml");
        echo $xml;
}

function makeFlv($id) {
        if (empty($id)) {
                return;        
                }
                $mtv_url="http://www.yinyuetai.com/mvplayer/get-video-info?flex=true&videoId=".$id;
                $mtv=get_contents($mtv_url);
                //$mtv=mb_convert_encoding($mtv, 'GBK', 'UTF-8');
                preg_match("/http:\/\/.*\.flv/i",$mtv,$flvs); //这里我改了正则，匹配“http”开头并以“.flv”结束的地址，加/i不计大小写。
                @$flv=$flvs[0];
                if ($flv) {
                        header("Location: $flv"); //如果有地址直接跳转。
                        //echo $flv;
                }
        }

function get_contents($url) {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $ch = curl_init();
        $timeout = 30;
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        $c = curl_exec($ch);
        curl_close($ch);
        return $c;
}
function get_array($f){
        preg_match_all('|parent_per_([0-9]+)"|',$f,$s);
        return $s[1];
}
function get_name($s){
        //preg_match_all('|img alt="([^"]*)" src=|',$s,$c);
        preg_match_all('|class="img"><img alt="([^"]*)" src=|', $s, $c);
		return $c[1];
}
?>