<?php
header('Content-Type: text/plain; charset=UTF-8');
ini_set("max_execution_time", "30000000");
ini_set('date.timezone', 'Asia/Shanghai');

$fp = "epgkai.xml";
// 日期变量定义（优化重复定义，保留核心变量）
$dt1 = date('Ymd');
$dt2 = date('Ymd', strtotime('+1 day'));
$dt11 = date('Y-m-d');
$dt12 = date('Y-m-d', strtotime('+1 day'));
$targetDates = [$dt11, $dt12]; // 统一目标日期数组

// 工具函数（保留原功能，优化代码格式）
function compress_html($string) {
    $string = str_replace(["\r", "\n", "\t"], '', $string);
    return $string;
}

function escape($str) { 
    preg_match_all("/[\x80-\xff].|[\x01-\x7f]+/", $str, $r); 
    $ar = $r[0]; 
    foreach ($ar as $k => $v) { 
        $ar[$k] = ord($v[0]) < 128 ? rawurlencode($v) : "%u" . bin2hex(iconv("UTF-8", "UCS-2", $v)); 
    } 
    return join("", $ar); 
} 

function replace_unicode_escape_sequence($match) {       
    return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');     
}          

// XML头部结构
$chn = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!DOCTYPE tv SYSTEM \"http://api.torrent-tv.ru/xmltv.dtd\">\n<tv generator-info-name=\"秋哥綜合\" generator-info-url=\"https://www.tdm.com.mo/c_tv/?ch=Satellite\">\n";

// 频道配置
$id30 = 101951;
$cid30 = [
    ['76','HOY國際財經'],
    ['77','HOY TV'],
    ['78','HOY資訊'],
   
];
$nid30 = sizeof($cid30);

// 第一步：生成频道节点
for ($idm30 = 1; $idm30 <= $nid30; $idm30++){
    $idd30 = $id30 + $idm30;
    $chn .= "<channel id=\"".$cid30[$idm30-1][1]."\"><display-name lang=\"zh\">".$cid30[$idm30-1][1]."</display-name></channel>\n";
}

// 第二步：获取并解析每个频道的EPG数据（核心修复部分）
for ($idm30 = 1; $idm30 <= $nid30; $idm30++){
    $channelCode = $cid30[$idm30-1][0];
    $channelName = $cid30[$idm30-1][1];
    $url30 = "https://epg-file.hoy.tv/hoy/OTT{$channelCode}{$dt1}.xml";

    // 1. CURL请求（新增错误判断）
    $ch30 = curl_init();
    curl_setopt_array($ch30, [
        CURLOPT_URL => $url30,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_SSL_VERIFYHOST => FALSE,
        CURLOPT_ENCODING => 'Vary: Accept-Encoding',
        CURLOPT_CONNECTTIMEOUT => 10, // 新增连接超时，避免卡死
        CURLOPT_TIMEOUT => 20, // 新增请求超时
    ]);
    $xmlContent = curl_exec($ch30);

    // 检查CURL错误
    if (curl_errno($ch30)) {
        $curlErr = curl_error($ch30);
        error_log("频道【{$channelName}】CURL请求失败: {$curlErr}（URL: {$url30}）");
        curl_close($ch30);
        continue; // 跳过当前频道，继续下一个
    }
    curl_close($ch30);

    // 2. XML解析（新增解析错误捕获）
    $xml = simplexml_load_string($xmlContent);
    if (!$xml) {
        // 获取详细解析错误
        $xmlErrors = libxml_get_errors();
        $errorMsg = [];
        foreach ($xmlErrors as $err) {
            $errorMsg[] = "行{$err->line}：{$err->message}";
        }
        libxml_clear_errors();
        error_log("频道【{$channelName}】XML解析失败: " . implode(' | ', $errorMsg));
        continue; // 跳过当前频道，继续下一个
    }

    // 3. 遍历EPG数据（确保节点存在再遍历）
    $epgItems = $xml->Channel->EpgItem;
    if (empty($epgItems)) {
        error_log("频道【{$channelName}】未找到EpgItem节点（XML结构可能异常）");
        continue;
    }

    foreach ($epgItems as $item) {
        $startTime = (string)$item->EpgStartDateTime;
        $date = substr($startTime, 0, 10);

        // 只保留目标日期的节目
        if (in_array($date, $targetDates)) {
            $title = (string)$item->EpisodeInfo->EpisodeShortDescription;
            $endTime = (string)$item->EpgEndDateTime;

            // 格式化时间（优化替换逻辑，减少重复函数调用）
            $formatTime = function($timeStr) {
                return str_replace(['-', ':', ' '], '', $timeStr);
            };
            $startFormatted = $formatTime($startTime);
            $endFormatted = $formatTime($endTime);

            // 拼接节目节点
            $chn .= "<programme start=\"{$startFormatted} +0800\" stop=\"{$endFormatted} +0800\" channel=\"{$channelName}\">\n";
            $chn .= "<title lang=\"zh\">" . htmlspecialchars($title, ENT_XML1) . "</title>\n"; // 新增HTML转义，避免XML语法错误
            $chn .= "<desc lang=\"zh\"> </desc>\n</programme>\n";
        }
    }
}

// 闭合XML标签并写入文件
$chn .= "</tv>\n";
file_put_contents($fp, $chn);

echo "EPG文件生成完成！路径：{$fp}";
?>
