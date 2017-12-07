<?php

error_reporting(false);

const IMG_PATH = '/img/';
const IMG_BLOCK_PATH = '/img/block/';

$allowTypes = array('image/jpeg','image/tiff');
$template = array (150, 300, 574, 300, 150); // массив с координатами нарезки (высота блока)


// создание шаблона preview изображения
function getHtmclCodeFile($file) {
    if (!empty($file)) {
        $html = '<img src="' . IMG_PATH . $file . '">';
    }
    return $html;
}

// создание шаблона html блока для вывода порезанного изображения
function createImageHtml($images)
{
    $html = "<div class='content'>";

    foreach ($images as $image) {
        $src = IMG_BLOCK_PATH . $image;
        $html .= "<div class='drop-img img1'><img src='$src' alt=''></div>";
    }
    $html .= "</div>";
    return $html;

}
// функция резки изображения на части
// колво блоков, на которое необходимо разбить изображение рассчитывается из колва значений в массиве $template
// высота блоков берется из массива $template
function cutImage($image, $template)
{

    $pattern = '#^(.+?)\.(.+?)$#';
    preg_match($pattern, $image, $matches);
    if (is_array($matches)) {
        $nameFile = $matches[1];
        $extension = strtolower($matches[2]);
    } else {
        return false;
    }
    // получаем расширение файла
    $imageFile = __DIR__. IMG_PATH . $image;
//    $extension = 'jpg';
//    $nameFile = $image;
    $numberOfParts = count($template);      // получаем количество частей, на которые необходимо разбить картинку
    $imageSize = getimagesize($imageFile);  // получаем размер изображения
    $width = $imageSize[0];                 // получаем ширину изображения - 800px
    $height = $imageSize[1];                // получаем высоту изображения - 578px
    $onePart = ceil($width / $numberOfParts);     // ширина одного блока изображения
    $middleOfImage = ceil($height / 2);           // середина изображения по высоте
    switch ($extension) {
        case "jpg":
            $srcf = @imagecreatefromjpeg($imageFile);
            break;
        case "gif":
            $srcf = @imagecreatefromgif($imageFile);
            break;
        case "png":
            $srcf = @imagecreatefrompng($imageFile);
            break;
    }
    if (!isset($srcf)) {
        return false;
    }
    $dst_x = 0;         // координата Х исходного изображения
    $dst_y = 0;         // координата Y исходного изображения
    $src_x = 0;         // x-координата исходного изображения
    $src_y = 0;         // y-координата исходного изображения
    $src_w = 0;         // Ширина исходного изображения
    $src_h = 0;         // Высота исходного изображения
    for ($i = 0; $i < $numberOfParts; $i++) {
        //$dest = imagecreatetruecolor($onePart, $template[$i]);
        $dest = imagecreatetruecolor($onePart, $template[$i]);
        $src_y = $middleOfImage - ceil($template[$i] / 2);
        if ($imagePart = imagecopy($dest, $srcf, $dst_x, $dst_y, $src_x, $src_y, $onePart, $template[$i])) {
            if (!$resultSaveParts = @imagegif($dest, __DIR__ . IMG_BLOCK_PATH . $nameFile . '_' . $i . '.' . $extension)) {
                return false;
            };
        };
        $src_x = $src_x + $onePart;         // новая x-координата исходного изображения
        $arrNames[] = $nameFile . '_' . $i . '.' . $extension;
    }
    imagedestroy($dest);
    imagedestroy($srcf);
    return $arrNames;
}


// *******************основная часть скрипта **************************
// ********************************************************************
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Если к нам идёт Ajax запрос, то ловим его
    // если есть post тогда это обработка разбития файла на части
    if (isset($_POST['create-image']) && (!empty($_POST['create-image']))) {
        $file = $_POST['create-image'];
        $arrImages = cutImage($file, $template);
        if ($arrImages == false) {
            $response = [
                'resp' => 'error',
                'rhtml' => '<div>Ошибка сервера</div>'
            ];
            echo json_encode($response);
            die();
        }
        if (is_array($arrImages)) {
            $html = createImageHtml($arrImages);
            $response = [
                'resp' => 'success',
                'rhtml' => $html,
            ];
            echo json_encode($response);
            die();
        }
    }
    if (isset($_FILES['upfile'])) {
        if (!in_array($_FILES['upfile']['type'], $allowTypes)) {
            $response = [
                'resp' => 'error',
                'rhtml' => '<h4 class="error">Недопустимый формат файла</h4>'
            ];
            echo json_encode($response);
            die();
        }
        $ext = $_FILES['upfile']['type'];
        $size = round(($_FILES['upfile']['size']/1024/1024), 2);
        if ($size <= 300 ) {
            //echo json_encode('файл загружен');
            // копируем файл из временной папки в каталог с картинками
            if (!@copy($_FILES['upfile']['tmp_name'], __DIR__ . IMG_PATH . $_FILES['upfile']['name'])) {
                $response = [
                    'resp' => 'error',
                    'rhtml' => '<h4 class="error">Ошибка передачи файла</h4>'
                ];
                echo json_encode($response);
                die();
            } else {
                //echo 'Загрузка удачна';
                // режем на части согласно шаблона
                $file = __DIR__ . IMG_PATH . $_FILES['upfile']['name'];
                $imgFile = $_FILES['upfile']['name'];
                $html = getHtmclCodeFile($_FILES['upfile']['name']);
                $response = [
                    'resp' => 'success',
                    'file' => $imgFile,
                    'rhtml' => $html
                ];
                echo json_encode($response);
                die();
            }
            // выводим в браузере
        } else {
            $response = [
                'resp' => 'error',
                'rhtml' => '<h4 class="error">Большой размер файла</h4>'
            ];
            echo json_encode($response);
            die();
        }
    }
}

?>