<?php
    function resizeImageProportionnaly($src, $newW, $newH) {
        $w = imagesx($src);
        $h = imagesy($src);
        $wRatio = $w/$newW;
        $hRatio = $h/$newH;
        
        if(abs($wRatio-1) > abs($hRatio-1))
            $newH = ceil($h / $wRatio);
        else
            $newW = ceil($w / $hRatio);
            
        $dst = imagecreatetruecolor($newW,$newH);
        imagecopyresampled($dst,$src,0,0,0,0,$newW,$newH,$w,$h);
        return $dst;
    }