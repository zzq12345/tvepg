<?php
// 载入原始 EPG 文件
$xml = simplexml_load_file('epgshanghai.xml') or die('无法加载 XML 文件');

// 创建新的 XML 结构
$newXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><tv></tv>');

// 设置 tv 节点属性（可根据原文件修改）
$newXml->addAttribute('generator-info-name', 'EPG Filter Script');
$newXml->addAttribute('source-info-name', 'Filtered EPG');

// 目标频道 ID
$targetChannels = ['快乐垂钓', '茶频道','游戏风云','生活时尚','动漫秀场','乐游','都市剧场','法治天地','都市频道','哈哈炫动','东方影视','新闻综合','五星体育','第一财经','上海教育','东方财经','金色学堂','卡酷少儿','中国教育-4','金鹰卡通','欢笑剧场','嘉佳卡通','中国教育-2','家庭理财'];

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
$newXml->asXML('epgnewshanghai.xml');
/*
$channelsToRemove = ['浙江卫视','家家购物', '好享购物','央广购物','风云足球','央视台球','兵器科技','世界地理','女性时尚','高尔夫网球','怀旧剧场','风云剧场','第一剧场','风云音乐','央视文化精品','CCTV-1','东方购物-1','东方购物-2','CCTV-4K','CCTV-4K']; // 替換為你要刪除的頻道 ID

// 讀取 XML 文件
$xmlFile = 'epgshanghai.xml';


if (!file_exists($xmlFile)) {
    die("文件不存在：$xmlFile");
}

// 載入 XML
$xml = simplexml_load_file($xmlFile);
if (!$xml) {
    die("無法載入 XML 文件。");
}

// 使用 DOM 操作以便於刪除節點
$dom = new DOMDocument();
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->load($xmlFile);

// 刪除 <channel> 節點
$xpath = new DOMXPath($dom);
foreach ($channelsToRemove as $channelId) {
    // 刪除 <channel> 節點
    foreach ($xpath->query("//channel[@id='$channelId']") as $node) {
        $node->parentNode->removeChild($node);
    }

    // 刪除 <programme> 節點
    foreach ($xpath->query("//programme[@channel='$channelId']") as $node) {
        $node->parentNode->removeChild($node);
    }
}

// 保存為新文件
$newFile = 'epgnewshanghai.xml';
if ($dom->save($newFile)) {
    echo "處理完成，新文件已保存為：$newFile";
} else {
    echo "保存文件時出錯。";
}

*/


?>
