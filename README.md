一.文件架構


1.file.json 用來複製一些epg文件！

2.kai.php kai1.php用來生成HOY電視台EPG.

3.egziyong.php用來生成綜合性epg.

4.epggen2.php用來替換channel id

5.epgnewshanghai.php用來提取上海地方台

6.epgnewhebei.php提取河北地方台

7.twepg.xml  繁體版本epg,適合港澳台
  swepg.xml  簡體版本epg,适合内地！


二. yml啟動先後順序
  
1.epg copy

2.epgkai

3.epgkai1

4.convert guangdong

5.epgnewshanghai

6.epgnewguangdong

7.epgnewhebei

8.epgtihuan

9.epgziyong

10.Convert to Simplified Chinese

11.Convert to Traditional Chinese
