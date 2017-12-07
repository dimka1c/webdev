<?php
    session_start();

    require_once 'vendor/csrf.php';

    $csrf = createCsrf();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WEBDEV Загрузка изображений</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!--<link href="css/style.css" rel="stylesheet"> -->
    <link href="css/main.css" rel="stylesheet">

</head>
<body>
<div class="container">
    <div class="row form-top">
        <div class="col-md-6">
            <form method="post" id="load-picture" enctype="multipart/form-data">
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-1">
                        <span>Допустимые форматы <b style="color:#f94a88">jpg, png, gif</b></span>
                        <input type="file" name='file' id="picture" accept="image/jpeg,image/png,image/gif">
                        <input type="hidden" name="token" value="<?=$csrf?>">
                        <button type="submit" class="btn btn-default" id="button">Загрузить</button>
                    </div>
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                                    0%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="preview"></div>
        </div>
    </div>
    <div class="load-progress">
        <img src="/img/load.gif" alt="">
    </div>
</div>
<script src="js/jquery-3.1.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>
