    <?php
    $fname = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
    $listname = array (
    'day' => '今日最多播放',
    'wek' => '本周最多播放',
    'his' => '历史最多播放',
    'new' => '最新上映',
    'love' => '用户好评'
    );
    $xml = '<list>' . "\n";
    function file_data($url) {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $ch = curl_init();
    $timeout = 30;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    @ $data = curl_exec($ch);
    curl_close($ch);
    @ $data = iconv('UTF-8', 'GBK', $data);
    return $data;
    }
    function film_list_data($url) {
    $youku_film = 'http://movie.youku.com/search/index/_page40487_';
    $s = '';
    for ($p = 1; $p <= 4; $p++) {
    $u = $youku_film . $p . $url;
    $s .= file_data($u);
    }
    return $s;
    }
    function tv_list_data($url) {
    $youku_tv = 'http://movie.youku.com/search/index/_page40177_';
    $s = '';
    for ($p = 1; $p <= 4; $p++) {
    $u = $youku_tv . $p . $url;
    $s .= file_data($u);
    }
    return $s;
    }
    function yu_gao($url) {
    $s = file_data($url);
    preg_match('|target="_blank"  href="http://v.youku.com/v_show/id_([^"]+)\.html"  ><span class="status">|U', $s, $a);
    $t = '<m type="youku"  streamtype="flv" src="' . $a[1] . '" label="点击播放预告 " />' . "\n";
    return $t;
    }
    function make_moive_list($str) {
    $list_str = film_list_data($str);
    $strl = '';
    preg_match_all('|<li class="p_title"><a title="([^>]+)"  target="_blank">|ims', $list_str, $ar);
    foreach ($ar[1] as $k => $v) {
    if (preg_match('|v_show/id_([^"]+)\.html|', $v, $ac)) {
    $z = explode('" href="', $v);
    $strl .= '<m type="youku" streamtype="flv" password="" src="' . $ac[1] . '" label="' . $z[0] . '"/>' . "\n";
    }
    elseif (preg_match('|show_page|', $v)) {
    $z = explode('" href="', $v);
    $strl .= '<m list_src="http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . '?yugao=' . $z[1] . '" label="' . $z[0] . '"/>' . "\n";
    }
    }
    return $strl;
    }
    function make_tv_list($str) {
    global $fname;
    $list_str = tv_list_data($str);
    $strl = '';
    preg_match_all('|<li class="p_title"><a title="([^>]+)"  target="_blank">|ims', $list_str, $ar);
    foreach ($ar[1] as $k => $v) {
    $z = explode('" href="', $v);
    $strl .= '<m list_src="' . $fname . '?tvid=' . $z[1] . '" label="' . $z[0] . '"/>' . "\n";
    }
    return $strl;
    }
    function tv_list($tv_url) {
    preg_match('|id_([0-9a-zA-Z]+)\.html|', $tv_url, $tvid);
    $str = file_data($tv_url);
    if (preg_match('|/([0-9]+)</span>条|', $str, $num)) {
    $f = ceil($num[1] / 6);
    $n = '';
    for ($i = 1; $i <= $f; $i++) {
    $t = 'http://www.youku.com/show_eplist/showid_' . $tvid[1] . '_type_list_from_ajax_page_' . $i . '.html';
    @ $n .= file_data($t);
    }
    preg_match_all('|"  title="([^>]+)\.html" target="video">|ims', $n, $arr);
    $q = '';
    foreach ($arr[1] as $k => $v) {
    $j = explode('" href="http://v.youku.com/v_show/id_', $v);
    $q .= '<m type="youku" streamtype="flv"  src="' . $j[1] . '" label="' . $j[0] . '"/>' . "\n";
    }
    }elseif(preg_match_all('|PlayListFlag_([^"]+)"|ims',$str,$num)){
    $q='';
    foreach ($num[1] as  $k=> $v) {
    $z=$k+1;
    $q .= '<m type="youku" streamtype="flv"  src="' . $v . '" label="第' . $z . '集"/>' . "\n";
    }
    }else{
    $q='<m label="暂无内容" />';
    }
    return $q;
    }
    function film_default($list) {
    $xml = '';
    $day = '_cmodid_40487?cc-showdivid=no&cc-ms-q=showcategory%3A%E7%94%B5%E5%BD%B1+state%3Anormal+allowfilter%3A1+++&cc-ms-ob=7&cc-ms-fd=R%7Cshowday_vv%7C%7Cshowday_vv&cmodid=40487&__rt=1&__ro=m13055109992';
    $wek = '_cmodid_40487?cc-showdivid=no&cc-ms-q=showcategory%3A%E7%94%B5%E5%BD%B1+state%3Anormal+allowfilter%3A1+++&cc-ms-ob=6&cc-ms-fd=R%7Cshowday_vv%7C%7Cshowweek_vv&cmodid=40487&__rt=1&__ro=m13055109992';
    $his = '_cmodid_40487?cc-showdivid=no&cc-ms-q=showcategory%3A%E7%94%B5%E5%BD%B1%20state%3Anormal%20allowfilter%3A1%20%20%20&cc-ms-ob=1&cc-ms-fd=R%7Cshowday_vv%7C%7Cshowtotal_vv&cmodid=40487&__rt=1&__ro=m13055109992';
    $new = '_cmodid_40487?cc-showdivid=no&cc-ms-q=showcategory%3A%E7%94%B5%E5%BD%B1%20state%3Anormal%20allowfilter%3A1%20%20%20&cc-ms-ob=3&cc-ms-fd=R%7Cshowday_vv%7C%7Creleasedate&cmodid=40487&__rt=1&__ro=m13055109992';
    $love = '_cmodid_40487?cc-showdivid=no&cc-ms-q=showcategory%3A%E7%94%B5%E5%BD%B1+state%3Anormal+allowfilter%3A1+++&cc-ms-ob=11&cc-ms-fd=R%7Cshowday_vv%7C%7Cshowtotal_up+showtotal_down&cmodid=40487&__rt=1&__ro=m13055109992';
    $list = $$list;
    $xml .= make_moive_list($list);
    return $xml;
    }
    function tv_default($list) {
    $xml = '';
    $day = '_cmodid_40177?cc-showdivid=no&cc-ms-q=showcategory%3A%E7%94%B5%E8%A7%86%E5%89%A7+state%3Anormal+allowfilter%3A1+++&cc-ms-ob=7&cc-ms-fd=R%7Cshowday_vv%7C%7Cshowday_vv&cmodid=40177&__rt=1&__ro=m13050845531';
    $wek = '_cmodid_40177?cc-showdivid=no&cc-ms-q=showcategory%3A%E7%94%B5%E8%A7%86%E5%89%A7+state%3Anormal+allowfilter%3A1+++&cc-ms-ob=6&cc-ms-fd=R%7Cshowday_vv%7C%7Cshowweek_vv&cmodid=40177&__rt=1&__ro=m13050845531';
    $his = '_cmodid_40177?cc-showdivid=no&cc-ms-q=showcategory%3A%E7%94%B5%E8%A7%86%E5%89%A7+state%3Anormal+allowfilter%3A1+++&cc-ms-ob=1&cc-ms-fd=R%7Cshowday_vv%7C%7Cshowtotal_vv&cmodid=40177&__rt=1&__ro=m13050845531';
    $new = '_cmodid_40177?cc-showdivid=no&cc-ms-q=showcategory%3A%E7%94%B5%E8%A7%86%E5%89%A7+state%3Anormal+allowfilter%3A1+++&cc-ms-ob=3&cc-ms-fd=R%7Cshowday_vv%7C%7Creleasedate&cmodid=40177&__rt=1&__ro=m13050845531';
    $love = '_cmodid_40177?cc-showdivid=no&cc-ms-q=showcategory%3A%E7%94%B5%E8%A7%86%E5%89%A7+state%3Anormal+allowfilter%3A1+++&cc-ms-ob=11&cc-ms-fd=R%7Cshowday_vv%7C%7Cshowtotal_up+showtotal_down&cmodid=40177&__rt=1&__ro=m13050845531';
    $list = $$list;
    $xml .= make_tv_list($list);
    return $xml;
    }
    function null_list() {
    global $fname;
    $xml = '';
    $xml .= '<m list_src="' . $fname . '?type=moive" label="优酷电影" />' . "\n";
    $xml .= '<m list_src="' . $fname . '?type=tv" label="优酷电视剧" />' . "\n";
    return $xml;
    }
    if (isset ($_GET['type'])) {
    if ($_GET['type'] == 'tv') {
    foreach ($listname as $k => $v) {
    $xml .= '<m list_src="' . $fname . '?tv=' . $k . '" label="' . $v . '" />' . "\n";
    }
    }
    elseif ($_GET['type'] == 'moive') {
    foreach ($listname as $k => $v) {
    $xml .= '<m list_src="' . $fname . '?moive=' . $k . '" label="' . $v . '" />' . "\n";
    }
    }
    }
    elseif (isset ($_GET['tv'])) {
    $xml .= tv_default($_GET['tv']);
    }
    elseif (isset ($_GET['moive'])) {
    $xml .= film_default($_GET['moive']);
    }
    elseif (isset ($_GET['tvid'])) {
    $xml .= tv_list($_GET['tvid']);
    }
    elseif (isset ($_GET['yugao'])) {
    $xml .= yu_gao($_GET['yugao']);
    } else {
    $xml .= null_list();
    }
    $xml .= '</list>';
    echo $xml;
    ?>