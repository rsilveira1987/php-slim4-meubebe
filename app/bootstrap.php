<?php

    // vendor
    require_once __DIR__ . '/vendor/autoload.php';

    // Should be set to 0 in production
    error_reporting(E_ALL);

    // Should be set to '0' in production
    ini_set('display_errors', '1');

    // Settings
    $settings = [];

    define('ROOT',__DIR__);
    define('UPLOAD_PATH', ROOT . '/public/images/profile');

    function UUID_v4($data = null) {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);
    
        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    
        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
