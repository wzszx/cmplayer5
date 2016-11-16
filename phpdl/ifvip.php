<?php
//PHP 凤凰新媒体VIP节目新型代理
$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n<list>\n";
function t_v($url) {
       $user_agent = $_SERVER['HTTP_USER_AGENT'];
       $ch = curl_init(); 
       $timeout = 30;
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
       @ $file = curl_exec($ch);
       curl_close($ch);
       return $file;
}
$fname = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["SCRIPT_NAME"];
if(isset ($_GET['u'])){
       $u=$_GET['u'];
       $t = explode('-', $u);
       for ($i = 1; $i <= $t[1]; $i++) {
       $y = 'http://vip.v.ifeng.com/viplist/0018-00' . $t[0] . '/' . $i . '/list.html';
       $xml .= '<m list_src="' . $fname . '?p=' . $y . '" label="第' . $i . '页" />'."\n";
}}
elseif(isset ($_GET['p'])){
       $a=$_GET['p'];
       $str = t_v($a);
       preg_match_all('|<a href="([^<]+)</a></dd>|ims', $str, $a1);
       foreach ($a1[1] as $k => $v){
       $a2 = explode('.shtml" target="video">', $v);
       $a3 = $a2[1];
       $xml .='<m type="2" src="'.$fname.'?vid=http://vip.v.ifeng.com'.$a2[0].'.shtml" label="'.$a3.'" />'."\n";
}}
elseif(isset ($_GET['vid'])){
       $a=$_GET['vid'];
       $str = t_v($a);
       preg_match('|"vid":"(.*?)","|ims', $str, $as);
       $ur='http://partner.itv.ifeng.com/IfengVideoSearch/GetVipPlayerXml.aspx?id='.$as[1].'';
       $str1 = t_v($ur);
       preg_match_all('|VideoPlayUrl="([^<]+).mp4" PlayerUrl="|', $str1, $id);
       $id = $id[1];
       $j = ''.$id[0].'.mp4?start={start_seconds}';
       header("location:$j");
}
else{
$ifvip = array ('最新资讯' => '01-705',
       '独家评论' => '02-421',
       '社会专题' => '03-243',
       '访谈' => '06-271',
       '历史文化' => '05-210',
       '娱乐时尚' => '04-231',
       '财经' => '07-243',
       '经典节目' => '10-56',
       '台港澳' => '11-64',
       '探索发现' => '13-16');
foreach ($ifvip as $k => $v) {
       $xml .= '<m list_src="' . $fname . '?u=' . $v  . '" label="凤凰VIP-' . $k . '" />'."\n";
}}
$xml .= '</list>';
echo $xml;
?>