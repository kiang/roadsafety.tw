<?php

$basePath = dirname(__DIR__);
$populationPath = '/home/kiang/public_html/tw_population/population';
$population = [];
$dataPath = $basePath . '/data/csv';
if(!file_exists($dataPath)) {
    mkdir($dataPath, 0777, true);
}

$toReplace = [
    ' ' => '',
    '　' => '',
    '高雄市三民一' => '高雄市三民區',
    '高雄市三民二' => '高雄市三民區',
    '高雄市鳳山一' => '高雄市鳳山區',
    '高雄市鳳山二' => '高雄市鳳山區',
    '桃園縣桃園市' => '桃園市桃園區',
    '桃園縣中壢市' => '桃園市中壢區',
    '桃園縣大溪鎮' => '桃園市大溪區',
    '桃園縣楊梅市' => '桃園市楊梅區',
    '桃園縣蘆竹市' => '桃園市蘆竹區',
    '桃園縣大園鄉' => '桃園市大園區',
    '桃園縣龜山鄉' => '桃園市龜山區',
    '桃園縣八德市' => '桃園市八德區',
    '桃園縣龍潭鄉' => '桃園市龍潭區',
    '桃園縣平鎮市' => '桃園市平鎮區',
    '桃園縣新屋鄉' => '桃園市新屋區',
    '桃園縣觀音鄉' => '桃園市觀音區',
    '桃園縣復興鄉' => '桃園市復興區',
    '彰化縣員林鎮' => '彰化縣員林市',
    '苗栗縣頭份鎮' => '苗栗縣頭份市',
];
foreach (glob($basePath . '/raw/GetCitiesAreaAccDataStatistics/*/*.json') as $jsonFile) {
    $p1 = pathinfo($jsonFile);
    if (substr($p1['dirname'], -3) !== 'ALL') {
        $parts = explode('_', $p1['filename']);
        if (!isset($population[$parts[0]][$parts[1]])) {
            $pYear = $parts[0] + 1911;
            $populationYm = $populationPath . '/' . $pYear . '/' . $parts[1];
            if (file_exists($populationYm)) {
                if (!isset($population[$parts[0]])) {
                    $population[$parts[0]] = [];
                }
                $population[$parts[0]][$parts[1]] = [];
                foreach (glob($populationYm . '/*.csv') as $csvFile) {
                    $fh = fopen($csvFile, 'r');
                    $header = fgetcsv($fh, 4096);
                    if (!in_array('區域別', $header)) {
                        $header = fgetcsv($fh, 4096);
                    }
                    while ($line = fgetcsv($fh, 4096)) {
                        $data = array_combine($header, $line);
                        $data['區域別'] = strtr($data['區域別'], $toReplace);
                        if(!isset($population[$parts[0]][$parts[1]][$data['區域別']])) {
                            $population[$parts[0]][$parts[1]][$data['區域別']] = 0;
                        }
                        $population[$parts[0]][$parts[1]][$data['區域別']] += $data['人口數'];
                    }
                }
            }
        }
        if (isset($population[$parts[0]][$parts[1]])) {
            $json = json_decode(file_get_contents($jsonFile), true);
            foreach($json AS $item) {
                $area = $item['row'][0] . $item['col'][0];
                $dataFile = $dataPath . '/' . $p1['filename'] . '.csv';
                if(!file_exists($dataFile)) {
                    $oFh = fopen($dataFile, 'w');
                    fputcsv($oFh, ['area', 'population', 'death', 'rate']);
                } else {
                    $oFh = fopen($dataFile, 'a');
                }
                $rate = round(($item['value'] / $population[$parts[0]][$parts[1]][$area]) * 100000, 3);
                fputcsv($oFh, [$area, $population[$parts[0]][$parts[1]][$area], $item['value'], $rate]);
                fclose($oFh);
            }
        }
    }
}
