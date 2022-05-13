<?php
    function camelCaseToSnakeCase($str) {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $str));  
    }