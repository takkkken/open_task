<?php

/**
 * CB_Thumbnail.php
 *
 * @copyright  2004-2007 CYBRiDGE
 * @license    CYBRiDGE 1.0
 */

require_once( LIB_DIR . "/thumbnail_v2/thumbnail.inc.php" );


class CB_Thumbnail extends Thumbnail {

    /**
     * Class constructor
     *
     * @param string $fileName
     * @return Thumbnail
     */
    function CB_Thumbnail($fileName) {
        //make sure the GD library is installed
    	if(!function_exists("gd_info")) {
        	echo 'You do not have the GD Library installed.  This class requires the GD library to function properly.' . "\n";
        	echo 'visit http://us2.php.net/manual/en/ref.image.php for more information';
        	exit;
        }
    	//initialize variables
        $this->errmsg               = '';
        $this->error                = false;
        $this->currentDimensions    = array();
        $this->newDimensions        = array();
        $this->fileName             = $fileName;
        $this->imageMeta			= array();
        $this->percent              = 100;
        $this->maxWidth             = 0;
        $this->maxHeight            = 0;

        //check to see if file exists
        if(!file_exists($this->fileName)) {
            $this->errmsg = 'File not found';
            $this->error = true;
        }
        //check to see if file is readable
        elseif(!is_readable($this->fileName)) {
            $this->errmsg = 'File is not readable';
            $this->error = true;
        }

        //if there are no errors, determine the file format
        if($this->error == false) {
            //check if gif
            if(stristr(strtolower($this->fileName),'.gif')) $this->format = 'GIF';
            //check if jpg
            elseif(stristr(strtolower($this->fileName),'.jpg') || stristr(strtolower($this->fileName),'.jpeg')) $this->format = 'JPG';
            //check if png
            elseif(stristr(strtolower($this->fileName),'.png')) $this->format = 'PNG';
            //unknown file format
            else {
                $this->errmsg = 'Unknown file format';
                $this->error = true;
            }
        }

        //initialize resources if no errors
        if($this->error == false) {
            switch($this->format) {
                case 'GIF':
                    $this->oldImage = ImageCreateFromGif($this->fileName);
                    break;
                case 'JPG':
                    $this->oldImage = ImageCreateFromJpeg($this->fileName);
                    break;
                case 'PNG':
                    $this->oldImage = ImageCreateFromPng($this->fileName);
                    break;
            }

            $size = GetImageSize($this->fileName);
            $this->currentDimensions = array('width'=>$size[0],'height'=>$size[1]);
            $this->newImage = $this->oldImage;
            $this->gatherImageMeta();
        }

        if($this->error == true) {
            $this->showErrorImage();
            break;
        }
    }

    function showErrorImage() {
        header('Content-type: image/png');
        $errImg = ImageCreate(220,25);
        $bgColor = imagecolorallocate($errImg,255,255,255);
        $fgColor1 = imagecolorallocate($errImg,255,0,0);
        $fgColor2 = imagecolorallocate($errImg,0,0,0);
        imagestring($errImg,3,6,6,'Error:',$fgColor2);
        imagestring($errImg,3,55,6,$this->errmsg,$fgColor1);
        imagepng($errImg);
        imagedestroy($errImg);
    }
    
    
    
}
