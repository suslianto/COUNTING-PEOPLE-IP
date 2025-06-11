<?php
function CreateThumbnail($pic,$thumb,$thumbwidth, $quality = 90)
{

        $im1=ImageCreateFromJPEG($pic);

        //if(function_exists("exif_read_data")){
                $exif = exif_read_data($pic);
                if(!empty($exif['Orientation'])) {
                switch($exif['Orientation']) {
                case 8:
                    $im1 = imagerotate($im1,90,0);
                    break;
                case 3:
                    $im1 = imagerotate($im1,180,0);
                    break;
                case 6:
                    $im1 = imagerotate($im1,-90,0);
                    break;
                } 
                }
        //}
        $info = @getimagesize($pic);

        $width = $info[0];

        $w2=ImageSx($im1);
        $h2=ImageSy($im1);
        $w1 = ($thumbwidth <= $info[0]) ? $thumbwidth : $info[0]  ;

        $h1=floor($h2*($w1/$w2));
        $im2=imagecreatetruecolor($w1,$h1);

        imagecopyresampled ($im2,$im1,0,0,0,0,$w1,$h1,$w2,$h2); 
        $path=addslashes($thumb);
        ImageJPEG($im2,$path,$quality);
        ImageDestroy($im1);
        ImageDestroy($im2);
}
?>