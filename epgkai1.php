<?php
header( 'Content-Type: text/plain; charset=UTF-8');
ini_set("max_execution_time", "30000000");
ini_set('date.timezone','Asia/Shanghai');
$fp="epgkai1.xml";//压缩版本的扩展名后加.gz
$dt1=date('Ymd');//獲取當前日期
$dt2=date('Ymd',time()+24*3600);//第二天日期
$dt21=date('Ymd',time()+48*3600);//第三天日期
$dt22=date('Ymd',time()-24*3600);//前天日期
$dt3=date('Ymd',time()+7*24*3600);
$dt11=date('Y-m-d');
$dt12=date('Y-m-d',time()+24*3600);//第二天日期
$dt13=date('Y-m-d',time()+24*3600);//第二天日期
$w1=date("w");//當前第幾周
if ($w1<'1') {$w1=7;}
$w2=$w1+1;
function compress_html($string) {
    $string = str_replace("\r", '', $string); //清除换行符
    $string = str_replace("\n", '', $string); //清除换行符
    $string = str_replace("\t", '', $string); //清除制表符
    return $string;
}

function escape($str) 
{ 
preg_match_all("/[\x80-\xff].|[\x01-\x7f]+/",$str,$r); 
$ar = $r[0]; 
foreach($ar as $k=>$v) 
{ 
if(ord($v[0]) < 128) 
$ar[$k] = rawurlencode($v); 
else 
$ar[$k] = "%u".bin2hex(iconv("UTF-8","UCS-2",$v)); 
} 
return join("",$ar); 
} 
function replace_unicode_escape_sequence($match)
{       
		return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');     
}          

$chn="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE tv SYSTEM \"http://api.torrent-tv.ru/xmltv.dtd\">\n<tv generator-info-name=\"弄著玩的，歡迎使用\" generator-info-url=\"https://www.tdm.com.mo/c_tv/?ch=Satellite\">\n";

$url='https://api2.hoy.tv/api/v3/a/channel';
  $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch,CURLOPT_ENCODING,'Vary: Accept-Encoding');
   $re = curl_exec($ch);
 $re = str_replace('&', '&amp;', $re);	
    curl_close($ch);
 $nid= count(json_decode($re)->data);
for ($idm = 0; $idm <= $nid-1; $idm++){
//for ($idm = 3; $idm <=3; $idm++){
    $name[$idm] =json_decode($re)->data[$idm]->name->zh_hk;
     $chn.="<channel id=\"".$name[$idm] ."\"><display-name lang=\"zh\">".$name[$idm] ."</display-name></channel>\n";
}
for ($idm = 0; $idm <= $nid-1; $idm++){
//$epg30[$idm] =json_decode($re)->data[$idm]->epg;
 $ch30 = curl_init();
    curl_setopt($ch30, CURLOPT_URL, json_decode($re)->data[$idm]->epg );
    curl_setopt($ch30, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch30, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch30, CURLOPT_SSL_VERIFYHOST, FALSE);
 //  curl_setopt($ch30,CURLOPT_ENCODING,'Vary: Accept-Encoding');
   $re30 = curl_exec($ch30);
    curl_close($ch30);

//print $re30;
preg_match_all('|<EpgStartDateTime>(.*?)</EpgStartDateTime>|i',$re30,$um30,PREG_SET_ORDER);//播放开始时间
preg_match_all('|<EpgEndDateTime>(.*?)</EpgEndDateTime>|i',$re30,$un30,PREG_SET_ORDER);//播放结束时间吗
preg_match_all('|<EpisodeShortDescription>(.*?)</EpisodeShortDescription>|i',$re30,$uk30,PREG_SET_ORDER);//播放内容
//print_r($um30);


$trm30=count($um30);
  for ($k30 = 0; $k30 <=$trm30-1 ; $k30++) {  
   $chn.="<programme start=\"".str_replace(' ','',str_replace(':','',str_replace('-','',$um30[$k30][1]))).' +0800'."\" stop=\"".str_replace(' ','',str_replace(':','',str_replace('-','',$un30[$k30][1]))).' +0800'."\" channel=\"".$name[$idm]."\">\n<title lang=\"zh\">".$uk30[$k30][1]."</title>\n<desc lang=\"zh\"> </desc>\n</programme>\n";
                                                                                                               }                 


}


$chn .= "</tv>\n";
file_put_contents($fp, $chn);

echo "EPG文件生成完成！路径：{$fp}";
?>
