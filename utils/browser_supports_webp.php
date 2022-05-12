<?php
    function browserSupportsWebp(){
        return str_contains($_SERVER['HTTP_ACCEPT']??"", 'image/webp');
    }