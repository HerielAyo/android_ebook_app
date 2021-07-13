<?php

    //database configuration
    $host       = "ls-2ecdaf3ca752beffe7326963663ab114b41ebfa3.cnzpnjlolslk.ap-south-1.rds.amazonaws.com";
    $user       = "dbmasteruser";
    $pass       = "android_ebook_app_2021";
    // $database   = "android_ebook_app_db";
    $database   = "android_ebook_app_2021";

    $connect = new mysqli($host, $user, $pass, $database);

    if (!$connect) {
        die ("connection failed: " . mysqli_connect_error());
    } else {
        $connect->set_charset('utf8');
    }

    $ENABLE_RTL_MODE = 'false';

?>