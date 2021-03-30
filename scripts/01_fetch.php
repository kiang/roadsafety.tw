<?php
$dataPath = dirname(__DIR__) . '/raw/GetCitiesAreaAccDataStatistics';
$options = ['ALL', '基隆市', '臺北市', '新北市', '桃園市', '新竹市', '新竹縣', '苗栗縣', '臺中市', '彰化縣', '南投縣', '雲林縣', '嘉義市', '嘉義縣',
'臺南市', '高雄市', '屏東縣', '宜蘭縣', '花蓮縣', '臺東縣', '澎湖縣', '金門縣', '連江縣'];
$baseUrl = 'https://roadsafety.tw/motcgisDashboard/api/DashboardAjax/GetCitiesAreaAccDataStatistics?';
for($y = 98; $y <= 110; $y++) {
    for($m = 1; $m <= 12; $m++) {
        if($y === 110 && $m > 1) {
            continue;
        }
        $ty = str_pad($y, 3, '0', STR_PAD_LEFT);
        $tm = str_pad($m, 2, '0', STR_PAD_LEFT);
        foreach($options AS $option) {
            $optionPath = $dataPath . '/' . $option;
            if(!file_exists($optionPath)) {
                mkdir($optionPath, 0777, true);
            }
            $targetFile = "{$optionPath}/{$ty}_{$tm}.json";
            if(!file_exists($targetFile)) {
                $city = urlencode($option);
                file_put_contents($targetFile, file_get_contents("{$baseUrl}City={$city}&Cyear={$ty}%E5%B9%B4&Month={$tm}%E6%9C%88&Type=30%E6%97%A5%E6%AD%BB%E4%BA%A1%E4%BA%BA%E6%95%B8"));
            }
        }
        
    }
}