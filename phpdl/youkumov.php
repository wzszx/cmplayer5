<?php
$fname='http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
$xml = "<list>\n";
error_reporting(0);
function file_data($url) {
        for ($i = 0; $i < 3; $i++) {
                $data = file_get_contents($url);
                if ($data)
                        break;
        }
        if ($data)
                return $data;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        @ $data = curl_exec($ch);
        curl_close($ch);
        return $data;
}
$youid=$_GET['id'];
if (isset ($youid)){
        $a = file_data('http://www.youku.com/show_eplist/showid_' . $youid . '_type_pic_from_ajax_page_1.html');
        preg_match_all("/type_pic_from_ajax_page_(.*?).html(.*?)<\/a>/",$a,$b);
        $d = count($b[0]);
        for($m=0;$m<$d;$m++){
        $i=$m+1;
        $c .= file_data('http://www.youku.com/show_eplist/showid_' . $youid . '_type_pic_from_ajax_page_' . $i . '.html');
                            }
        preg_match_all("/href=\"http:\/\/v.youku.com\/v_show\/id_(.*?).html(.*?)target=\"video\">(.*?)<\/a>/",$c,$e);
        $f = count($e[0]);
for($g=0;$g<$f;$g++){
                    if ($e[3][$g]){
                                    $xml .= '<m type="youku" streamtype="flv" src="' . $e[1][$g] . '" label="' . $e[3][$g] . '"/>'."\n";
                                  }
                    }
                   }
elseif(isset ($_GET['vid'])){
global $fname;
$you = file_data('http://tv.youku.com/search/index/_page40177_1_cmodid_40177?cc-showdivid=no&srcmid=40178&srcidx=2&show_catalogs_q_40178_area=&show_catalogs_q_40178_genre=tv_genre' . $_GET['vid'] . '&show_catalogs_q_40178_releaseyear=&show_catalogs_q_40178_orderby=7&show_catalogs_fd_40178=&cmodid=40177&__rt=1&__ro=m13050845531');
$you1 = preg_match("'<li class=\"pass\">...<\/li>(.*)<div class=\"turn\">'isU",$you, $you2);
$you3 = preg_match("'charset=\"(.*)\">(.*)<\/a>'isU",$you2[0], $you4);
$d = $you4[2];
for($w=0;$w<$d;$w++){
$z=$w+1;
$xml .= '<m list_src="'.$fname.'?sid='.urlencode($_GET['vid']).'&page='.$z.'" label="第'.$z.'页" />'."\n";
                    }
                             }
elseif(isset ($_GET['sid'])){
global $fname;
$youku = file_data('http://tv.youku.com/search/index/_page40177_'.$_GET['page'].'_cmodid_40177?cc-showdivid=no&srcmid=40178&srcidx=2&show_catalogs_q_40178_area=&show_catalogs_q_40178_genre=tv_genre' . $_GET['sid'] . '&show_catalogs_q_40178_releaseyear=&show_catalogs_q_40178_orderby=7&show_catalogs_fd_40178=&cmodid=40177&__rt=1&__ro=m13050845531');
preg_match_all("/<li class=\"p_link\"><a title=\"(.*?)\" href=\"http:\/\/www.youku.com\/show_page\/id_(.*?).html/",$youku,$youku2);
$o= count($youku2[0]);
for($u=0;$u<$o;$u++){
$xml .= '<m list_src="'.$fname.'?id=' . $youku2[2][$u] . '" label="' . $youku2[1][$u] . '" />'."\n";
    }
} else {
        $xml .= null_list();
}
function null_list() {
global $fname;
$xml .= '<m list_src="'.$fname.'?vid=%3A%E5%8F%A4%E8%A3%85" label="古装" />'."\n";
$xml .= '<m list_src="'.$fname.'?vid=%3A%E6%AD%A6%E4%BE%A0" label="武侠" />'."\n";
$xml .= '<m list_src="'.$fname.'?vid=%3A%E8%AD%A6%E5%8C%AA" label="警匪" />'."\n";
$xml .= '<m list_src="'.$fname.'?vid=%3A%E5%86%9B%E4%BA%8B" label="军事" />'."\n";
$xml .= '<m list_src="'.$fname.'?vid=%3A%E7%A5%9E%E8%AF%9D" label="神话" />'."\n";
$xml .= '<m list_src="'.$fname.'?vid=%3A%E7%A7%91%E5%B9%BB" label="科幻" />'."\n";
$xml .= '<m list_src="'.$fname.'?vid=%3A%E6%82%AC%E7%96%91" label="悬疑" />'."\n";
$xml .= '<m list_src="'.$fname.'?vid=%3A%E5%8E%86%E5%8F%B2" label="历史" />'."\n";
$xml .= '<m list_src="'.$fname.'?vid=%3A%E5%84%BF%E7%AB%A5" label="儿童" />'."\n";
$xml .= '<m list_src="'.$fname.'?vid=%3A%E9%83%BD%E5%B8%82" label="都市" />'."\n";
$xml .= '<m list_src="'.$fname.'?vid=%3A%E5%AE%B6%E5%BA%AD" label="家庭" />'."\n";
$xml .= '<m list_src="'.$fname.'?vid=%3A%E6%90%9E%E7%AC%91" label="搞笑" />'."\n";
$xml .= '<m list_src="'.$fname.'?vid=%3A%E5%81%B6%E5%83%8F" label="偶像" />'."\n";
$xml .= '<m list_src="'.$fname.'?vid=%3A%E5%81%B6%E5%83%8F" label="言情" />'."\n";
        return $xml;
}
$xml .= '</list>';
echo $xml;
?>