<?php include_once("config.php"); ?>
<!DOCTYPE html>
<html lang="<?php print_r(LANGUAGE); ?>">
    <head>
        <title><?php print_r(PROJECT); ?></title>
        <!-- info -->
        <meta charset="UTF-8">
        <meta name="author" content="derogab">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv = 'Content-Language' content = '<?php echo LANGUAGE; ?>'/>
        <meta name="description" content="Fake Artificial intelligence using Twitter replies">
        <!-- icon -->
        <link href="assets/icon/favicon.ico" rel="icon" type="image/x-icon">
        <!-- css -->
        <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="assets/css/bootstrap-theme.min.css">
        <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="assets/css/jquerysctipttop.css">
        <link rel="stylesheet" type="text/css" href="assets/css/bender.css">
        <link rel="stylesheet" type="text/css" href="assets/css/app.css">

        <!--[if IE]>
            <script src="assets/js/html5shiv.min.js"></script>
            <script src="assets/js/respond.min.js"></script>
        <![endif]-->
            
    </head>

    <body>
        <div id="particles-js"></div>

        <div id="et">

            <!-- bender -->
            <?php require("bender.php"); ?>

            <!-- i/o -->
            <input type="text" id="question">
            <div id="reply"></div>
            <h1 id="error"></h1>
            <div id="log" class="hidden"></div>

        </div> <!--et-->

        <!-- js -->
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/jquery-migrate.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/starrr.min.js"></script>
        <script src="assets/js/particles.js"></script>
        <script src="assets/js/app.js"></script>
        <script>
        
            $(document).ready(function(){

                particlesJS.load('particles-js', 'assets/conf/particles.json', function() {
                    console.log('callback - particles.js config loaded');
                });

                $('#question').keypress(function(event) {

                    if (event.keyCode == 13) {

                        if ($('#question').val() != "") {
                            ask($('#question').val());
                        };
                    
                    }
                });

            });
            
        </script>
    </body>
</html>