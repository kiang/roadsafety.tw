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

foreach (glob($basePath . '/raw/1000/*.csv') as $csvFile) {
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
            $input = urlencode(str_replace('、', ' ', $data['路口']));
            $city = $cityMap[$data['縣市']];
            $key = 'n9tiDRcuMYqKKPYXeVe1lO9DPri9qkQ4Lq1A7QKknIo%3D';
            $jsonFile = $jsonPath . '/' . $data['縣市'] . $data['鄉鎮市區'] . $data['路口'] . '.json';
            if (!file_exists($jsonFile)) {
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

        }
    }
}