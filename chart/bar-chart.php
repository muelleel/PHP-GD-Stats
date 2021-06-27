<?php

$font = "font/roboto.ttf";
$title = "A Pie chart";
$embleme = "";



if(isset($_GET['error'])) {ini_set('display_errors', 1); error_reporting(E_ALL);}
putenv('GDFONTPATH=' . realpath('.'));

$data = json_decode($_GET['data'], true);
arsort($data);

$option = array("height" => count($data)*50, "width" => 500, "margin" => 30, "summaryX" => ceil(round(array_sum($data)/count($data), 0)/5), "freeY" => count($data));
$option["freeX"] = (ceil(max($data)/$option["summaryX"])*$option["summaryX"])+$option["summaryX"];

$image = imagecreatetruecolor($option["width"], $option["height"]);
imagefill($image, 0, 0, imagecolorallocate($image, 235, 249, 240));

$gray = imagecolorallocate($image, 200, 200, 200);
$barcolor = imagecolorallocate($image, 16, 147, 167);
imagerectangle($image, 0, 0, $option["width"]-1, $option["height"]-1, $gray);

//grid
for ($i=-1; $i < $option["width"]; $i+=$option["width"]/$option["freeX"]*$option["summaryX"]) {
    imageline($image, $i, 0, $i, $option["height"], $gray);
}

foreach ($data as $key => $value) {
    static $c = 0;
    $y = $c*50;
    $c++;

    if($c == 1) $thisbar = imagecolorallocate($image, 50, 200, 255); else $thisbar = $barcolor;
    imagefilledrectangle($image, 0, $y+10, $value*$option["width"]/$option["freeX"], $y+30+10, $thisbar);
    imagettftext($image, 10, 0, 5, $y+30, imagecolorallocate($image, 30, 30, 30), $font, "(".$value.") - ".$key);
}


//legend
$outer = imagecreate($option["width"]+$option["margin"]*2, $option["height"]+$option["margin"]*2);
imagefill($outer, 0, 0, imagecolorallocate($outer, 235, 249, 250));
imagecopymerge($outer, $image, $option["margin"], $option["margin"], 0, 0, $option["width"], $option["height"], 100);
$image = $outer;

$tmp["lastlegend"]["x"] = 0;
for ($i=-1; $i < $option["width"]; $i+=$option["width"]/$option["freeX"]*$option["summaryX"]) {
    imagettftext($image, 10, -45, $i+$option["margin"], $option["height"]+$option["margin"]+10, imagecolorallocate($image, 0, 0, 0), $font, $tmp["lastlegend"]["x"]);
    $tmp["lastlegend"]["x"] += $option['summaryX'];

}



imagettftext($image, 15, 0, 70, $option["margin"]/1.5, imagecolorallocate($image, 0, 0, 0), $font, $title);
imagettftext($image, 10, 0, 350, 15, imagecolorallocate($image, 100, 100, 100), $font, $embleme);


header("Content-Type: image/png");
imagepng($image);
