<?php
// 载入原始 EPG 文件
$xml = simplexml_load_file('epgguangdong.xml') or die('无法加载 XML 文件');

// 创建新的 XML 结构
$newXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><tv></tv>');

// 设置 tv 节点属性（可根据原文件修改）
$newXml->addAttribute('generator-info-name', 'EPG Filter Script');
$newXml->addAttribute('source-info-name', 'Filtered EPG');

// 目标频道 ID
$targetChannels = ['广州综合', '广州新闻','广州法治','深圳都市','深圳体育','深圳电视剧','深圳财经','深圳少儿','深圳宝安','深圳龙岗','佛山综合','佛山公共','佛山顺德','佛山南海','东莞新闻综合','东莞生活咨询','惠州综合','惠州都市生活','珠海','江门综合','中山综合','香山文化','肇庆新闻综合','肇庆生活服务','汕头新闻综合','汕头经济生活','揭阳综合','揭阳生活','潮州综合','潮州民生','汕尾新闻综合','尾文化生活','湛江新闻综合','湛江公共','茂名综合','茂名文化生活','阳江-1','阳江-2','云浮综合','云浮文旅','清远新闻综合','韶关新闻综合','河源新闻综合','河源公共','梅州综合','梅州客家生活','睛彩竞技','睛彩篮球','睛彩青少','睛彩广场舞','广州南国都市4K'];

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
$newXml->asXML('epgnewguangdong.xml');
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
$newFile = 'epgnewsguangdong.xml';
if ($dom->save($newFile)) {
    echo "處理完成，新文件已保存為：$newFile";
} else {
    echo "保存文件時出錯。";
}

*/


?>
