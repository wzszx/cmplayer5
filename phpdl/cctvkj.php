<?php
header("Content-type: text/xml; charset=utf-8");
function get_page($url, $bool=false) {
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
        $timeout = 60;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        if ($bool) {return iconv('gbk', 'utf-8', $data);}
        return $data;
}

function preg($pattern,$str,$bool=0) {
        if ($bool) {
                if (preg_match_all($pattern,$str,$match)) {
                        return $match[1];                
                 }else {
                        die("\tErrorr $pattern"); 
                }                    
        }else {
                if (preg_match($pattern,$str,$match)) {
                        return $match[1];                
                 }else {
                        die("\tErrorr $pattern"); 
                }                     
        } 
}
function get_vid($url){
      // return preg('|"videoCenterId","(.*?)"|',get_page($url,1));
	   return preg("/fo.addVariable\(\"videoCenterId\",\"(.*)\"\);\/\/视频生产中心id/",get_page($url,1));
}

function default_list(){
        $filename = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["SCRIPT_NAME"];
        $url = "http://cctv.cntv.cn/lm/baijiajiangtan/video/index.shtml";
        $fmt = "<m type='merge' src='$filename?url=%s' label='%s'/>\n"; 
        $page = get_page($url);
        $items = preg("/=new title_array\(([^\)]*)\);/",$page,1);
        foreach($items as $item){
                $arr = explode(",",str_replace("'","",$item));
                $list .= sprintf($fmt,$arr[2],$arr[1]);
        }
        echo "<list>\n$list</list>";
}

function get_flvurl($url){
        $fmt = "<u bytes='' duration='%s' src='%s?start={start_seconds}'/>\n";
        $vid = get_vid($url);
        $flvurls = json_decode(get_page("http://vdn.apps.cntv.cn/api/getHttpVideoInfo.do?pid=$vid"))->video->chapters2;
        foreach($flvurls as $flvurl){
                $list .=sprintf($fmt,$flvurl->duration,$flvurl->url);
        }
        echo "<m starttype='0' label='' type='mp4' bytes='' duration=''>\n$list\n</m> ";
}

if (isset ($_GET['url'])) {
    get_flvurl($_GET['url']);    
} else {
    default_list();
} 
  
?>