<?php
$basePath = dirname(__DIR__);

$fh = fopen($basePath . '/raw/1000/csv/budget.csv', 'r');
$budget = $points = [];
while ($line = fgetcsv($fh, 2048)) {
    foreach ($line as $k => $v) {
        $line[$k] = str_replace(chr(13), '', $v);
    }
    if (!empty($line[3])) {
        switch ($line[3]) {
            case '核定路口數':
                $header1 = $line;
                break;
            case '路口位置':
                $header2 = $line;
                break;
            default:
                if (count($line) === 7) {
                    $data = array_combine($header1, $line);
                    $budget[$data['縣市別']] = $data;
                } elseif (count($line) === 4) {
                    $data = array_combine($header2, $line);
                    if (!isset($points[$data['縣市別']])) {
                        $points[$data['縣市別']] = [];
                    }
                    $points[$data['縣市別']][$data['市區鄉鎮'] . $data['路口位置']] = true;
                }
        }
    }
}

$oFh = fopen($basePath . '/data/1000.csv', 'w');
$oFhHeader = false;
foreach (glob($basePath . '/raw/1000/*.csv') as $csvFile) {
    $fh = fopen($csvFile, 'r');
    $lineCount = 0;
    while ($line = fgetcsv($fh, 2048)) {
        switch (++$lineCount) {
            case 1: //skip
                break;
            case 2:
                $header = $line;
                foreach ($header as $k => $v) {
                    $header[$k] = str_replace(chr(13), '', $v);
                }
                $header[3] = '路口';
                array_shift($header);
                break;
            default:
                array_shift($line);
                $data = array_combine($header, $line);
                if (!isset($budget[$data['縣市']]['路口數'])) {
                    $budget[$data['縣市']]['路口數'] = 0;
                }
                $budget[$data['縣市']]['路口數']++;
                if (isset($points[$data['縣市']][$data['鄉鎮市區'] . $data['路口']])) {
                    $data['補助'] = '有';
                } else {
                    $data['補助'] = '無';
                }
                if (false === $oFhHeader) {
                    fputcsv($oFh, array_merge($header, ['補助']));
                    $oFhHeader = true;
                }
                fputcsv($oFh, $data);
        }
    }
}

$oFh = fopen($basePath . '/data/1000_budget.csv', 'w');
$oFhHeader = false;
$header1[] = '路口數';
foreach ($budget as $item) {
    if (count($item) === 1) {
        continue;
    }
    if (false === $oFhHeader) {
        array_shift($header1);
        fputcsv($oFh, $header1);
        $oFhHeader = true;
    }
    array_shift($item);
    fputcsv($oFh, $item);
}
