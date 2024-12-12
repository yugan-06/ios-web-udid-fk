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
// 获取请求中的UDID参数
$udid = $_GET['UDID'] ?? '';

if (!$udid) {
    echo json_encode(['success' => false, 'message' => '未检测到UDID']);
    exit;
}

// 检查拉黑名单文件是否存在
if (!file_exists('lahei.txt')) {
    echo json_encode(['success' => false, 'message' => '拉黑名单文件不存在']);
    exit;
}

// 读取拉黑名单文件
$blacklist = file('lahei.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// 检查该UDID是否在拉黑名单中
if (in_array($udid, $blacklist)) {
    echo json_encode(['success' => false, 'message' => '该设备已被拉黑']);
    exit;
}

// 检查udid.txt文件是否存在
if (!file_exists('udid.txt')) {
    echo json_encode(['success' => false, 'message' => 'UDID记录文件不存在']);
    exit;
}

// 检查udid.txt文件中该UDID的记录次数
$udidRecords = file('udid.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$udidCount = 0;
foreach ($udidRecords as $record) {
    list($recordedUdid, $ip) = explode('|', $record);
    if ($recordedUdid === $udid) {
        $udidCount++;
    }
}

// 如果UDID记录次数超过两次，则拉黑该设备并停止处理
if ($udidCount >= 2) {
    file_put_contents('lahei.txt', $udid . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => '该设备已被拉黑']);
    exit;
}

// 检查key.txt文件是否存在
if (!file_exists('key.txt')) {
    echo json_encode(['success' => false, 'message' => '卡密文件不存在']);
    exit;
}

// 获取key.txt中的卡密
$keys = file('key.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if (count($keys) > 0) {
    // 取出第一个卡密
    $key = array_shift($keys);
    
    // 删除已经分配的卡密并更新key.txt文件
    if (file_put_contents('key.txt', implode("\n", $keys)) === false) {
        echo json_encode(['success' => false, 'message' => '更新卡密文件失败']);
        exit;
    }
    
    // 记录UDID获取卡密成功的信息
    $ip = $_SERVER['REMOTE_ADDR'];
    if (file_put_contents('udid.txt', $udid . '|' . $ip . "\n", FILE_APPEND) === false) {
        echo json_encode(['success' => false, 'message' => '记录UDID信息失败']);
        exit;
    }
    
    // 返回卡密给前端
    echo json_encode(['success' => true, 'key' => $key]);
} else {
    // 如果没有卡密，则返回提示信息，但不显示失败信息
    echo json_encode(['success' => true, 'message' => '卡密已全部发放完毕，请稍后重试。']);
}
?>