<?php

$font = "font/roboto.ttf";
$title = "A Pie chart";
$embleme = "";


if(isset($_GET['error'])) {ini_set('display_errors', 1); error_reporting(E_ALL);}
putenv('GDFONTPATH=' . realpath('.'));
setlocale(LC_TIME, 'de_DE', 'de_DE.UTF-8');

$data = json_decode($_GET['data'], true);
$data = array_reverse($data);
$option = array("height" => 300, "width" => 700, "margin" => 30, "summaryY" => ceil(round(array_sum($data)/count($data), 0)/5)*5, "freeX" => 13);
$option["freeY"] = (ceil(max($data)/$option["summaryY"])*$option["summaryY"])+$option["summaryY"];

$image = imagecreatetruecolor($option["width"], $option["height"]);
imagefill($image, 0, 0, imagecolorallocate($image, 235, 249, 240));


$gray = imagecolorallocate($image, 200, 200, 200);
imagerectangle($image, 0, 0, $option["width"]-1, $option["height"]-1, $gray);

//grid
for ($i=-1; $i < $option["width"]; $i+=$option["width"]/$option['freeX']) {
    imageline($image, $i, 0, $i, $option["height"], $gray);
}
for ($a=0; $a < $option["height"]; $a+=$option["height"]/$option['freeY']*$option["summaryY"]) {
    imageline($image, 0, $a, $option["width"], $a, $gray);
}

function calculate_median($arr) {
    $arr = array_filter($arr, function($a) { return ($a !== 0); });
    sort($arr);
    if(count($arr) % 2) { // odd number, middle is the median
        $median = $arr[floor((count($arr)-1)/2)];
    } else { // even number, calculate avg of 2 medians
        $median = (($arr[floor((count($arr)-1)/2)]+$arr[floor((count($arr)-1)/2)+1])/2);
    }
    return $median;
}

//average
$average = $option["height"]-round(array_sum($data)/count(array_filter($data, function($a) { return ($a !== 0); })), 1)*$option["height"]/$option['freeY'];
$median = $option["height"]-calculate_median($data)*$option["height"]/$option['freeY'];
imageline($image, 0, $average, $option["width"], $average, imagecolorallocate($image, 255, 100, 100));
imagettftext($image, 10, 0, 1, $average-1, imagecolorallocate($image, 255, 100, 100), $font, round(array_sum($data)/count(array_filter($data, function($a) { return ($a !== 0); })), 1));

imageline($image, 0, $median, $option["width"], $median, imagecolorallocate($image, 100, 100, 255));
imagettftext($image, 10, 0, 1, $median-1, imagecolorallocate($image, 100, 100, 255), $font, calculate_median($data));


//chart
foreach ($data as $key => $value) {
    $key++;
    if($value == 0) continue;
    $x = $option["width"]-$key*$option["width"]/$option['freeX'];
    $y = $option["height"]-$value*$option["height"]/$option['freeY'];
    imagefilledellipse($image, $x, $y, 10, 10, imagecolorallocate($image, 0, 255, 0));
    imagesetthickness($image, 2);
    if(isset($tmp["lastcords"])) imageline($image, $tmp["lastcords"]["x"], $tmp["lastcords"]["y"], $x, $y, imagecolorallocate($image, 200, 200, 200));
    imagettftext($image, 10, 0, $x, $y-5, imagecolorallocate($image, 0, 100, 100), $font, $value);
    $tmp["lastcords"]["x"] = $x;
    $tmp["lastcords"]["y"] = $y;
}


//legend
$outer = imagecreate($option["width"]+$option["margin"]*2, $option["height"]+$option["margin"]*2);
imagefill($outer, 0, 0, imagecolorallocate($outer, 235, 249, 250));
imagecopymerge($outer, $image, $option["margin"], $option["margin"], 0, 0, $option["width"], $option["height"], 100);
$image = $outer;

$tmp["lastlegend"]["x"] = $option["freeY"];
for ($a=0; $a < $option["height"]+1; $a+=$option["height"]/$option['freeY']*$option["summaryY"]) {
    imagettftext($image, 10, 0, $option['margin']/2/2, $a+$option['margin']+5, imagecolorallocate($image, 0, 0, 0), $font, $tmp["lastlegend"]["x"]);
    $tmp["lastlegend"]["x"] -= $option['summaryY'];
}
foreach ($data as $key => $value) {
    $key++;
    $x = $option["width"]-$key*$option["width"]/$option['freeX'];
    imagettftext($image, 10, -45, $x+$option["margin"], $option["height"]+$option["margin"]+$option["margin"]/4, imagecolorallocate($image, 0, 0, 0), $font, strftime("%h", mktime(0, 0, 0, abs($key-13), 10)));
}
imagettftext($image, 15, 0, 70, $option["margin"]/1.5, imagecolorallocate($image, 0, 0, 0), $font, $title);
imagettftext($image, 10, 0, 500, 15, imagecolorallocate($image, 100, 100, 100), $font, $embleme);


header("Content-Type: image/png");
imagepng($image);
