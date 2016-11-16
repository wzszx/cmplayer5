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
    if (isset ($_GET['id'])) {
            $a = $_GET['id'];
            $a = str_replace('detail', 'cover', $a);
            $b = g_s($a);
            preg_match('|<span>(.*)</span>|U', $b, $f);
             $f = htmlspecialchars($f[1], ENT_QUOTES);
            if (preg_match_all('#sv="([0-9a-zA-Z|]+)"  tl=#', $b, $c)) {
                    $c = $c[1];
                    foreach ($c as $d => $e) {
                            @ $e = explode('|', $e);
                            $dt = $d +1;
                            $xml .= '<m src="' . $filename . '?vid=' . $e[0] . '" label="第' . $dt . '集"  />';
                            $xml .= "\n";
                    }
            }elseif(preg_match_all('#<li><a target="_self" href="javascript:;" id="([0-9a-zA-Z]+)"#', $b, $g)){
                    $g=$g[1];
                    foreach($g as $u =>$r){
                            $dt=$u+1;
                            $xml .= '<m src="' . $filename . '?vid=' . $r . '" label="' . $f . '-' . $dt . '"  />';
                            $xml .= "\n";

                    }
            }
    }
    elseif (isset ($_GET['vid'])) {
            $g = 'http://vv.video.qq.com/geturl?vid=' . $_GET['vid'];
            $h = g_s($g);
            preg_match('|<url>(.*)</url>|', $h, $j);
            $j = $j[1];
            header("location:$j");
    }
    elseif (isset ($_GET['tv'])) {
            $k = 'http://sns.video.qq.com/fcgi-bin/txv_lib?mi_mtype=3&mi_type=-1&mi_year=-1&mi_area=-1&mi_show_type=0&mi_sort=1&mi_pagesize=30&mi_pagenum=' . $_GET['tv'] . '&mi_online=1&mi_index_type=0&otype=json&&_=2182175';
            $l = g_s($k);
            //$l = mb_convert_encoding($l, 'GBK', 'UTF-8');
            preg_match_all('|cover_id":"([0-9a-zA-Z]+)"|', $l, $m);
            preg_match_all('|"title":"([^"]+)","view|', $l, $n);
            $m = $m[1];
            $n = $n[1];
            $list = '';
            $num = count($m);
            for ($o = 0; $o < $num; $o++) {
                    $nm = $m[$o];
                    $mn = str_split($nm, 1);
                    @$newname = htmlspecialchars($n[$o], ENT_QUOTES);
                    @ $list .= '<m list_src="' . $filename . '?id=http://v.qq.com/cover/' . $mn[0] . '/' . $nm . '.html" label="' . $newname . '" />';
                    $list .= "\n";
            }
            $xml .= $list;
    } else {
            for ($p = 0; $p <= 28; $p++) {
                    $pp=$p+1;
                    $xml .= '<m list_src="' . $filename . '?tv=' . $p . '" label="Page ' . $pp . '" />';
                    $xml .= "\n";
            }
    }
    $xml .= '</list>';
    $nullurl = '<m list_src="' . $filename . '?id=http://v.qq.com/cover.html" label="" />';
    $nullname = 'label="请稍后重试 Please come back later"';
    $xml = str_replace('label=""', $nullname, $xml);
    $xml = str_replace($nullurl, '', $xml);
    echo $xml;
    ?>