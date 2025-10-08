<?php
// 定义要删除的频道列表
$channelsToRemove = [
    '3D电视试验频道', '风云音乐', 'noepg', '梨园', '风云足球', '央视台球', '兵器科技', '世界地理', '女性时尚', '高尔夫网球', '怀旧剧场', '风云剧场', '第一剧场', '风云音乐', '央视文化精品', '发现之旅', '东方购物-1', '女性时尚', 'CCTV-4K', 'CCTV4K'
];

// XML 文件路径
$sourceXml = 'epg51zmt.xml';
$outputXml = 'epgnew51zmt.xml';

// 检查源文件是否存在
if (!file_exists($sourceXml)) {
    die("错误：源文件不存在 - $sourceXml");
}

// 使用 DOM 加载 XML 文件
$dom = new DOMDocument();
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;

// 启用错误处理
libxml_use_internal_errors(true);

// 加载文件并处理加载错误
if (!$dom->load($sourceXml)) {
    $errors = libxml_get_errors();
    $errorMsg = "无法加载 XML 文件：\n";
    foreach ($errors as $error) {
        $errorMsg .= sprintf("行 %d, 列 %d: %s\n", $error->line, $error->column, $error->message);
    }
    libxml_clear_errors();
    die($errorMsg);
}

// 使用 XPath 定位节点
$xpath = new DOMXPath($dom);

// 遍历删除目标频道的 channel 和 programme 节点
foreach ($channelsToRemove as $channelId) {
    // 删除 channel 节点（匹配 id 属性）
    $channels = $xpath->query("//channel[@id='$channelId']");
    foreach ($channels as $node) {
        $node->parentNode->removeChild($node);
    }
    
    // 删除 programme 节点（匹配 channel 属性）
    $programmes = $xpath->query("//programme[@channel='$channelId']");
    foreach ($programmes as $node) {
        $node->parentNode->removeChild($node);
    }
}

// 保存修改后的文件（修复了输出路径问题）
if ($dom->save($outputXml)) {
    echo "处理完成！已删除 " . count($channelsToRemove) . " 个频道\n";
    echo "新文件已保存为：" . realpath($outputXml) . "\n"; // 显示完整路径
    
    // 验证输出文件
    if (file_exists($outputXml)) {
        echo "文件大小: " . filesize($outputXml) . " 字节";
    } else {
        echo "错误：文件未成功创建！";
        exit(1); // 退出状态码1表示错误
    }
} else {
    echo "保存文件时出错。";
    exit(1);
}
?>
