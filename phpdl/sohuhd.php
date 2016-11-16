    <?php
    $fname = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["SCRIPT_NAME"];
    //定义fname变量，指定地址，比如your.sinaapp.com/php/sohuhd.php
    $xml = "<?xml version=\"1.0\" encoding=\"GBK\" ?>\n<list>\n";

    if (isset ($_GET['u'])) //如果链接中php？后字母为u，则执行该子程序
    {
        function get_pagenumber($u) //子函数，取电影分类下每个分页链接
        {
            global $fname;
            $t = explode('-', $u);//字符串分割函数 explode(分割起始点,字符串,limit)
            $list = '';
            for ($i = 1; $i <= $t[1]; $i++)
            {
                    $q = 'http://so.tv.sohu.com/list_p11_p2_' . $t[0] . '_p3_p4-1_p5_p6_p70_p82_p9-1_p10' . $i . '_p11.html';
            //http://so.tv.sohu.com/list_p11_p2_u7231_u60c5_u7247_p3_p4-1_p5_p6_p70_p80_p9-1_p101 _p11.html
                    $list .= '<m label="第' . $i . '页" list_src="' . $fname . '?n=' . $q . '" />' . "\n";  //设定下一级识别码为n
    //<m label="第一页" list_src="http://your.sinaapp.com/php/sohuhd.php? n=http://so.tv.sohu.com/list_p11_p2_u7231_u60c5_u7247_p3_p4-1_p5_p6_p70_p82_p9-1_p101_p11.html />
            }
            return $list;
        }
            $xml .= get_pagenumber($_GET['u']);
    }
    else   
         if (isset ($_GET['n'])) //如果链接中php？后字母为n，则执行该子程序
             {
               function get_movienumber($n) //子函数，获取当前电影页面地址
                {
                     global $fname;
                     function file_data($url) //子函数，获取当前网页数据
                                  {
                                      $user_agent = $_SERVER['HTTP_USER_AGENT'];
                                      $ch = curl_init();
                                      $timeout = 30;
                                       curl_setopt($ch, CURLOPT_URL, $url);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                                         curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
                                        @ $c = curl_exec($ch);
                                        curl_close($ch);
                                    return $c;
                                 }
                     $str = file_data($n);
                     preg_match_all('|<h4><a href="([^<]+)</a></h4>|', $str, $a1);
                     //正则表达式，匹配函数，提取特征
                     return $a1[1];
                     $list = '';
               
                  foreach ($a1[1] as $k => $v)
                     {
                         $a2 = explode('" target="_blank">', $v);
                         $list .= '<m label="' . $a2[1] . '" list_src="' . $fname . '?id=' . $a2[0] . '" />' . "\n";
                                                                                          //设定下一级识别码为id
             //对比<m label="最爱" list_src="http://your.sinaapp.com/php/sohuhd.php?id=http://tv.sohu.com/s2011/dyzuiai/" />
                     }
               
                  return $list;
               
               }
            $xml .= get_movienumber($_GET['n']);
             }
          
          else
               if (isset ($_GET['id'])) //如果链接中php？后字母为id，则执行该子程序
                {
                
                      function get_playadress($id) //子函数，获取当前影片实际播放地址
                      {
                          $str = file_get_contents($id);
                          if (preg_match('|var vid="([0-9]+)|', $str, $as))
                             {
                                   $im = $as[1];
                             }
                         
                          else  
            
                                 if (preg_match('|<div class=area id=picFocus><a \n\nhref="([^"]+)"|ims', $str, $as))
                                    {
                                         $str1 = file_data($as[1]);
                                         preg_match('|var vid="([0-9]+)|', $str1, $as);
                                         $im = $as[1];
                   
                                    }
                                 $url = 'http://hot.vrs.sohu.com/vrs_flash.action?vid=' . $im;
                                 function file_data($url)
                                  {
                                      $user_agent = $_SERVER['HTTP_USER_AGENT'];
                                      $ch = curl_init();
                                      $timeout = 30;
                                       curl_setopt($ch, CURLOPT_URL, $url);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                                         curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
                                        @ $c = curl_exec($ch);
                                        curl_close($ch);
                                    return $c;
                                 }
                                 
                                 $fp = file_data($url);
                                 preg_match_all('|[url]http://data.vod.itc.cn[/url]([^"]+)"|', $fp, $ar1);
                                 preg_match('|tvName":"([^"]+)","|', $fp, $name);
                                 preg_match('|"su":\["(.*)"\],"|', $fp, $ar2);
                                 $ar1 = $ar1[1];
                                 $ar2 = $ar2[1];
                                 $ar2 = explode('","', $ar2);
                                  @ $ar = array_combine($ar1, $ar2);
                                  $list = '';    //清空播放列表
            
            
                                  foreach ($ar as $k => $v)
                                  {
                                        $u = 'http://220.181.61.229/?prot=2&file=' . $k . '&new=' . $v;
                                        $m = file_data($u);
                                        $s = explode('|', $m);
                                        @ $flv = $s[0] . $v . '?key=' . $s[3];
                                        $list .= '<m type="2" src="' . $flv . '" label="' . $name[1] . '." />';
                                        $list .= "\n";
                                  }
                                  
                                  return $list;
                     }
                        $xml .= get_playadress($_GET['id']);
                }
                else //前面都没有匹配到，说明取得是首页分类链接
                     {   
                         function indexurl() //取首页分类链接地址参数
                          {
                            global $fname;
                            $lb = array
                               (
                               '爱情' => 'u7231_u60c5_u7247-21',
                               '动作' => 'u52a8_u4f5c_u7247-19',
                               '喜剧' => 'u559c_u5267_u7247-14',
                               '科幻' => 'u79d1_u5e7b_u7247-2',
                               '战争' => 'u6218_u4e89_u7247-3',
                               '恐怖' => 'u6050_u6016_u7247-6',
                               '风月' => 'u98ce_u6708_u7247-3',
                               '剧情' => 'u5267_u60c5_u7247-47',
                               '音乐' => 'u97f3_u4e50_u7247-1',
                               '动画' => 'u52a8_u753b_u7247-1',
                               '纪录' => 'u7eaa_u5f55_u7247-1'
                              );
                            $list = '';
                            
                            foreach ($lb as $k => $v)
                              {
                                 $list .= '<m label="' . $k . '" list_src="' . $fname . '?u=' . $v . '" />' . "\n";
    //<m label="爱情"  list_src="http://your.sinaapp.com/php/sohuhd.php?u=u7231_u60c5_u7247-21" />
                              }
                        return $list;
                         }
                     $xml .= indexurl();
                     }
                     
    $xml .= "</list>\n";
    echo $xml;
    ?>