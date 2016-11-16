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
    function movie_p($typeid,$p){
                global $filename;
            $u = 'http://sns.video.qq.com/fcgi-bin/txv_lib?mi_mtype=2&mi_type='.$typeid.'&mi_year=-1&mi_area=-1&mi_show_type=0&mi_sort=1&mi_pagesize=30&mi_pagenum=' . $p . '&mi_online=1&mi_index_type=0&otype=json&&_=2182429';
            $a = g_s($u);
            preg_match_all('|cover_id":"([^"]+)"|', $a, $m);
            preg_match_all('|"title":"([^"]+)","view|', $a, $n);
            $m=$m[1];
            $n=$n[1];
            $num=count($m);
            $list='';
            for($o=0;$o<$num;$o++){
                             $nm=$m[$o];
                    $mn=str_split($nm,1);
                    @$newname = htmlspecialchars($n[$o], ENT_QUOTES);
                    $list .= '<m list_src="' . $filename . '?vod=http://v.qq.com/cover/' .$mn[0].'/'. $nm . '.html" label="' . $newname . '" />';
                    $list.= "\n";
            }
            return $list;
            /*
            $arr = array_combine($m[1], $n[1]);
            foreach ($arr as $k => $v) {
                    $xml .= '<m list_src="' . $filename . '?vod=http://v.qq.com/cover' . $k . '.html" label="' . $v . '" />';
                    $xml .= "\n";
            }
            */
    }
    function vid($vid){
            $g = 'http://vv.video.qq.com/geturl?vid='. $vid .'';
            $h = g_s($g);
            preg_match('|<url>(.*)</url>|', $h, $j);
            preg_match('|<vt>(.*)</vt>|', $h, $vt);
            if ($vt[1] == "203"){
                    vid($vid);
            }else{
                    $j = $j[1];
                    header("location:$j");
            }
    }
    $xml = '<list>';
    $xml .= "\n";
    $filename = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["SCRIPT_NAME"];
    if (isset ($_GET['vod'])) {
            $str = g_s($_GET['vod']);
            preg_match('@vid:"([0-9a-zA-Z|]+)"@', $str, $ar);
            preg_match('|<span id="h1_title">(.*)</span>|', $str, $fn);
            $fn = $fn[1];
            $ar = $ar[1];
            $ar = explode('|', $ar);
            foreach ($ar as $k => $v) {
                    $z = $k +1;
                    $xml .= '<m type="2" src="' . $filename . '?vid=' . $v . '" label="' . $fn . 'part.' . $z . '" /> ';
                    $xml .= "\n";
            }
    }
    elseif (isset ($_GET['vid'])) {
            vid($_GET['vid']);
    }
    elseif (isset ($_GET['type'])) {
                /* $type_p = array(
                    "0"=>23,
                    "1"=>5,
                    "3"=>11,
                    "2"=>9,
                    "5"=>1,
                    "6"=>2,
                    "7"=>3,
                    "8"=>2,
                    "9"=>4,
                    "10"=>0,
                    "4"=>2,
                    "19"=>0,
                    "16"=>1,
                    "17"=>0,
                    "18"=>0,
                    "15"=>0,
                    "14"=>0,
                    "22"=>0,
                    "13"=>0);
                    $pp = $type_p[$_GET['type']];
                    */
            for ($i = 0; $i <= 23; $i++) {
                            $xml.=movie_p($_GET['type'],$i);
                    }
    } else {
                $mi_type = array(
                    "0"=>"¶¯×÷",
                    "1"=>"Ã°ÏÕ",
                    "3"=>"Ï²¾ç",
                    "2"=>"°®Çé",
                    "5"=>"Õ½Õù",
                    "6"=>"¿Ö²À",
                    "7"=>"·¸×ï",
                    "8"=>"ÐüÒÉ",
                    "9"=>"¾ªã¤",
                    "10"=>"ÎäÏÀ",
                    "4"=>"¿Æ»Ã",
                    "19"=>"ÒôÀÖ",
                    "16"=>"¶¯»­",
                    "17"=>"Ææ»Ã",
                    "18"=>"¼ÒÍ¥",
                    "15"=>"¾çÇé",
                    "14"=>"Â×Àí",
                    "22"=>"¼ÇÂ¼",
                    "13"=>"ÀúÊ·");
            foreach ($mi_type as $k => $v) {
                    $xml .= '<m list_src="' . $filename . '?type=' . $k . '" label="' . $v . '" />';
                    $xml .= "\n";
            }
    }
    $nullurl='<m list_src="'.$filename .'?vod=http://v.qq.com/cover.html" label="" />';
    $nullname='label=""';
    $xml=str_replace($nullurl,'',$xml);
    $xml=str_replace($nullname,'label="Null Name"',$xml);
    $xml .= '</list>';
    echo $xml;
    ?>