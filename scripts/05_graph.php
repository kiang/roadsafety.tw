<?php
$basePath = dirname(__DIR__);

$pngPath = $basePath . '/docs/png/city';
if (!file_exists($pngPath)) {
    mkdir($pngPath, 0777, true);
}


function randomColor()
{
    $r = rand(1, 255);
    $g = rand(1, 255);
    $b = rand(1, 255);
    return "rgb({$r},{$g},{$b})";
}
foreach (glob($basePath . '/raw/GetCitiesAccData_EachYM/*/*.json') as $jsonFile) {
    $chart = [
        'labels' => [],
        'datasets' => [],
    ];
    $json = json_decode(file_get_contents($jsonFile), true);
    $pool1 = $pool2 = [];
    $toPass = false;
    foreach ($json as $item) {
        if ('ALL' === $item['row'][0]) {
            $toPass = true;
            continue;
        } else {
            $title = $item['row'][0];
        }
        if (isset($item['row'][2])) {
            $parts = explode('/', $item['col'][0]);
            $parts[0] += 1911;
            $parts[1] = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
            $key = implode('/', $parts);
            switch ($item['row'][2]) {
                case '死亡人數':
                    $pool1[$key] = $item['value'];
                    break;
                case '受傷人數':
                    $pool2[$key] = $item['value'];
                    break;
            }
        }
    }
    if ($toPass) {
        continue;
    }

    $chart['labels'] = array_keys($pool1);
    $chart['datasets'][] = [
        'label' => '死亡',
        'backgroundColor' => randomColor(),
        'data' => array_values($pool1),
    ];
    $chart['datasets'][] = [
        'label' => '受傷',
        'backgroundColor' => randomColor(),
        'data' => array_values($pool2),
    ];

    file_put_contents($basePath . '/tmp/chart.json', json_encode([
        'title' => $title . '交通事故死亡統計',
        'data' => $chart,
        'pngFilePath' => $pngPath . '/' . $title . '.png',
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    exec("/usr/bin/node {$basePath}/scripts/rawCharts.js");
}
