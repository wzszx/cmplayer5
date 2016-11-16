    <?php
    function getdata($url) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            $ch = curl_init();
            $timeout = 30;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
            @ $c = curl_exec($ch);
            curl_close($ch);
            //$c = mb_convert_encoding($c, 'GBK', 'UTF-8');
            $c = iconv('UTF-8', 'GBK', $c);
            return $c;
    }
    $xml = '<list>'."\n";
    $u = getdata('http://xiu.56.com/api/liveListv3.php');
    preg_match_all('/"roomArray":(.*),\"onlineUser/imsU',$u, $ar);
    preg_match_all('/\{([^}]+)\}/ims',$ar[1][0], $arr);
            $arr=$arr[1];
            $num=count($arr);
            $list= '<m label="我秀直播大厅('.$num.')">'."\n";
            for($o=0;$o<$num;$o++){
                    $m='{'.$arr[$o].'}';
                    $obj = json_decode($m);
                    $user_id = $obj->user_id;
                    $host = $obj->host;
                    $token = $obj->token;
                    $nickname = $obj->nickname;
                    $nickname = iconv("UTF-8", "gbk//IGNORE" , $nickname);
                    $room_img = $obj->room_img;
                    $roomid = $obj->roomid;
                    $count = $obj->count;
                    $starttime = $obj->starttime;
                    $list .='<m src="'.$token.'" rtmp="rtmp://play.xiu.v-56.com/vshow" label="'.$nickname.'" image="'.$room_img.'" text="观众数：'.$count.' 开播: '.$starttime.'" />'."\n";
                    }
            $list .= "</m>\n";
    $xml .=$list.'</list>';
    echo $xml;
    ?>