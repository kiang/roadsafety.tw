<?php
/*
DashboardAjax/GetDashboardStaticData?sCyear=[row0]&sM=[row1]&sCity=[row2]&Type=[row3]&TypeDeathHurt=[row4];109年,12月,ALL,與去年同期比較,0
DashboardAjax/GetDashboardStaticDataChart?sCity=[row0]&TypeDeathHurt=[row1]&ItemType=[row2];ALL,0,事故總件數
DashboardAjax/GetCitiesAccData_EachYM?City=[row0]&Area=[row1];ALL,ALL
DashboardAjax/GetCitiesAccData_Near12Month?City=[row0]&Area=[row1];ALL,ALL
DashboardAjax/GetStaticData?Topic=%E5%B9%B4%E9%BD%A1%E8%88%87%E9%81%8B%E5%85%B7&City=[row0];ALL
DashboardAjax/GetStaticData?Topic=%E7%A2%B0%E6%92%9E%E5%9E%8B%E6%85%8B%E8%88%87%E4%BD%8D%E7%BD%AE%E8%88%87%E8%82%87%E4%BA%8B%E5%8E%9F%E5%9B%A0&City=[row0];ALL
DashboardAjax/GetStaticData?Topic=%E5%B9%B4%E9%BD%A1%E8%88%87%E7%A2%B0%E6%92%9E%E5%9E%8B%E6%85%8B%E8%88%87%E4%BD%8D%E7%BD%AE&City=[row0];ALL
DashboardAjax/GetStaticData?Topic=%E7%A2%B0%E6%92%9E%E5%9E%8B%E6%85%8B%E8%88%87%E4%BD%8D%E7%BD%AE%E8%88%87%E9%81%8B%E5%85%B7&City=[row0];ALL
DashboardAjax/GetStaticData?Topic=%E8%82%87%E4%BA%8B%E5%8E%9F%E5%9B%A0%E8%88%87%E9%81%8B%E5%85%B7&City=[row0];ALL
DashboardAjax/GetCitiesAccData_YearEachMonth?City=[row0]&Area=[row1];ALL,ALL
DashboardAjax/GetCitiesAreaAccDataStatistics?City=[row0]&Cyear=[row1]&Month=[row2]&Type=30%E6%97%A5%E6%AD%BB%E4%BA%A1%E4%BA%BA%E6%95%B8;ALL,109年,12月
DashboardAjax/GetCitiesAreaAccDataStatistics?City=[row0]&Cyear=[row1]&Month=[row2]&Type=[row3];臺北市,109年,12月,每千人死傷數
DashboardAjax/GetCitiesAreaAccDataStatistics?City=ALL&Cyear=[row0]&Month=[row1]&Type=[row2];109年,12月,每十萬人死傷數
DashboardAjax/GetAreaAccDataStatisticsThisYearDiff?City=[row0]&Type=[row1];ALL,同期比較
DashboardAjax/GetAgesAccDataStatisticsNear12MonthDiff?City=[row0]&Area=[row1];ALL,ALL
DashboardAjax/GetAgesAccDataStatistics?City=[row0]&Area=[row1];ALL,ALL
DashboardAjax/GetAgesAccDataStatisticsNear12Month?City=[row0]&Area=[row1];ALL,ALL 
*/
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

$dataPath2 = dirname(__DIR__) . '/raw/GetCitiesAccData_EachYM';
$baseUrl2 = 'https://roadsafety.tw/motcgisDashboard/api/DashboardAjax/GetCitiesAccData_EachYM?';
foreach($options AS $option) {
    $optionPath = $dataPath2 . '/' . $option;
    if(!file_exists($optionPath)) {
        mkdir($optionPath, 0777, true);
    }
    $targetFile = "{$optionPath}/data.json";
    if(!file_exists($targetFile)) {
        $city = urlencode($option);
        file_put_contents($targetFile, file_get_contents("{$baseUrl2}City={$city}&Area=ALL"));
    }
}

$dataPath3 = dirname(__DIR__) . '/raw/GetStaticData';
$topics = ['年齡與運具', '碰撞型態與位置與肇事原因', '年齡與碰撞型態與位置', '碰撞型態與位置與運具', '肇事原因與運具'];
$baseUrl3 = 'https://roadsafety.tw/motcgisDashboard/api/DashboardAjax/GetStaticData?';
foreach($topics AS $topic) {
    $uTopic = urlencode($topic);
    foreach($options AS $option) {
        $optionPath = $dataPath3 . '/' . $option;
        if(!file_exists($optionPath)) {
            mkdir($optionPath, 0777, true);
        }
        $targetFile = "{$optionPath}/{$topic}.json";
        if(!file_exists($targetFile)) {
            $city = urlencode($option);
            file_put_contents($targetFile, file_get_contents("{$baseUrl3}Topic={$uTopic}&City={$city}"));
        }
    }
}