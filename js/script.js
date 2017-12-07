$(document).ready(function(){

    var size;   //размер загружаемого файла
    var files;
    var percentComplete = 0;
    var token;

    $('input[type=file]').change(function(){
        $('.preview').hide();
        $('.load-progress img').hide();
        $(".progress-bar").css("width", 0+'%');
        size = parseFloat(($('#picture')[0].files[0].size/1024/1024).toFixed(2));
        files = this.files;
        if (size > 300) {
            $('#button').hide();
            $('.error').html('файл более 300Mb').show();
        } else {
            $('.error').html('Размер файла ' + size + ' Mb');
            $('#button').show();
        }
    });

    $('#button').on('click', function() {
        event.preventDefault();
        $('.block-image').html("");
        if (size <= 300) {
            $('.row .progress').show();
            percentComplete = 0;
            var data = new FormData();
            data.append( 'upfile', files[0] );
            data.append('token', $('[name=token]').val());
            $.ajax({
                xhr: function()
                {
                    var xhr = new window.XMLHttpRequest();
                    // прогресс загрузки на сервер
                    xhr.upload.addEventListener("progress", function(evt){
                        if (evt.lengthComputable) {
                            percentComplete = ( evt.loaded / evt.total ) * 100;
                            $('.progress-bar').html( Math.round( percentComplete ));
                            $(".progress-bar").css("width", percentComplete+'%');
                        }
                    }, false);
                    return xhr;
                },
                url: '/upload.php',
                type: 'post',
                data: data,
                cache: false,
                dataType: 'json',
                processData: false, // Не обрабатываем файлы (Don't process the files)
                contentType: false, // Так jQuery скажет серверу что это строковой запрос
                success: function( respond, textStatus, jqXHR ) {
                    if (respond.resp == 'success') {
                        $('.preview').html(respond.rhtml).show();
                        $('.load-progress img').show();
                        // отправляем второй запрос на обработку файла
                        var $createImg = $.ajax({
                            url: '/upload.php',
                            type: 'post',
                            data: 'create-image=' + respond.file,
                            cache: false,
                            dataType: 'json',
                            success: function (respond, textStatus, jqXHR) {
                                if (respond.resp == 'success') {
                                    $('.preview').html(respond.rhtml);
                                }
                                if (respond.resp == 'error') {
                                    $('.preview').html(respond.rhtml);
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                console.log(errorThrown);
                            },
                            complete: function () {
                                $('.load-progress img').hide();
                            }
                        });
                    }
                    if (respond.resp == 'error') {
                        $('.preview').html(respond.rhtml).show();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    errors = true;
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);

                },
                beforeSend: function () {
                    $('.progress').show();
                },
            });

        }
    });

});

