<?php

header("Content-type: text/xml; charset=utf-8");
$fname='http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
error_reporting(0);
function file_data($url, $bool=false) {
        for ($i = 0; $i < 3; $i++) {
                $data = file_get_contents($url);
                if ($data)
                        break;
        }
        if ($data){
                if ($bool) {return iconv('gbk', 'utf-8', $data);}
                return $data;
        }

        $ch = curl_init();
        $timeout = 20;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        if ($bool) {return iconv('gbk', 'utf-8', $data);}
        return $data;
}
function get_leshi_flv($url){
             $flvpage = file_data($url);
         preg_match('#vid:(.*),(.*)mmsid#iUs',$flvpage,$f);
                 return $f[1];
}
function get_leshi_flvurl($url){
                $obj = simplexml_load_file ( $url, 'SimpleXMLElement', LIBXML_NOCDATA );
                $flvdms = $obj->mmsJson;
        $obj = json_decode($flvdms);
                $flv = $obj->bean->video;
        return 'http://g3.letv.cn/'.$flv[0]->storeuri.'';
}
function get_youku_id($url){
             $flvpage = file_data($url);
         preg_match("#var videoId2= '(.*)';#iUs",$flvpage,$f);
                 return $f[1];
}
function get_tudou_id($url){
             $flvpage = file_data($url);
         preg_match("#,(.*)iid:(.*),cdnType:(.*),cartoonType:(.*),kw:(.*),icode:(.*)#iUs",$flvpage,$f);
                 return rtrim($f[2]);
}
function get_qiyi_id($url){
             $flvpage = file_data($url);
         preg_match('#videoId":"(.*)","(.*)albumName":"(.*)","(.*)#iUs',$flvpage,$f);
                 return $f[1];
}
function get_m1905_id($url){
             $flvpage = file_data($url);
         preg_match('#var streamflv = "(.*)";(.*)var arrstreamflv =#iUs',$flvpage,$f);
                 return $f[1];
}
function get_pps_id($url){
             $flvpage = file_data($url);
         preg_match('#html5_url: "(.*)",(.*)html5_ct#iUs',$flvpage,$f);
                 return $f[1];
}





if(isset ($_GET['vid'])){
global $fname;
      $xml = "<list>\n";
          $you = file_data('http://v.360.cn/dianying/list.php?cat='.$_GET['vid'].'');
          $you3 = preg_match('#下一页(.*)<div class="watched-bar">#iUs',$you, $you2);
          $you1 = preg_match('#</span></span></a><a href=(.*)" target="_self"><span><span>(.*)</span></span></a></div>#iUs',$you2[0], $you4);
          $d = $you4[2];
          for($w=0;$w<$d;$w++){
          $z=$w+1;
          $xml .= '<m list_src="'.$fname.'?sid=http://v.360.cn/dianying/list.php?cat='.$_GET['vid'].'&amp;page='.$z.'" label="第'.$z.'页" />'."\n";
          }
      $xml .= '</list>';
}

elseif(isset ($_GET['sid'])){
global $fname;
    $xml = "<list>\n";
        $urlpage = '' . $_GET['sid'] . '&pageno=' . $_GET['page'] . '';
        $zhpage = file_data($urlpage);
        preg_match_all('#<dt class="video-title"><em>(.*)</em><a href="(.*)" title="(.*)" sitename="(.*)">(.*)</a></dt>(.*)<dd class="video-cover">#iUs',$zhpage,$zhpage2);
        $o= count($zhpage2[0]);
        for($u=0;$u<$o;$u++){
                $page = file_data('http://v.360.cn'.$zhpage2[2][$u].'');
                preg_match('#<a playtype="dianying" class="play_btn" sitename="(.*)" needrecord="(.*)" movieid="(.*)" href="(.*)" title="立即播放(.*)" >#iUs',$page, $page2);

                $type =$page2[1];
                     switch($type){
                         case $type==leshi;
                                 $flv = get_leshi_flv($page2[4]);
                                 $flvurl = get_leshi_flvurl('http://www.letv.com/v_xml/' . $flv . '.xml');
                                 $xml .= '<m type="2" src="'. $flvurl . '?start={start_bytes}" label="' . $zhpage2[3][$u] . '【乐视】" />'."\n";
                                 break;
                         case $type==youku;
                                 $flv = get_youku_id($page2[4]);
                                 $xml .= '<m type="youku" streamtype="flv" src="' . $flv . '" label="[标清]' . $zhpage2[3][$u] . '【优酷】" />'."\n";
                                                                 $xml .= '<m type="youku" streamtype="mp4" src="' . $flv . '" label="[高清]' . $zhpage2[3][$u] . '【优酷】" />'."\n";
                                                                 $xml .= '<m type="youku" streamtype="hd2" src="' . $flv . '" label="[超清]' . $zhpage2[3][$u] . '【优酷】" />'."\n";
                                 break;
                         case $type==tudou;
                                 $flv = get_tudou_id($page2[4]);
                                 $xml .= '<m type="tudou"  brt="3" src="' . $flv . '" label="[360P]' . $zhpage2[3][$u] . '【土豆】" />'."\n";
                                                                 $xml .= '<m type="tudou"  brt="4" src="' . $flv . '" label="[480P]' . $zhpage2[3][$u] . '【土豆】" />'."\n";
                                                                 $xml .= '<m type="tudou"  brt="5" src="' . $flv . '" label="[720P]' . $zhpage2[3][$u] . '【土豆】" />'."\n";
                                 break;
                         case $type==qiyi;
                                 $flv = get_qiyi_id($page2[4]);
                                 $xml .= '<m type="qiyi" src="http://cache.video.qiyi.com/v/' . $flv . '" label="' . $zhpage2[3][$u] . '【奇艺】" />'."\n";
                                 break;
                         case $type==m1905;
                                 $flv = get_m1905_id($page2[4]);
                                 $xml .= '<m type="2" src="http://flv1.vodfile.m1905.com/movie' . $flv . '" label="' . $zhpage2[3][$u] . '【M1905】" />'."\n";
                                 break;
                         case $type==pps;
                                 $flv = get_pps_id($page2[4]);
                                 $xml .= '<m type="2" src="' . $flv . '?start={start_bytes}" label="' . $zhpage2[3][$u] . '【PPS】" />'."\n";
                                 break;
                         case $type==sohu;
                               //  $xml .= '<m src="proxy:link,' . $page2[4] . '" label="' . $zhpage2[3][$u] . '【双击进搜狐播放页】" />'."\n";
                                 break;
                         case $type==cntv;
                                // $xml .= '<m src="proxy:link,' . $page2[4] . '" label="' . $zhpage2[3][$u] . '【双击进CNTV播放页】" />'."\n";
                                 break;
                         case $type==pptv;
                              //   $xml .= '<m src="proxy:link,' . $page2[4] . '" label="' . $zhpage2[3][$u] . '【双击进入PPTV播放页】" />'."\n";
                                 break;
                         case $type==kumi;
                             //    $xml .= '<m src="proxy:link,' . $page2[4] . '" label="' . $zhpage2[3][$u] . '【双击进入酷米播放页】" />'."\n";
                                 break;
                         case $type==xunlei;
                              //   $xml .= '<m src="proxy:link,' . $page2[4] . '" label="' . $zhpage2[3][$u] . '【双击进入迅雷播放页】" />'."\n";
                                 break;
                         default:
                            //     $xml .= '<m src="proxy:link,' . $page2[4] . '" label="' . $zhpage2[3][$u] . '【双击进入播放页】" />'."\n";
                         }
         }
        $xml .= '</list>';
}


else {
    $xml .= null_list();
}


function null_list() {
global $fname;

        $xml = "<list>\n";
        $xml .= '<m list_src="'.$fname.'?vid=all" label="全部" />'."\n";
        $xml .= '<m list_src="'.$fname.'?vid=103" label="喜剧" />'."\n";
        $xml .= '<m list_src="'.$fname.'?vid=106" label="动作" />'."\n";
        $xml .= '<m list_src="'.$fname.'?vid=100" label="爱情" />'."\n";
        $xml .= '<m list_src="'.$fname.'?vid=102" label="恐怖" />'."\n";
        $xml .= '<m list_src="'.$fname.'?vid=104" label="科幻" />'."\n";
        $xml .= '<m list_src="'.$fname.'?vid=112" label="剧情" />'."\n";
        $xml .= '<m list_src="'.$fname.'?vid=108" label="战争" />'."\n";
        $xml .= '<m list_src="'.$fname.'?vid=115" label="悬疑" />'."\n";
        $xml .= '<m list_src="'.$fname.'?vid=113" label="奇幻" />'."\n";
        $xml .= '<m list_src="'.$fname.'?vid=105" label="犯罪" />'."\n";
        $xml .= '<m list_src="'.$fname.'?vid=107" label="动画" />'."\n";
        $xml .= '</list>';

return $xml;
}

echo $xml;

?>