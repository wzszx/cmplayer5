    <?

    /*
    $sexy=array('男'=>'0','女'=>'1','组合'=>'2');
    $areas=array('港台'=>'0','大陆'=>'1','欧美'=>'2','韩国'=>'3','日本'=>'4','其他'=>'5');
    */
    error_reporting(0);
    header("Content-type: text/xml; charset=utf-8");
    $thisurl = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
    $classid = $_REQUEST['classid'];
    $sid = $_REQUEST['sid'];
    if($sid){
            Makesid($sid);
    }
    if ($classid) {
            MakeClass($classid);
    }else{
    $arr = array("港台男","港台女","港台组合","大陆男","大陆女","大陆组合","欧美男","欧美女","欧美组合","韩国男","韩国女","韩国组合","日本男","日本女","日本组合","其他");
    $list = "<list>\n";
      foreach($arr as $k=>$v){
              $ks=$k+1;
                $list.="<m list_src=\"$thisurl?classid=$ks\" label=\"".$v."歌手\" />\n";
      } $list .= "</list>";
      echo $list;

    }
    function Makesid($sid){
            $mysql = new SaeMysql();
            $sql="SELECT * FROM `mtv` WHERE `sid`='".$sid."'";
            $datas=$mysql->getData($sql);
            $list = "<list>\n";
              foreach($datas as $r){
            $mvname=$r['mtvname'];
            $m=$r['mtvhash'];
            $mvurl="http://mvfiles.kugou.com/mp4/".$m{0}.$m{1}."/".$m{2}.$m{3}."/".$m.".mp4";
              $list.="<m label=\"$mvname\" src=\"$mvurl\" />\n";
              }
            $list .= "</list>";
            $mysql->closeDb();
            echo $list;
            exit;


    }
    function CreatXml($sql) {
            global $thisurl ;
            $mysql = new SaeMysql();
            $result = $mysql->getData($sql);
            $list = "<list>\n";
            foreach ($result as $r) {
                    $sid = $r['sid'];
                    $sname = htmlspecialchars($r['sname']);
                      $list .= "<m list_src=\"$thisurl?sid=$sid\" label=\"$sname\"  />\n";
                   
            }
            $list .= "</list>";
            unset ($thisurl);
            $mysql->closeDb();
            echo $list;
            exit;

    }

    function MakeClass($classid) {
              $area=floor(($classid-1)/3);
            $sex=($classid-1)%3;
            $sql = 'SELECT * FROM `mtvsinger` WHERE ';
              if($classid=='16'){
              $sql.="`area`='5'";
            }else{
            $sql.="`area`='".$area."'  and `sex`='".$sex."'";
            }
             //$sql .= " LIMIT 0 , 100"; //显示歌手数  想显示多少位 就将100改成多少位 然后去掉前面注释
               CreatXml($sql);
    }
    ?>