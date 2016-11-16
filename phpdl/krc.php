<?
error_reporting(0);
$query = $_SERVER['QUERY_STRING'];
parse_str($query);
if ($type=="video") {
        exit;
} 
if ($title) {
        kugou($title);
        exit;

} 
//支持KUGOU  hash值获取歌词例 ?hash=A890A1454A8BC4D33B0123955F1AC5D7
if($hash){
        kmcurl($hash);
        exit;
}

function kugou($str) {
        $str = urlencode($str);
        $url = "http://www1.kugou.com/ting/Search.aspx?keywords=$str";
        $kustr = file_get_contents($url);
        preg_match('/SongManager\.Listen4\(\'(.*)\'\)/imsU', $kustr, $hash);
        $hash = explode('|', $hash[1]);
        kmcurl($hash[2]);
}

function  kmcurl($hash){
        $key  = $hash."kgcloud";
        $key= md5($key);
        $songurl="http://tracker2.kugou.com/i/?hash=$hash&key=$key&cmd=3";
        $songobj=json_decode(file_get_contents($songurl));
        $time=$songobj->timeLength."000";
        $fileName=$songobj->fileName;
        $songname=urlencode(str_replace(' ','',$fileName));
        $thistime=time();
        $kurl="http://61.142.208.213:7790/mykugoo.html?SearchKey=$songname&TimeLength=$time&KRC=1&Manual=0&time=$thistime&Hash=$hash";
        $karr=file($kurl);
        $kid=explode('|',$karr[1]);
        $kmc="http://61.142.208.213:7790/mykugoo.html?time=$thistime&KRC=1&SearchID=$kid[2]";
        echo file_get_contents($kmc);
}
?>