<?php
/**
央视动画 ;
*/
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
        return preg("/fo.addVariable\(\"videoCenterId\",\"(.*)\"\);\/\/视频生产中心id/",get_page($url,1));
}
function default_list(){
        $fpath = $_SERVER['SCRIPT_NAME'];
        $url = "http://bugu.cntv.cn/news/mil/jqgcz/videopage/index.shtml";
        $url = "http://donghua.cntv.cn/donghuarebobang/index.shtml";
        $fmt = "<m type='merge' list_src='$fpath?link=%s' label='%s'/>\n";        
        $page = get_page($url,1);
        $items = preg("/= new array_fenye\((.*)\);/",$page,1);
        foreach($items as $item){
                $arr = explode(",",str_replace("'","",$item));
                $list .= sprintf($fmt,urlencode($arr[1]),$arr[0]);
        }
        echo "<list>$list</list>";
}
function links_list($link){
        $fpath = $_SERVER['SCRIPT_NAME'];
        $fmt = "<m type='merge' src='$fpath?url=%s' label='%s'/>";        
        $page = get_page(urldecode($link),1);
        $items = preg("/=new j_title_array\(([^\)]*)\);/",$page,1);
        foreach($items as $item){
                $arr = explode(",",str_replace("'","",$item));
                $list .= sprintf($fmt,urlencode($arr[3]),$arr[0]);
        }
        echo "<list>$list</list>";                 
}
function get_flvurl($url){
        $fmt = "<u bytes='' duration='%s' src='%s?start={start_seconds}'/>";
        $vid = get_vid(urldecode($url));
        $flvurls = json_decode(get_page("http://vdn.apps.cntv.cn/api/getHttpVideoInfo.do?pid=$vid"))->video->chapters;
        foreach($flvurls as $flvurl){
                $list .=sprintf($fmt,$flvurl->duration,$flvurl->url);
                $d += $flvurl->duration;
        }
        echo "<m starttype='0' label='' type='mp4' bytes='' duration='$d'>$list</m> "; /**/
}
if(isset($_GET['link'])){
        links_list($_GET['link']);
}elseif(isset ($_GET['url'])){
        get_flvurl($_GET['url']); 
}else{
        default_list();
}         
?>