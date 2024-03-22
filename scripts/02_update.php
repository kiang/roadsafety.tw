<?php

include dirname(__DIR__) . '/vendor/autoload.php';

use Goutte\Client;

$client = new Client();
$client->setServerParameter('HTTP_USER_AGENT', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:73.0) Gecko/20100101 Firefox/73.0');

$client->request('GET', 'https://roadsafety.tw/Dashboard/Custom?type=30%E6%97%A5%E6%AD%BB%E4%BA%A1%E4%BA%BA%E6%95%B8');

$page = $client->getResponse()->getContent();
$pos = strpos($page, 'DashboardAjax/GetCitiesAreaAccDataStatistics');
if (false !== $pos) {
    $pos = strpos($page, '?City', $pos);
    $posEnd = strpos($page, '</div>', $pos);
    $url = html_entity_decode(urldecode(substr($page, $pos, $posEnd - $pos)));
    $url = preg_replace('/[^0-9,]/', '', $url);
    $parts = explode(',', $url);
    $dataPath = dirname(__DIR__) . '/raw/GetCitiesAreaAccDataStatistics';
    if (!file_exists("{$dataPath}/ALL/{$parts[1]}_{$parts[2]}.json")) {
        $options = [
            'ALL', '基隆市', '臺北市', '新北市', '桃園市', '新竹市', '新竹縣', '苗栗縣', '臺中市', '彰化縣', '南投縣', '雲林縣', '嘉義市', '嘉義縣',
            '臺南市', '高雄市', '屏東縣', '宜蘭縣', '花蓮縣', '臺東縣', '澎湖縣', '金門縣', '連江縣'
        ];
        $baseUrl = 'https://roadsafety.tw/api/DashboardAjax/GetCitiesAreaAccDataStatistics?';
        foreach ($options as $option) {
            $optionPath = $dataPath . '/' . $option;
            if (!file_exists($optionPath)) {
                mkdir($optionPath, 0777, true);
            }
            $targetFile = "{$optionPath}/{$parts[1]}_{$parts[2]}.json";
            $city = urlencode($option);
            if (!file_exists($targetFile)) {
                $client->request('GET', "{$baseUrl}City={$city}&Cyear={$parts[1]}%E5%B9%B4&Month={$parts[2]}%E6%9C%88&SType=ALL&Type=30%E6%97%A5%E6%AD%BB%E4%BA%A1%E4%BA%BA%E6%95%B8");
                $json = json_decode($client->getResponse()->getContent());
                file_put_contents($targetFile, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
        }

        $dataPath3 = dirname(__DIR__) . '/raw/GetStaticData';
        $topics = ['年齡與運具', '碰撞型態與位置與肇事原因', '年齡與碰撞型態與位置', '碰撞型態與位置與運具', '肇事原因與運具'];
        $baseUrl3 = 'https://roadsafety.tw/api/DashboardAjax/GetStaticData?';
        foreach ($topics as $topic) {
            $uTopic = urlencode($topic);
            foreach ($options as $option) {
                $city = urlencode($option);
                $client->request('GET', "{$baseUrl3}Topic={$uTopic}&City={$city}");
                $json = json_decode($client->getResponse()->getContent(), true);
                if (!empty($json)) {
                    $optionPath = $dataPath3 . '/' . $option . '/' . $json[0]['row'][1];
                    if (!file_exists($optionPath)) {
                        mkdir($optionPath, 0777, true);
                    }
                    $targetFile = "{$optionPath}/{$topic}.json";
                    file_put_contents($targetFile, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                }
            }
        }
    }
}