<?php
//127.0.0.1/epofiii.php
header( 'Content-Type: text/plain; charset=UTF-8');
ini_set("max_execution_time", "65000000000");
// 设置当前脚本最大执行时间（单位：秒）

//htaccess php_value max_execution_time 0;
ini_set('date.timezone','Asia/Shanghai');
$fp="epgofiii.xml";//压缩版本的扩展名后加.gz
$dt1=date('Ymd');//獲取當前日期
$dt6=date('YmdHi',time());
$dt4=date("Y/n/j");//獲取當前日期
$dt7=date('Y');//獲取當前日期
$dt5=date('Y/n/j',time()+24*3650);//第二天日期
$dt2=date('Ymd',time()+24*3650);//第二天日期
$dt21=date('Ymd',time()+48*3650);//第三天日期
$dt22=date('Ymd',time()-24*3650);//前天日期
$dt3=date('Ymd',time()+7*24*3650);
$dt11=date('Y-m-d');
$time111=strtotime(date('Y-m-d',time()))*1000;
$dt12=date('Y-m-d',time()+24*3650);
$dt10=date('Y-m-d',time()-24*3650);
$w1=date("w");//當前第幾周
if ($w1<'1') {$w1=7;}
$w2=$w1+1;
function match_string($matches)
{
    return  iconv('UCS-2', 'UTF-8', pack('H4', $matches[1]));
    //return  iconv('UCS-2BE', 'UTF-8', pack('H4', $matches[1]));
    //return  iconv('UCS-2LE', 'UTF-8', pack('H4', $matches[1]));
}


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




//適合php7以上
function replace_unicode_escape_sequence($match)
{       
		return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');     
}          

$chn="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE tv SYSTEM \"http://api.torrent-tv.ru/xmltv.dtd\">\n<tv generator-info-name=\"http://192.168.10.1/system/opt/\" generator-info-url=\"QQ \" >\n";

$uuu='https://www.ofiii.com/channel/watch/ofiii16';
$ch = curl_init($uuu);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    $uuk = curl_exec($ch);
    curl_close($ch);
preg_match('/"buildId":"(.*)",/i', $uuk, $uum);


$id650=650081;//起始节目编号
$cid650=array(

array('4gtv-4gtv066','台視'),
 array('4gtv-4gtv040','中視'),
 array('4gtv-4gtv041','華視'),
 array('litv-ftv17','好消息2台'),
 array('litv-ftv16','好消息'),
  array('litv-longturn20','ELTV生活英語台'),
 array('litv-longturn01','龍華卡通台'),
 array('4gtv-4gtv076','亞洲旅遊台'),
 array('litv-longturn19','Smart知識台'),
 array('litv-longturn18','龍華戲劇台'),
 array('litv-longturn22','台灣戲劇台'),
 array('4gtv-4gtv102','東森購物1台'),
 array('litv-longturn12','龍華偶像台'),
 array('litv-longturn11','龍華日韓台'),
array('4gtv-4gtv103','東森購物2台'),
 array('4gtv-4gtv052','華視新聞'),
 array('nnews-zh','倪珍播新聞'),
 array('iNEWS','三立新聞iNEWS'),
 array('4gtv-4gtv158','寰宇財經台'),
 array('4gtv-4gtv051','台視新聞'),
 array('4gtv-4gtv074','中視新聞'),
 array('4gtv-4gtv009','中天新聞台'),
 array('4gtv-4gtv156','寰宇新聞台灣台'),
 array('litv-longturn14','寰宇新聞台'),
 array('4gtv-4gtv104','第1商業台'),
 array('4gtv-4gtv084','國會頻道1台'),
 array('4gtv-4gtv085','國會頻道2台'),
 array('litv-longturn21','龍華經典台'),
  array('litv-longturn03','龍華電影台'),
 array('litv-longturn02','龍華洋片台'),

);

$nid650=sizeof($cid650);



for ($idm650=1; $idm650 <= $nid650; $idm650++){
 $idd650=$id650+$idm650;
   $chn.="<channel id=\"".$cid650[$idm650-1][1]."\"><display-name lang=\"zh\">".$cid650[$idm650-1][1]."</display-name></channel>\n";
}



//print  $yuu651[1];
for ($idm650=1; $idm650 <= $nid650; $idm650++){

   $url650='https://www.ofiii.com/_next/data/'.$uum[1].'/channel/watch/'.$cid650[$idm650-1][0].'.json?contentId='.$cid650[$idm650-1][0];
                    
//https://www.ofiii.com/_next/data/$uum/channel/watch/'.$cid650[$idm650-1][0].'.json?contentId='.$cid650[$idm650-1][0];
 $idd650=$id650+$idm650;
 $ch650=curl_init();
curl_setopt($ch650,CURLOPT_URL,$url650);
curl_setopt($ch650,CURLOPT_SSL_VERIFYPEER,false);
curl_setopt($ch650,CURLOPT_SSL_VERIFYHOST,false);
curl_setopt($ch650,CURLOPT_RETURNTRANSFER,1);
$headers650=[
'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36 Edg/114.0.1823.86',
//'Referer: Referer: https://www.ebs.co.kr/schedule?channelCd='.$cid650[$idm650-1][0].'&onor='.$cid650[$idm650-1][0],
//'Cookie: WHATAP=zq1p9og4vtfru; XTVID=A230717100728270943; PCID=16895596508411605420033; ONAIR_MODE=DEFAULT; SESSION=502b96e9-e81f-4235-b8db-49a23eb3d60e; ONAIR_RATING=1689661221687:257c4cda

];
curl_setopt($ch650, CURLOPT_HTTPHEADER, $headers650);
$re650=curl_exec($ch650);
$re650=str_replace('&','',$re650);
curl_close($ch650);
//print $re650 ;
$re650= preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $re650);// 適合php7
$tuu650=json_decode($re650)->pageProps->channel ->Schedule;
for ( $i650=0 ; $i650<=count($tuu650)-2 ; $i650++ ) {
$AirDateTime[$i650]=json_decode($re650)->pageProps->channel ->Schedule[$i650]->AirDateTime;//2025-04-24T07:30:00Z
$endDateTime[$i650]=json_decode($re650)->pageProps->channel ->Schedule[$i650+1]->AirDateTime;//2025-04-24T07:30:00Z
$Title650[$i650]=json_decode($re650)->pageProps->channel ->Schedule[$i650]->program->Title;
$abtr650[$i650]=json_decode($re650)->pageProps->channel ->Schedule[$i650]->program->Description;
$chn.="<programme start=\"".str_replace('-','',str_replace(':','',str_replace('T','', str_replace('Z','', $AirDateTime[$i650])))).' +0000'."\" stop=\"".str_replace('-','',str_replace(':','',str_replace('T','', str_replace('Z','', $endDateTime[$i650])))).' +0000'."\" channel=\"".$cid650[$idm650-1][1]."\">\n<title lang=\"zh\">".str_replace('<','',str_replace('>','',$Title650[$i650]))."</title>\n<desc lang=\"zh\">".str_replace('<','',str_replace('>','',$abtr650[$i650]))."</desc>\n</programme>\n";
}

}



$id655=655081;//起始节目编号
$cid655=array(
 array('ofiii13','公視金獎台'),
array('ofiii16','空中英語教室HD頻道'),
 array('ofiii22','哆啦Ａ夢台'),
 array('ofiii23','新哆啦A夢(中文版)'),
 array('ofiii24','新哆啦A夢(中文版)2台'),
 array('ofiii31','TVBS食尚玩家'),
 array('ofiii32','東森娛樂台'),
 array('ofiii36','中天亞洲精采台'),
 array('ofiii38','經典劇場'),
 array('ofiii39','短劇馬拉松'),
 array('ofiii47','Yes娛樂'),
 array('ofiii1048','Focus風采戲劇台'),
  array('ofiii50','掏掏新聞'),
array('ofiii55','國際大小事'),
 array('ofiii64','第1財經'),
 array('ofiii70','Golden 強片台'),
 array('ofiii73','周星馳台'),
 array('ofiii74','歐飛電影台'),
 array('ofiii75','歐飛動作電影台'),
 array('ofiii76','Golden影迷台'),
 array('ofiii81','全民星攻略 知識開箱'),
 array('ofiii82','Focus探索新知台'),
 array('ofiii83','鄉民大學問'),
 array('ofiii85','社會NOW什麼'),
 array('ofiii88','啦啦隊獨家專訪'),
 array('ofiii89','泰可愛旋風'),
 array('ofiii91','演唱會直擊'),
 array('ofiii92','健康問良醫'),
 array('ofiii94','ASMR行車紀錄'),
 array('ofiii95','Freeman @台灣'),
 array('ofiii96','饕客揪愛吃'),
 array('ofiii97','人氣動漫預告'),
 array('ofiii99','九九敬老頻道'),
 array('ofiii100','長安十二時辰'),
 array('ofiii101','韶華若錦'),
 array('ofiii102','白髮'),
 array('ofiii103','三國'),
 array('ofiii104','武媚娘傳奇'),
 array('ofiii105','長樂曲'),
 array('ofiii106','軍師聯盟'),
 array('ofiii107','軍師聯盟2 虎嘯龍吟'),
 array('ofiii108','國色芳華'),
 array('ofiii109','落花時節又逢君'),
 array('ofiii110','楚喬傳'),
 array('ofiii111','倚天屠龍記 2019'),
 array('ofiii112','鳳囚凰'),
 array('ofiii113','步步驚心'),
 array('ofiii114','九重紫'),
 array('ofiii115','私藏浪漫'),
 array('ofiii116','去有風的地方'),
 array('ofiii117','佔有姜西'),
 array('ofiii118','向風而行'),
 array('ofiii119','以家人之名'),
 array('ofiii120','有你的時光裡'),
 array('ofiii121','我的差評女友'),
 array('ofiii122','請叫我總監'),
 array('ofiii123','三十而已'),
 array('ofiii124','月嫂先生'),
 array('ofiii125','綜藝大集合'),
 array('ofiii126','豬哥壹級棒'),
 array('ofiii127','鬼話連篇'),
 array('ofiii128','醫師好辣'),
 array('ofiii129','WTO姐妹會'),
 array('ofiii131','全民星攻略'),
 array('ofiii132','健康2.0'),
 array('ofiii133','台灣啟示錄'),
 array('ofiii134','法眼黑與白'),
 array('ofiii135','天才衝衝衝'),
 array('ofiii136','一步一腳印 發現新台灣'),
 array('ofiii137','女人我最大'),
 array('ofiii139','威廉沈歡樂送'),
 array('ofiii140','木曜4超玩 一日系列'),
 array('ofiii141','月曜1起玩'),
 array('ofiii142','一字千金'),
 array('ofiii143','11點熱吵店'),
 array('ofiii144','麥卡貝網路電視'),
 array('ofiii145','回到20歲'),
 array('ofiii146','白雪公主非死不可'),
 array('ofiii147','好搭檔'),
 array('ofiii148','月升之江'),
 array('ofiii150','台灣靈異事件'),
 array('ofiii151','包青天 1993版'),
 array('ofiii152','我的婆婆怎麼那麼可愛'),
 array('ofiii153','村裡來了個暴走女外科'),
 array('ofiii154','一把青'),
 array('ofiii155','茶金'),
 array('ofiii156','苦力'),
 array('ofiii157','俗女養成記'),
 array('ofiii158','麻醉風暴'),
 array('ofiii159','雖然等級只有1級但固有技能是最強的'),
 array('ofiii160','擁有超常技能的異世界流浪美食家'),
 array('ofiii161','離開A級隊伍的我，和從前的弟子往迷宮深處邁進'),
 array('ofiii162','在異世界獲得超強能力的我，在現實世界照樣無敵～等級提升改變人生命運～'),
 array('ofiii163','第一神拳'),
 array('ofiii164','刀劍神域(中文版)'),
 array('ofiii165','蠟筆小新(中文版)'),
 array('ofiii166','我們這一家'),
 array('ofiii167','獵人(中文版)'),
 array('ofiii168','中華一番(中文版)'),
 array('ofiii169','隊長小翼(中文版)'),
 array('ofiii170','SPY X FAMILY 間諜家家酒'),
 array('ofiii171','SPY X FAMILY 間諜家家酒(中文版)'),
 array('ofiii172','無職轉生，到了異世界就拿出真本事'),
 array('ofiii173','關於我轉生變成史萊姆這檔事(國)'),
 array('ofiii174','夏目友人帳'),
 array('ofiii175','怪醫黑傑克TV(中文版)'),
 array('ofiii177','鋼之鍊金術師'),
 array('ofiii178','JOJO 的奇妙冒險'),
 array('ofiii179','葬送的芙莉蓮'),
 array('ofiii180','史上最強弟子兼一(中文版)'),
 array('ofiii182','忍者亂太郎(中文版)'),
 array('ofiii183','膽大黨'),
 array('ofiii184','新忍者哈特利'),
 array('ofiii185','凡爾賽玫瑰'),
 array('ofiii186','城市獵人'),
 array('ofiii187','超人力霸王台'),
 array('ofiii192','生命的贏家'),
 array('daystar','DayStar'),
 array('ofiii195','海派甜心'),
 array('ofiii196','不良笑花'),
 array('ofiii198','王子看見二公主'),
 array('ofiii200','劣人傳之詭計'),
 array('ofiii201','大新聞大爆卦'),
 array('ofiii202','新聞大白話'),
 array('ofiii203','文茜的世界周報'),
 array('ofiii204','寰宇全視界'),
 array('ofiii205','文茜的世界財經周報'),
 array('ofiii206','環球大戰線'),
 array('ofiii207','少康戰情室'),
 array('ofiii208','國民大會'),
 array('ofiii209','台灣最前線'),
 array('ofiii210','台灣向前行'),
 array('ofiii211','民視異言堂'),
 array('ofiii212','新聞觀測站'),
 array('ofiii215','電動車時代'),
 array('ofiii216','SiCAR愛車酷'),
 array('ofiii217','狂人日誌'),
 array('ofiii218','脖子解說 Mr. Neck'),
 array('ofiii225','食尚玩家-Hello腹餓代'),
 array('ofiii226','食尚玩家-天菜就醬吃'),
 array('ofiii227','食尚玩家-2天1夜go'),
 array('ofiii228','食尚玩家-熱血48小時'),
 array('ofiii234','非凡大探索'),
 array('ofiii235','台灣1001個故事'),
 array('ofiii236','詹姆士出走料理'),
 array('ofiii237','進擊的台灣'),
 array('ofiii238','世界第一等'),
 array('ofiii239','台灣第一等'),
 array('ofiii240','溢遊未盡'),
 array('ofiii241','台灣真善美'),
 array('ofiii242','請問今晚住誰家'),
 array('ofiii243','早餐中國'),
 array('ofiii244','溢起趣打卡'),
 array('ofiii245','台灣壹百種味道'),
 array('ofiii246','中國美食大探索'),
 array('ofiii247','中國旅遊大探索'),
 array('ofiii248','秘境不思溢'),
 array('ofiii250','紓壓雷雨聲'),
 array('ofiii251','療癒下雨聲'),
 array('ofiii252','放鬆的爵士午後'),
 array('ofiii254','夏日陽光海浪聲'),
 array('ofiii255','大自然流水聲'),

);

$nid655=sizeof($cid655);
for ($idm655=1; $idm655 <= $nid655; $idm655++){
 $idd655=$id655+$idm655;
   $chn.="<channel id=\"".$cid655[$idm655-1][1]."\"><display-name lang=\"zh\">".$cid655[$idm655-1][1]."</display-name></channel>\n";
}




    //$jsonUrl = "https://www.ofiii.com/_next/data/_10zLM4oyU8wcrcrw1Uic/channel/watch/{$id}.json?contentId={$id}";
      // $jsonUrl = "https://www.ofiii.com/_next/data/".$uum[1]."/channel/watch/".$id.".json?contentId={$id}";


for ($idm655=1; $idm655 <= $nid655; $idm655++){


$url655= "https://www.ofiii.com/_next/data/".$uum[1]."/channel/watch/".$cid655[$idm655-1][0].".json?contentId=".$cid655[$idm655-1][0];
 $idd655=$id655+$idm655;
//print $url655;
 $ch655=curl_init();
curl_setopt($ch655,CURLOPT_URL,$url655);
curl_setopt($ch655,CURLOPT_SSL_VERIFYPEER,false);
curl_setopt($ch655,CURLOPT_SSL_VERIFYHOST,false);
curl_setopt($ch655,CURLOPT_RETURNTRANSFER,1);
$headers655=[
'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36 Edg/114.0.1823.86',
//'Referer: Referer: https://www.ebs.co.kr/schedule?channelCd='.$cid655[$idm655-1][0].'&onor='.$cid655[$idm655-1][0],
//'Cookie: WHATAP=zq1p9og4vtfru; XTVID=A230717100728270943; PCID=16895596558411605420033; ONAIR_MODE=DEFAULT; SESSION=502b96e9-e81f-4235-b8db-49a23eb3d60e; ONAIR_RATING=1689661221687:257c4cda
];
curl_setopt($ch655, CURLOPT_HTTPHEADER, $headers655);
$re655=curl_exec($ch655);
$re655=str_replace('&','',$re655);
//$re655=str_replace('>','',$re655);
//$re655=str_replace('<','',$re655);
//$re655=$re600 = preg_replace('/\s(?=)/', '',$re655);
$re655= preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $re655);// 適合php7
curl_close($ch655);
//print $re655 ;


$tuu655=json_decode($re655)->pageProps->channel ->vod_channel_schedule->programs;
//print count($tuu655);


for ( $i655=0 ; $i655<=count($tuu655)-2 ; $i655++ ) {


$AirDateTime[$i655]=json_decode($re655)->pageProps->channel ->vod_channel_schedule->programs[$i655]->p_start;//2025-04-24T07:30:00Z
$endDateTime[$i655]=json_decode($re655)->pageProps->channel ->vod_channel_schedule->programs[$i655+1]->p_start;//2025-04-24T07:30:00Z
$Title655[$i655]=json_decode($re655)->pageProps->channel ->vod_channel_schedule->programs[$i655]->title;
$subtitle655[$i655]=json_decode($re655)->pageProps->channel ->vod_channel_schedule->programs[$i655]->subtitle;
$abtr655[$i655]=json_decode($re655)->pageProps->channel ->vod_channel_schedule->programs[$i655]->vod_channel_description;
//$chn.="<programme start=\"".str_replace('-','',str_replace(':','',str_replace('T','', str_replace('Z','', convertMillisToDateTime($AirDateTime[$i655]))))).' +0000'."\" stop=\"".str_replace('-','',str_replace(':','',str_replace('T','', str_replace('Z','', convertMillisToDateTime($endDateTime[$i655]))))).' +0000'."\" channel=\"".$cid655[$idm655-1][1]."\">\n<title lang=\"zh\">".str_replace('<','',str_replace('>','',$Title655[$i655]))."</title>\n<desc lang=\"zh\">".str_replace('<','',str_replace('>','',$abtr655[$i655]))."</desc>\n</programme>\n";

$chn.="<programme start=\"".str_replace('-','',str_replace(':','',str_replace('T','', str_replace('Z','', date("YmdHis",$AirDateTime[$i655]/1000))))).' +0000'."\" stop=\"".str_replace('-','',str_replace(':','',str_replace('T','', str_replace('Z','', date("YmdHis",$endDateTime[$i655]/1000))))).' +0000'."\" channel=\"".$cid655[$idm655-1][1]."\">\n<title lang=\"zh\">".str_replace('<','',str_replace('>','',$Title655[$i655]))."</title>\n<desc lang=\"zh\">".str_replace('<','',str_replace('>','',$abtr655[$i655]))."</desc>\n</programme>\n";
}

}

$chn.="</tv>\n";
//写入文件。这里一次性写入，可以自己分次写入操作
file_put_contents($fp, $chn);
//exec("mv -f epgmewatch.xml /www");
//创建压缩版本
/*
$fn = gzopen ($fp.'.gz', 'w9');
gzwrite($fn, file_get_contents($fp));
gzclose($fn);
*/
?>
