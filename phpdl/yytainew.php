<?php

/*--------------------------歌手封包---------------------*/
class SongList {
        public $singersex;
        public $area;
        public $page;
        function __construct($area = '', $singersex = '', $page = '1') {
                $this->singersex = $singersex;
                $this->area = $area;
                $this->page = $page;
        }
        function file_contents($url) {
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
        function list_str() {
                $str = '';
                for ($i = 1; $i <= $this->page; $i++) {
                        $url = 'http://www.yinyuetai.com/fanAll?area=' . $this->area . '&page=' . $i . '&property=' . $this->singersex;
                        $str .= $this->file_contents($url);
                }
                return $str;
        }
        function curl_str() {
                $str = $this->list_str();
                preg_match_all('|class="song" title="(.*)"|', $str, $ar);
                $z = '';
                $filename = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["SCRIPT_NAME"];
                foreach ($ar[1] as $k => $v) {
                        $v = explode('(', $v);
                        $z .= '<m list_src="' . $filename . '?y=' . $v[0] . '" label="' . $v[0] . '" />';
                        $z .= "\n";
                }
                return $z;

        }
}



/*----------------------------------歌手抓取封包---------------------------*/
function get_contents($url) {
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
function get_array($f) {
        preg_match_all('|parent_per_([0-9]+)"|', $f, $s);
        return $s[1];
}
function get_name($s) {      
		 preg_match_all('|class="img"><img alt="([^"]*)" src=|', $s, $c);
		//preg_match_all('|img alt="([^"]*)" src=|', $s, $c);
        return $c[1];
}
$name = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["SCRIPT_NAME"];
$xml = "<list>";
if (isset ($_GET['y'])) {
        $yy = urlencode($_GET['y']);
        $f = '';
        if (isset ($_GET['p'])) {
                $p = $_GET['p'];
                for ($k = 4 * $p -3; $k < 4 * $p +1; $k++) {
                        $up = "http://www.yinyuetai.com/search/index?page=" . $k . "&orderType=totalViews&keyword=" . $yy . "&videoSourceType=music_video";
                        $f .= get_contents($up);
                }
        } else {

                for ($p = 1; $p <= 4; $p++) {
                        $up = "http://www.yinyuetai.com/search/index?page=" . $p . "&orderType=totalViews&keyword=" . $yy . "&videoSourceType=music_video";
                        $f .= get_contents($up);
                }
        }
        $f = mb_convert_encoding($f, 'GBK', 'UTF-8');
        $st = array_unique(get_array($f));
        $sn = array_unique(get_name($f));
        $num=count($st)-1;
        $list = '';
        //for ($i =0 改为 $i=1
		for ($i = 0; $i < $num; $i++) {
                $sname=$sn[$i];
                $sname=htmlspecialchars($sname, ENT_QUOTES); 
                @ $list .= '<m src="' . $name . '?id=' . $st[$i] . '" label="' . $sname . '" />';// $st[$i]改为 $st[$i-1]
                $list .= "\n";
        }
        $xml .= $list;

}
elseif (isset ($_GET['id'])) {
        $mtv_url = "http://www.yinyuetai.com/mvplayer/get-video-info?flex=true&videoId=" . $_GET['id'];
        $mtv = get_contents($mtv_url);
        //$mtv=mb_convert_encoding($mtv, 'GBK', 'UTF-8');
        preg_match("|http(.*)\.flv|", $mtv, $flv);
        $flv = "http" . $flv[1] . ".flv";
        if(strlen($flv)<18){        $xml.= 'Not the songs';}else{
        header("location:$flv");
        }
}
elseif (isset ($_GET['m'])) {
        $m = urlencode($_GET['m']);
        $searchurl = 'http://www.yinyuetai.com/search/index?keyword=' . $m . '&videoSourceType=music_video&orderType=totalViews';
        $fp = get_contents($searchurl);
        preg_match('|href="/video/([0-9]+)"|', $fp, $ar);
        $flvurl = $name . '?id=' . $ar[1];
        header("location:${flvurl}");
}elseif (isset ($_GET['s'])) {
        $s1 = new SongList('ML', 'Boy', '1');
        $s2 = new SongList('ML', 'Girl', '1');
        $s3 = new SongList('ML', 'Combo', '1');
        $s4 = new SongList('HT', 'Boy', '1'); //增加的港台歌手
        $s5 = new SongList('HT', 'Girl', '1');  //增加的港台歌手
        $s6 = new SongList('HT', 'Combo', '1'); //增加的港台歌手
        $s7 = new SongList('US', 'Boy', '1');
        $s8 = new SongList('US', 'Girl', '1');
        $s9 = new SongList('US', 'Combo', '1');
        $s10 = new SongList('KR', 'Boy', '1');
        $s11 = new SongList('KR', 'Girl', '1');
        $s12 = new SongList('KR', 'Combo', '1');
        $s13 = new SongList('JP', 'Boy', '1');
        $s14 = new SongList('JP', 'Girl', '1');
        $s15 = new SongList('JP', 'Combo', '1');
        $s = "s" . $_GET['s'];
        $xml.= $$s->curl_str();
}else {
$xml .='<m list_src="'.$name.'?s=1" label="华语男歌手" />'."\n";        
$xml .='<m list_src="'.$name.'?s=2" label="华语女歌手" />'."\n";
$xml .='<m list_src="'.$name.'?s=3" label="华语乐队/组合" />'."\n";
$xml .='<m list_src="'.$name.'?s=4" label="港台男歌手" />'."\n";        
$xml .='<m list_src="'.$name.'?s=5" label="港台女歌手" />'."\n";
$xml .='<m list_src="'.$name.'?s=6" label="港台乐队/组合" />'."\n";
$xml .='<m list_src="'.$name.'?s=7" label="欧美男歌手" />'."\n";
$xml .='<m list_src="'.$name.'?s=8" label="欧美女歌手" />'."\n";
$xml .='<m list_src="'.$name.'?s=9" label="欧美乐队/组合" />'."\n";
$xml .='<m list_src="'.$name.'?s=10" label="韩语男歌手" />'."\n";
$xml .='<m list_src="'.$name.'?s=11" label="韩语女歌手" />'."\n";
$xml .='<m list_src="'.$name.'?s=12" label="韩语乐队/组合" />'."\n"; 
$xml .='<m list_src="'.$name.'?s=13" label="日语男歌手" />'."\n"; 
$xml .='<m list_src="'.$name.'?s=14" label="日语女歌手" />'."\n"; 
$xml .='<m list_src="'.$name.'?s=15" label="日语乐队/组合" />'."\n";         
        }
$xml .= "</list>";
$nullurl = '<m list_src="' . $name . '?id=" label="" />';
$xml = str_replace($nullurl, "", $xml);
echo $xml;
?>