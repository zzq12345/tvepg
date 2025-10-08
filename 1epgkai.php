<?php
header( 'Content-Type: text/plain; charset=UTF-8');
ini_set("max_execution_time", "30000000");
ini_set('date.timezone','Asia/Shanghai');
$fp="epgkai.xml";//压缩版本的扩展名后加.gz
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

$chn="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE tv SYSTEM \"http://api.torrent-tv.ru/xmltv.dtd\">\n<tv generator-info-name=\"秋哥綜合\" generator-info-url=\"https://www.tdm.com.mo/c_tv/?ch=Satellite\">\n";


$id30=101951;
$cid30=array(
array('76','HOY國際財經'),
array('77','HOY TV'),
array('78','HOY資訊'),
array('91','HOY生活台'),
array('92','HOY劇集台'),
array('93','HOY怪談台'),
array('96','Sportplus'),
array('97','Travel food'),
array('98','Sport on news'),
    );
$nid30=sizeof($cid30);
for ($idm30 = 1; $idm30 <= $nid30; $idm30++){
 $idd30=$id30+$idm30;
   $chn.="<channel id=\"".$cid30[$idm30-1][1]."\"><display-name lang=\"zh\">".$cid30[$idm30-1][1]."</display-name></channel>\n";
}
for ($idm30 = 1; $idm30 <= $nid30; $idm30++){
$url30="https://epg-file.hoy.tv/hoy/OTT".$cid30[$idm30-1][0].$dt1.".xml";
 $idd30=$id30+$idm30;
    $ch30 = curl_init();
    curl_setopt($ch30, CURLOPT_URL, $url30);
    curl_setopt($ch30, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch30, CURLOPT_SSL_VERIFYPEER, FALSE);

	curl_setopt($ch30, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch30,CURLOPT_ENCODING,'Vary: Accept-Encoding');
   $xmlContent = curl_exec($ch30);
    curl_close($ch30);
// 加載XML內容
$xml = simplexml_load_string($xmlContent);



// 目標日期
//$targetDates = ['2025-03-26', '2025-03-27'];
$targetDates = [$dt11, $dt12];
foreach ($xml->Channel->EpgItem as $item) {
    $startTime = (string) $item->EpgStartDateTime;
    
    // 取得節目開始的日期部分
    $date = substr($startTime, 0, 10);

    if (in_array($date, $targetDates)) {
        $title = (string) $item->EpisodeInfo->EpisodeShortDescription;
        $endTime = (string) $item->EpgEndDateTime;
        //  $chn.="<programme start=\"".str_replace(':','',$startTime).'00 +0800'."\" stop=\"".str_replace(':','',$endTime).'00 +0800'."\" channel=\"".$cid30[$idm30-1][1]."\">\n<title lang=\"zh\">".preg_replace('/\s(?=)/','',str_replace('</h4>','',$title))."</title>\n<desc lang=\"zh\"> </desc>\n</programme>\n"; 

$chn.="<programme start=\"".str_replace(' ','',str_replace('-','',str_replace(':','',$startTime)))." +0800\" stop=\"".str_replace(' ','',str_replace('-','',str_replace(':','',$endTime))) ." +0800\"  channel=\"".$cid30[$idm30-1][1]."\">\n<title lang=\"zh\">". $title."</title>\n<desc lang=\"zh\"> </desc>\n</programme>\n";


  
    }
}
}
$chn.="</tv>\n";
//写入文件。这里一次性写入，可以自己分次写入操作
file_put_contents($fp, $chn);

?>
