<?php
namespace Api\Util;

class UtilClass {
    
    public static function message($code_error, $message, $type = 'error') {
        header('Content-Type: application/json');
        http_response_code($code_error);
        exit(json_encode([$type => $message]));
    }

    public static function output($data, $code = 200) {
        header('Content-Type: application/json');
        http_response_code($code);
        exit(json_encode($data));
    }
}