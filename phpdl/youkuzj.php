<?php
error_reporting(0);
@ini_set('memory_limit',          '200M');

$mh = curl_multi_init();
$handles = array();


$num = $_GET['id'];

preg_match_all('|(.*?),|ims', $num, $x);

$id = count($x[1]);


for($i=0;$i<$id;$i++)


{



$urll ="http://zxb56100.sinaapp.com/?list=".$x[1][$i]."";

$ch = curl_init();



curl_setopt($ch, CURLOPT_URL, $urll);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 50);



curl_multi_add_handle($mh,$ch);


$handles[] = $ch;
}

$running=null;
do 
{
curl_multi_exec($mh,$running);

usleep (1);
} while ($running > 0);


$xml="<list>"."\n";
$output="";
for($i=0;$i<count($handles);$i++)
{

$output.= curl_multi_getcontent($handles[$i]);


curl_multi_remove_handle($mh,$handles[$i]);
}






curl_multi_close($mh);


$y=str_replace('<list>'."\n",'',$output);
$xml.=str_replace('</list>','',$y);

$xml.="</list>"."\n";

echo $xml;
?>

