<?php
$time_start = microtime(true);
define('ROOT', dirname(__FILE__).'/');
define('MATCH_LENGTH', 0.1*1024*1024); //字符串长度 0.1M 自己设置，一般够了。
define('RESULT_LIMIT',100);

function my_scandir($path){ //获取数据文件地址
        $filelist=array();
        if($handle=opendir($path)){
        while (($file=readdir($handle))!==false){
         if($file!="." && $file !=".."){
             if(is_dir($path."/".$file)){
                $filelist=array_merge($filelist,my_scandir($path."/".$file));
                 }else{
                  $filelist[]=$path."/".$file;
                 }
            }
        }
     }
    closedir($handle);
    return $filelist;
}

function get_results($keyword){ //查询
    $return=array();
    $count=0;
    $datas=my_scandir(ROOT."database"); //数据库文档目录
    if(!empty($datas))foreach($datas as $filepath){
        $filename = basename($filepath);
        $start = 0;
        $fp = fopen($filepath, 'r');
          while(!feof($fp)){
                fseek($fp, $start);
                $content = fread($fp, MATCH_LENGTH);
                $content.=(feof($fp))?"\n":'';
                $content_length = strrpos($content, "\n");
                $content = substr($content, 0, $content_length);
                $start += $content_length;
                $end_pos = 0;
                while (($end_pos = strpos($content, $keyword, $end_pos)) !== false){
                    $start_pos = strrpos($content, "\n", -$content_length + $end_pos);
                    $start_pos = ($start_pos === false)?0:$start_pos;
                    $end_pos = strpos($content, "\n", $end_pos);
                    $end_pos=($end_pos===false)?$content_length:$end_pos;
                    $return[]=array(
                       'f'=>$filename,
                       't'=>trim(substr($content, $start_pos, $end_pos-$start_pos))
                         );
                    $count++;
                    if ($count >= RESULT_LIMIT) break;
                  }
                unset($content,$content_length,$start_pos,$end_pos);
                if ($count >= RESULT_LIMIT) break;
                  }
        fclose($fp);
       if ($count >= RESULT_LIMIT) break;
     }
     return $return;
}
if(!empty($_POST)&&!empty($_POST['q'])){
    set_time_limit(0);				//不限定脚本执行时间
    $q=strip_tags(trim($_POST['q']));
    $results=get_results($q);
    $count=count($results);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>观风 - 大数据平台</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="keywords" content="社工库,MD5库,泄漏密码查询,密码泄露,社工数据库,社工数据,社工密码,黑客社工,大数据,查询,观风" />
<meta name="description" content="社工库,MD5库,泄漏密码查询,密码泄露,社工数据库,社工数据,社工密码,黑客社工,大数据,查询,观风" />
<link rel="stylesheet" type="text/css" href="css/default1.css" />
<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
			</div>
<style type="text/css">
    body,td,th{
    color: #FFF;
}
    a:link{
    color: #0C0;
    text-decoration: none;
}
    
    a:visited {
    text-decoration: none;
    color: #999;
}
a:hover{
    text-decoration: none;
}
a:active{
    text-decoration: none;
    color: #F00;
}
div#div1{
    position:fixed;
    top:0;
    left:0;
    bottom:0;
    right:0;
    z-index:-1;
}
div#div1 > img {
    height:100%;
    width:100%;
    border:0;
}         
</style>
<script>
<!--
function check(form){
if(form.q.value==""){
  alert("Not null！");
  form.q.focus();
  return false;
  }
}

-->
</script>
</head>
    <body>
    <div id="div1"><img src="bg.jpg" /></div>
    <div id="container"><div id="header"><a href="http://<?echo $_SERVER['SERVER_NAME'];?>" ></a></div><br /><br /><br /><br />
<form name="from" action="index.php" method="post">
    <div id="content">
    <div id="create_form">
    <br><br><br><br><br><br><br>
    <font color="#FFFFFF"><label>请输入关键字:<input class="inurl" size="26" id="unurl" name="q" value="<?php echo !empty($q)?$q:''; ?>"/></label>
    <p class="ali"><label for="alias">关键词:</label><span>用户、电子邮件、QQ帐户、论坛帐户…</span></p></font>
    <p class="but"><input onClick="check(form)" type="submit" value="查询" class="submit" /></p>
    </form>
    </div>
  <?php
       if(isset($count))
       {
         echo 'Get ' .$count .' results,&nbsp;&nbsp;cost ' . (microtime(true) - $time_start) . " seconds";
         if(!empty($results)){
         echo '<ul>';
         foreach($results as $v){
         echo '<font color="#FFFFFF"><li>From_['.$v['f'].']_Datas <br />Content: '.$v['t'].'</li><br /></font>';
           }
         echo '<br /><br /><span style="font-weight:bold;"><font color=#ffff00><li>Resources from the Internet.<br />The information here does not represent the views of this site.</li></font></span>';
         echo '</ul>';
            }
         echo '<span style="font-weight:bold;"><hr align="center" width="550" color="#2F2F2F" size="1"><font color="#FFFFFF">本站提供数据保护服务';
         echo '<br />This site provides data protection service</span>
';
         echo '<br /><span style="font-weight:bold;">如果查询到相关敏感信息请联系我们删除。（每条10R）';
         echo '<br />If the query to the relevant sensitive information, please contact us to delete. (each 10RMB)</span>
';
         echo '<br /><span style="font-weight:bold;">MSN:admin#heavensec.org</font></span>';
         echo '</ul>';
         }
         ?>
</li><li><a href="http:/<?echo $_SERVER['SERVER_NAME'];?>" target="_blank">为提高查询准确率请减少使用单字节模糊查询</a></li></ul>
</li><li><a href="http:/<?echo $_SERVER['SERVER_NAME'];?>" target="_blank">大量数据正在逐步添加中(支持中文查询)</a></li></ul>
</li><li><a href="http:/<?echo $_SERVER['SERVER_NAME'];?>" target="_blank">数据量过大，导入速度较慢</a></li></ul>
<div id="nav">
<ul><li class="current"><a href="http://www.heavensec.org" target="_blank">关于我们</a></li><li class="current"><a href="statement.php" target="_blank">免责声明</a></li><li class="current"><a href="log.php" target="_blank"><font color="red">更新日志</font></a></li></ul>
</div>
<div id="footer">
<p>Social Engineering Data &copy;2015-2016 Powered By <a href="http://www.heavensec.org/data/" target="_blank">See Wind<?echo $_SERVER['SERVER_NAME'];?><a></p><div style="display:none">
</div>
<!-- Baidu Button BEGIN -->
<script type="text/javascript" id="bdshare_js" data="type=slide&amp;img=5&amp;pos=right&amp;uid=0" ></script>
<script type="text/javascript" id="bdshell_js"></script>
<script type="text/javascript">
var bds_config={"bdTop":194};
document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?cdnversion=" + Math.ceil(new Date()/3600000);
</script>
<meta name="baidu-site-verification" content="vzYcxVwG2F" />
<!-- Baidu Button END -->
</body>
</html>