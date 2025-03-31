<?php
// 定义要合并的XML文件列表
$xmlFiles = [
    './epganywhere.xml', 
    './epgmytvsuper.xml',
    './epg4gtv2.xml',
    './epgpixman.xml',
    './epgshanghai.xml',
    './epgastro.xml',
    './feiyang.xml',
    './epgunifi.xml',
    './epgkai.xml'
];

// 创建新的SimpleXMLElement对象作为根元素
$mergedXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><tv></tv>');

foreach ($xmlFiles as $file) {
    // 检查文件是否存在
    if (!file_exists($file)) {
        error_log("跳过不存在的文件: $file");
        continue;
    }

    // 加载XML文件并检查错误
    $xml = simplexml_load_file($file);
    if ($xml === false) {
        error_log("加载失败: $file");
        continue;
    }

    // 合并<channel>元素
    if (isset($xml->channel)) {
        foreach ($xml->channel as $channel) {
            $mergedChannel = $mergedXml->addChild('channel');
            $mergedChannel->addAttribute('id', (string)$channel['id']);
            
            $displayName = htmlspecialchars((string)$channel->{'display-name'}, ENT_XML1);
            $mergedChannel->addChild('display-name', $displayName)->addAttribute('lang', (string)$channel->{'display-name'}['lang']);
        }
    }

    // 合并<programme>元素
    if (isset($xml->programme)) {
        foreach ($xml->programme as $programme) {
            $mergedProgramme = $mergedXml->addChild('programme');
            $mergedProgramme->addAttribute('start', (string)$programme['start']);
            $mergedProgramme->addAttribute('stop', (string)$programme['stop']);
            $mergedProgramme->addAttribute('channel', (string)$programme['channel']);
            
            $title = htmlspecialchars((string)$programme->title, ENT_XML1);
            $mergedProgramme->addChild('title', $title)->addAttribute('lang', (string)$programme->title['lang']);
            
            $desc = htmlspecialchars((string)$programme->desc, ENT_XML1);
            $mergedProgramme->addChild('desc', $desc)->addAttribute('lang', (string)$programme->desc['lang']);
        }
    }
}

// 将合并后的XML保存到文件
$mergedXml->asXML('epgziyong.xml');
//echo "XML文件合并完成，已保存为 epgziyong.xml";
?>
