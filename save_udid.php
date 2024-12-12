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
session_start();

// 获取传入的 UDID 和用户 IP
$udid = isset($_SESSION['udid']) ? $_SESSION['udid'] : '';
$userIp = $_SERVER['REMOTE_ADDR'];

// 检查是否有有效的 UDID
if (empty($udid)) {
    echo json_encode(['success' => false, 'message' => '未找到有效的UDID。']);
    exit();
}

// 定义文件路径
$udidFile = 'udid.txt';
$ipFile = 'ip.txt';
$laheiFile = 'lahei.txt';

// 读取拉黑列表
$laheiList = file_exists($laheiFile) ? file($laheiFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

// 检查 UDID 是否在拉黑列表中
if (in_array($udid, $laheiList)) {
    echo json_encode(['success' => false, 'message' => '此UDID已被拉黑。']);
    exit();
}

// 读取已记录的 UDID
$udids = file_exists($udidFile) ? file($udidFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

// 检查同一个 UDID 的记录次数
$udidCount = count(array_filter($udids, function($record) use ($udid) {
    return strpos($record, $udid) === 0;
}));

// 如果 UDID 记录超过 2 次，则将其拉黑并停止操作
if ($udidCount >= 2) {
    file_put_contents($laheiFile, $udid . PHP_EOL, FILE_APPEND);
    echo json_encode(['success' => false, 'message' => '此UDID已被拉黑。']);
    exit();
}

// 记录新的 UDID 和 IP
file_put_contents($udidFile, $udid . ' - ' . $userIp . PHP_EOL, FILE_APPEND);
file_put_contents($ipFile, $userIp . PHP_EOL, FILE_APPEND);

// 返回成功信息
echo json_encode(['success' => true, 'message' => 'UDID已成功保存。']);
exit();
?>