<?php
function get_date($url) {
$ch = curl_init();
$User_Agent = $_SERVER['HTTP_USER_AGENT'];
$Referer_Url = "http://biz.vsdn.tv380.com/";
$timeout = 3;
curl_setopt ($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($ch, CURLOPT_USERAGENT, $User_Agent);
curl_setopt ($ch, CURLOPT_REFERER, $Referer_Url);
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
$c = curl_exec($ch);
curl_close($ch);
return $c;
}

//生成CMP列表开始
function variety_list(){
        $Url = "http://www.netitv.com/a_flash/tysx_1_2/livelistTV/livePlayer_list.shtml";
        $list=''; 
        //提取来源地址
        $l_str=preg(get_date($Url),'|<input type=(.*) />|imsU',true); //从来源页分析数组
        foreach($l_str as $value){
                preg_match_all("/\": (.*),/isU",$value,$ar); //提取主要的几个值采用组方式
                $lib=$ar[1][1]; //取得组里的uuid值
                $lib1=$ar[1][0];//取得组里的id值
                preg_match_all("/\": \"(.*)\"/isU",$value,$arr); //取名称
                $lname=$arr[1][0];
                $surl=$_SERVER["PHP_SELF"]; 
                $list.="<m type=\"2\" src=\"$surl?id=$lib-$lib1\" label=\"$lname\" />\n";
        }
return $list;
}

//取播放资源开始
function variety_id($id) {
        $uid = explode('-', $id);
        //取对应的XML里的播放地址
        $url="http://www.netitv.com/$uid[0]/proXml/$uid[1]_1.xml";
        //取播放地址组，有些播放地址有多个。优先取高清地址组
        preg_match_all("/bit_stream=\"2\"(.*)<\/url>/isU",get_date($url),$ar); 
        $vid=$ar[1][0];//取第一个组数据
        //地址组里有部分是以http://开头的可以直接使用，判断不包含http
        if($vid!=='http://'){ 
                //判断地址长度小余100重新取标清地址组
                if(strlen($vid)<=100){
                        //因为前面已经把短地址如http开头的直接取数据了。
                        preg_match_all("/bit_stream=\"1\"(.*)<\/url>/isU",get_date($url),$ar); 
                        $vid=$ar[1][0];
                }
                //再次缩短要的数据组
                preg_match_all("/CDATA\[(.*)\]/isU",$vid,$arr); 
                //取组第一数据
                $vdata=$arr[1][0];
                if(strlen($vdata)<=100){
                        //如果数据长度小余100，就取组第二个数据。
                        $vdata=$arr[1][1];
                }
                //$urll="http://biz.vsdn.tv380.com/playlive.php?$vdata";
                $urll=get_date("http://biz.vsdn.tv380.com/playlive.php?".$vdata);
        }
        else{
                $urll=get_date($vdata);
        }
          $urll= str_replace('" />','+',$urll);
          $urll= str_replace('rtmp://','http://',$urll);
        $addresstemp = strstr($urll,"http://");
        $address = strtok($addresstemp,"+");
        header("location:$address");
        //return $vdata;
        //return $urll;
}

$xml="<list>\n";
if(isset($_GET['id'])){
$xml.=variety_id($_GET['id']);
}else{
$xml.=variety_list();
}
$xml.="</list>\n";
echo $xml;

function preg($url, $preg, $bool) {
        if ($bool) {
                preg_match_all($preg, $url, $ar);
        } else {
                preg_match($preg, file_get_contents($url), $ar);
        }
        return $ar[1];
}
?>