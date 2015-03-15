<?php

if ($_GET['cid'] > 0 && $_GET['pid'] > 0) {
	$path = "images/$_GET[cid]/$_GET[pid].jpg";
	if (file_exists($path)) {
		$size = getimagesize($path);
		$width = $size['0'];
		$height = $size['1'];
		$mul = $mul2 = 1;
		$fixwidth = $fixheight = $resize = 0;

		if ($_GET['width'] > 0) { $width = $_GET['width']; $fixedwidth = 1; $resize = 1; }
		if ($_GET['height'] > 0) { $height = $_GET['height']; $fixedheight = 1; $resize = 1; }

		if ($resize == 0 && !isset($_GET['max'])) {
			$tmp = imagecreatefromjpeg($path);
		} else {
			$in = imagecreatefromjpeg($path);
			if ($_GET['max'] > 0) {
				$mul = min($_GET['max'] / $size['0'], $_GET['max'] / $size['1']);
			}
			if ($fixedwidth == 1) {
				$mul = $width / $size['0'];
				if ($_GET['max'] > 0) {
					if (floor($width * $mul) > $_GET['max']) {
						$mul2 = $_GET['max'] / ($width * $mul);
					}
				}
			}
			if ($fixedheight == 1 && (!isset($mul) || $height / $size['1'] < $mul)) {
				$mul = $height / $size['1'];
				if ($_GET['max'] > 0) {
					if (floor($height * $mul > $_GET['max'])) {
						$mul2 = $_GET['max'] / ($height * $mul);
					}
				}
			}
			if ($fixedwidth == 0) {
				$width = floor($width * $mul * $mul2);
			}
			if ($fixedheight == 0) {
				$height = floor($height * $mul * $mul2);
			}
			$tmp = imagecreate($width, $height);
			imagecopyresized($tmp, $in, floor(($width - $size['0'] * $mul) / 2), floor(($height - $size['1'] * $mul) / 2), 0, 0, floor($size['0'] * $mul) - 1, floor($size['1'] * $mul) - 1, $size['0'] - 1, $size['1'] - 1);
		}
	}
}

if (!isset($tmp)) {
	$width = $height = 50;
	if ($_GET['max'] > 0) { $width = $height = $_GET['max']; }
	if ($_GET['width'] > 0) { $width = $_GET['width']; }
	if ($_GET['height'] > 0) { $height = $_GET['height']; }
	$tmp = imagecreate($width, $height);
	$white = imagecolorallocate($tmp, 255, 255, 255);
	$black = imagecolorallocate($tmp, 0, 0, 0);
	imagerectangle($tmp, 0, 0, $width - 1, $height - 1, $black);
	imagerectangle($tmp, 2, 2, $width - 3, $height - 3, $black);
}

if ($_GET['border'] == 1) {
	$out = imagecreate($width + 2, $height + 2);
	$black = imagecolorallocate($out, 0, 0, 0);
	imagecopy($out, $tmp, 1, 1, 0, 0, $width, $height);
} else {
	$out = $tmp;
}

header("Content-Type: image/png");
imagepng($out);

?>
