<?php
// 载入原始 EPG 文件
$xml = simplexml_load_file('epgshanghai.xml') or die('无法加载 XML 文件');

// 创建新的 XML 结构
$newXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><tv></tv>');

// 设置 tv 节点属性
$newXml->addAttribute('generator-info-name', 'EPG Filter Script');
$newXml->addAttribute('source-info-name', 'Filtered EPG');

// 目标频道 ID 列表
$targetChannels = [
    '快乐垂钓', '茶频道', '游戏风云', '生活时尚', '动漫秀场', '乐游', '都市剧场',
    '法治天地', '都市频道', '哈哈炫动', '东方影视', '新闻综合', '五星体育', '第一财经',
    '上海教育', '东方财经', '金色学堂', '卡酷少儿', '中国教育-4', '金鹰卡通', '欢笑剧场',
    '嘉佳卡通', '中国教育-2', '家庭理财'
];

// 1. 复制目标频道信息
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
       
        $newProgramme->addAttribute('start', (string)$programme['start']);
        $newProgramme->addAttribute('stop', (string)$programme['stop']);
        $newProgramme->addAttribute('channel', $id);
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

// ✅ 使用 DOMDocument 格式化输出（自动换行和缩进）
$dom = new DOMDocument('1.0', 'UTF-8');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($newXml->asXML());

// 保存到文件
$dom->save('epgnewshanghai.xml');

echo "✅ 已生成格式化的 EPG 文件：epgnewshanghai.xml\n";
?>
