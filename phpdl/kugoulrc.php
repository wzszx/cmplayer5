    <?php
    $type=$_REQUEST[type];
    $alltype=array("2","video","flv","mp4","youku");
    if(in_array($type,$alltype)){
    exit;
    }
    error_reporting(0);
    $title = urldecode($_REQUEST[title]);
    $name = explode('-', $title);
    $size = sizeof($name);
    $lrc = baidu_lrc($name[0], $name[1]);
    if (!$lrc) {
            $lrc = qq_lrc($title);
    }
    if (!$lrc) {
            $lrc = "[ti:歌词没找到]";
    }
    echo $lrc;
    function file_data($url) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            $ch = curl_init();
            $timeout = 8;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_REFERER, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
    }
    function baidu_data($url) {
            $str = file_data($url);
            if (preg_match('|<div class="iii">\s+<a href="([^"]+)" target|U', $str, $lrc)) {
                    $lrcstr = file_data($lrc[1]);
                    return $lrcstr;
            } else {
                    return;
            }
    }
    function qq_lrc($name) {
            $url = 'http://portalcgi.music.qq.com/fcgi-bin/music_mini_portal/cgi_mini_portal_search_json.fcg?search_input=' . urlencode($name) . '&start=1&return_num=20&utf8=0&outputtype=1';
            $str = file_data($url);
            if (preg_match('|songID":([0-9]+),"|U', $str, $lrcid)) {
                    //如果匹配到歌曲ID 则执行 否则返回失败
                    $lrcurl = 'http://portalcgi.music.qq.com/fcgi-bin/music_download/fcg_get_lyric.fcg?id=' . $lrcid[1];
                    $lrcstr = file_data($lrcurl);
                    return lrc_th($lrcstr);
            } else {
                    return;
            }
    }
    function baidu_lrc($songname, $singername) {
            if (!empty ($singername)) {
                    //如果歌手为空 则搜索地址为下
                    $url = 'http://mp3.baidu.com/m?f=3&tn=baidump3lyric&ct=150994944&lf=2&rn=10&word=' . $songname . '+' . $singername . '&lm=-1&oq=' . $songname . '+&rsp=0';
            } else {
                    //否则歌曲搜索结果地址为下
                    $url = 'http://mp3.baidu.com/m?f=ms&tn=baidump3lyric&ct=150994944&lf=2&rn=10&word=' . $songname . '&lm=-1';
            }
            $str = baidu_data($url);
            if (preg_match('|<!DOCTYPE|i', $str)) {
                    //如果检测到是网页 则返回
                    return;
            }
            elseif (empty ($str)) {
                    //如果为空则返回
                    return;
            } else {
                    return lrc_th($str);
            }
    }
    function lrc_th($str) {
            $str = preg_replace("@(\w+)?\.?(\w+)\.(com|org|info|net|cn|biz|cc|uk|tk|jp|la|ru|us|ws)@U", '435861067.qzone.qq.com', $str);
            // 替换域名
            $str = preg_replace("@\[by:\s?([^\]]+)\]@U", '[by:深山红叶]', $str);
            //替换歌词制作者
            $str = preg_replace("@(\d+){5,11}@", '245054917', $str);
            //替换5位以上的数字为QQ
            $str = preg_replace("@编辑\s?：?:?\s?([^\[]+)\[@", '编辑：深山红叶-QQ:435861067[', $str);
            //替换编辑者
            return $str;
    }
    ?>