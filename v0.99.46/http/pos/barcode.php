<?php

function barcode($code) {
	$size = 1;
	$height = 20;

	if ($_GET['size'] > 0) { $size = $_GET['size']; }
	if ($_GET['height'] > 0) { $height = $_GET['height']; }

	if (strlen($code) % 2 == 1) { $code = "0$code"; }
	$lengths = array('nnwwn', 'wnnnw', 'nwnnw', 'wwnnn', 'nnwnw', 'wnwnn', 'nwwnn', 'nnnww', 'wnnwn', 'nwnwn');
	$string = 'nnnn';
	for ($i=0; $i<strlen($code); $i+=2) {
		for ($j=0; $j<5; $j++) {
			$string .= substr($lengths[substr($code, $i, 1)], $j, 1) . substr($lengths[substr($code, $i+1, 1)], $j, 1);
		}
	}
	$string .= "wnn";
	$image = imagecreate(10 + (9+strlen($code)*9)*$size, $height);
	$white = imagecolorallocate($image, 255, 255, 255);
	$black = imagecolorallocate($image, 0, 0, 0);
	$red = imagecolorallocate($image, 255, 0, 0);

	$cur = 5;
	for ($i=0; $i<strlen($string); $i++) {
		if ($i % 2 == 0) { $color = $black; } else { $color = $white; }
		if (substr($string, $i, 1) == 'w') {
			imagefilledrectangle($image, $cur, 0, $cur - 1 + 3 * $size, $height - 1, $color);
			$cur += 3 * $size;
		} else {
			imagefilledrectangle($image, $cur, 0, $cur - 1 + $size, $height - 1, $color);
			$cur += $size;
		}
	}

	if ($_GET['border'] == 1) {
		imagerectangle($image, 0, 0, 9 + (9+strlen($code)*9)*$size, $height - 1, $red);
		imagerectangle($image, 1, 1, 8 + (9+strlen($code)*9)*$size, $height - 2, $white);
	}

	header("Content-Type: image/png");
	imagepng($image);
}

barcode($_GET['barcode']);

?>
