<?php

$basePath = dirname(__DIR__);
$areaPath = $basePath . '/data/area';
$dataPath = $basePath . '/data/city';
if (!file_exists($dataPath)) {
    mkdir($dataPath, 0777, true);
}
foreach (glob($areaPath . '/*.csv') as $csvFile) {
    $p = pathinfo($csvFile);
    $fh = fopen($csvFile, 'r');
    fgetcsv($fh, 1024);
    $pool = [];
    while ($line = fgetcsv($fh, 1024)) {
        $city = mb_substr($line[0], 0, 3, 'utf-8');
        if (!isset($pool[$city])) {
            $pool[$city] = [
                'city' => $city,
                'population' => 0,
                'death' => 0,
                'rate' => 0.0,
            ];
        }
        $pool[$city]['population'] += $line[1];
        $pool[$city]['death'] += $line[2];
    }
    $targetFile = $dataPath . '/' . $p['basename'];
    $oFh = fopen($targetFile, 'w');
    fputcsv($oFh, ['city', 'population', 'death', 'rate']);
    foreach ($pool as $city => $val) {
        $rate = round(($val['death'] / $val['population']) * 100000, 3);
        fputcsv($oFh, [$city, $val['population'], $val['death'], $rate]);
    }
}
