<?php   
    include_once(__DIR__ . "/../config/config.php");

    function sendEmail($to, $object, $content) {
        $headers = "";
        $headers .= "Reply-To: ReMiSoL <noreply@" . WEBSITE_URL . ">\r\n"; 
        $headers .= "Return-Path: ReMiSoL <noreply@" . WEBSITE_URL . ">\r\n"; 
        $headers .= "From: ReMiSoL <noreply@" . WEBSITE_URL . ">\r\n";  
        $headers .= "Organization: ReMiSoL\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/plain; charset=utf-8\r\n";
        $headers .= "X-Priority: 3\r\n";
        $headers .= "X-Mailer: PHP". phpversion() ."\r\n" ;

        return mail($to, $object, $content, $headers);
    }