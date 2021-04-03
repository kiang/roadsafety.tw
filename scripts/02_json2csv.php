<?php
echo urldecode('DashboardAjax/GetDashboardStaticData?sCyear=[row0]&sM=[row1]&sCity=[row2]&Type=[row3]&TypeDeathHurt=[row4];109年,12月,ALL,與去年同期比較,0
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
DashboardAjax/GetAgesAccDataStatisticsNear12Month?City=[row0]&Area=[row1];ALL,ALL');
exit();
$dataPath = dirname(__DIR__) . '/raw/GetCitiesAccData_EachYM/ALL/data.json';
$json = json_decode(file_get_contents($dataPath), true);
$result = [];
foreach($json AS $line) {
    $y = intval(substr($line['col'][0], 0, 3)) + 1911;
    if(!isset($result[$y])) {
        $result[$y] = [];
    }
    if(!isset($result[$y][$line['row'][2]])) {
        $result[$y][$line['row'][2]] = 0;
    }
    $result[$y][$line['row'][2]] += $line['value'];
}
print_r($result);