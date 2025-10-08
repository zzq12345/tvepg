import sys
import xml.etree.ElementTree as ET

def convert_epg(xml_file):
    tree = ET.parse(xml_file)
    root = tree.getroot()

    # 示例：提取所有频道名称
    channels = root.findall('channel')
    with open('channels.txt', 'w', encoding='utf-8') as f:
        for ch in channels:
            ch_id = ch.get('id')
            display_name = ch.find('display-name').text if ch.find('display-name') is not None else 'Unknown'
            f.write(f"{ch_id}: {display_name}\n")

if __name__ == '__main__':
    if len(sys.argv) != 2:
        print("Usage: python convert.py epgguangdong.xml")
        sys.exit(1)
    convert_epg(sys.argv[1])
