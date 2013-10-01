<?php

/**
 * CB_mime_type.php
 *
 * @copyright  2004-2007 CYBRiDGE
 * @license    CYBRiDGE 1.0
 */


///////////////////////////////
// MIME-TYPEの設定
///////////////////////////////

$GLOBALS['contents_type'] = array(
                "doc"=>array("application/msword","word.png"),
                "docx"=>array("application/msword","word.png"),
                "pdf"=>array("application/pdf","pdf.png"),
                "rdf"=>array("application/rdf+xml","download.png"),
                "rss"=>array("application/rss+xml","download.png"),
                "xls"=>array("application/vnd.ms-excel","excel.png"),
                "xlsx"=>array("application/vnd.ms-excel","excel.png"),
                "(pot|pps|pptx|ppt|ppz)"=>array("application/vnd.ms-powerpoint","ppt.png"),
                "gz"=>array("application/x-gzip","zip.png"),
                "cgi"=>array("application/x-httpd-cgi","download.png"),
                "(lha|lzh)"=>array("application/x-lzh","download.png"),
                "swf"=>array("application/x-shockwave-flash","download.png"),
                "zip"=>array("application/zip","zip.png"),
                "wav"=>array("audio/x-wav","download.png"),
                "bmp"=>array("image/bmp","download.png"),
                "fif"=>array("image/fif","download.png"),
                "gif"=>array("image/gif","gif.png"),
                "(jpe|jpeg|jpg)"=>array("image/jpeg","jpeg.png"),
                "png"=>array("image/png","png.png"),
                "(tif|tiff)"=>array("image/tiff","download.png"),
                "css"=>array("text/css","download.png"),
                "htm|html"=>array("text/html","download.png"),
                "asc|txt"=>array("text/plain","download.png"),
                "rtx"=>array("text/richtext","download.png"),
                "rtf"=>array("text/rtf","download.png"),
                "tsv"=>array("text/tab-separated-values","download.png"),
                "rt"=>array("text/vnd.rn-realtext","download.png"),
                "(xml|xsl)"=>array("text/xml","download.png"),
                "mp4"=>array("video/3gpp","download.png"),
                "(mpe|mpeg|mpg)"=>array("video/mpeg","download.png"),
                "wmv"=>array("video/x-ms-wmv","download.png"),
                "avi"=>array("video/x-msvideo","download.png"),
                "movie"=>array("video/x-sgi-movie","download.png"),
                );

$GLOBALS['image_type'] = array(
                "bmp"=>array("image/bmp",),
                "gif"=>array("image/gif",),
                "(jpe|jpeg|jpg)"=>array("image/jpeg",),
                "png"=>array("image/png",),
                );