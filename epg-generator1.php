<?php

// 频道源映射配置
$sources = [
   
   // "epgshanghai.xml" => "https://epg.deny.vip/sh/tel-epg.xml",
   // "epg51zmtnew.xml" => "https://raw.githubusercontent.com/zzq12345/tvepg/refs/heads/main/epgnew51zmt.xml",
    "epgunifi.xml" => "https://raw.githubusercontent.com/AqFad2811/epg/refs/heads/main/unifitv.xml"
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
    
    if (curl_errno($ch)) {
        echo "Error processing $filename: " . curl_error($ch) . "\n";
        continue;
    }
    
    curl_close($ch);

// 修改 <channel> 标签

    // 解析并处理XML
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;

    if (!$dom->loadXML($xmlContent)) {
        echo "Invalid XML format for $filename\n";
        continue;
    }

    // 构建频道ID映射表
    $channelMapping = [];
    foreach ($dom->getElementsByTagName('channel') as $channel) {
       $channel->setAttribute('id', (string)$channel->getAttribute('epg_id'));
    $channel->removeAttribute('epg_id'); // 移除 epg_id 属性
        $oldId = $channel->getAttribute('id');
        $displayName = $channel->getElementsByTagName('display-name')->item(0)->nodeValue;
        $channelMapping[$oldId] = $displayName;
    }

    // 更新频道ID
    foreach ($dom->getElementsByTagName('channel') as $channel) {
        $oldId = $channel->getAttribute('id');
        if (isset($channelMapping[$oldId])) {
            $channel->setAttribute('id', $channelMapping[$oldId]);
        }
    }

    // 更新节目单关联
    foreach ($dom->getElementsByTagName('programme') as $programme) {
        $oldChannelId = $programme->getAttribute('channel');
        if (isset($channelMapping[$oldChannelId])) {
            $programme->setAttribute('channel', $channelMapping[$oldChannelId]);
        }
    }

    // 直接保存到当前目录
    $dom->save($filename);
    echo "Generated: $filename\n";
}

echo "All EPG files processed!\n";
?>
