<?php
$basePath = dirname(__DIR__);
$jsonPath = $basePath . '/raw/1000/json';
if (!file_exists($jsonPath)) {
    mkdir($jsonPath, 0777, true);
}
$cityMap = [
    '屏東縣' => '10013',
    '宜蘭縣' => '10002',
    '高雄市' => '64000',
    '桃園市' => '68000',
    '新北市' => '65000',
    '臺北市' => '63000',
    '臺南市' => '67000',
    '新竹縣' => '10004',
    '南投縣' => '10008',
    '臺中市' => '66000',
    '雲林縣' => '10009',
    '彰化縣' => '10007',
    '花蓮縣' => '10015',
    '臺東縣' => '10014',
    '嘉義縣' => '10010',
    '嘉義市' => '10020',
    '基隆市' => '10017',
    '苗栗縣' => '10005',
    '新竹市' => '10018',
    '澎湖縣' => '10016',
    '連江縣' => '09007',
    '金門縣' => '09020',
];
$pool = array(
    '桃園市八德區中華路、高城路' =>
        array(
            'x' => 121.275514,
            'y' => 24.981114,
        ),
    '桃園市平鎮區中豐路山頂段、南豐路' =>
        array(
            'x' => 121.210959,
            'y' => 24.900054,
        ),
    '桃園市平鎮區中豐路山頂段、日星街' =>
        array(
            'x' => 121.211750,
            'y' => 24.904224,
        ),
    '新北市新店區祥和路、安興路' =>
        array(
            'x' => 121.515320,
            'y' => 24.969466,
        ),
    '新北市三重區國路一段、力行路二段' =>
        array(
            'x' => 121.481829,
            'y' => 25.072873,
        ),
    '桃園市平鎮區中豐路南勢二段、南京路' =>
        array(
            'x' => 121.210697,
            'y' => 24.916353,
        ),
    '新北市汐止區龍安路28巷、連興街' =>
        array(
            'x' => 121.654352,
            'y' => 25.063095,
        ),
    '雲林縣虎尾鎮林森路二段、八德街' =>
        array(
            'x' => 120.427607,
            'y' => 23.703032,
        ),
    '嘉義縣新港鄉宮前村中山路、登雲路' =>
        array(
            'x' => 120.347631,
            'y' => 23.555651,
        ),
    '新竹縣竹北市光明六路東二段、嘉興路' =>
        array(
            'x' => 121.029566,
            'y' => 24.818897,
        ),
    '基隆市中山區中山一路、港西街' =>
        array(
            'x' => 121.740542,
            'y' => 25.134813,
        ),
    '苗栗縣苗栗市中正路、中正路1297巷' =>
        array(
            'x' => 120.816837,
            'y' => 24.542950,
        ),
);

$fc = [
    'type' => 'FeatureCollection',
    'features' => [],
];

foreach (glob($basePath . '/raw/1000/*.csv') as $csvFile) {
    $p = pathinfo($csvFile);
    $fh = fopen($csvFile, 'r');
    $header = false;
    while ($line = fgetcsv($fh, 2048)) {
        if (empty($line[6])) {
            continue;
        }
        foreach ($line as $k => $v) {
            $line[$k] = preg_replace('/\s+/', '', $v);
        }
        if (false === $header) {
            $line[3] = '路口';
            $header = $line;
        } else {
            $data = array_combine($header, $line);
            $key = $data['縣市'] . $data['鄉鎮市區'] . $data['路口'];
            $parts = explode('、', $data['路口']);
            sort($parts);
            $jsonFile = $jsonPath . '/' . $key . '.json';
            if (!file_exists($jsonFile)) {
                $city = $cityMap[$data['縣市']];
                $key = 'n9tiDRcuMYqKKPYXeVe1lO9DPri9qkQ4Lq1A7QKknIo%3D';
                $input = urlencode(implode(' ', $parts));
                $q = "curl 'https://gis.tgos.tw/TGLocator/TGLocator.ashx?types=roadcross&input={$input}&srs=EPSG:4326&format=jsonp&pnum=1&county1={$city}&county2={$city}&ignoreGeometry=false&keystr={$key}' -H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/117.0' -H 'Connection: keep-alive' -H 'Referer: https://api.tgos.tw/' -H 'Sec-Fetch-Dest: script' -H 'Sec-Fetch-Mode: no-cors' -H 'Sec-Fetch-Site: same-site' -H 'Save-Data: on'";
                $result = exec($q);
                if (false === strpos($result, 'REQUEST_DENIED')) {
                    if (substr($result, 0, 9) === 'callback(') {
                        $result = substr($result, 9, -2);
                    }
                    file_put_contents($jsonFile, $result);
                } else {
                    echo $result;
                    exit();
                }
            }
            $c = file_get_contents($jsonFile);
            if (false !== strpos($c, 'REQUEST_DENIED')) {
                unlink($jsonFile);
            } else {
                if (substr($c, 0, 9) === 'callback(') {
                    $c = substr($c, 9, -2);
                    file_put_contents($jsonFile, $c);
                }
            }

            $json = json_decode($c, true);
            $selected = false;
            if (count($json['results']) > 1) {
                foreach ($json['results'] as $node) {
                    $matchParts = [];
                    $matchParts[] = explode(',', $node['NAME1'])[0];
                    $matchParts[] = explode(',', $node['NAME2'])[0];
                    sort($matchParts);
                    if ($matchParts === $parts) {
                        $selected = $node['geometry'];
                        break;
                    }
                }
                if (false === $selected) {
                    $selected = $json['results'][0]['geometry'];
                }
            } else {
                if (isset($json['results'][0])) {
                    $selected = $json['results'][0]['geometry'];
                }
            }

            if (false === $selected && isset($pool[$key])) {
                $selected = $pool[$key];
            }
            $data['type'] = $p['filename'];
            $fc['features'][] = [
                'type' => 'Feature',
                'properties' => $data,
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [
                        floatval($selected['x']),
                        floatval($selected['y']),
                    ],
                ],
            ];
        }
    }
}

file_put_contents($basePath . '/docs/1000.json', json_encode($fc, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));