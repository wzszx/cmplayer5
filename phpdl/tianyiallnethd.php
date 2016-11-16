<?php
/*天意视讯代理【默认支持拖动，自动选最高清地址】
return move_list($ar1[$n],$ar2[$n],'2','1');中的‘1’为支持拖动功能 （加载列表慢）。
return move_list($ar1[$n],$ar2[$n],'2','0');为0是一般功能（加载列表快）。
在代码后面部分三个地方的$ar2=array(*）这里面的数字全是代表对应分类的取多少页数据！
标清的更改：大概30行：return $src[$arr-1]  改成 return $src[0]
64行：$url[$t-1] 改成 $url[0] 
130行：$url[$t-1] 同上！
168行：$url[$t-1] 同上！
共4处！就可以了！
*/
$xml = "<list>\n";
function g_s($url) {
   $user_agent = $_SERVER['HTTP_USER_AGENT'];
   $ch = curl_init();
   $timeout = 30;
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
   curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
   $c = curl_exec($ch);
   curl_close($ch);
   return $c;
}
function curl_xml($str, $item) {
   if ($str) {
     preg_match_all('|"><!\[CDATA\[([^>]+)\]\]>|',$str,$ar);
     $src=$ar[1];
     $arr=count($src);
     return $src[$arr-1];
   }
}
function my_page($playid) {
   $url = 'http://pgmsvr.tv189.cn/program/getVideoPlayInfo?pid='.$playid.'&indexid=1';
   $str = g_s($url);
   $flv = curl_xml($str,'url');
   header("location:$flv");
}
function tv_page($tvid) {
   $ar = explode('|', $tvid);
   $url = 'http://pgmsvr.tv189.cn/program/getVideoPlayInfo?pid=' . $ar[0] . '&indexid=' . $ar[1];
   $str = g_s($url);
   $flv = curl_xml($str,'url');
   header("location:$flv");
}
function tv_pid($pid) {
   global $fname;
   $ar = explode('|', $pid);
   $list = '';
   if ($ar[2] != '1') {
     for ($i = 1; $i <= $ar[2]; $i++) {
        if ($ar[3] == '0'){
          $list .= '<m type="2" src="'.$fname.'?tvid='.$ar[0].'|'.$i.'" label="'.$ar[1].'-NO.'.$i.'" />'."\n";
        }else{
          $str='http://pgmsvr.tv189.cn/program/getVideoPlayInfo?pid='.$ar[0].'&indexid='.$i;
          $strr=g_s($str);
          preg_match_all('|"><!\[CDATA\[([^>]+)\]\]>|',$strr,$url);
          preg_match_all('|P" tm="([0-9]+)" vid="|',$strr,$tm);
          preg_match_all('|"  sz="([0-9]+)">|',$strr,$sz);
          $url=$url[1];
          $tm=$tm[1];
          $sz=$sz[1];
          $t=count($url);
          $list .= '<m type="2" src="'.$url[$t-1].'?start={start_bytes}" duration="'.$tm[$t-1].'" bytes="'.$sz[$t-1].'" label="'.$ar[1].'-NO.'.$i.'" />'."\n";
        }
     }
   }
   return $list;
}
function move_list($u, $p,$type,$d) {
   global $fname;
   $str = '';
   $list = '';
   if ($type == '1') {
     $list_url = 'http://tv.tv189.com/l/'.$u.'_0_0_0_0_0_0_0/1.htm';
     $str = g_s($list_url);
        if ($p != '1') {
          for ($i = 2; $i <= $p; $i++) {
          $list_url = 'http://tv.tv189.com/l/'.$u.'_0_0_0_0_0_0_0/'.$i.'.htm';
          $str .= g_s($list_url);
          }
        }
        preg_match_all("|<dl><dt><span class=\"img\">(.*)<\/a><\/div><\/dd><\/dl>|imsU", $str, $ar);
        $ar = array_unique($ar[1]);
        foreach ($ar as $k => $v) {
          preg_match("|<a href=\"\/v\/([0-9]+).htm|", $v, $ar);
          preg_match("|\"  target=\"video\">(.*)<\/a><\/font>|", $v, $arr);
          preg_match("|<\/font><p>(.*)</p><span>|", $v, $ar1);
          preg_match("|共(.*)集|", $ar1[1], $ar2);
          preg_match("|更新至第([0-9]+)集|", $ar1[1], $ar3);
          $ar=$ar[1];
          $arr=$arr[1];
          $ar1=$ar1[1];
          $ar3=$ar3[1];
          if ($ar3=='') {
            $ar3=$ar2[1];
          }
          $list .= '<m list_src="'.$fname.'?pid='.$ar.'|'.$arr.'|'.$ar3.'|'.$d.'" label="'.$arr.'['.$ar1.']" />'."\n";
        }
        return $list;
    }
    elseif ($type == '2') {
        $list_url = 'http://movie.tv189.com/l/'.$u.'_0_0_0_0_0_0_1/1.htm';
        $str = g_s($list_url);
        if ($p != '1') {
           for ($i = 2; $i <= $p; $i++) {
               $list_url = 'http://movie.tv189.com/l/'.$u.'_0_0_0_0_0_0_1/'.$i.'.htm';
               $str .= g_s($list_url);
            }
        }
        preg_match_all("|<dl><dt><span class=\"img\">(.*)<\/span><\/dd><\/dl>|imsU", $str, $ar);
        $ar = array_unique($ar[1]);
        foreach ($ar as $k => $v) {
          preg_match("|<a href=\"\/v\/([0-9]+).htm|", $v, $ar);
          preg_match("|\" alt=\"(.*)\" width=|", $v, $arr);
          $ar=$ar[1];
          $arr=$arr[1];
          if ($d == '0'){
            $list .= '<m type="2" src="'.$fname.'?playid='.$ar.'" label="'.$arr.'" />'."\n";
          }else{
            $str='http://pgmsvr.tv189.cn/program/getVideoPlayInfo?pid='.$ar.'&indexid=1';
            $strr=g_s($str);
            preg_match_all('|"><!\[CDATA\[([^>]+)\]\]>|',$strr,$url);
            preg_match_all('|P" tm="([0-9]+)" vid="|',$strr,$tm);
            preg_match_all('|"  sz="([0-9]+)">|',$strr,$sz);
            $url=$url[1];
            $tm=$tm[1];
            $sz=$sz[1];
            $t=count($tm);
            $list .= '<m type="2" src="'.$url[$t-1].'?start={start_bytes}" duration="'.$tm[$t-1].'" bytes="'.$sz[$t-1].'" label="'.$arr.'" />'."\n";
          }
        }
        return $list ;
    }elseif ($type == '3') {
        if($u == '0'){
        $list_u='l/0_0_0_0_0_0';
        $list_url = 'http://zy.tv189.com/'.$list_u.'_0_0/1.htm';
        }else{
        $list_u='g/'.$u;
        $list_url = 'http://zy.tv189.com/'.$list_u.'_0_0/1.htm';
        }
        $str = g_s($list_url);
        if ($p != '1') {
           for ($i = 2; $i <= $p; $i++) {
             $list_url = 'http://zy.tv189.com/'.$list_u.'_0_0/'.$i.'.htm';
             $str .= g_s($list_url);
           }
        }
        preg_match_all("|<dl><dt><span class=\"img\">(.*)<\/a><\/div><\/dd><\/dl>|imsU", $str, $ar);
        $ar = array_unique($ar[1]);
        foreach ($ar as $k => $v) {
          preg_match("|<a href=\"\/v\/([0-9]+).htm|", $v, $ar);
          preg_match("|\"  target=\"video\">(.*)<\/a><\/font>|", $v, $arr);
          $ar=$ar[1];
          $arr=$arr[1];
          if ($d == '0'){
            $list .= '<m type="2" src="'.$fname.'?playid='.$ar.'" label="'.$arr.'" />'."\n";
          }else{
            $str='http://pgmsvr.tv189.cn/program/getVideoPlayInfo?pid='.$ar.'&indexid=1';
            $strr=g_s($str);
            preg_match_all('|"><!\[CDATA\[([^>]+)\]\]>|',$strr,$url);
            preg_match_all('|P" tm="([0-9]+)" vid="|',$strr,$tm);
            preg_match_all('|"  sz="([0-9]+)">|',$strr,$sz);
            $url=$url[1];
            $tm=$tm[1];
            $sz=$sz[1];
            $t=count($tm);
            $list .= '<m type="2" src="'.$url[$t-1].'?start={start_bytes}" duration="'.$tm[$t-1].'" bytes="'.$sz[$t-1].'" label="'.$arr.'" />'."\n";
          }
        }
        return $list ;
     }
}
function dy_list($m) {
$ar1=array('0','11','3','12','49','51','43','36','57','40','37','50','42','39','52','41','73','53','54','55');
$ar2=array('10','7','7','7','5','6','7','4','1','7' ,'7' ,'7' ,'5' ,'3' ,'2' ,'2' ,'7' ,'7' ,'7','7');
$n=$m-1;
return move_list($ar1[$n],$ar2[$n],'2','1');
}
function tv_list($tv) {
$ar1=array('0','4','44','45','46','47','48','58','59','60','61','62','63','64','65','147');
$ar2=array('7','4','4','4','4','2','2','4','5','5','3','4','2','4','4','3');
$n=$tv-1;
return move_list($ar1[$n],$ar2[$n],'1','1');
}
function zy_list($m) {
$ar1=array('0','快乐大本营','非诚勿扰','老公看你的','我们约会吧','天天向上','爱情连连看','中国达人秀','饭没了秀','我爱记歌词','欢喜冤家','称心如意');
$ar2=array('5','2','3','2','2','2','3','1','2','2' ,'2' ,'2');
$n=$m-1;
return move_list($ar1[$n],$ar2[$n],'3','1');
}
function default_list() {
   global $fname;
   $list = '';
   $tv = array ('全部','偶像','年代','古装','都市','谍战','神幻','言情','励志','刑侦','历史','伦理','武侠','悬疑','喜剧','革命');
   $film = array ('全部','爱情','动作','喜剧','伦理','战争','剧情','恐怖','灾难','动画','悬疑','惊悚','科幻','歌舞','武侠','纪录','冒险','经典','预告','花絮');
   $zy = array ('全部','快乐大本营','非诚勿扰','老公看你的','我们约会吧','天天向上','爱情连连看','中国达人秀','饭没了秀','我爱记歌词','欢喜冤家','称心如意');
   $list .= '<m label="电视剧">'."\n";
   foreach ($tv as $key => $value) {
     $keys = $key +1;
     $list .= '<m list_src="'.$fname.'?tv='.$keys.'" label="'.$value.'" />'."\n";
   }
   $list .= '</m>'."\n";
   $list .= '<m label="电影">'."\n";
   foreach ($film as $k => $v) {
     $ks = $k +1;
     $list .= '<m list_src="'.$fname.'?m='.$ks.'" label="'.$v.'" />'."\n";
   }
   $list .= '</m>' . "\n";
   $list .= '<m label="综艺">'."\n";
   foreach ($zy as $k1 => $v1) {
     $ks = $k1 +1;
     $list .= '<m list_src="'.$fname.'?zy='.$ks.'" label="'.$v1.'" />'."\n";
   }
   $list .= '</m>'."\n";
   return $list;
}
$fname = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["SCRIPT_NAME"];
if (isset ($_GET['pid'])) {
   $xml .= tv_pid($_GET['pid']);
}
elseif (isset ($_GET['playid'])) {
   my_page($_GET['playid']);
}
elseif (isset ($_GET['tvid'])) {
   tv_page($_GET['tvid']);
}
elseif (isset ($_GET['m'])) {
   $xml .= dy_list($_GET['m']);
}
elseif (isset ($_GET['tv'])) {
   $xml .= tv_list($_GET['tv']);
}
elseif (isset ($_GET['zy'])) {
   $xml .= zy_list($_GET['zy']);
}else{
   $xml .= default_list();
}
$xml .= "</list>";
echo $xml;
?>