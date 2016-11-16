    <?php
    error_reporting(0);
    header("Content-type: text/xml; charset=utf-8");
    $guid=$_REQUEST['guid'];
    $path=$_REQUEST['path'];
    $aid=$_REQUEST['aid'];
    if($guid){
    uuGuid($guid);
    }
    if($aid){
    uuAid($aid);
    }
    if($path){
    uupath($path);
    }
    function uuAid($aid){
    $pathone=substr($aid,-3,3);
    $pathtwo=substr($aid,-6,3);
    $url="http://js1.uusee.com/new/player/$pathone/$pathtwo/$aid.js";
    preg_match("/guid:'([^']+)'/imsU",file_get_contents($url),$guid);
    uuGuid($guid[1]);
    exit;

    }
    function uupath($path){
    $str=file_get_contents($path);
    $json=json_decode($str);
    $f4v=$json->l;
    header("location:$f4v");
    }

    function uuGuid($guid){
    $pathone=substr($guid,1,2);
    $pathtwo=substr($guid,3,2);
    $uu="http://player.uusee.com/f4v_player/xml/$pathone/$pathtwo/".urlencode($guid).".xml";
    $uustr=file_get_contents($uu);;
    preg_match_all('/url="([^"]+)"/',$uustr,$url);
    $xml="<list>\n";
    foreach($url[1]  as $k=>$v){
    $d=$k+1;
    $v=str_replace('http://v1.uusee.com','http://221.238.29.80',$v);
    $f = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
    $xml.="<m label=\"µÚ{$d}¶Î\"  src=\"{$f}?path={$v}\"  />\n";
    }
    $xml.="</list>";
    echo $xml;
    exit;
    }

    ?>