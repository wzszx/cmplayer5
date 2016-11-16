    <?php
   // header("Content-type: text/xml; charset=UTF-8");
    error_reporting(0);
    $classid = $_REQUEST['classid'];
    $singerid = $_REQUEST['singerid'];
    $List = "<list>\n";
    $f = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
    if ($classid) {
            MakeClass($classid);
    }
    if ($singerid) {
            singerid($singerid);

    } else {
            index();
    }
    function singerid($sid) {
            global $List;
            $mysql = new SaeMysql();
            $sql = "SELECT * FROM yy_song WHERE singerid='$sid'";
            $data = $mysql->getData($sql);
            $mysql->closeDb();
            foreach ($data as $r) {
                    $songname = htmlspecialchars($r['songname']);
                    $flv = $r['flv'];
                    /*
                    完整版数据库含缩略图 精简版不含
                    $songpic = $r['songpic'];
                    $List .= "<m src=\"$flv\" image=\"http://www.yinyuetai.com$songpic\" label=\"$songname\" />\n";
                    */
                    $List .= "<m src=\"$flv\"  label=\"$songname\" />\n";
                   
            }
            $List .= "</list>";
            echo $List;
            exit;

    }
    function CreatXml($sql) {
            global $f, $List;
            $mysql = new SaeMysql();
            $result = $mysql->getData($sql);
            $mysql->closeDb();
            foreach ($result as $arr) {
                    $singerid = $arr['singerid'];
                    $singername = htmlspecialchars(substr($arr['singername'], 0, 50));
                    $count = $arr['count'];
                    /*
                    $singerpic=$arr['singerpic'];
                    $List .= "<m list_src=\"$f?singerid=$singerid\" image=\"http://www.yinyuetai.com$singerpic\" label=\"$singername" . '[' . $count . "首]\" />\n";
                    */
                    $List .= "<m list_src=\"$f?singerid=$singerid\"   label=\"$singername" . '[' . $count . "首]\" />\n";
                   

            }
            $List .= '</list>';
            echo $List;
            exit;

    }

    function MakeClass($classid) {
            $classid = $classid -1;
            $area = floor($classid / 3) + 1;
            $sex = floor($classid % 3) + 1;
            $sql = 'SELECT * FROM `yy_singer` WHERE ';
            $sql .= "`area`='" . $area . "'  and `sex`='" . $sex . "'";
            $sql .= " ORDER BY   `count` DESC  LIMIT 0 , 200"; //显示歌手数  想显示多少位 就将100改成多少位 然后去掉前面注释
            CreatXml($sql);
    }
    function index() {
            global $f, $List;
            $class = array (
                    "内地男",
                    "内地女",
                    "内地组合",
                    "港台男",
                    "港台女",
                    "港台组合",
                    "欧美男",
                    "欧美女",
                    "欧美组合",
                    "韩国男",
                    "韩国女",
                    "韩国组合",
                    "日本男",
                    "日本女",
                    "日本组合"
            );
            foreach ($class as $key => $value) {
                    //防止 参数等于0时接收不到classid
                    $keys = $key +1;
                    $List .= "<m list_src=\"$f?classid=$keys\" label=\"".$value."歌手\" />\n";
            }
            $List .= '</list>';
            echo $List;

    }
    ?>