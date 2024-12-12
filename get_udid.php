<?php
// 获取 User-Agent
$user_agent = $_SERVER['HTTP_USER_AGENT'];

// 定义允许访问的自带浏览器关键词 (iOS Safari 或 Android Chrome)
if (
    (strpos($user_agent, 'Safari') === false && strpos($user_agent, 'Mobile') === false) && // iOS Safari
    (strpos($user_agent, 'Chrome') === false && strpos($user_agent, 'Android') === false)  // Android Chrome
) {
    // 如果检测不到自带浏览器，显示错误信息
    echo "访问受限：请使用自带浏览器访问本页面。";
    exit;
}

// 之后是你的原始代码...
header('Content-Type: application/xml');
header('Content-Disposition: attachment; filename="get_udid.mobileconfig"');

// 生成描述文件
echo '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
    <dict>
        <key>PayloadContent</key>
        <dict>
            <key>URL</key>
            <string>https://kuka.qfios.vip/ReceiveUDID.php</string>
            <key>DeviceAttributes</key>
            <array>
                <string>UDID</string>
                <string>IMEI</string>
                <string>ICCID</string>
                <string>VERSION</string>
                <string>PRODUCT</string>
            </array>
        </dict>
        <key>PayloadOrganization</key>
        <string>【个人定制】官方认证</string>
        <key>PayloadDisplayName</key>
        <string>查询设备UDID</string>
        <key>PayloadVersion</key>
        <integer>1</integer>
        <key>PayloadUUID</key>
        <string>3C4DC7D2-E475-3375-489C-0BB8D737A651</string>
        <key>PayloadIdentifier</key>
        <string>api-cer.dvffz8.cn</string>
        <key>PayloadDescription</key>
        <string>本文件仅用来获取设备ID</string>
        <key>PayloadType</key>
        <string>Profile Service</string>
    </dict>
</plist>';
?>