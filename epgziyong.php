<?php

// 定义要合并的XML文件列表
$xmlFiles = ['./epgshanghai.xml','./epg4gtv2.xml','http://zzqwe.giize.com:12228/epgmytvsuper.xml','./epganywhere.xml','./epgpixman.xml','./epgastro.xml','./feiyang.xml','./epgunifi.xml']; // 替换为实际的文件路径

// 创建一个新的SimpleXMLElement对象作为根元素
$mergedXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><tv></tv>');

foreach ($xmlFiles as $file) {
    // 加载每个XML文件
    $xml = simplexml_load_file($file);



    // 合并<channel>元素
    foreach ($xml->channel as $channel) {
        $mergedChannel = $mergedXml->addChild('channel');
        $mergedChannel->addAttribute('id', (string)$channel['id']);
        $mergedChannel->addChild('display-name', (string)$channel->{'display-name'})->addAttribute('lang', (string)$channel->{'display-name'}['lang']);
    }

    // 合并<programme>元素
    foreach ($xml->programme as $programme) {
        $mergedProgramme = $mergedXml->addChild('programme');
        $mergedProgramme->addAttribute('start', (string)$programme['start']);
        $mergedProgramme->addAttribute('stop', (string)$programme['stop']);
        $mergedProgramme->addAttribute('channel', (string)$programme['channel']);
        $mergedProgramme->addChild('title', (string)$programme->title)->addAttribute('lang', (string)$programme->title['lang']);
        $mergedProgramme->addChild('desc', (string)$programme->desc)->addAttribute('lang', (string)$programme->desc['lang']);
    }
}

// 将合并后的XML保存到文件
$mergedXml->asXML('epgziyong.xml');

//echo "XML文件合并完成，已保存为 merged_output.xml";
?>
