<?php

    //database configuration
    $host       = "ls-792b9344ff5d9c02697c01268ccb68488c23d854.cbdgjqmgsybg.us-west-2.rds.amazonaws.com";
    $user       = "dbmasteruser";
    $pass       = "ebook_2021";
    $database   = "android_ebook_app";

    $connect = new mysqli($host, $user, $pass, $database);

    if (!$connect) {
        die ("connection failed: " . mysqli_connect_error());
    } else {
        $connect->set_charset('utf8');
    }

    $ENABLE_RTL_MODE = 'false';

?>