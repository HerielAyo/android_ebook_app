<?php
    //start session
    ob_start(); 
    session_start();
?>

<html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="msapplication-tap-highlight" content="no">
        <meta name="description" content="Android Ebook App is a mobile Ebook system which run under Android platform that used for your own ebook or story application. Using Admin Panel backend with powerful and responsive can manage unlimited books category and story, etc. This application created by Android for client side and then PHP MySQL for Admin side. Run under Android platform which is the most popular operating system in the world. Using this application you can save your money and time in creating application for your own Ebook application.">
        <meta name="keywords" content="android, app, template, android studio, material design, admob, firebase, json, ebook, ebook app, pdf, php, push notification">
        <title>Android Ebook App</title>
        <link rel="icon" href="assets/images/favicon.png" type="image/x-icon">
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
        <link href="assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="assets/plugins/node-waves/waves.css" rel="stylesheet" />
        <link href="assets/plugins/animate-css/animate.css" rel="stylesheet" />
        <link href="assets/css/style.css" rel="stylesheet">
        <link href="assets/css/custom.css" rel="stylesheet">
    </head>

    <body class="login-page">
        <?php include "public/login-form.php" ?>
        <script src="assets/plugins/jquery/jquery.min.js"></script>
        <script src="assets/plugins/bootstrap/js/bootstrap.js"></script>
        <script src="assets/plugins/node-waves/waves.js"></script>
        <script src="assets/plugins/jquery-validation/jquery.validate.js"></script>
        <script src="assets/js/admin.js"></script>
        <script src="assets/js/pages/examples/sign-in.js"></script>
    </body>

</html>