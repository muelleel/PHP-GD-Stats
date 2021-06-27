<?php

$font = "font/roboto.ttf";
$title = "A Pie chart";
$embleme = "";

$coloring = array(
    array(255,242,0),
    array(242,101,34),
    array(237,28,36),
    array(148,44,97),
    array(46,49,146),
    array(0,114,188),
    array(0,169,157),
    array(0,166,81),
    array(141,198,63)
);



if(isset($_GET['error'])) {ini_set('display_errors', 1); error_reporting(E_ALL);}
putenv('GDFONTPATH=' . realpath('.'));

$data = json_decode($_GET['data'], true);
asort($data);

$option = array("height" => 300, "width" => 500, "margin" => 30, "spacer" => 30-count($data));


$image = imagecreatetruecolor($option["width"], $option["height"]);
imagefill($image, 0, 0, imagecolorallocate($image, 235, 249, 240));

$gray = imagecolorallocate($image, 100, 100, 100);
imagerectangle($image, 0, 0, $option["width"]-1, $option["height"]-1, $gray);

$black = imagecolorallocate($image, 0, 0, 0);

$lastdeg = 0;
$c = 0;
foreach ($data as $key => $value) {
    if(isset($coloring[$c])) $color = $colorindex[] = imagecolorallocate($image, $coloring[$c][0], $coloring[$c][1], $coloring[$c][2]); else $color = $colorindex[] = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
    $c++;
    $deg = $value/array_sum($data)*360+$lastdeg;
    $prc = $value/array_sum($data)*100;
    imagefilledarc($image, $option["height"]/2, $option["height"]/2, $option["height"]-10, $option["height"]-10, $lastdeg, $deg, $color, IMG_ARC_EDGED);

    $type_space = imagettfbbox(10, 0, $font, round($prc, 0)."%");
    $wtext = abs($type_space[4] - $type_space[0]);
    $htext = abs($type_space[5] - $type_space[1]);

    $xtext = $option["height"]/2 + (cos(deg2rad(($lastdeg+$deg)/2))*($option["height"]/2.5))-$wtext/2;
    $ytext = $option["height"]/2 + (sin(deg2rad(($lastdeg+$deg)/2))*($option["height"]/2.5))+$htext/2-1;

    $rgb = imagecolorat($image, $xtext, $ytext);
    $r = ($rgb >> 16) & 0xFF;
    $g = ($rgb >> 8) & 0xFF;
    $b = $rgb & 0xFF;

    if(($r+$g+$b)/3 > 128) $textcolor = $black; else $textcolor = imagecolorallocate($image, 255, 255, 255);

    imagettftext($image, 10, 0, $xtext, $ytext, $textcolor, $font, round($prc, 0)."%");
    $lastdeg = $deg;
}


for ($i=1; $i <= count($data); $i++) {
    $y = $i*$option["spacer"];
    $y = ($i-1)*(($option["height"]-$option["spacer"])/count($data)+1)+$option["spacer"];
    imagefilledrectangle($image, 350, $y, 360, $y+10, $colorindex[$i-1]);
    imagerectangle($image, 350, $y, 360, $y+10, $black);
    imagettftext($image, 10, 0, 360+5, $y+10, $black, $font, "(".$data[array_keys($data)[$i-1]].") - ".array_keys($data)[$i-1]);

}
imageellipse($image, $option["height"]/2, $option["height"]/2, $option["height"]-10, $option["height"]-10, $black);

//legend
$outer = imagecreate($option["width"]+$option["margin"]*2, $option["height"]+$option["margin"]*2);
imagefill($outer, 0, 0, imagecolorallocate($outer, 235, 249, 250));
imagecopymerge($outer, $image, $option["margin"], $option["margin"], 0, 0, $option["width"], $option["height"], 100);
$image = $outer;
imagefill($outer, 0, 0, imagecolorallocate($outer, 235, 249, 250));

imagettftext($image, 15, 0, 70, $option["margin"]/1.5, imagecolorallocate($image, 0, 0, 0), $font, $title);
imagettftext($image, 10, 0, 350, 15, $gray, $font, $embleme);


header("Content-Type: image/png");
imagepng($image);
