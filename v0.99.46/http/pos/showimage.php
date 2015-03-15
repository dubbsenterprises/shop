<?
session_start();
if ($_GET['id'] > 0 && $_GET['image_db_id'] > 0) {
        include_once('../../includes/shop.php');
        include_once('../../includes/general_functions.php');
        $General_DAL        = new General_DAL();
        $ImageInfo          = $General_DAL->get_ImageInfo_by_ImageID_and_image_db_id(quote_smart($_GET['id']),$_GET['image_db_id']);
                
        $Image_DAL          = new IMAGE_DATA_DAL($ImageInfo[0]->hostname,$ImageInfo[0]->username,$ImageInfo[0]->password,$ImageInfo[0]->databasename);
        $result             = $Image_DAL->get_ImageData_byID(quote_smart($_GET['id']));

        if (count($result) > 0) {
                $image = imagecreatefromstring(base64_decode($result[0]->image));
                $m1 = $m2 = 1;
                if ($result[0]->width == 0 ) { $result[0]->width = $ImageInfo[0]->width; }
                if ($result[0]->height == 0 ) { $result[0]->height = $ImageInfo[0]->height; }
                if (isset($_GET['w']) && $_GET['w'] > 0) { $m1 = $_GET['w'] / $result[0]->width; }
                if (isset($_GET['h']) && $_GET['h'] > 0) { $m2 = $_GET['h'] / $result[0]->height; }
                $m = $m1 > $m2 ? $m2 : $m1;
                $m = $m > 1 ? 1 : $m;
                $x = floor($result[0]->width * $m);
                $y = floor($result[0]->height * $m);
                $image2 = imagecreatetruecolor($x, $y);
                imagecopyresized($image2, $image, 0, 0, 0, 0, $x, $y, $result[0]->width, $result[0]->height);
                header("Content-Type: image/png");
                imagepng($image2);
        }
} else {
        $image = imagecreatetruecolor(80, 80);
        $black = imagecolorallocate($image, 0, 0, 0);
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image, 0, 0, 79, 79, $white);
        imageline($image, 0, 0, 79, 79, $black);
        imageline($image, 79, 0, 0, 79, $black);
        header("Content-Type: image/png");
        imagepng($image);
}
?>
