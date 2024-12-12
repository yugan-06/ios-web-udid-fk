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
$data = file_get_contents('php://input');
$plistBegin   = '<?xml version="1.0"';
$plistEnd   = '</plist>';
$pos1 = strpos($data, $plistBegin);
$pos2 = strpos($data, $plistEnd);
$data2 = substr ($data,$pos1,$pos2-$pos1);
$xml = xml_parser_create();
xml_parse_into_struct($xml, $data2, $vs);
xml_parser_free($xml);

$UDID = "";

$CHALLENGE = "";

$DEVICE_NAME = "";

$DEVICE_PRODUCT = "";

$DEVICE_VERSION = "";

$iterator = 0;

$arrayCleaned = array();
foreach($vs as $v){
    if($v['level'] == 3 && $v['type'] == 'complete'){

    $arrayCleaned[]= $v;

    }
$iterator++;

}

$data = "";
$iterator = 0;

foreach($arrayCleaned as $elem){

    $data .= "\n==".$elem['tag']." -> ".$elem['value']."<br/>";

    switch ($elem['value']) {

        case "CHALLENGE":

            $CHALLENGE = $arrayCleaned[$iterator+1]['value'];

            break;

        case "DEVICE_NAME":

            $DEVICE_NAME = $arrayCleaned[$iterator+1]['value'];

            break;

        case "PRODUCT":

            $DEVICE_PRODUCT = $arrayCleaned[$iterator+1]['value'];

            break;

        case "UDID":

            $UDID = $arrayCleaned[$iterator+1]['value'];

            break;

        case "VERSION":

            $DEVICE_VERSION = $arrayCleaned[$iterator+1]['value'];

            break;

        }
        $iterator++;

}



$params = "UDID=".$UDID;

//header("Location: http://dev.skyfox.org/udid?data=".rawurlencode($params));
header('HTTP/1.1 301 Moved Permanently');
header("Location: https://kuka.qfios.vip/index.html?".$params);
?>
