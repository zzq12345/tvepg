<?php
// 载入原始 EPG 文件
$xml = simplexml_load_file('epgyidong.xml') or die('无法加载 XML 文件');
// 创建新的 XML 结构
$newXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><tv></tv>');
// 目标频道 ID
$targetChannels = ['中国天气', 'CETV2','CETV4','福建新闻','福建乡村振兴','福建新闻','福建电视剧','福建旅游','福建经济','福建文体','福建少儿','海峡卫视','福建教育','纪实科教','先锋乒羽','快乐垂钓','茶频道','卡酷少儿','金鹰卡通','保定新闻综合','保定公共频道','保定生活健康','衡水新闻综合','衡水经济科教','邢台综合','邢台城市生活','邯郸新闻综合','邯郸公共','邯郸科技教育'];
// 设置 tv 节点属性（可根据原文件修改）
$newXml->addAttribute('generator-info-name', 'EPG Filter Script');
$newXml->addAttribute('source-info-name', 'Filtered EPG');

// 1. 先复制目标频道信息
foreach ($xml->channel as $channel) {
    $id = (string)$channel['id'];
    if (in_array($id, $targetChannels)) {
        $newChannel = $newXml->addChild('channel');
        $newChannel->addAttribute('id', $id);

        // 添加 display-name
        foreach ($channel->{'display-name'} as $name) {
            $displayName = $newChannel->addChild('display-name', (string)$name);
            foreach ($name->attributes() as $attrKey => $attrValue) {
                $displayName->addAttribute($attrKey, $attrValue);
            }
        }
    }
}

// 2. 复制目标频道的节目单
foreach ($xml->programme as $programme) {
    $id = (string)$programme['channel'];
    if (in_array($id, $targetChannels)) {
        $newProgramme = $newXml->addChild('programme');
        $newProgramme->addAttribute('channel', $id);
        $newProgramme->addAttribute('start', (string)$programme['start']);
        $newProgramme->addAttribute('stop', (string)$programme['stop']);

        // 添加 title
        foreach ($programme->title as $title) {
            $titleNode = $newProgramme->addChild('title', (string)$title);
            foreach ($title->attributes() as $attrKey => $attrValue) {
                $titleNode->addAttribute($attrKey, $attrValue);
            }
        }

        // 添加 desc
        foreach ($programme->desc as $desc) {
            $descNode = $newProgramme->addChild('desc', (string)$desc);
            foreach ($desc->attributes() as $attrKey => $attrValue) {
                $descNode->addAttribute($attrKey, $attrValue);
            }
        }
    }
}

// 保存新文件
$newXml->asXML('epgnewyidong.xml');
?>
