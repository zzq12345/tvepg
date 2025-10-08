#!/usr/bin/python3

import aiohttp
import asyncio
from lxml import etree
from concurrent.futures import ThreadPoolExecutor

# 待處理的EPG來源
sources = {
    "epgshanghai.xml": {
        "url": "https://epg.deny.vip/sh/tel-epg.xml",
        "source_id": "shanghai"  # 用於拼接頻道ID的原始標識
    },
    "epg51zmt.xml": {
        "url": "https://epg.51zmt.top:8001/difang.xml",
        "source_id": "51zmt"     # 用於拼接頻道ID的原始標識
    },
    "epgunifi.xml": {
        "url": "https://raw.githubusercontent.com/AqFad2811/epg/refs/heads/main/unifitv.xml",
        "source_id": ""     # 不存在或為空時，不拼接
    }
}

max_workers = 4    # 執行緒池大小

async def fetch_xml(session, url):
    """非同步取得XML內容"""
    try:
        async with session.get(url) as response:
            response.raise_for_status()
            return await response.text()
    except Exception as e:
        print(f"下載XML時發生錯誤: {str(e)}")
        return None

def get_display_name(channel):
    """
    智慧取得頻道顯示名稱，優先返回中文名稱
    處理邏輯：
    1. 優先尋找 lang="zh" 的 display-name
    2. 若無，則尋找任意 display-name（無論是否有 lang 屬性）
    3. 若都沒有，則返回 None
    """
    # 嘗試取得中文名稱
    zh_name_elem = channel.find(".//display-name[@lang='zh']")
    if zh_name_elem is not None and zh_name_elem.text:
        return zh_name_elem.text.strip()
    
    # 嘗試取得任意語言的名稱
    name_elems = channel.findall(".//display-name")
    for elem in name_elems:
        if elem.text and elem.text.strip():
            return elem.text.strip()
    
    # 嘗試從頻道ID取得
    channel_id = channel.get('id')
    if channel_id:
        return channel_id
    
    # 最終手段：返回預留位置名稱
    return "未知頻道"

def process_xml(xml_content, source_id):
    """使用lxml處理XML內容並格式化為標準XML"""
    if not xml_content:
        return None
    
    try:
        # 建立解析器，保留註解和CDATA
        parser = etree.XMLParser(
            remove_blank_text=True,  # 移除空白文字以便重新格式化
            remove_comments=False,   # 保留註解
            strip_cdata=False,       # 保留CDATA
            recover=True             # 嘗試復原錯誤
        )
        
        # 解析XML
        root = etree.fromstring(xml_content.encode('utf-8'), parser=parser)
        
        # 建立頻道ID映射表 (舊ID -> 新ID)
        channel_map = {}
        
        # 處理所有頻道節點
        for channel in root.xpath('.//channel'):
            channel_id = channel.get('id')
            
            # 智慧取得頻道名稱
            display_name = get_display_name(channel)
            
            if display_name:
                # 僅當source_id存在且非空時才拼接
                if source_id and source_id.strip():
                    new_id = f"{display_name}_{source_id}"
                else:
                    new_id = display_name
                
                # 更新頻道ID
                channel.set('id', new_id)
                channel_map[channel_id] = new_id
        
        # 處理所有節目節點
        for programme in root.xpath('.//programme'):
            old_channel = programme.get('channel')
            if old_channel in channel_map:
                programme.set('channel', channel_map[old_channel])
        
        # 建立新的XML樹以便格式化
        tree = etree.ElementTree(root)
        
        # 產生格式化後的XML字串
        xml_bytes = etree.tostring(
            tree,
            encoding='utf-8',
            xml_declaration=True,      # 包含XML宣告
            pretty_print=True,          # 啟用格式化輸出
            with_comments=True         # 保留註解
        )
        
        return xml_bytes.decode('utf-8')
    
    except Exception as e:
        print(f"處理XML時發生錯誤: {str(e)}")
        import traceback
        traceback.print_exc()
        return None

async def process_source(filename, source_info, session, executor):
    """處理單一來源：下載、處理並儲存XML"""
    url = source_info["url"]
    # 安全取得source_id，若不存在則為空字串
    source_id = source_info.get("source_id", "").strip()
    
    print(f"開始處理來源: {filename} (ID: '{source_id}')")
    
    # 非同步下載XML
    xml_content = await fetch_xml(session, url)
    if not xml_content:
        print(f"無法取得 {filename} 的XML內容")
        return False
    
    # 在執行緒池中同步處理XML（避免阻塞事件循環）
    processed_xml = await asyncio.get_event_loop().run_in_executor(
        executor, 
        process_xml, 
        xml_content, 
        source_id  # 傳遞原始標識用於拼接
    )
    
    if not processed_xml:
        print(f"處理 {filename} 的XML失敗")
        return False
    
    # 直接使用字典鍵作為檔名
    try:
        with open(filename, 'w', encoding='utf-8') as f:
            f.write(processed_xml)
        print(f"成功儲存: {filename}")
        return True
    except Exception as e:
        print(f"儲存檔案 {filename} 時發生錯誤: {str(e)}")
        return False

async def main():
    """主非同步函式"""
    # 建立執行緒池用於處理CPU密集型任務
    executor = ThreadPoolExecutor(max_workers=max_workers)
    
    # 建立aiohttp會話
    async with aiohttp.ClientSession() as session:
        tasks = []
        for filename, source_info in sources.items():
            task = asyncio.create_task(
                process_source(filename, source_info, session, executor)
            )
            tasks.append(task)
        
        # 等待所有任務完成
        results = await asyncio.gather(*tasks)
        
        success_count = sum(1 for r in results if r)
        print(f"\n處理完成! 成功: {success_count}/{len(sources)}")
        
        # 關閉執行緒池
        executor.shutdown()

if __name__ == "__main__":
    asyncio.run(main())