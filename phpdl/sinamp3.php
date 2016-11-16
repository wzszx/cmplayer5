    <?php
    function g_s($url) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            $ch = curl_init();
            $timeout = 30;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
            @ $c = curl_exec($ch);
            curl_close($ch);
            $c = mb_convert_encoding($c, 'GBK', 'UTF-8');
            return $c;
    }
    $xml = '<list>';
    $xml .= "\n";
    $filename = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["SCRIPT_NAME"];
    if (isset ($_GET['vod'])) {
            $aaa = file_get_contents('http://music.sina.com.cn/yueku/singer_more_song.php?id='.$_GET['vod']);               
                    $r=explode('&&page=',$aaa);
    if(count($r) <2)
    {$address = str_replace("','",";;;",$aaa);
    $address = str_replace(array("'",")"),'',$address);
    preg_match_all('/this, (.*?);/',$address, $m);
    preg_match_all('/;;;(.*?)"/',$address, $n);
    $m=$m[1];
             $n=$n[1];
            $num=count($m);        
            for($o=0;$o<$num;$o++){
                             $nm=$m[$o];                                               
                    $mn=str_split($nm,1);
                    @$newname = htmlspecialchars($n[$o], ENT_QUOTES);
                                    $newname = str_replace(",","-",$newname);
                                    $newname = str_replace(" ","",$newname);
                               $newname = str_replace(",","-",$newname);
                            $newname = str_replace(" ","",$newname);
                    $xml .= '<m type="1" src="' . $filename . '?vid=' . $nm. '" label="' . $newname . '" lrc="lrc/lrc.php?title='.$newname . '" /> ';
                    $xml .= "\n";
            } }
    else
    {
    $a = str_replace('<a id="pg_on">1<','&&page=1"',$aaa);
    preg_match_all('/&&page=(.*?)"/',$a, $m);
    $m=$m[1];
            
            $num=count($m);
            
            for($o=0;$o<$num;$o++){
                             $nm=$m[$o];               
                    $mn=str_split($nm,1);
                              $xml .= '<m list_src="' . $filename . '?vcd='.$_GET['vod'].'&page=' . $nm . '" label="第' . $nm . '页" />';
                    $xml .="\n";
                                    }}                
                                    }
    elseif (isset ($_GET['vcd'])) {
    $a = file_get_contents('http://music.sina.com.cn/yueku/singer_more_song.php?id='.$_GET['vcd'].'&page='.$_GET['page']);
    $address = str_replace("','",";;;",$a);
    $address = str_replace(array("'",")"),'',$address);
    preg_match_all('/this, (.*?);/',$address, $m);
    preg_match_all('/;;;(.*?)"/',$address, $n);
    $m=$m[1];
             $n=$n[1];
            $num=count($m);        
            for($o=0;$o<$num;$o++){
                             $nm=$m[$o];                                               
                    $mn=str_split($nm,1);
                    @$newname = htmlspecialchars($n[$o], ENT_QUOTES);
                                    $newname = str_replace(",","-",$newname);
                                    $newname = str_replace(" ","",$newname);
                              $xml .= '<m type="1" src="' . $filename . '?vid=' . $nm. '" label="' . $newname . '" lrc="lrc/lrc.php?title='.$newname . '" /> ';
                    $xml .="\n";
            }
    }
    elseif (isset ($_GET['vid'])) {
      $url = file_get_contents('http://down.v.iask.com/ask_n.php?oid='.$_GET['vid'].'&pid=707');
    $addresstemp = strstr($url,"http://");
    $address = strtok($addresstemp,"\"");
    header("location:$address");
    }
    elseif (isset ($_GET['x'])) {
    $ii = explode("-",$_GET['x']);
    $iii = $ii[0];
    $iiii = $ii[1];
    for ($i = 1; $i <= $iiii; $i++) {
                    $t = $i ;
                    $xml .= '<m list_src="' . $filename . '?p=' .$iii.'-'. $i . '" label="第' . $t . '页 " />';
                    $xml .= "\n";
            }
    }
    elseif (isset ($_GET['p'])) {
               $address = explode('-',$_GET['p']);
    $a = file_get_contents('http://music.sina.com.cn/category/singer.php?singer='.$address[0].'&p='.$address[1]);
    $address = explode("按热度排序",$a);
    $address = $address[1];
    $address = str_replace(array("('","')"),"",$address);
    $address= preg_replace("/[\s]{2,}/","",$address).'';
    preg_match_all('/onclick="Cat_playsinger(.*?);"/',$address, $m);
    preg_match_all('/;" title="(.*?)">/',$address, $n);

    $m=$m[1];
             $n=$n[1];
            $num=count($m);      
            for($o=0;$o<$num;$o++){
                             $nm=$m[$o];                                               
                    $mn=str_split($nm,1);
                    @$newname = htmlspecialchars($n[$o], ENT_QUOTES);
                               $xml .= '<m list_src="' . $filename . '?vod=' . $nm . '" label="' . $newname . '" />';
                    $xml .="\n";
                                    }
    } else {
    $xml= '<list>
    <m list_src="' . $filename . '?x=1_1-55" label="华语男艺人" />
    <m list_src="' . $filename . '?x=1_2-50" label="华语女艺人" />
    <m list_src="' . $filename . '?x=1_3-22" label="华语乐队" />
    <m list_src="' . $filename . '?x=2_1-272" label="欧美男艺人" />
    <m list_src="' . $filename . '?x=2_2-94" label="欧美女艺人" />
    <m list_src="' . $filename . '?x=2_3-319" label="欧美乐队" />
    <m list_src="' . $filename . '?x=3_1-13" label="日韩男艺人" />
    <m list_src="' . $filename . '?x=3_2-15" label="日韩女艺人" />
    <m list_src="' . $filename . '?x=3_3-16" label="日韩乐队" />
    ';
    }
    $xml .= '</list>';
    echo $xml;
    ?>