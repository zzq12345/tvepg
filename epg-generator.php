<?php

// 待處理的EPG來源
$sources = [
    "epgshanghai.xml" => [
        "url" => "https://epg.deny.vip/sh/tel-epg.xml",
        "id_suffix" => "shanghai"  // 用於拼接頻道ID的後綴
    ],

"heiptv.xml" => [
        "url" => "https://epg.mb6.top/heiptv.xml",
 "id_suffix" => "hebeiiptv"  // 用於拼接頻道ID的後綴
    ],
    
    
    "epg51zmt.xml" => [
        "url" => "https://epg.51zmt.top:8001/difang.xml",
        "id_suffix" => "51zmt"     // 用於拼接頻道ID的後綴
    ],
    "epgunifi.xml" => [         // 沒有後綴的情況
        "url" => "https://raw.githubusercontent.com/AqFad2811/epg/refs/heads/main/unifitv.xml",
        // 沒有 id_suffix 欄位
    ]
];

$maxWorkers = 4; // 最大並行處理進程數

/**
 * 取得頻道顯示名稱（優先中文）
 */
function get_display_name($channel) {
    // 尋找中文名稱
    $xpath = new DOMXPath($channel->ownerDocument);
    $zhName = $xpath->query(".//display-name[@lang='zh']", $channel);
    if ($zhName->length > 0 && trim($zhName->item(0)->nodeValue) !== '') {
        return trim($zhName->item(0)->nodeValue);
    }
    
    // 尋找任意語言名稱
    $allNames = $xpath->query(".//display-name", $channel);
    foreach ($allNames as $node) {
        if (trim($node->nodeValue) !== '') {
            return trim($node->nodeValue);
        }
    }
    
    // 嘗試從頻道ID取得
    if ($channel->hasAttribute('id') && $channel->getAttribute('id') !== '') {
        return $channel->getAttribute('id');
    }
    
    return "未知頻道";
}

/**
 * 處理XML內容
 */
function process_xml($xmlContent, $idSuffix) {
    if (empty($xmlContent)) return null;
    
    try {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xmlContent, LIBXML_NOERROR | LIBXML_NOWARNING);
        
        $channelMap = [];
        $xpath = new DOMXPath($dom);
        
        // 處理所有頻道節點
        $channels = $xpath->query('//channel');
        foreach ($channels as $channel) {
            $oldId = $channel->getAttribute('id');
            $displayName = get_display_name($channel);
            
            // 檢查是否需要新增後綴
            if (!empty($idSuffix)) {
                // 使用id_suffix拼接新的頻道ID
                $newId = ($oldId !== $displayName) 
                    ? "{$displayName}_{$idSuffix}"
                    : "{$oldId}_{$idSuffix}";
            } else {
                // 不新增後綴
                $newId = ($oldId !== $displayName) 
                    ? $displayName
                    : $oldId;
            }
            
            $channel->setAttribute('id', $newId);
            $channelMap[$oldId] = $newId;
        }
        
        // 處理所有節目節點
        $programmes = $xpath->query('//programme');
        foreach ($programmes as $programme) {
            $oldChannel = $programme->getAttribute('channel');
            if (isset($channelMap[$oldChannel])) {
                $programme->setAttribute('channel', $channelMap[$oldChannel]);
            }
        }
        
        return $dom->saveXML();
    } catch (Exception $e) {
        error_log("處理XML時發生錯誤: " . $e->getMessage());
        return null;
    }
}

/**
 * 處理單一來源
 */
function process_source($filename, $source) {
    $url = $source['url'];
    
    // 取得id_suffix，如果不存在則為空字串
    $idSuffix = isset($source['id_suffix']) ? $source['id_suffix'] : '';
    
    echo "開始處理來源: $filename" . 
         ($idSuffix ? " (ID後綴: $idSuffix)" : " (無ID後綴)") . "\n";
    
    // 下載XML內容
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => 'gzip'
    ]);
    
    $xmlContent = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200 || empty($xmlContent)) {
        error_log("下載失敗: $filename (HTTP $httpCode)");
        return false;
    }
    
    // 處理XML - 使用id_suffix作為後綴（可能為空）
    $processedXml = process_xml($xmlContent, $idSuffix);
    if (!$processedXml) {
        error_log("處理失敗: $filename");
        return false;
    }
    
    // 儲存檔案 - 直接使用來源的鍵作為檔名
    $bytesWritten = file_put_contents($filename, $processedXml);
    if ($bytesWritten === false) {
        error_log("儲存失敗: $filename");
        return false;
    }
    
    echo "成功儲存: $filename (大小: " . number_format($bytesWritten) . " 位元組)\n";
    return true;
}

/**
 * 主處理函式（使用多進程）
 */
function main() {
    global $sources, $maxWorkers;
    
    $children = [];
    $results = [];
    
    foreach ($sources as $filename => $source) {
        $pid = pcntl_fork();
        
        if ($pid == -1) {
            die("無法建立子進程");
        } elseif ($pid) {
            // 父進程記錄PID
            $children[$pid] = $filename;
        } else {
            // 子進程處理
            $result = process_source($filename, $source);
            exit($result ? 0 : 1);
        }
        
        // 控制並行進程數
        while (count($children) > $maxWorkers) {
            $pid = pcntl_wait($status);
            if ($pid > 0) {
                $filename = $children[$pid];
                $results[$filename] = pcntl_wexitstatus($status) === 0;
                unset($children[$pid]);
            }
        }
    }
    
    // 等待剩餘子進程
    while (count($children) > 0) {
        $pid = pcntl_wait($status);
        if ($pid > 0) {
            $filename = $children[$pid];
            $results[$filename] = pcntl_wexitstatus($status) === 0;
            unset($children[$pid]);
        }
    }
    
    // 輸出結果
    $successCount = count(array_filter($results));
    $total = count($sources);
    echo "\n處理完成! 成功: $successCount/$total\n";
    
    // 列出所有產生的檔案
    echo "產生的檔案:\n";
    foreach (array_keys($sources) as $filename) {
        $status = isset($results[$filename]) && $results[$filename] ? "✓" : "✗";
        $size = file_exists($filename) ? number_format(filesize($filename)) . " 位元組" : "不存在";
        echo " - {$status} {$filename} ({$size})\n";
    }
    
    return $successCount === $total;
}

// 執行主程式
if (PHP_SAPI === 'cli') {
    if (!function_exists('pcntl_fork')) {
        die("需要啟用pcntl擴充功能\n");
    }
    
    // 確保當前目錄可寫入
    if (!is_writable('.')) {
        die("錯誤: 當前目錄不可寫入，請檢查權限\n");
    }
    
    main();
} else {
    die("請在CLI模式下執行此腳本\n");
}
