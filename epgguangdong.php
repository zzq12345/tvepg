<?php

// 需要处理的文件路径
$gzFilePath = './epgguangdong.xml.gz'; // 请将此路径替换为您的 pl.xml.gz 文件的实际路径
$xmlFilePath = 'epgguangdong.xml';  // 目标 XML 文件路径

// 打开 gzip 文件
$gzFile = gzopen($gzFilePath, 'rb');
if (!$gzFile) {
    die('无法打开 gz 文件');
}

// 创建用于保存XML内容的文件句柄
$xmlFile = fopen($xmlFilePath, 'wb');
if (!$xmlFile) {
    gzclose($gzFile);
    die('无法创建目标 XML 文件');
}

// 每次读取缓冲区大小
$bufferSize = 4096;

// 循环读取 gz 文件内容并写入到目标 XML 文件
while (!gzeof($gzFile)) {
    $data = gzread($gzFile, $bufferSize);
    fwrite($xmlFile, $data);
}

// 关闭文件句柄
gzclose($gzFile);
fclose($xmlFile);

echo "转换完成，XML 文件保存为: $xmlFilePath";
?>
