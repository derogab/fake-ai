<?php require('../config.php'); ?>
<html>
    <head>
        <!-- title -->
        <title><?php print_r(PROJECT.' • learning'); ?></title>
        <!-- icon -->
        <link href="../assets/icon/favicon.ico" rel="icon" type="image/x-icon">
        <!-- css -->
        <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/app.css">
    </head>

    <body>
        <button id="learn" onclick="learn()">Learn Now</button>

        <div id="result">
            <p id="message"></p>
            <p id="error"></p>
        </div>

        <script src="../assets/js/jquery.min.js"></script>
        <script src="../assets/js/app.js"></script>
    </body>
</html>