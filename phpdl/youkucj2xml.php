<?php
/* 支持缩略图可选，一键复制代码（限IE）。
格式：mm.php?id=url&thumb=1
如果不需要缩略图 thumb 参数可以省略。
*/
error_reporting(0);
if (isset ($_GET['id'])) { 
$xml .= yk_list($_GET['id']);
}
function geturl($url){
        $filedata = iconv('UTF-8', 'GBK//IGNORE',file_get_contents($url));
        return $filedata;
}
function yk_list($url) { 
       preg_match('/http\:\/\/.*\/([A-Za-z0-9-_]+)\/id\_/',$url,$ns);
		$hwd=$ns[0];
		preg_match('|id_([0-9a-zA-Z]+)\.html|', $url, $id);  
		$filedata = geturl($url); 
        $xml .= '<list>'. "\n";;
        if (preg_match('|[/>]([0-9]+)</span>条|', $filedata, $num)) { 
                $m = ceil($num[1] / 20); 
                $filedata = '';
                for ($i = 1; $i <= $m; $i++) {  
                        $u = $hwd . $id[1] . '_ascending_1_mode_pic_page_' . $i . '.html';
                        $filedata .= geturl($u); 
                }
        }
        preg_match_all('|PlayListFlag_([^"]+)"|ims',$filedata,$num);
        preg_match_all('| class=\"v_thumb\"><img src=\"[a-zA-z]+://[a-zA-z0-9]+.ykimg.com[^\s]*" alt=".*?"|', $filedata, $img);
        $arr = array_merge($num[1],$img[0]);
        $result = count($arr);
        for ($i=0; $i<$result/2; $i++){
                $j = explode('"', $arr[$result/2+$i]);
                if($_GET['thumb']!=null){
                $xml .= '<m type="youku" streamtype="flv" image="'.$j[3].'" src="' . $arr[$i] . '" stream="true" label="' . $j[5] . '"/>' . "\n"; 
                }else{
                $xml .= '<m type="youku" streamtype="flv" src="' . $arr[$i] . '" stream="true" label="' . $j[5] . '"/>' . "\n"; 
                }
}
$xml .= '</list>';
return $xml; 
} 
echo $xml. "\n";