    <?php
    function g_s($url) {
       $user_agent = $_SERVER['HTTP_USER_AGENT'];
       $referer = "http://www.dj-dj.net/";
       $ch = curl_init();
       $timeout = 30;
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch, CURLOPT_REFERER, $referer);
       curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
       curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
       $c = curl_exec($ch);
       curl_close($ch);
       return $c;
    }
    function dj_id($id) {
            global $fname;
            $list = '';
            $url='http://www.dj-dj.net/home/04/01.html?id='.$id;
            $str=g_s($url);
            preg_match_all('|/other/playflvlist001\.php\?id=(.*)\'\,\'theFlashUrl|',$str,$ar);
            $ar=$ar[1];
            $url1='http://www.dj-dj.net/other/playflvlist001.php?id='.$ar[0];
            $str1=g_s($url1);
            preg_match('|<location>(.*)</location>|',$str1,$arr);
            $arr=$arr[1];
            header("location:$arr");
    }
    function dj_list() {
            global $fname;
            $list = "<list>\n";
            $url="http://www.dj-dj.net/home/03/02.html?pages_id=1";
            $str=g_s($url);
            preg_match_all('|<td><a target="mplay" href="http://www\.dj-dj\.net/home/04/01\.html\?id=(.*)</a></td>|',$str,$ar);
            $ar=$ar[1];
            foreach ($ar as $k => $v) {
                  $arr = explode('&type_db=ÖÐÎÄÎèÇú">', $v);
                  $list .= '<m type="2" src="'.$fname.'?id='.$arr[0].'" label="'.$arr[1].'" />'."\n";
            }
            $list .= "</list>";
       return $list;
    }
    $fname = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["SCRIPT_NAME"];
    if (isset ($_GET['id'])) {
       $xml .= dj_id($_GET['id']);
    }
    else{
       $xml .= dj_list();
    }
    echo $xml;
    ?>