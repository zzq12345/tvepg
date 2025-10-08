<?php
// 频道源映射配置
$sources = [
   "epgshanghai.xml" => "https://epg.deny.vip/sh/tel-epg.xml",
   "epghebeiiptv1.xml" => "https://raw.githubusercontent.com/zzq12345/tvepg/refs/heads/main/epghebeiiptv.xml",
   "epgyidong.xml" => "https://epg.136605.xyz/3days.xml"
 
  
];

foreach ($sources as $filename => $targetUrl) {
    echo "Processing: $filename\n";
    
    // 获取XML数据
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $targetUrl,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_TIMEOUT => 15
    ]);

    $xmlContent = curl_exec($ch);
    curl_close($ch);

    if ($xmlContent === false) {
        echo "Error downloading $filename\n";
        continue;
    }

    // 解析 XML
    $xml = simplexml_load_string($xmlContent);
    if (!$xml) {
        echo "Error parsing XML for $filename\n";
        continue;
    }

    // 遍历所有 channel，把 id 换成 name
    $idMap = [];
    foreach ($xml->channel as $channel) {
        $name = (string)$channel->{'display-name'};
        $idMap[(string)$channel['id']] = $name; // 保存映射关系
        $channel['id'] = $name;
    }

    // 遍历所有 programme，把 channel 属性替换掉
    foreach ($xml->programme as $programme) {
        $oldId = (string)$programme['channel'];
        if (isset($idMap[$oldId])) {
            $programme['channel'] = $idMap[$oldId];
        }
    }

    // 保存到文件
    $xml->asXML($filename);
    echo "Generated: $filename\n";
}
